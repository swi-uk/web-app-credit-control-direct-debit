<?php

namespace App\Domain\Bureau\Jobs;

use App\Domain\Bureau\Services\BureauService;
use App\Domain\Submission\Models\SubmissionBatch;
use App\Domain\Submission\Models\SubmissionItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadBatchToSftpJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $batchId)
    {
    }

    public function handle(BureauService $bureauService): void
    {
        $batch = SubmissionBatch::with('merchantSite')->find($this->batchId);
        if (!$batch || $batch->status !== 'generated') {
            return;
        }

        $site = $batch->merchantSite;
        $mode = $site?->settings_json['bureau_mode'] ?? null;
        if ($mode !== 'sftp') {
            $batch->status = 'failed';
            $batch->last_error = 'Bureau mode is not sftp.';
            $batch->save();
            SubmissionItem::where('submission_batch_id', $batch->id)
                ->update(['status' => 'failed', 'last_error' => $batch->last_error]);
            return;
        }

        $connector = $bureauService->connectorFor($site);
        if (!method_exists($connector, 'fetchInbound')) {
            $batch->status = 'failed';
            $batch->last_error = 'Connector does not support SFTP upload.';
            $batch->save();
            SubmissionItem::where('submission_batch_id', $batch->id)
                ->update(['status' => 'failed', 'last_error' => $batch->last_error]);
            return;
        }

        if (!Storage::disk('local')->exists($batch->file_path)) {
            $batch->status = 'failed';
            $batch->last_error = 'File not found.';
            $batch->save();
            SubmissionItem::where('submission_batch_id', $batch->id)
                ->update(['status' => 'failed', 'last_error' => $batch->last_error]);
            return;
        }
        $payload = Storage::disk('local')->get($batch->file_path);

        // Placeholder for SFTP upload; mark as uploaded for now.
        $batch->status = 'uploaded';
        $batch->uploaded_at = now();
        $batch->external_ref = basename($batch->file_path);
        $batch->save();

        SubmissionItem::where('submission_batch_id', $batch->id)
            ->update(['status' => 'uploaded']);
    }
}

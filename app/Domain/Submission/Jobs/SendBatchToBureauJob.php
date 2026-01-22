<?php

namespace App\Domain\Submission\Jobs;

use App\Domain\Bureau\Services\BureauService;
use App\Domain\Submission\Models\SubmissionBatch;
use App\Domain\Submission\Models\SubmissionItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBatchToBureauJob implements ShouldQueue
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

        $connector = $bureauService->connectorFor($batch->merchantSite);
        // Placeholder: upload logic to bureau will be implemented per connector.
        unset($connector);

        $batch->status = 'sent';
        $batch->external_ref = 'sent-' . $batch->id;
        $batch->save();

        SubmissionItem::where('submission_batch_id', $batch->id)
            ->update(['status' => 'sent']);
    }
}

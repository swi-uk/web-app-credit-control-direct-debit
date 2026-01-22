<?php

namespace App\Domain\Bureau\Jobs;

use App\Domain\Bacs\Jobs\ProcessBacsReportJob;
use App\Domain\Bacs\Models\BacsReport;
use App\Domain\Bureau\Services\BureauService;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PollSftpReportsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $merchantSiteId)
    {
    }

    public function handle(BureauService $bureauService): void
    {
        $site = MerchantSite::find($this->merchantSiteId);
        if (!$site) {
            return;
        }

        $mode = $site->settings_json['bureau_mode'] ?? null;
        if ($mode !== 'sftp') {
            return;
        }

        $connector = $bureauService->connectorFor($site);
        $package = $connector->fetchInbound(now()->subDays(config('ccdd.bureau_report_window_days', 7)), now());

        foreach ($package->files as $file) {
            $contents = $connector->downloadReport($file->remoteId);
            $hash = hash('sha256', $contents);
            $existing = BacsReport::where('merchant_id', $site->merchant_id)
                ->where(function ($query) use ($file, $hash) {
                    $query->where('remote_id', $file->remoteId)
                        ->orWhere('file_hash', $hash);
                })
                ->first();
            if ($existing) {
                continue;
            }

            $path = config('ccdd.bacs_storage_path', 'bacs') . '/' . now()->format('Ymd_His') . '_' . $file->filename;
            Storage::disk('local')->put($path, $contents);

            $report = BacsReport::create([
                'merchant_id' => $site->merchant_id,
                'type' => $file->type ?? 'ARUDD',
                'source' => 'bureau',
                'original_filename' => $file->filename,
                'file_path' => $path,
                'status' => 'pending',
                'remote_id' => $file->remoteId,
                'file_hash' => $hash,
            ]);

            ProcessBacsReportJob::dispatch($report->id);
        }
    }
}

<?php

namespace App\Domain\Bureau\Jobs;

use App\Domain\Bureau\Services\BureauService;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ArchiveRemoteFilesJob implements ShouldQueue
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
        // Archive logic is connector-specific and can be implemented later.
        unset($connector);
    }
}

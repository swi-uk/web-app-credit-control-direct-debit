<?php

namespace App\Console\Commands;

use App\Domain\Bureau\Jobs\ArchiveRemoteFilesJob;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Console\Command;

class ArchiveSftpReports extends Command
{
    protected $signature = 'ccdd:archive-sftp-reports';
    protected $description = 'Archive processed SFTP reports';

    public function handle(): int
    {
        $sites = MerchantSite::all();
        foreach ($sites as $site) {
            ArchiveRemoteFilesJob::dispatch($site->id);
        }

        $this->info('Dispatched SFTP archive jobs: ' . $sites->count());

        return self::SUCCESS;
    }
}

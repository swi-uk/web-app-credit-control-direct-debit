<?php

namespace App\Console\Commands;

use App\Domain\Bureau\Jobs\PollSftpReportsJob;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Console\Command;

class PollSftpReports extends Command
{
    protected $signature = 'ccdd:poll-sftp-reports';
    protected $description = 'Poll SFTP inbound directories for bureau reports';

    public function handle(): int
    {
        $sites = MerchantSite::all();
        foreach ($sites as $site) {
            PollSftpReportsJob::dispatch($site->id);
        }

        $this->info('Dispatched SFTP report polls: ' . $sites->count());

        return self::SUCCESS;
    }
}

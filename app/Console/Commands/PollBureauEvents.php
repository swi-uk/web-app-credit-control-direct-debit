<?php

namespace App\Console\Commands;

use App\Domain\Bureau\Jobs\PollBureauEventsJob;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Console\Command;

class PollBureauEvents extends Command
{
    protected $signature = 'ccdd:poll-bureau-events';
    protected $description = 'Poll bureau API events';

    public function handle(): int
    {
        $sites = MerchantSite::all();
        foreach ($sites as $site) {
            PollBureauEventsJob::dispatch($site->id);
        }

        $this->info('Dispatched bureau event polls: ' . $sites->count());

        return self::SUCCESS;
    }
}

<?php

namespace App\Console;

use App\Console\Commands\CreateMerchantSite;
use App\Console\Commands\DispatchWebhooks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CreateMerchantSite::class,
        DispatchWebhooks::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('ccdd:dispatch-webhooks')->everyMinute();
    }
}

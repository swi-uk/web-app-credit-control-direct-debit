<?php

namespace App\Console;

use App\Console\Commands\CreateMerchantSite;
use App\Console\Commands\DispatchWebhooks;
use App\Console\Commands\ImportBacsReport;
use App\Console\Commands\RunPaymentRetries;
use App\Console\Commands\SendAdvanceNotices;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CreateMerchantSite::class,
        DispatchWebhooks::class,
        ImportBacsReport::class,
        RunPaymentRetries::class,
        SendAdvanceNotices::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('ccdd:dispatch-webhooks')->everyMinute();
        $schedule->command('ccdd:run-payment-retries')->hourly();
        $schedule->command('ccdd:send-advance-notices')->daily();
    }
}

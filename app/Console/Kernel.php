<?php

namespace App\Console;

use App\Console\Commands\CreateMerchantSite;
use App\Console\Commands\DispatchWebhooks;
use App\Console\Commands\FetchBureauReports;
use App\Console\Commands\GenerateSubmissionBatches;
use App\Console\Commands\GenerateInvoices;
use App\Console\Commands\ImportBacsReport;
use App\Console\Commands\RunPaymentRetries;
use App\Console\Commands\SendAdvanceNotices;
use App\Console\Commands\SendSubmissionBatches;
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
        GenerateSubmissionBatches::class,
        SendSubmissionBatches::class,
        FetchBureauReports::class,
        GenerateInvoices::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('ccdd:dispatch-webhooks')->everyMinute();
        $schedule->command('ccdd:run-payment-retries')->hourly();
        $schedule->command('ccdd:send-advance-notices')->daily();
        $schedule->command('ccdd:generate-submission-batches')->hourly();
        $schedule->command('ccdd:send-submission-batches')->hourly();
        $schedule->command('ccdd:fetch-bureau-reports')->hourly();
        $schedule->command('ccdd:generate-invoices')->monthly();
    }
}

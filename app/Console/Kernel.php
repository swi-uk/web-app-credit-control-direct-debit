<?php

namespace App\Console;

use App\Console\Commands\CreateMerchantSite;
use App\Console\Commands\DispatchWebhooks;
use App\Console\Commands\FetchBureauReports;
use App\Console\Commands\GenerateSubmissionBatches;
use App\Console\Commands\GenerateInvoices;
use App\Console\Commands\PollSftpReports;
use App\Console\Commands\ArchiveSftpReports;
use App\Console\Commands\PollBureauEvents;
use App\Console\Commands\ApplyRetentionPolicies;
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
        PollSftpReports::class,
        ArchiveSftpReports::class,
        PollBureauEvents::class,
        ApplyRetentionPolicies::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('ccdd:dispatch-webhooks')->everyMinute();
        $schedule->command('ccdd:run-payment-retries')->hourly();
        $schedule->command('ccdd:send-advance-notices')->daily();
        $schedule->command('ccdd:generate-submission-batches')->hourly();
        $schedule->command('ccdd:send-submission-batches')->hourly();
        $schedule->command('ccdd:fetch-bureau-reports')->hourly();
        $schedule->command('ccdd:poll-sftp-reports')->hourly();
        $schedule->command('ccdd:archive-sftp-reports')->daily();
        $schedule->command('ccdd:poll-bureau-events')->hourly();
        $schedule->command('ccdd:generate-invoices')->monthly();
        $schedule->command('ccdd:apply-retention')->weekly();
    }
}

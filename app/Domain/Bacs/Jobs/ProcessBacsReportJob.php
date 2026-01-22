<?php

namespace App\Domain\Bacs\Jobs;

use App\Domain\Bacs\Models\BacsReport;
use App\Domain\Bacs\Services\BacsReportIngestionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBacsReportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $reportId)
    {
    }

    public function handle(BacsReportIngestionService $ingestionService): void
    {
        $report = BacsReport::find($this->reportId);
        if (!$report || $report->status !== 'pending') {
            return;
        }

        $ingestionService->process($report);
    }
}

<?php

namespace App\Domain\Submission\Jobs;

use App\Domain\Submission\Models\SubmissionBatch;
use App\Domain\Submission\Services\SubmissionBatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePaymentBatchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $merchantSiteId)
    {
    }

    public function handle(SubmissionBatchService $service): void
    {
        $batch = SubmissionBatch::create([
            'merchant_site_id' => $this->merchantSiteId,
            'type' => 'payment',
            'status' => 'pending',
            'format_version' => 'v1',
        ]);

        $service->generatePaymentBatch($batch);
    }
}

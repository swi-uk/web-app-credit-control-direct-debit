<?php

namespace App\Domain\Submission\Jobs;

use App\Domain\Submission\Models\SubmissionBatch;
use App\Domain\Submission\Services\SubmissionBatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMandateBatchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $batchId)
    {
    }

    public function handle(SubmissionBatchService $service): void
    {
        $batch = SubmissionBatch::find($this->batchId);
        if (!$batch || $batch->status !== 'pending') {
            return;
        }

        $service->generateMandateBatch($batch);
    }
}

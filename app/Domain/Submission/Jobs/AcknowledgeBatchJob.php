<?php

namespace App\Domain\Submission\Jobs;

use App\Domain\Submission\Models\SubmissionBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AcknowledgeBatchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $batchId)
    {
    }

    public function handle(): void
    {
        $batch = SubmissionBatch::find($this->batchId);
        if (!$batch || $batch->status !== 'sent') {
            return;
        }

        $batch->status = 'acknowledged';
        $batch->save();
    }
}

<?php

namespace App\Console\Commands;

use App\Domain\Submission\Jobs\SendBatchToBureauJob;
use App\Domain\Submission\Models\SubmissionBatch;
use Illuminate\Console\Command;

class SendSubmissionBatches extends Command
{
    protected $signature = 'ccdd:send-submission-batches';
    protected $description = 'Send generated batches to bureau';

    public function handle(): int
    {
        $batches = SubmissionBatch::where('status', 'generated')
            ->orderBy('id')
            ->get();

        foreach ($batches as $batch) {
            SendBatchToBureauJob::dispatch($batch->id);
        }

        $this->info('Dispatched batches: ' . $batches->count());

        return self::SUCCESS;
    }
}

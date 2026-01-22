<?php

namespace App\Console\Commands;

use App\Domain\Submission\Jobs\GenerateMandateBatchJob;
use App\Domain\Submission\Jobs\GeneratePaymentBatchJob;
use App\Domain\Submission\Models\SubmissionBatch;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Console\Command;

class GenerateSubmissionBatches extends Command
{
    protected $signature = 'ccdd:generate-submission-batches';
    protected $description = 'Generate outbound submission batches';

    public function handle(): int
    {
        $sites = MerchantSite::all();
        $count = 0;

        foreach ($sites as $site) {
            $mandateBatch = SubmissionBatch::create([
                'merchant_id' => $site->merchant_id,
                'merchant_site_id' => $site->id,
                'type' => 'mandate',
                'status' => 'pending',
            ]);
            GenerateMandateBatchJob::dispatch($mandateBatch->id);

            $paymentBatch = SubmissionBatch::create([
                'merchant_id' => $site->merchant_id,
                'merchant_site_id' => $site->id,
                'type' => 'payment',
                'status' => 'pending',
            ]);
            GeneratePaymentBatchJob::dispatch($paymentBatch->id);

            $count += 2;
        }

        $this->info('Queued batches: ' . $count);

        return self::SUCCESS;
    }
}

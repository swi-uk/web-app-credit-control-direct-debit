<?php

namespace App\Console\Commands;

use App\Domain\Submission\Jobs\GenerateMandateBatchJob;
use App\Domain\Submission\Jobs\GeneratePaymentBatchJob;
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
            $mode = $site->settings_json['bureau_mode'] ?? null;
            if ($mode !== 'sftp') {
                continue;
            }
            GenerateMandateBatchJob::dispatch($site->id);
            GeneratePaymentBatchJob::dispatch($site->id);
            $count += 2;
        }

        $this->info('Queued batches: ' . $count);

        return self::SUCCESS;
    }
}

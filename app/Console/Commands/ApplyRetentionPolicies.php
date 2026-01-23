<?php

namespace App\Console\Commands;

use App\Domain\Retention\Models\RetentionPolicy;
use App\Domain\Documents\Models\Document;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ApplyRetentionPolicies extends Command
{
    protected $signature = 'ccdd:apply-retention';
    protected $description = 'Apply data retention policies per merchant';

    public function handle(): int
    {
        $policies = RetentionPolicy::all();
        foreach ($policies as $policy) {
            $this->purgeDocuments($policy);
            $this->purgePayments($policy);
            $this->purgeMandates($policy);
        }

        $this->info('Retention policies applied.');

        return self::SUCCESS;
    }

    private function purgeDocuments(RetentionPolicy $policy): void
    {
        $cutoff = now()->subDays($policy->documents_retention_days);
        $documents = Document::where('merchant_id', $policy->merchant_id)
            ->where('created_at', '<', $cutoff)
            ->get();
        foreach ($documents as $doc) {
            if ($doc->file_path) {
                Storage::disk('local')->delete($doc->file_path);
            }
            $doc->delete();
        }
    }

    private function purgePayments(RetentionPolicy $policy): void
    {
        $cutoff = now()->subDays($policy->payments_retention_days);
        Payment::where('merchant_id', $policy->merchant_id)
            ->where('created_at', '<', $cutoff)
            ->delete();
    }

    private function purgeMandates(RetentionPolicy $policy): void
    {
        $cutoff = now()->subDays($policy->mandates_retention_days);
        Mandate::where('merchant_id', $policy->merchant_id)
            ->where('created_at', '<', $cutoff)
            ->delete();
    }
}

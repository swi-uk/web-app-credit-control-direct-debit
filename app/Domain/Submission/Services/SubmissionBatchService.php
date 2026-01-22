<?php

namespace App\Domain\Submission\Services;

use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use App\Domain\Submission\Contracts\OutboundFileGeneratorInterface;
use App\Domain\Submission\Generators\CsvGeneratorV1;
use App\Domain\Submission\Models\SubmissionBatch;
use App\Domain\Submission\Models\SubmissionItem;
use Illuminate\Support\Facades\Storage;

class SubmissionBatchService
{
    public function __construct(private readonly ?OutboundFileGeneratorInterface $generator = null)
    {
    }

    public function generateMandateBatch(SubmissionBatch $batch): void
    {
        $mandates = Mandate::whereHas('merchant', function ($query) use ($batch) {
                $query->where('id', $batch->merchantSite->merchant_id);
            })
            ->where('status', 'captured')
            ->whereNotIn('id', function ($query) {
                $query->select('entity_id')
                    ->from('submission_items')
                    ->where('entity_type', 'mandate')
                    ->whereIn('status', ['included', 'uploaded']);
            })
            ->limit(200)
            ->get();

        $generator = $this->generator ?? new CsvGeneratorV1();
        $generated = $generator->generateMandateFile($mandates->all());

        foreach ($mandates as $mandate) {
            SubmissionItem::create([
                'submission_batch_id' => $batch->id,
                'entity_type' => 'mandate',
                'entity_id' => $mandate->id,
                'status' => 'included',
            ]);
        }

        $path = $this->storeFile($generated);
        $batch->file_path = $path;
        $batch->file_sha256 = $generated->sha256;
        $batch->record_count = $generated->recordCount;
        $batch->format_version = $generated->formatVersion;
        $batch->generated_at = now();
        $batch->status = 'generated';
        $batch->save();
    }

    public function generatePaymentBatch(SubmissionBatch $batch): void
    {
        $payments = Payment::whereHas('merchant', function ($query) use ($batch) {
                $query->where('id', $batch->merchantSite->merchant_id);
            })
            ->where('status', 'scheduled')
            ->whereDate('due_date', '<=', now()->toDateString())
            ->whereNotIn('id', function ($query) {
                $query->select('entity_id')
                    ->from('submission_items')
                    ->where('entity_type', 'payment')
                    ->whereIn('status', ['included', 'uploaded']);
            })
            ->limit(200)
            ->get();

        $generator = $this->generator ?? new CsvGeneratorV1();
        $generated = $generator->generatePaymentFile($payments->all());

        foreach ($payments as $payment) {
            SubmissionItem::create([
                'submission_batch_id' => $batch->id,
                'entity_type' => 'payment',
                'entity_id' => $payment->id,
                'status' => 'included',
            ]);
        }

        $path = $this->storeFile($generated);
        $batch->file_path = $path;
        $batch->file_sha256 = $generated->sha256;
        $batch->record_count = $generated->recordCount;
        $batch->format_version = $generated->formatVersion;
        $batch->generated_at = now();
        $batch->status = 'generated';
        $batch->save();
    }

    private function storeFile($generated): string
    {
        $path = 'bureau/outbound/' . $generated->filename;
        Storage::disk('local')->put($path, $generated->contents);

        return $path;
    }
}

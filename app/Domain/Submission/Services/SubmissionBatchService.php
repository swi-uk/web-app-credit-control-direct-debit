<?php

namespace App\Domain\Submission\Services;

use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use App\Domain\Submission\Models\SubmissionBatch;
use App\Domain\Submission\Models\SubmissionItem;
use Illuminate\Support\Facades\Storage;

class SubmissionBatchService
{
    public function generateMandateBatch(SubmissionBatch $batch): void
    {
        $mandates = Mandate::where('merchant_id', $batch->merchant_id)
            ->where('status', 'captured')
            ->whereNotIn('id', function ($query) {
                $query->select('entity_id')
                    ->from('submission_items')
                    ->where('entity_type', 'mandate')
                    ->whereIn('status', ['included', 'sent']);
            })
            ->limit(200)
            ->get();

        $rows = [];
        foreach ($mandates as $mandate) {
            $rows[] = [
                'mandate_id' => $mandate->id,
                'reference' => $mandate->reference,
                'customer_id' => $mandate->customer_id,
                'created_at' => $mandate->created_at?->toDateTimeString(),
            ];
            SubmissionItem::create([
                'submission_batch_id' => $batch->id,
                'entity_type' => 'mandate',
                'entity_id' => $mandate->id,
                'status' => 'included',
            ]);
        }

        $path = $this->writeCsv($batch, $rows, ['mandate_id', 'reference', 'customer_id', 'created_at']);
        $batch->file_path = $path;
        $batch->status = 'generated';
        $batch->save();
    }

    public function generatePaymentBatch(SubmissionBatch $batch): void
    {
        $payments = Payment::where('merchant_id', $batch->merchant_id)
            ->where('status', 'scheduled')
            ->whereNotIn('id', function ($query) {
                $query->select('entity_id')
                    ->from('submission_items')
                    ->where('entity_type', 'payment')
                    ->whereIn('status', ['included', 'sent']);
            })
            ->limit(200)
            ->get();

        $rows = [];
        foreach ($payments as $payment) {
            $rows[] = [
                'payment_id' => $payment->id,
                'external_order_id' => $payment->external_order_id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'due_date' => $payment->due_date,
            ];
            SubmissionItem::create([
                'submission_batch_id' => $batch->id,
                'entity_type' => 'payment',
                'entity_id' => $payment->id,
                'status' => 'included',
            ]);
        }

        $path = $this->writeCsv($batch, $rows, ['payment_id', 'external_order_id', 'amount', 'currency', 'due_date']);
        $batch->file_path = $path;
        $batch->status = 'generated';
        $batch->save();
    }

    private function writeCsv(SubmissionBatch $batch, array $rows, array $header): string
    {
        $filename = $batch->type . '_batch_' . $batch->id . '.csv';
        $path = 'bureau/outbound/' . $filename;

        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, $header);
        foreach ($rows as $row) {
            fputcsv($handle, array_values($row));
        }
        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        Storage::disk('local')->put($path, $contents ?? '');

        return $path;
    }
}

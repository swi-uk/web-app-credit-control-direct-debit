<?php

namespace App\Domain\Submission\Generators;

use App\Domain\Submission\Contracts\OutboundFileGeneratorInterface;
use App\Domain\Submission\DTO\GeneratedFile;

class CsvGeneratorV1 implements OutboundFileGeneratorInterface
{
    public function generateMandateFile(array $mandates): GeneratedFile
    {
        $rows = [];
        foreach ($mandates as $mandate) {
            $rows[] = [
                $mandate->id,
                $mandate->reference,
                $mandate->customer_id,
                $mandate->created_at?->toDateTimeString(),
            ];
        }

        $contents = $this->csvContents(['mandate_id', 'reference', 'customer_id', 'created_at'], $rows);

        return new GeneratedFile(
            'mandates_v1_' . now()->format('Ymd_His') . '.csv',
            $contents,
            count($rows),
            hash('sha256', $contents),
            'v1'
        );
    }

    public function generatePaymentFile(array $payments): GeneratedFile
    {
        $rows = [];
        foreach ($payments as $payment) {
            $rows[] = [
                $payment->id,
                $payment->external_order_id,
                $payment->amount,
                $payment->currency,
                $payment->due_date,
            ];
        }

        $contents = $this->csvContents(['payment_id', 'external_order_id', 'amount', 'currency', 'due_date'], $rows);

        return new GeneratedFile(
            'payments_v1_' . now()->format('Ymd_His') . '.csv',
            $contents,
            count($rows),
            hash('sha256', $contents),
            'v1'
        );
    }

    private function csvContents(array $header, array $rows): string
    {
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, $header);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return $contents ?: '';
    }
}

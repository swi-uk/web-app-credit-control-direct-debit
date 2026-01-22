<?php

namespace App\Domain\Bacs\Services;

use App\Domain\Bacs\Models\BacsReport;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;

class BacsMatcher
{
    public function match(BacsReport $report, array $data): array
    {
        $externalOrderId = $data['external_order_id'] ?? null;
        if ($externalOrderId) {
            $payment = Payment::where('merchant_id', $report->merchant_id)
                ->where('external_order_id', $externalOrderId)
                ->orderByDesc('id')
                ->first();
            if ($payment) {
                return ['type' => 'payment', 'model' => $payment];
            }
        }

        $reference = $data['reference'] ?? null;
        if ($reference) {
            $mandate = Mandate::where('merchant_id', $report->merchant_id)
                ->where('reference', $reference)
                ->first();
            if ($mandate) {
                return ['type' => 'mandate', 'model' => $mandate];
            }

            $payment = Payment::where('merchant_id', $report->merchant_id)
                ->where('external_order_key', $reference)
                ->orderByDesc('id')
                ->first();
            if ($payment) {
                return ['type' => 'payment', 'model' => $payment];
            }
        }

        $amount = $data['amount'] ?? null;
        if ($amount !== null && is_numeric($amount)) {
            $normalizedAmount = number_format((float) $amount, 2, '.', '');
            $payment = Payment::where('merchant_id', $report->merchant_id)
                ->whereIn('status', ['scheduled', 'submitted'])
                ->where('amount', $normalizedAmount)
                ->orderByDesc('id')
                ->first();
            if ($payment) {
                return ['type' => 'payment', 'model' => $payment];
            }
        }

        return ['type' => 'none', 'model' => null];
    }
}

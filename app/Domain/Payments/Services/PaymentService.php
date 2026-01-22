<?php

namespace App\Domain\Payments\Services;

use App\Domain\Mandates\Models\Mandate;
use App\Domain\Orders\Models\OrderLink;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentEvent;

class PaymentService
{
    public function createFromOrderLink(OrderLink $orderLink, Mandate $mandate): Payment
    {
        $customer = $orderLink->customer;
        $merchant = $orderLink->merchantSite->merchant;
        $creditProfile = $customer->creditProfile;
        $daysDefault = (int) ($creditProfile?->days_default ?? 0);

        $payment = Payment::create([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'mandate_id' => $mandate->id,
            'source_site_id' => $orderLink->merchant_site_id,
            'external_order_id' => $orderLink->external_order_id,
            'external_order_key' => $orderLink->external_order_key,
            'external_order_type' => $orderLink->external_order_type ?? 'order',
            'amount' => $orderLink->amount,
            'currency' => $orderLink->currency,
            'due_date' => now()->addDays($daysDefault)->toDateString(),
            'status' => 'scheduled',
            'retry_count' => 0,
        ]);

        PaymentEvent::create([
            'payment_id' => $payment->id,
            'event_type' => 'scheduled',
            'amount' => $payment->amount,
            'occurred_at' => now(),
            'metadata_json' => ['source' => 'created'],
        ]);

        return $payment;
    }
}

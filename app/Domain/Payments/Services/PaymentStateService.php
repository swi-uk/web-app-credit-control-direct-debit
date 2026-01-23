<?php

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentEvent;
use App\Domain\Credit\Services\CreditTierService;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Risk\Policies\RiskPolicyService;
use App\Domain\Risk\Services\RiskScoringService;
use App\Domain\Webhooks\Services\WebhookOutboxService;

class PaymentStateService
{
    private array $allowedTransitions = [
        'scheduled' => ['submitted', 'collected', 'unpaid_returned', 'cancelled', 'retry_scheduled'],
        'submitted' => ['processing', 'collected', 'unpaid_returned', 'failed_final', 'retry_scheduled'],
        'processing' => ['collected', 'unpaid_returned', 'failed_final', 'retry_scheduled'],
        'retry_scheduled' => ['scheduled', 'submitted', 'failed_final'],
        'unpaid_returned' => ['retry_scheduled', 'failed_final'],
        'failed_final' => [],
        'cancelled' => [],
        'collected' => [],
    ];

    public function __construct(
        private readonly CreditTierService $creditTierService,
        private readonly WebhookOutboxService $webhookOutboxService,
        private readonly RiskScoringService $riskScoringService,
        private readonly RiskPolicyService $riskPolicyService
    )
    {
    }

    public function transition(Payment $payment, string $toStatus, array $metadata = []): bool
    {
        if ($payment->status === $toStatus) {
            return false;
        }

        $allowed = $this->allowedTransitions[$payment->status] ?? [];
        if (!in_array($toStatus, $allowed, true)) {
            return false;
        }

        $payment->status = $toStatus;
        $payment->save();

        PaymentEvent::create([
            'payment_id' => $payment->id,
            'event_type' => $toStatus,
            'amount' => $payment->amount,
            'occurred_at' => now(),
            'metadata_json' => $metadata,
        ]);

        $customer = $payment->customer;
        if ($customer && $customer->creditProfile) {
            if ($toStatus === 'collected') {
                $customer->creditProfile->successful_collections += 1;
                $customer->creditProfile->save();
            }
            $this->creditTierService->assignTier($customer);
            $score = $this->riskScoringService->score($customer);
            $this->riskPolicyService->apply($customer, $score);
        }

        $site = $payment->sourceSite ?: $payment->merchant?->sites?->first();
        if ($site) {
            $this->webhookOutboxService->enqueue($site, 'payment.update', $this->buildPaymentPayload($payment, $site));
        }

        return true;
    }

    private function buildPaymentPayload(Payment $payment, $site): array
    {
        $customer = $payment->customer;
        $customer->load('creditProfile');
        $externalLink = ExternalLink::where('merchant_site_id', $site->id)
            ->where('entity_type', 'customer')
            ->where('entity_id', $customer->id)
            ->first();

        $externalCustomerId = $externalLink?->external_id;
        $externalCustomerType = $externalLink?->external_type ?? 'user';
        $legacyWooUserId = $site->platform === 'woocommerce' ? $externalCustomerId : null;
        $legacyOrderId = $site->platform === 'woocommerce' ? $payment->external_order_id : null;

        return [
            'type' => 'payment.update',
            'data' => [
                'external_order_id' => $payment->external_order_id,
                'external_order_type' => $payment->external_order_type ?? 'order',
                'external_customer_id' => $externalCustomerId,
                'external_customer_type' => $externalCustomerType,
                'order_id' => $legacyOrderId,
                'woocommerce_user_id' => $legacyWooUserId,
                'mandate_status' => $payment->mandate?->status,
                'payment_status' => $payment->status,
                'current_exposure' => $customer->creditProfile?->current_exposure_amount,
                'credit_status' => $customer->status,
            ],
        ];
    }
}

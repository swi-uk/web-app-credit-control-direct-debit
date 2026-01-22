<?php

namespace App\Console\Commands;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Payments\Models\Payment;
use App\Domain\Webhooks\Services\WebhookOutboxService;
use Illuminate\Console\Command;

class RunPaymentRetries extends Command
{
    protected $signature = 'ccdd:run-payment-retries';
    protected $description = 'Release retry scheduled payments for processing';

    public function __construct(private readonly WebhookOutboxService $webhookOutboxService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $payments = Payment::with(['customer', 'merchant', 'sourceSite', 'mandate'])
            ->where('status', 'retry_scheduled')
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now())
            ->get();

        foreach ($payments as $payment) {
            $payment->status = 'scheduled';
            $payment->next_retry_at = null;
            $payment->save();

            AuditEvent::create([
                'merchant_id' => $payment->merchant_id,
                'customer_id' => $payment->customer_id,
                'event_type' => 'payment.retry_released',
                'message' => null,
                'payload_json' => [
                    'payment_id' => $payment->id,
                ],
                'created_at' => now(),
            ]);

            $site = $payment->sourceSite ?: $payment->merchant->sites()->first();
            if ($site) {
                $this->webhookOutboxService->enqueue($site, 'payment.update', $this->buildPaymentPayload($payment, $site));
            }
        }

        $this->info('Processed retries: ' . $payments->count());

        return self::SUCCESS;
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

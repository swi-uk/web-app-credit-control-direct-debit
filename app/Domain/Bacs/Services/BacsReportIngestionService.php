<?php

namespace App\Domain\Bacs\Services;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Bacs\Models\BacsReport;
use App\Domain\Bacs\Models\BacsReportItem;
use App\Domain\Customers\Models\Customer;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Credit\Services\CreditTierService;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use App\Domain\Webhooks\Services\WebhookOutboxService;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BacsReportIngestionService
{
    public function __construct(
        private readonly AruddParser $aruddParser,
        private readonly AddacsParser $addacsParser,
        private readonly BacsMatcher $matcher,
        private readonly WebhookOutboxService $webhookOutboxService,
        private readonly CreditTierService $creditTierService
    ) {
    }

    public function process(BacsReport $report): void
    {
        if ($report->status === 'processed') {
            return;
        }

        try {
            $contents = Storage::disk('local')->get($report->file_path);
            $items = $report->type === 'ARUDD'
                ? $this->aruddParser->parse($contents)
                : $this->addacsParser->parse($contents);

            foreach ($items as $data) {
                $itemHash = $this->hashItem($data['raw'] ?? $data);
                if ($this->existsInReport($report, $itemHash)) {
                    continue;
                }
                $duplicateAcrossMerchant = $this->isDuplicateAcrossMerchant($report, $itemHash);

                $item = BacsReportItem::create([
                    'bacs_report_id' => $report->id,
                    'record_type' => $report->type,
                    'reference' => $data['reference'] ?? null,
                    'external_order_id' => $data['external_order_id'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'code' => $data['code'] ?? null,
                    'description' => $data['description'] ?? null,
                    'raw_json' => $this->normalizeRaw($data['raw'] ?? $data),
                    'matched_entity_type' => 'none',
                    'matched_entity_id' => null,
                    'item_hash' => $itemHash,
                ]);

                if ($duplicateAcrossMerchant) {
                    continue;
                }

                $match = $this->matcher->match($report, $data);
                if ($match['type'] !== 'none' && $match['model']) {
                    $item->matched_entity_type = $match['type'];
                    $item->matched_entity_id = $match['model']->id;
                    $item->save();
                }

                if ($report->type === 'ARUDD' && $match['type'] === 'payment') {
                    $this->applyArudd($report, $item, $match['model']);
                }

                if ($report->type === 'ADDACS' && $match['type'] === 'mandate') {
                    $this->applyAddacs($report, $item, $match['model']);
                }
            }

            $report->status = 'processed';
            $report->processed_at = now();
            $report->save();
        } catch (Throwable $e) {
            $report->status = 'failed';
            $report->save();
            throw $e;
        }
    }

    private function applyArudd(BacsReport $report, BacsReportItem $item, Payment $payment): void
    {
        if ($payment->status === 'failed_final') {
            return;
        }

        $payment->failure_code = $item->code;
        $payment->failure_description = $item->description;
        $payment->reported_at = now();
        $payment->status = 'unpaid_returned';
        $payment->save();

        $this->audit($payment, 'payment.unpaid_returned', [
            'code' => $item->code,
            'description' => $item->description,
        ]);

        $policy = $this->retryPolicy($report);
        $customer = $payment->customer;
        $customer->load('creditProfile');
        $creditProfile = $customer->creditProfile;

        if ($creditProfile) {
            $creditProfile->bounces_60d = $creditProfile->bounces_60d + 1;
            $creditProfile->save();
        }

        if ($payment->retry_count < $policy['max_retries']) {
            $payment->retry_count = $payment->retry_count + 1;
            $payment->status = 'retry_scheduled';
            $payment->next_retry_at = now()->addDays($policy['retry_days'][$payment->retry_count] ?? 3);
            $payment->save();

            $this->audit($payment, 'payment.retry_scheduled', [
                'retry_count' => $payment->retry_count,
                'next_retry_at' => $payment->next_retry_at,
            ]);

            if ($customer->status !== 'locked') {
                $customer->status = 'restricted';
                $customer->save();
            }
        } else {
            $payment->status = 'failed_final';
            $payment->save();

            $this->audit($payment, 'payment.failed_final', [
                'retry_count' => $payment->retry_count,
            ]);

            $customer->status = 'locked';
            $customer->lock_reason = 'Payment overdue / unpaid';
            $customer->locked_at = now();
            $customer->save();

            $creditProfile = $customer->creditProfile;
            if ($creditProfile) {
                $creditProfile->lock_reason = 'Payment overdue / unpaid';
                $creditProfile->save();
            }
        }

        if ($creditProfile) {
            $this->creditTierService->assignTier($customer);
        }

        $site = $payment->sourceSite ?: $payment->merchant->sites()->first();
        if ($site) {
            $this->webhookOutboxService->enqueue($site, 'payment.update', $this->buildPaymentPayload($payment, $site));
            $this->webhookOutboxService->enqueue($site, 'customer.credit.update', $this->buildCreditPayload($customer, $site));
            if ($customer->status === 'locked') {
                $this->webhookOutboxService->enqueue($site, 'customer.lock', $this->buildCustomerLockPayload($customer, $site));
            }
        }
    }

    private function applyAddacs(BacsReport $report, BacsReportItem $item, Mandate $mandate): void
    {
        $status = $this->resolveAddacsStatus($item->code, $item->description);
        $mandate->status = $status;
        $mandate->addacs_code = $item->code;
        $mandate->addacs_description = $item->description;
        $mandate->reported_at = now();
        $mandate->save();

        $this->auditMandate($mandate, 'mandate.addacs_update', [
            'code' => $item->code,
            'description' => $item->description,
            'status' => $status,
        ]);

        $customer = $mandate->customer;
        $customer->load('creditProfile');

        $pendingPayments = $mandate->payments()
            ->whereIn('status', ['scheduled', 'submitted', 'processing', 'retry_scheduled'])
            ->get();

        foreach ($pendingPayments as $payment) {
            $payment->status = 'cancelled';
            $payment->save();

            $site = $payment->sourceSite ?: $payment->merchant->sites()->first();
            if ($site) {
                $this->webhookOutboxService->enqueue($site, 'payment.update', $this->buildPaymentPayload($payment, $site));
            }
        }

        if ($status === 'cancelled') {
            $hasUnpaid = $customer->payments()
                ->whereIn('status', ['unpaid_returned', 'retry_scheduled', 'failed_final'])
                ->exists();
            if ($hasUnpaid && $customer->status !== 'locked') {
                $customer->status = 'restricted';
                $customer->save();
            }
        }

        $sites = $pendingPayments->map(fn ($payment) => $payment->sourceSite)
            ->filter()
            ->unique('id');
        if ($sites->isEmpty()) {
            $sites = $mandate->merchant->sites()->get();
        }
        foreach ($sites as $site) {
            $this->webhookOutboxService->enqueue($site, 'customer.credit.update', $this->buildCreditPayload($customer, $site));
        }
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

    private function buildCreditPayload(Customer $customer, $site): array
    {
        $customer->load('creditProfile');
        $externalLink = ExternalLink::where('merchant_site_id', $site->id)
            ->where('entity_type', 'customer')
            ->where('entity_id', $customer->id)
            ->first();
        $externalCustomerId = $externalLink?->external_id;
        $externalCustomerType = $externalLink?->external_type ?? 'user';
        $legacyWooUserId = $site->platform === 'woocommerce' ? $externalCustomerId : null;

        return [
            'type' => 'customer.credit.update',
            'data' => [
                'external_customer_id' => $externalCustomerId,
                'external_customer_type' => $externalCustomerType,
                'woocommerce_user_id' => $legacyWooUserId,
                'credit_status' => $customer->status,
                'credit_limit_amount' => $customer->creditProfile?->limit_amount,
                'credit_days_max' => $customer->creditProfile?->days_max,
                'current_exposure' => $customer->creditProfile?->current_exposure_amount,
                'lock_reason' => $customer->lock_reason ?? '',
            ],
        ];
    }

    private function buildCustomerLockPayload(Customer $customer, $site): array
    {
        $externalLink = ExternalLink::where('merchant_site_id', $site->id)
            ->where('entity_type', 'customer')
            ->where('entity_id', $customer->id)
            ->first();
        $externalCustomerId = $externalLink?->external_id;
        $externalCustomerType = $externalLink?->external_type ?? 'user';

        return [
            'type' => 'customer.lock',
            'data' => [
                'external_customer_id' => $externalCustomerId,
                'external_customer_type' => $externalCustomerType,
                'status' => $customer->status,
                'lock_reason' => $customer->lock_reason ?? '',
            ],
        ];
    }

    private function resolveAddacsStatus(?string $code, ?string $description): string
    {
        $normalizedCode = strtoupper((string) $code);
        $normalizedDescription = strtolower((string) $description);

        if (str_contains($normalizedDescription, 'cancel')
            || in_array($normalizedCode, ['C', '1', '2', '3'], true)) {
            return 'cancelled';
        }

        return 'rejected';
    }

    private function retryPolicy(BacsReport $report): array
    {
        $settings = $report->merchant?->settings_json ?? [];
        return [
            'retry_days' => [
                1 => $settings['retry_1_days'] ?? config('ccdd.retry_1_days', 3),
                2 => $settings['retry_2_days'] ?? config('ccdd.retry_2_days', 7),
            ],
            'max_retries' => $settings['max_retries'] ?? config('ccdd.max_retries', 2),
        ];
    }

    private function audit(Payment $payment, string $type, array $payload = []): void
    {
        AuditEvent::create([
            'merchant_id' => $payment->merchant_id,
            'customer_id' => $payment->customer_id,
            'event_type' => $type,
            'message' => null,
            'payload_json' => $payload,
            'created_at' => now(),
        ]);
    }

    private function auditMandate(Mandate $mandate, string $type, array $payload = []): void
    {
        AuditEvent::create([
            'merchant_id' => $mandate->merchant_id,
            'customer_id' => $mandate->customer_id,
            'event_type' => $type,
            'message' => null,
            'payload_json' => $payload,
            'created_at' => now(),
        ]);
    }

    private function normalizeRaw($raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }

        return ['raw' => $raw];
    }

    private function hashItem($raw): string
    {
        return hash('sha256', json_encode($this->normalizeRaw($raw)));
    }

    private function existsInReport(BacsReport $report, string $itemHash): bool
    {
        return BacsReportItem::where('bacs_report_id', $report->id)
            ->where('item_hash', $itemHash)
            ->exists();
    }

    private function isDuplicateAcrossMerchant(BacsReport $report, string $itemHash): bool
    {
        return BacsReportItem::where('item_hash', $itemHash)
            ->whereHas('report', function ($query) use ($report) {
                $query->where('merchant_id', $report->merchant_id)
                    ->where('id', '!=', $report->id);
            })
            ->exists();
    }
}

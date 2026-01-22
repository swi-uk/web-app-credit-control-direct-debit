<?php

namespace App\Domain\Channels\Controllers;

use App\Domain\Channels\Http\Requests\UpdateCreditRequest;
use App\Domain\Customers\Models\Customer;
use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Customers\Services\CustomerService;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Merchants\Models\MerchantSite;
use App\Domain\Webhooks\Services\WebhookOutboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ChannelCustomerController extends Controller
{
    public function __construct(
        private readonly WebhookOutboxService $webhookOutboxService,
        private readonly CustomerService $customerService
    ) {
    }

    public function updateCredit(UpdateCreditRequest $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');

        return $this->updateCreditFromPayload($site, $request->validated());
    }

    public function updateCreditFromPayload(MerchantSite $site, array $payload): JsonResponse
    {
        $customerPayload = $payload['customer'] ?? [];
        $externalCustomerId = $customerPayload['external_customer_id'] ?? null;
        $externalCustomerType = $customerPayload['external_customer_type'] ?? 'user';

        $link = ExternalLink::where('merchant_site_id', $site->id)
            ->where('entity_type', 'customer')
            ->where('external_type', $externalCustomerType)
            ->where('external_id', $externalCustomerId)
            ->first();

        if (!$link) {
            return response()->json(['error' => 'not_found'], 404);
        }

        $customer = Customer::where('merchant_id', $site->merchant_id)
            ->where('id', $link->entity_id)
            ->first();

        if (!$customer) {
            return response()->json(['error' => 'not_found'], 404);
        }

        $creditData = $payload['credit'] ?? [];
        if (array_key_exists('status', $creditData) && $creditData['status']) {
            $customer->status = $creditData['status'];
        }
        if (array_key_exists('lock_reason', $creditData)) {
            $customer->lock_reason = $creditData['lock_reason'];
        }
        $customer->save();

        $creditProfile = $customer->creditProfile;
        if (!$creditProfile) {
            $creditProfile = new CreditProfile([
                'customer_id' => $customer->id,
                'limit_amount' => 250,
                'current_exposure_amount' => 0,
                'days_max' => 14,
                'days_default' => 14,
            ]);
        }
        if (array_key_exists('limit_amount', $creditData)) {
            $creditProfile->limit_amount = $creditData['limit_amount'];
        }
        if (array_key_exists('days_max', $creditData)) {
            $creditProfile->days_max = $creditData['days_max'];
        }
        if (array_key_exists('days_default', $creditData)) {
            $creditProfile->days_default = $creditData['days_default'];
        }
        $creditProfile->save();

        $externalUserId = $this->customerService->getExternalUserId($customer, $site, $externalCustomerType);
        $legacyWooUserId = $site->platform === 'woocommerce' ? $externalUserId : null;

        $payload = [
            'type' => 'customer.credit.update',
            'data' => [
                'external_customer_id' => $externalUserId,
                'external_customer_type' => $externalCustomerType,
                'woocommerce_user_id' => $legacyWooUserId,
                'credit_status' => $customer->status,
                'credit_limit_amount' => $creditProfile->limit_amount,
                'credit_days_max' => $creditProfile->days_max,
                'current_exposure' => $creditProfile->current_exposure_amount,
                'lock_reason' => $customer->lock_reason ?? '',
            ],
        ];

        $this->webhookOutboxService->enqueue($site, 'customer.credit.update', $payload);

        return response()->json(['ok' => true]);
    }
}

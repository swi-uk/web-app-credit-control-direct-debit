<?php

namespace App\Domain\Woo\Controllers;

use App\Domain\Customers\Models\Customer;
use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Webhooks\Services\WebhookOutboxService;
use App\Domain\Woo\Http\Requests\UpdateCreditRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class WooCustomerController extends Controller
{
    public function __construct(
        private readonly WebhookOutboxService $webhookOutboxService
    ) {
    }

    public function updateCredit(UpdateCreditRequest $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');
        $merchant = $site->merchant;

        $woocommerceUserId = $request->input('woocommerce_user_id')
            ?? $request->input('customer.woocommerce_user_id');

        $customer = Customer::where('merchant_id', $merchant->id)
            ->where('external_woocommerce_user_id', $woocommerceUserId)
            ->first();

        if (!$customer) {
            return response()->json(['error' => 'not_found'], 404);
        }

        if ($request->filled('status')) {
            $customer->status = $request->input('status');
        }
        if ($request->filled('lock_reason')) {
            $customer->lock_reason = $request->input('lock_reason');
        }
        $customer->save();

        $creditData = $request->input('credit', []);
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
        if (array_key_exists('limit', $creditData)) {
            $creditProfile->limit_amount = $creditData['limit'];
        }
        if (array_key_exists('days_max', $creditData)) {
            $creditProfile->days_max = $creditData['days_max'];
        }
        if (array_key_exists('days_default', $creditData)) {
            $creditProfile->days_default = $creditData['days_default'];
        }
        $creditProfile->save();

        $payload = [
            'event' => 'customer.credit.update',
            'customer' => [
                'id' => $customer->id,
                'woocommerce_user_id' => $customer->external_woocommerce_user_id,
                'email' => $customer->email,
                'status' => $customer->status,
                'credit' => [
                    'limit' => $creditProfile->limit_amount,
                    'current_exposure' => $creditProfile->current_exposure_amount,
                    'days_max' => $creditProfile->days_max,
                    'days_default' => $creditProfile->days_default,
                ],
            ],
        ];

        $this->webhookOutboxService->enqueue($site, 'customer.credit.update', $payload);

        return response()->json(['ok' => true]);
    }
}

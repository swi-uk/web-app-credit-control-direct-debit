<?php

namespace App\Http\Controllers;

use App\Domain\Credit\Services\CreditExposureService;
use App\Domain\Customers\Services\CustomerService;
use App\Domain\Mandates\Services\MandateService;
use App\Domain\Orders\Models\OrderLink;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Webhooks\Services\WebhookOutboxService;
use App\Support\Tokens\TokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DdiController extends Controller
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly MandateService $mandateService,
        private readonly PaymentService $paymentService,
        private readonly CreditExposureService $creditExposureService,
        private readonly WebhookOutboxService $webhookOutboxService,
        private readonly CustomerService $customerService
    ) {
    }

    public function show(string $token): View
    {
        $orderLink = $this->findValidOrderLink($token);
        if (!$orderLink) {
            return view('ddi.expired');
        }

        $merchantName = $orderLink->merchantSite?->merchant?->name ?? 'Merchant';

        return view('ddi.form', [
            'merchantName' => $merchantName,
            'amount' => $orderLink->amount,
            'currency' => $orderLink->currency,
            'token' => $token,
        ]);
    }

    public function submit(Request $request, string $token): View|RedirectResponse
    {
        $orderLink = $this->findValidOrderLink($token);
        if (!$orderLink) {
            return view('ddi.expired');
        }

        $validated = $request->validate([
            'account_holder_name' => ['required', 'string', 'min:2', 'max:120'],
            'sort_code' => ['required', 'regex:/^\d{6}$/'],
            'account_number' => ['required', 'regex:/^\d{8}$/'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'consent' => ['accepted'],
        ]);

        $mandate = $this->mandateService->createFromOrderLink($orderLink, $validated, $request);
        $payment = $this->paymentService->createFromOrderLink($orderLink, $mandate);

        $orderLink->status = 'completed';
        $orderLink->used_at = now();
        $orderLink->save();

        $this->creditExposureService->recalculate($orderLink->customer);
        $orderLink->customer->load('creditProfile');

        $creditProfile = $orderLink->customer->creditProfile;
        $externalLink = $orderLink->customer->externalLinks()
            ->where('merchant_site_id', $orderLink->merchant_site_id)
            ->first();
        $externalCustomerType = $externalLink?->external_type ?? 'user';
        $externalCustomerId = $orderLink->external_customer_id
            ?: $externalLink?->external_id
            ?: $this->customerService->getExternalUserId($orderLink->customer, $orderLink->merchantSite, $externalCustomerType);
        $legacyWooUserId = $orderLink->merchantSite->platform === 'woocommerce' ? $externalCustomerId : null;
        $legacyOrderId = $orderLink->merchantSite->platform === 'woocommerce'
            ? $orderLink->external_order_id
            : null;

        $paymentPayload = [
            'type' => 'payment.update',
            'data' => [
                'external_order_id' => $orderLink->external_order_id,
                'external_order_type' => $orderLink->external_order_type ?? 'order',
                'external_customer_id' => $externalCustomerId,
                'external_customer_type' => $externalCustomerType,
                'order_id' => $legacyOrderId,
                'woocommerce_user_id' => $legacyWooUserId,
                'mandate_status' => $mandate->status,
                'payment_status' => $payment->status,
                'current_exposure' => $creditProfile?->current_exposure_amount,
                'credit_status' => $orderLink->customer->status,
            ],
        ];

        $creditPayload = [
            'type' => 'customer.credit.update',
            'data' => [
                'external_customer_id' => $externalCustomerId,
                'external_customer_type' => $externalCustomerType,
                'woocommerce_user_id' => $legacyWooUserId,
                'credit_status' => $orderLink->customer->status,
                'credit_limit_amount' => $creditProfile?->limit_amount,
                'credit_days_max' => $creditProfile?->days_max,
                'current_exposure' => $creditProfile?->current_exposure_amount,
                'lock_reason' => $orderLink->customer->lock_reason ?? '',
            ],
        ];

        $this->webhookOutboxService->enqueue($orderLink->merchantSite, 'payment.update', $paymentPayload);
        $this->webhookOutboxService->enqueue($orderLink->merchantSite, 'customer.credit.update', $creditPayload);

        return redirect()->to($orderLink->return_success_url);
    }

    private function findValidOrderLink(string $token): ?OrderLink
    {
        $tokenHash = $this->tokenService->hash($token);

        return OrderLink::where('redirect_token_hash', $tokenHash)
            ->where('status', 'pending')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }
}

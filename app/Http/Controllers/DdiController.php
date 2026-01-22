<?php

namespace App\Http\Controllers;

use App\Domain\Credit\Services\CreditExposureService;
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
        private readonly WebhookOutboxService $webhookOutboxService
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

        $paymentPayload = [
            'event' => 'payment.update',
            'payment' => [
                'id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'woo_order_id' => $payment->woo_order_id,
                'customer_id' => $payment->customer_id,
            ],
        ];

        $creditProfile = $orderLink->customer->creditProfile;
        $creditPayload = [
            'event' => 'customer.credit.update',
            'customer' => [
                'id' => $orderLink->customer->id,
                'woocommerce_user_id' => $orderLink->customer->external_woocommerce_user_id,
                'email' => $orderLink->customer->email,
                'status' => $orderLink->customer->status,
                'credit' => [
                    'limit' => $creditProfile?->limit_amount,
                    'current_exposure' => $creditProfile?->current_exposure_amount,
                    'days_max' => $creditProfile?->days_max,
                    'days_default' => $creditProfile?->days_default,
                ],
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

<?php

namespace App\Http\Controllers;

use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Mandates\Models\MandateUpdateLink;
use App\Domain\Webhooks\Services\WebhookOutboxService;
use App\Support\Tokens\TokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class MandateUpdateController extends Controller
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly WebhookOutboxService $webhookOutboxService
    ) {
    }

    public function show(string $token): View
    {
        $link = $this->findValidLink($token);
        if (!$link) {
            return view('mandate.expired');
        }

        return view('mandate.update_form', [
            'token' => $token,
            'customer' => $link->customer,
        ]);
    }

    public function submit(Request $request, string $token): View|RedirectResponse
    {
        $link = $this->findValidLink($token);
        if (!$link) {
            return view('mandate.expired');
        }

        $validated = $request->validate([
            'account_holder_name' => ['required', 'string', 'min:2', 'max:120'],
            'sort_code' => ['required', 'regex:/^\d{6}$/'],
            'account_number' => ['required', 'regex:/^\d{8}$/'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'consent' => ['accepted'],
        ]);

        $customer = $link->customer;
        $merchant = $customer->merchant;

        $previousMandate = Mandate::where('customer_id', $customer->id)
            ->whereIn('status', ['captured', 'active'])
            ->orderByDesc('id')
            ->first();

        if ($previousMandate) {
            $previousMandate->status = 'cancelled';
            $previousMandate->save();
        }

        $baseReference = 'DD-' . $customer->id . '-UPDATE';
        $reference = $baseReference;
        $suffix = 2;
        while (Mandate::where('merchant_id', $merchant->id)->where('reference', $reference)->exists()) {
            $reference = $baseReference . '-' . $suffix;
            $suffix++;
        }

        $bankAddress = [];
        if (!empty($validated['bank_name'])) {
            $bankAddress['bank_name'] = $validated['bank_name'];
        }

        $mandate = Mandate::create([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'reference' => $reference,
            'account_holder_name' => $validated['account_holder_name'],
            'sort_code' => $validated['sort_code'],
            'account_number' => $validated['account_number'],
            'bank_address_json' => $bankAddress ?: null,
            'consent_timestamp' => now(),
            'consent_ip' => $request->ip(),
            'consent_user_agent' => substr($request->userAgent() ?? '', 0, 2000),
            'status' => 'captured',
        ]);

        $link->used_at = now();
        $link->save();

        $site = $link->merchantSite ?: $merchant->sites()->first();
        if ($site) {
            $externalLink = ExternalLink::where('merchant_site_id', $site->id)
                ->where('entity_type', 'customer')
                ->where('entity_id', $customer->id)
                ->first();
            $externalCustomerId = $externalLink?->external_id;
            $externalCustomerType = $externalLink?->external_type ?? 'user';
            $legacyWooUserId = $site->platform === 'woocommerce' ? $externalCustomerId : null;

            $payload = [
                'type' => 'mandate.update',
                'data' => [
                    'external_customer_id' => $externalCustomerId,
                    'external_customer_type' => $externalCustomerType,
                    'woocommerce_user_id' => $legacyWooUserId,
                    'mandate_status' => $mandate->status,
                    'mandate_reference' => $mandate->reference,
                ],
            ];

            $this->webhookOutboxService->enqueue($site, 'mandate.update', $payload);
            $this->webhookOutboxService->enqueue($site, 'customer.credit.update', [
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
            ]);
        }

        return redirect()->route('portal.mandates');
    }

    private function findValidLink(string $token): ?MandateUpdateLink
    {
        $tokenHash = $this->tokenService->hash($token);
        return MandateUpdateLink::where('token_hash', $tokenHash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }
}

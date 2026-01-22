<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Mandates\Models\MandateUpdateLink;
use App\Support\Tokens\TokenService;
use Illuminate\Http\RedirectResponse;

class BankDetailsController extends PortalBaseController
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    public function createLink(): RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }

        $latestPayment = $customer->payments()->orderByDesc('id')->first();
        $siteId = $latestPayment?->source_site_id;

        $token = $this->tokenService->generate();
        $hash = $this->tokenService->hash($token);

        MandateUpdateLink::create([
            'customer_id' => $customer->id,
            'merchant_site_id' => $siteId,
            'token_hash' => $hash,
            'expires_at' => now()->addMinutes(config('ccdd.mandate_update_ttl_minutes', 60)),
        ]);

        return redirect()->to(url('/mandate/update/' . $token));
    }
}

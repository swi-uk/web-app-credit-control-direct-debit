<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Mandates\Models\MandateUpdateLink;
use App\Domain\Payments\Models\Payment;
use App\Support\Tokens\TokenService;
use Illuminate\Http\RedirectResponse;

class MandateController extends PortalBaseController
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    public function createUpdateLink(): RedirectResponse
    {
        $customer = $this->requireCustomer();
        if ($customer instanceof RedirectResponse) {
            return $customer;
        }

        $siteId = Payment::where('customer_id', $customer->id)
            ->whereNotNull('source_site_id')
            ->orderByDesc('id')
            ->value('source_site_id');

        $token = $this->tokenService->generate();
        $tokenHash = $this->tokenService->hash($token);

        MandateUpdateLink::create([
            'customer_id' => $customer->id,
            'merchant_site_id' => $siteId,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addMinutes(config('ccdd.mandate_update_ttl_minutes', 60)),
        ]);

        return redirect()->to(url('/mandate/update/' . $token));
    }
}

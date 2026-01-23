<?php

namespace App\Domain\Channels\Controllers;

use App\Domain\Channels\Http\Requests\SsoRequest;
use App\Domain\Integrations\Models\ExternalLink;
use App\Domain\Portal\Models\PortalSsoToken;
use App\Support\Tokens\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ChannelSsoController extends Controller
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    public function create(SsoRequest $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');
        $externalCustomerId = $request->input('external_customer_id');
        $externalCustomerType = $request->input('external_customer_type', 'user');

        $link = ExternalLink::where('merchant_site_id', $site->id)
            ->where('entity_type', 'customer')
            ->where('external_type', $externalCustomerType)
            ->where('external_id', $externalCustomerId)
            ->first();

        if (!$link) {
            return response()->json(['error' => 'not_found'], 404);
        }

        $token = $this->tokenService->generate();
        $hash = $this->tokenService->hash($token);

        PortalSsoToken::create([
            'customer_id' => $link->entity_id,
            'token_hash' => $hash,
            'expires_at' => now()->addMinutes(config('ccdd.portal_sso_ttl_minutes', 10)),
        ]);

        $redirect = $request->input('redirect_url') ?? url('/portal/sso/' . $token);

        return response()->json([
            'redirect_url' => $redirect,
        ]);
    }
}

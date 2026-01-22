<?php

namespace App\Domain\Woo\Http\Middleware;

use App\Domain\Merchants\Models\MerchantSite;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateMerchantSite
{
    public function handle(Request $request, Closure $next): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $tokenHash = hash('sha256', $token);
        $site = MerchantSite::where('api_key_hash', $tokenHash)->first();
        if (!$site) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $siteId = $request->input('site_id') ?? $request->input('merchant_site_id');
        if (!$siteId || $siteId !== $site->site_id) {
            return response()->json([
                'error' => 'forbidden',
                'message' => 'site_id mismatch',
            ], 403);
        }

        $request->attributes->set('merchantSite', $site);

        return $next($request);
    }
}

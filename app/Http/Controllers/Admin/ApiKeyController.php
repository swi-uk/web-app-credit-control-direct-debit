<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Merchants\Models\MerchantSite;
use App\Domain\Security\Models\MerchantSiteApiKey;
use App\Support\Tokens\TokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    public function index(MerchantSite $merchantSite): View
    {
        $keys = MerchantSiteApiKey::where('merchant_site_id', $merchantSite->id)
            ->orderByDesc('id')
            ->get();

        return view('admin.api_keys.index', [
            'site' => $merchantSite,
            'keys' => $keys,
        ]);
    }

    public function create(MerchantSite $merchantSite): View
    {
        $rawKey = $this->tokenService->generate();
        $hash = $this->tokenService->hash($rawKey);

        MerchantSiteApiKey::create([
            'merchant_site_id' => $merchantSite->id,
            'key_hash' => $hash,
            'name' => 'Rotated key',
            'status' => 'active',
        ]);

        return view('admin.api_keys.index', [
            'site' => $merchantSite,
            'keys' => MerchantSiteApiKey::where('merchant_site_id', $merchantSite->id)->orderByDesc('id')->get(),
            'rawKey' => $rawKey,
        ]);
    }

    public function revoke(MerchantSite $merchantSite, MerchantSiteApiKey $merchantSiteApiKey): RedirectResponse
    {
        if ($merchantSiteApiKey->merchant_site_id === $merchantSite->id) {
            $merchantSiteApiKey->status = 'revoked';
            $merchantSiteApiKey->save();
        }

        return redirect()->route('admin.api_keys.index', $merchantSite);
    }
}

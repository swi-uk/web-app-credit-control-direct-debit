<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Merchants\Models\Merchant;
use App\Domain\Merchants\Models\MerchantSite;
use App\Support\Tokens\TokenService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    public function index(): View
    {
        $sites = MerchantSite::with('merchant')->orderBy('id', 'desc')->get();

        return view('admin.sites.index', [
            'sites' => $sites,
        ]);
    }

    public function create(): View
    {
        return view('admin.sites.create');
    }

    public function store(Request $request): View
    {
        $validated = $request->validate([
            'merchant_name' => ['required', 'string', 'max:120'],
            'site_id' => ['required', 'string', 'max:120'],
            'base_url' => ['required', 'url'],
        ]);

        $merchant = Merchant::firstOrCreate(
            ['name' => $validated['merchant_name']],
            ['plan' => 'starter', 'status' => 'active']
        );

        $apiKey = $this->tokenService->generate();
        $webhookSecret = $this->tokenService->generate();

        $site = MerchantSite::create([
            'merchant_id' => $merchant->id,
            'site_id' => $validated['site_id'],
            'base_url' => rtrim($validated['base_url'], '/'),
            'api_key_hash' => $this->tokenService->hash($apiKey),
            'webhook_secret' => $webhookSecret,
        ]);

        return view('admin.sites.create', [
            'created' => true,
            'site' => $site,
            'apiKey' => $apiKey,
            'webhookSecret' => $webhookSecret,
        ]);
    }
}

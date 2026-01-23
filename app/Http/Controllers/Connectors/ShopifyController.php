<?php

namespace App\Http\Controllers\Connectors;

use App\Domain\Connectors\Services\ConnectorProvisioningService;
use App\Domain\Merchants\Models\Merchant;
use App\Domain\Merchants\Models\MerchantSite;
use App\Domain\Channels\Controllers\ChannelOrderController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ShopifyController extends Controller
{
    public function __construct(
        private readonly ConnectorProvisioningService $provisioningService,
        private readonly ChannelOrderController $channelOrderController
    )
    {
    }

    public function install(Request $request): View
    {
        return view('admin.onboarding.shopify', [
            'shop' => $request->query('shop'),
        ]);
    }

    public function callback(Request $request): JsonResponse
    {
        $merchantName = $request->input('merchant_name', 'Shopify Merchant');
        $shopDomain = $request->input('shop', 'example.myshopify.com');

        $merchant = Merchant::firstOrCreate(
            ['name' => $merchantName],
            ['plan' => 'starter', 'status' => 'active']
        );

        $site = MerchantSite::create([
            'merchant_id' => $merchant->id,
            'site_id' => 'shopify_' . md5($shopDomain),
            'base_url' => 'https://' . $shopDomain,
            'platform' => 'shopify',
            'mode' => 'test',
            'api_key_hash' => '',
            'webhook_secret' => '',
            'settings_json' => [
                'shop_domain' => $shopDomain,
            ],
        ]);

        $this->provisioningService->markInstalled($site, 'shopify', $request->input('access_token'));

        return response()->json(['ok' => true, 'merchant_site_id' => $site->id]);
    }

    public function webhook(Request $request): JsonResponse
    {
        $siteId = $request->input('site_id');
        $site = $siteId ? MerchantSite::where('site_id', $siteId)->first() : null;
        if (!$site) {
            return response()->json(['error' => 'site_not_found'], 404);
        }

        $order = $request->input('order', $request->all());
        $customer = $order['customer'] ?? [];

        $payload = [
            'site_id' => $site->site_id,
            'platform' => 'shopify',
            'order' => [
                'external_order_type' => 'order',
                'external_order_id' => (string) ($order['id'] ?? ''),
                'external_order_key' => $order['order_number'] ?? null,
                'amount' => $order['total_price'] ?? null,
                'currency' => $order['currency'] ?? null,
            ],
            'customer' => [
                'external_customer_type' => 'customer',
                'external_customer_id' => (string) ($customer['id'] ?? ''),
                'email' => $customer['email'] ?? ($order['email'] ?? null),
                'phone' => $customer['phone'] ?? null,
                'billing' => $customer['default_address'] ?? [],
            ],
            'return_urls' => [
                'success' => $order['order_status_url'] ?? $site->base_url,
                'cancel' => $site->base_url,
            ],
        ];

        $this->channelOrderController->initDdFromPayload($site, $payload);

        return response()->json(['ok' => true]);
    }
}

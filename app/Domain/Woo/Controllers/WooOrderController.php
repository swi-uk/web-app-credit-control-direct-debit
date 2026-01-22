<?php

namespace App\Domain\Woo\Controllers;

use App\Domain\Channels\Controllers\ChannelOrderController;
use App\Domain\Woo\Http\Requests\InitDdRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class WooOrderController extends Controller
{
    public function __construct(
        private readonly ChannelOrderController $channelOrderController
    ) {
    }

    public function initDd(InitDdRequest $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');
        $payload = [
            'site_id' => $request->input('merchant_site_id'),
            'platform' => 'woocommerce',
            'order' => [
                'external_order_type' => 'order',
                'external_order_id' => (string) $request->input('order.order_id'),
                'external_order_key' => $request->input('order.order_key'),
                'amount' => $request->input('order.amount'),
                'currency' => $request->input('order.currency'),
            ],
            'customer' => [
                'external_customer_type' => 'user',
                'external_customer_id' => $request->input('customer.woocommerce_user_id'),
                'email' => $request->input('customer.email'),
                'phone' => $request->input('customer.phone'),
                'billing' => $request->input('customer.billing', []),
            ],
            'return_urls' => [
                'success' => $request->input('return_urls.success'),
                'cancel' => $request->input('return_urls.cancel'),
            ],
        ];

        return $this->channelOrderController->initDdFromPayload($site, $payload);
    }
}

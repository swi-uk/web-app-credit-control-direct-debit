<?php

namespace App\Domain\Orders\Services;

use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\MerchantSite;
use App\Domain\Orders\Models\OrderLink;

class OrderLinkService
{
    public function create(
        MerchantSite $site,
        Customer $customer,
        array $order,
        array $returnUrls,
        string $tokenHash
    ): OrderLink {
        return OrderLink::create([
            'merchant_site_id' => $site->id,
            'customer_id' => $customer->id,
            'woo_order_id' => $order['order_id'] ?? null,
            'woo_order_key' => $order['order_key'] ?? null,
            'amount' => $order['amount'] ?? null,
            'currency' => $order['currency'] ?? null,
            'redirect_token_hash' => $tokenHash,
            'return_success_url' => $returnUrls['success'] ?? null,
            'return_cancel_url' => $returnUrls['cancel'] ?? null,
            'status' => 'pending',
            'used_at' => null,
            'expires_at' => now()->addMinutes(config('ccdd.order_link_ttl_minutes', 60)),
        ]);
    }
}

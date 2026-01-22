<?php

namespace App\Domain\Webhooks\Services;

use App\Domain\Merchants\Models\MerchantSite;
use App\Domain\Webhooks\Models\WebhookDelivery;

class WebhookOutboxService
{
    public function enqueue(MerchantSite $site, string $eventType, array $payload): WebhookDelivery
    {
        return WebhookDelivery::create([
            'merchant_site_id' => $site->id,
            'event_type' => $eventType,
            'payload_json' => $payload,
            'status' => 'pending',
            'attempts' => 0,
            'next_attempt_at' => null,
            'last_error' => null,
        ]);
    }
}

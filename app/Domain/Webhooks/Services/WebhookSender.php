<?php

namespace App\Domain\Webhooks\Services;

use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Support\Crypto\Hmac;
use Illuminate\Support\Facades\Http;
use Throwable;

class WebhookSender
{
    public function __construct(private readonly Hmac $hmac)
    {
    }

    public function send(WebhookDelivery $delivery): array
    {
        $site = $delivery->merchantSite;
        $url = rtrim($site->base_url, '/') . '/wp-json/ccdd/v1/webhook';
        $timestamp = (string) time();
        $rawBody = json_encode($delivery->payload_json, JSON_UNESCAPED_SLASHES);
        if ($rawBody === false) {
            $rawBody = '{}';
        }
        $signature = $this->hmac->signature($timestamp, $rawBody, (string) $site->webhook_secret);

        try {
            $response = Http::timeout(config('ccdd.webhook_timeout', 10))
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-CCDD-Timestamp' => $timestamp,
                    'X-CCDD-Signature' => $signature,
                ])
                ->withBody($rawBody, 'application/json')
                ->post($url);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response_body' => $response->body(),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

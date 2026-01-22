<?php

namespace App\Domain\Channels\Controllers;

use App\Domain\Webhooks\Services\WebhookOutboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChannelWebhookController extends Controller
{
    public function __construct(private readonly WebhookOutboxService $webhookOutboxService)
    {
    }

    public function test(Request $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');
        $payload = [
            'type' => 'webhook.test',
            'data' => [
                'message' => 'Test webhook from core app.',
                'timestamp' => now()->toDateTimeString(),
            ],
        ];

        $this->webhookOutboxService->enqueue($site, 'webhook.test', $payload);

        return response()->json(['ok' => true]);
    }
}

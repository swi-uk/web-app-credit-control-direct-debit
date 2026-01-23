<?php

namespace App\Domain\Connectors\Connectors;

use App\Domain\Connectors\Contracts\ConnectorInterface;
use App\Domain\Connectors\DTO\ConnectorAuthArtifact;
use App\Domain\Connectors\DTO\ConnectorWebhookEvent;
use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Http\Request;

class WooConnector implements ConnectorInterface
{
    public function validateConfig(array $settings): void
    {
    }

    public function buildAuthFlow(): ConnectorAuthArtifact
    {
        return new ConnectorAuthArtifact('none', '');
    }

    public function handleInboundWebhook(Request $req): array
    {
        return [new ConnectorWebhookEvent('woo.webhook', $req->all())];
    }

    public function pushCustomerUpdate(Customer $customer, MerchantSite $site): void
    {
        // Woo updates are handled via webhook outbox.
    }
}

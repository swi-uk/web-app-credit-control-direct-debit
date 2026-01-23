<?php

namespace App\Domain\Connectors\Contracts;

use App\Domain\Connectors\DTO\ConnectorAuthArtifact;
use App\Domain\Connectors\DTO\ConnectorWebhookEvent;
use App\Domain\Customers\Models\Customer;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Http\Request;

interface ConnectorInterface
{
    public function validateConfig(array $settings): void;

    public function buildAuthFlow(): ConnectorAuthArtifact;

    /**
     * @return ConnectorWebhookEvent[]
     */
    public function handleInboundWebhook(Request $req): array;

    public function pushCustomerUpdate(Customer $customer, MerchantSite $site): void;
}

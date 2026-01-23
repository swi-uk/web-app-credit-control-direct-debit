<?php

namespace App\Domain\Connectors\Registry;

use App\Domain\Connectors\Connectors\CustomApiConnector;
use App\Domain\Connectors\Connectors\ShopifyConnector;
use App\Domain\Connectors\Connectors\WooConnector;
use App\Domain\Connectors\Contracts\ConnectorInterface;

class ConnectorRegistry
{
    public function for(string $connector): ConnectorInterface
    {
        return match ($connector) {
            'shopify' => new ShopifyConnector(),
            'custom' => new CustomApiConnector(),
            default => new WooConnector(),
        };
    }
}

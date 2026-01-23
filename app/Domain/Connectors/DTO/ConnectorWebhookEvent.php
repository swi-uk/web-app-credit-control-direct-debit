<?php

namespace App\Domain\Connectors\DTO;

class ConnectorWebhookEvent
{
    public function __construct(
        public readonly string $type,
        public readonly array $payload
    ) {
    }
}

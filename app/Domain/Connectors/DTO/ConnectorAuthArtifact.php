<?php

namespace App\Domain\Connectors\DTO;

class ConnectorAuthArtifact
{
    public function __construct(
        public readonly string $type,
        public readonly string $payload
    ) {
    }
}

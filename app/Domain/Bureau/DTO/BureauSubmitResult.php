<?php

namespace App\Domain\Bureau\DTO;

class BureauSubmitResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $externalRef = null,
        public readonly ?string $message = null,
        public readonly array $payload = []
    ) {
    }

    public static function success(?string $externalRef = null, array $payload = []): self
    {
        return new self(true, $externalRef, null, $payload);
    }

    public static function failure(string $message, array $payload = []): self
    {
        return new self(false, null, $message, $payload);
    }
}

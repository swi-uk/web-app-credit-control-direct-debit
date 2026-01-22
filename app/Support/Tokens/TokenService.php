<?php

namespace App\Support\Tokens;

class TokenService
{
    public function generate(): string
    {
        $raw = random_bytes(48);
        $token = base64_encode($raw);
        $token = strtr($token, '+/', '-_');
        return rtrim($token, '=');
    }

    public function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}

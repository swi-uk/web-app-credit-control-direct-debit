<?php

namespace App\Support\Crypto;

class Hmac
{
    public function signature(string $timestamp, string $rawBody, string $secret): string
    {
        $baseString = $timestamp . '.' . $rawBody;
        return hash_hmac('sha256', $baseString, $secret);
    }
}

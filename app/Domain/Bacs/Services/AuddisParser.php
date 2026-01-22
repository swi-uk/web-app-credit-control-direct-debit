<?php

namespace App\Domain\Bacs\Services;

class AuddisParser
{
    public function parse(string $contents): array
    {
        $parser = new AruddParser();
        return $parser->parse($contents);
    }
}

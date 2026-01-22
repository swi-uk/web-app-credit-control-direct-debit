<?php

namespace App\Domain\Bacs\Services;

class ReportParserRegistry
{
    public function __construct(
        private readonly AruddParser $aruddParser,
        private readonly AddacsParser $addacsParser
    ) {
    }

    public function parse(string $type, string $contents): array
    {
        return match (strtoupper($type)) {
            'ADDACS' => $this->addacsParser->parse($contents),
            default => $this->aruddParser->parse($contents),
        };
    }
}

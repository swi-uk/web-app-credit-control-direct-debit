<?php

namespace App\Domain\Bureau\DTO;

class BureauReportFile
{
    public function __construct(
        public readonly string $remoteId,
        public readonly string $filename,
        public readonly ?string $type = null,
        public readonly ?string $hash = null
    ) {
    }
}

<?php

namespace App\Domain\Bureau\DTO;

class InboundPackage
{
    /**
     * @param BureauReportFile[] $files
     * @param array $events
     */
    public function __construct(
        public readonly array $files = [],
        public readonly array $events = []
    ) {
    }
}

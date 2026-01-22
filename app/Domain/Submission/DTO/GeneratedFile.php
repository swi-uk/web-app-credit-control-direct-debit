<?php

namespace App\Domain\Submission\DTO;

class GeneratedFile
{
    public function __construct(
        public readonly string $filename,
        public readonly string $contents,
        public readonly int $recordCount,
        public readonly string $sha256,
        public readonly string $formatVersion
    ) {
    }
}

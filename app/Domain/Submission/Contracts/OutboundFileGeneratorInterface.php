<?php

namespace App\Domain\Submission\Contracts;

use App\Domain\Submission\DTO\GeneratedFile;

interface OutboundFileGeneratorInterface
{
    public function generateMandateFile(array $mandates): GeneratedFile;

    public function generatePaymentFile(array $payments): GeneratedFile;
}

<?php

namespace App\Domain\Bureau\Contracts;

use App\Domain\Bureau\DTO\BureauSubmitResult;
use App\Domain\Bureau\DTO\InboundPackage;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use Carbon\Carbon;

interface BureauConnectorInterface
{
    public function submitMandate(Mandate $mandate): BureauSubmitResult;

    public function submitPayment(Payment $payment): BureauSubmitResult;

    public function fetchInbound(Carbon $from, Carbon $to): InboundPackage;

    public function downloadReport(string $remoteId): string;
}

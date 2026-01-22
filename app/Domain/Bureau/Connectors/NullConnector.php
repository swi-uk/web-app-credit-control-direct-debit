<?php

namespace App\Domain\Bureau\Connectors;

use App\Domain\Bureau\Contracts\BureauConnectorInterface;
use App\Domain\Bureau\DTO\InboundPackage;
use App\Domain\Bureau\DTO\BureauSubmitResult;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use Carbon\Carbon;

class NullConnector implements BureauConnectorInterface
{
    public function submitMandate(Mandate $mandate): BureauSubmitResult
    {
        return BureauSubmitResult::success('null-mandate-' . $mandate->id);
    }

    public function submitPayment(Payment $payment): BureauSubmitResult
    {
        return BureauSubmitResult::success('null-payment-' . $payment->id);
    }

    public function fetchInbound(Carbon $from, Carbon $to): InboundPackage
    {
        return new InboundPackage();
    }

    public function downloadReport(string $remoteId): string
    {
        return '';
    }
}

<?php

namespace App\Domain\Bureau\Connectors;

use App\Domain\Bureau\Contracts\BureauConnectorInterface;
use App\Domain\Bureau\DTO\BureauSubmitResult;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use Carbon\Carbon;
use RuntimeException;

class AbsoluteApiConnector implements BureauConnectorInterface
{
    public function __construct(private readonly array $config)
    {
    }

    public function submitMandate(Mandate $mandate): BureauSubmitResult
    {
        return BureauSubmitResult::failure('API submission not implemented.', [
            'mandate_id' => $mandate->id,
        ]);
    }

    public function submitPayment(Payment $payment): BureauSubmitResult
    {
        return BureauSubmitResult::failure('API submission not implemented.', [
            'payment_id' => $payment->id,
        ]);
    }

    public function fetchReports(Carbon $from, Carbon $to): array
    {
        return [];
    }

    public function downloadReport(string $remoteId): string
    {
        throw new RuntimeException('API download not implemented.');
    }
}

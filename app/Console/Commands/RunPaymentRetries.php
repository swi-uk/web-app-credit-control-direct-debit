<?php

namespace App\Console\Commands;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Services\PaymentStateService;
use Illuminate\Console\Command;

class RunPaymentRetries extends Command
{
    protected $signature = 'ccdd:run-payment-retries';
    protected $description = 'Release retry scheduled payments for processing';

    public function __construct(private readonly PaymentStateService $paymentStateService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $payments = Payment::with(['customer', 'merchant', 'sourceSite', 'mandate'])
            ->where('status', 'retry_scheduled')
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now())
            ->get();

        foreach ($payments as $payment) {
            $payment->next_retry_at = null;
            $payment->save();

            $this->paymentStateService->transition($payment, 'scheduled', [
                'retry_release' => true,
            ]);

            AuditEvent::create([
                'merchant_id' => $payment->merchant_id,
                'customer_id' => $payment->customer_id,
                'event_type' => 'payment.retry_released',
                'message' => null,
                'payload_json' => [
                    'payment_id' => $payment->id,
                ],
                'created_at' => now(),
            ]);

            // payment.update already enqueued via PaymentStateService.
        }

        $this->info('Processed retries: ' . $payments->count());

        return self::SUCCESS;
    }

}

<?php

namespace App\Domain\Bureau\Jobs;

use App\Domain\Bureau\Models\BureauEvent;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Services\PaymentStateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBureauEventJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $eventId)
    {
    }

    public function handle(PaymentStateService $paymentStateService): void
    {
        $event = BureauEvent::find($this->eventId);
        if (!$event || $event->processed_at) {
            return;
        }

        if ($event->entity_type === 'payment') {
            $payment = $event->entity_id ? Payment::find($event->entity_id) : null;
            if (!$payment) {
                $payment = Payment::where('bureau_external_ref', $event->external_ref)->first();
            }
            if ($payment) {
                $status = $this->mapPaymentStatus($event->event_type);
                if ($status) {
                    $paymentStateService->transition($payment, $status, [
                        'bureau_event_id' => $event->id,
                    ]);
                }
            }
        }

        if ($event->entity_type === 'mandate') {
            $mandate = $event->entity_id ? Mandate::find($event->entity_id) : null;
            if (!$mandate) {
                $mandate = Mandate::where('bureau_external_ref', $event->external_ref)->first();
            }
            if ($mandate) {
                $mandate->status = $this->mapMandateStatus($event->event_type) ?? $mandate->status;
                $mandate->save();
            }
        }

        $event->processed_at = now();
        $event->save();
    }

    private function mapPaymentStatus(string $eventType): ?string
    {
        return match ($eventType) {
            'PAYMENT_ACCEPTED' => 'submitted',
            'PAYMENT_PROCESSING' => 'processing',
            'PAYMENT_COLLECTED' => 'collected',
            'PAYMENT_RETURNED' => 'unpaid_returned',
            'PAYMENT_FAILED' => 'failed_final',
            default => null,
        };
    }

    private function mapMandateStatus(string $eventType): ?string
    {
        return match ($eventType) {
            'MANDATE_SUBMITTED' => 'submitted',
            'MANDATE_ACTIVE' => 'active',
            'MANDATE_REJECTED' => 'rejected',
            'MANDATE_CANCELLED' => 'cancelled',
            default => null,
        };
    }
}

<?php

namespace App\Domain\Webhooks\Jobs;

use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Domain\Webhooks\Services\WebhookSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWebhookDeliveryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $deliveryId)
    {
    }

    public function handle(WebhookSender $sender): void
    {
        $delivery = WebhookDelivery::with('merchantSite')->find($this->deliveryId);
        if (!$delivery || $delivery->status !== 'pending') {
            return;
        }

        $result = $sender->send($delivery);
        if (($result['success'] ?? false) === true) {
            $delivery->status = 'sent';
            $delivery->next_attempt_at = null;
            $delivery->last_error = null;
            $delivery->save();
            return;
        }

        $delivery->attempts = $delivery->attempts + 1;
        $delivery->last_error = $result['error'] ?? ('HTTP ' . ($result['status'] ?? 'unknown'));

        $maxAttempts = config('ccdd.webhook_max_attempts', 8);
        if ($delivery->attempts >= $maxAttempts) {
            $delivery->status = 'failed';
            $delivery->next_attempt_at = null;
            $delivery->save();
            return;
        }

        $delivery->status = 'pending';
        $delivery->next_attempt_at = now()->addSeconds($this->backoffSeconds($delivery->attempts));
        $delivery->save();
    }

    private function backoffSeconds(int $attempts): int
    {
        return match ($attempts) {
            1 => 60,
            2 => 300,
            3 => 900,
            4 => 3600,
            5 => 21600,
            6 => 43200,
            7 => 86400,
            default => 0,
        };
    }
}

<?php

namespace App\Console\Commands;

use App\Domain\Webhooks\Jobs\SendWebhookDeliveryJob;
use App\Domain\Webhooks\Models\WebhookDelivery;
use Illuminate\Console\Command;

class DispatchWebhooks extends Command
{
    protected $signature = 'ccdd:dispatch-webhooks';
    protected $description = 'Dispatch pending webhook deliveries';

    public function handle(): int
    {
        $deliveries = WebhookDelivery::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('next_attempt_at')
                    ->orWhere('next_attempt_at', '<=', now());
            })
            ->orderBy('id')
            ->limit(100)
            ->get();

        foreach ($deliveries as $delivery) {
            SendWebhookDeliveryJob::dispatch($delivery->id);
        }

        $this->info('Dispatched ' . $deliveries->count() . ' webhooks.');

        return self::SUCCESS;
    }
}

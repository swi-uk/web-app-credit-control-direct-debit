<?php

namespace App\Domain\Bureau\Jobs;

use App\Domain\Bureau\Models\BureauEvent;
use App\Domain\Bureau\Services\BureauService;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PollBureauEventsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $merchantSiteId)
    {
    }

    public function handle(BureauService $bureauService): void
    {
        $site = MerchantSite::find($this->merchantSiteId);
        if (!$site) {
            return;
        }

        $connector = $bureauService->connectorFor($site);
        $package = $connector->fetchInbound(now()->subDays(7), now());

        foreach ($package->events as $event) {
            $exists = BureauEvent::where('merchant_site_id', $site->id)
                ->where('event_type', $event['event_type'])
                ->where('external_ref', $event['external_ref'])
                ->where('occurred_at', $event['occurred_at'] ?? null)
                ->first();
            if ($exists) {
                continue;
            }

            $record = BureauEvent::create([
                'merchant_site_id' => $site->id,
                'event_type' => $event['event_type'],
                'external_ref' => $event['external_ref'],
                'entity_type' => $event['entity_type'] ?? 'unknown',
                'entity_id' => $event['entity_id'] ?? null,
                'occurred_at' => $event['occurred_at'] ?? null,
                'payload_json' => $event,
            ]);

            ProcessBureauEventJob::dispatch($record->id);
        }
    }
}

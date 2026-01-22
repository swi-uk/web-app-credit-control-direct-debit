<?php

namespace App\Domain\Bureau\Jobs;

use App\Domain\Bureau\Models\BureauRequest;
use App\Domain\Bureau\Services\BureauService;
use App\Domain\Mandates\Models\Mandate;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubmitMandateToApiJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $mandateId, private readonly int $merchantSiteId)
    {
    }

    public function handle(BureauService $bureauService): void
    {
        $mandate = Mandate::find($this->mandateId);
        $site = MerchantSite::find($this->merchantSiteId);
        if (!$mandate || !$site) {
            return;
        }

        $idempotencyKey = 'mandate:' . $mandate->id . ':submit';
        $existing = BureauRequest::where('merchant_site_id', $site->id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();
        if ($existing) {
            return;
        }

        $request = BureauRequest::create([
            'merchant_site_id' => $site->id,
            'entity_type' => 'mandate',
            'entity_id' => $mandate->id,
            'request_type' => 'submit',
            'idempotency_key' => $idempotencyKey,
            'request_json' => [
                'mandate_id' => $mandate->id,
                'reference' => $mandate->reference,
            ],
            'status' => 'pending',
        ]);

        $connector = $bureauService->connectorFor($site);
        $result = $connector->submitMandate($mandate);

        $request->status = $result->success ? 'success' : 'failed';
        $request->response_json = $result->payload;
        $request->last_error = $result->message;
        $request->save();

        if ($result->success) {
            $mandate->bureau_external_ref = $result->externalRef;
            $mandate->status = 'submitted';
            $mandate->save();
        }
    }
}

<?php

namespace App\Domain\Bureau\Jobs;

use App\Domain\Bureau\Models\BureauRequest;
use App\Domain\Bureau\Services\BureauService;
use App\Domain\Payments\Models\Payment;
use App\Domain\Merchants\Models\MerchantSite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubmitPaymentToApiJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $paymentId, private readonly int $merchantSiteId)
    {
    }

    public function handle(BureauService $bureauService): void
    {
        $payment = Payment::find($this->paymentId);
        $site = MerchantSite::find($this->merchantSiteId);
        if (!$payment || !$site) {
            return;
        }

        $idempotencyKey = 'payment:' . $payment->id . ':submit';
        $existing = BureauRequest::where('merchant_site_id', $site->id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();
        if ($existing) {
            return;
        }

        $request = BureauRequest::create([
            'merchant_site_id' => $site->id,
            'entity_type' => 'payment',
            'entity_id' => $payment->id,
            'request_type' => 'submit',
            'idempotency_key' => $idempotencyKey,
            'request_json' => [
                'payment_id' => $payment->id,
                'external_order_id' => $payment->external_order_id,
            ],
            'status' => 'pending',
        ]);

        $connector = $bureauService->connectorFor($site);
        $result = $connector->submitPayment($payment);

        $request->status = $result->success ? 'success' : 'failed';
        $request->response_json = $result->payload;
        $request->last_error = $result->message;
        $request->save();

        if ($result->success) {
            $payment->bureau_external_ref = $result->externalRef;
            $payment->status = 'submitted';
            $payment->save();
        }
    }
}

<?php

namespace App\Domain\Mandates\Services;

use App\Domain\Mandates\Models\Mandate;
use App\Domain\Orders\Models\OrderLink;
use Illuminate\Http\Request;

class MandateService
{
    public function createFromOrderLink(OrderLink $orderLink, array $data, Request $request): Mandate
    {
        $customer = $orderLink->customer;
        $merchant = $orderLink->merchantSite->merchant;

        $baseReference = 'DD-' . $customer->id . '-' . $orderLink->external_order_id;
        $reference = $baseReference;
        $suffix = 2;
        while (Mandate::where('merchant_id', $merchant->id)->where('reference', $reference)->exists()) {
            $reference = $baseReference . '-' . $suffix;
            $suffix++;
        }

        $bankAddress = [];
        if (!empty($data['bank_name'])) {
            $bankAddress['bank_name'] = $data['bank_name'];
        }

        return Mandate::create([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'reference' => $reference,
            'account_holder_name' => $data['account_holder_name'],
            'sort_code' => $data['sort_code'],
            'account_number' => $data['account_number'],
            'bank_address_json' => $bankAddress ?: null,
            'consent_timestamp' => now(),
            'consent_ip' => $request->ip(),
            'consent_user_agent' => substr($request->userAgent() ?? '', 0, 2000),
            'status' => 'captured',
        ]);
    }
}

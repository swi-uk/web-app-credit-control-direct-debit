<?php

namespace App\Domain\Woo\Controllers;

use App\Domain\Channels\Controllers\ChannelCustomerController;
use App\Domain\Woo\Http\Requests\UpdateCreditRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class WooCustomerController extends Controller
{
    public function __construct(
        private readonly ChannelCustomerController $channelCustomerController
    ) {
    }

    public function updateCredit(UpdateCreditRequest $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');
        $woocommerceUserId = $request->input('woocommerce_user_id')
            ?? $request->input('customer.woocommerce_user_id');

        $payload = [
            'site_id' => $request->input('merchant_site_id'),
            'platform' => 'woocommerce',
            'customer' => [
                'external_customer_type' => 'user',
                'external_customer_id' => $woocommerceUserId,
                'email' => $request->input('customer.email'),
            ],
            'credit' => [
                'status' => $request->input('status'),
                'limit_amount' => $request->input('credit.limit') ?? $request->input('credit.limit_amount'),
                'days_max' => $request->input('credit.days_max'),
                'days_default' => $request->input('credit.days_default'),
                'lock_reason' => $request->input('lock_reason'),
            ],
        ];

        return $this->channelCustomerController->updateCreditFromPayload($site, $payload);
    }
}

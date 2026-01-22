<?php

namespace App\Domain\Woo\Controllers;

use App\Domain\Customers\Services\CustomerService;
use App\Domain\Credit\Services\CreditDecisionService;
use App\Domain\Credit\Services\CreditExposureService;
use App\Domain\Orders\Services\OrderLinkService;
use App\Domain\Woo\Http\Requests\InitDdRequest;
use App\Support\Tokens\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class WooOrderController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
        private readonly CreditExposureService $creditExposureService,
        private readonly CreditDecisionService $creditDecisionService,
        private readonly OrderLinkService $orderLinkService,
        private readonly TokenService $tokenService
    ) {
    }

    public function initDd(InitDdRequest $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');
        $merchant = $site->merchant;

        $customer = $this->customerService->upsertFromWoo($merchant, $request->input('customer', []));
        $creditProfile = $customer->creditProfile;

        $this->creditExposureService->recalculate($customer);
        $creditProfile->refresh();

        $orderAmount = (float) $request->input('order.amount');
        $denyReason = $this->creditDecisionService->denyReason($customer, $creditProfile, $orderAmount);
        if ($denyReason) {
            return response()->json([
                'error' => 'credit_denied',
                'reason' => $denyReason,
                'message' => 'Account restricted or credit limit exceeded.',
            ], 403);
        }

        $token = $this->tokenService->generate();
        $tokenHash = $this->tokenService->hash($token);

        $this->orderLinkService->create(
            $site,
            $customer,
            $request->input('order', []),
            $request->input('return_urls', []),
            $tokenHash
        );

        $redirectUrl = rtrim(config('ccdd.app_url'), '/') . '/ddi/' . $token;

        return response()->json([
            'redirect_url' => $redirectUrl,
        ]);
    }
}

<?php

namespace App\Domain\Channels\Controllers;

use App\Domain\Channels\Http\Requests\InitDdRequest;
use App\Domain\Customers\Services\CustomerService;
use App\Domain\Credit\Services\CreditDecisionService;
use App\Domain\Credit\Services\CreditExposureService;
use App\Domain\Merchants\Models\MerchantSite;
use App\Domain\Orders\Services\OrderLinkService;
use App\Support\Tokens\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ChannelOrderController extends Controller
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

        return $this->initDdFromPayload($site, $request->validated());
    }

    public function initDdFromPayload(MerchantSite $site, array $payload): JsonResponse
    {
        $merchant = $site->merchant;

        $customerPayload = $payload['customer'] ?? [];
        $customer = $this->customerService->upsertFromChannel($merchant, $site, $customerPayload);
        $creditProfile = $customer->creditProfile;

        $this->creditExposureService->recalculate($customer);
        $creditProfile->refresh();

        $orderAmount = (float) ($payload['order']['amount'] ?? 0);
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
            $payload['order'] ?? [],
            $payload['return_urls'] ?? [],
            $tokenHash,
            $customerPayload['external_customer_id'] ?? null
        );

        $redirectUrl = rtrim(config('ccdd.app_url'), '/') . '/ddi/' . $token;

        return response()->json([
            'redirect_url' => $redirectUrl,
        ]);
    }
}

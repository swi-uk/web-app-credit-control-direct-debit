<?php

namespace App\Domain\Channels\Controllers;

use App\Domain\Channels\Http\Requests\CustomerUpsertRequest;
use App\Domain\Customers\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ChannelCustomerManagementController extends Controller
{
    public function __construct(private readonly CustomerService $customerService)
    {
    }

    public function upsert(CustomerUpsertRequest $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');
        $merchant = $site->merchant;

        $customer = $this->customerService->upsertFromChannel($merchant, $site, $request->input('customer', []));

        return response()->json([
            'ok' => true,
            'customer_id' => $customer->id,
        ]);
    }
}

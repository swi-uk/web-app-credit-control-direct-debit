<?php

namespace App\Domain\Channels\Controllers;

use App\Domain\Channels\Http\Requests\InitDdRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ChannelPaymentIntentController extends Controller
{
    public function __construct(private readonly ChannelOrderController $channelOrderController)
    {
    }

    public function store(InitDdRequest $request): JsonResponse
    {
        $site = $request->attributes->get('merchantSite');

        return $this->channelOrderController->initDdFromPayload($site, $request->validated());
    }
}

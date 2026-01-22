<?php

namespace App\Http;

use App\Domain\Woo\Http\Middleware\AuthenticateMerchantSite;
use App\Http\Middleware\ApiRateLimiter;
use App\Http\Middleware\RequestLogger;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [];

    protected $middlewareGroups = [
        'web' => [],
        'api' => [
            RequestLogger::class,
        ],
    ];

    protected $routeMiddleware = [
        'woo.auth' => AuthenticateMerchantSite::class,
        'channel.auth' => AuthenticateMerchantSite::class,
        'api.rate' => ApiRateLimiter::class,
    ];
}

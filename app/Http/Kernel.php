<?php

namespace App\Http;

use App\Domain\Woo\Http\Middleware\AuthenticateMerchantSite;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [];

    protected $middlewareGroups = [
        'web' => [],
        'api' => [],
    ];

    protected $routeMiddleware = [
        'woo.auth' => AuthenticateMerchantSite::class,
        'channel.auth' => AuthenticateMerchantSite::class,
    ];
}

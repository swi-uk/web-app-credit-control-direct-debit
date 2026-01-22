<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class ApiRateLimiter
{
    public function __construct(private readonly RateLimiter $limiter)
    {
    }

    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $key = $this->resolveKey($request);
        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json(['error' => 'rate_limited'], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request);
    }

    private function resolveKey(Request $request): string
    {
        $site = $request->attributes->get('merchantSite');
        $siteId = $site?->id ?? 'anon';
        return 'api:' . $siteId . ':' . $request->ip();
    }
}

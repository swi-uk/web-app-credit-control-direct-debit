<?php

namespace App\Http\Middleware;

use App\Domain\Observability\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;

class RequestLogger
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);

        $durationMs = (int) ((microtime(true) - $start) * 1000);
        $correlationId = $request->headers->get('X-Correlation-Id') ?: bin2hex(random_bytes(8));
        $request->headers->set('X-Correlation-Id', $correlationId);
        $response->headers->set('X-Correlation-Id', $correlationId);

        $site = $request->attributes->get('merchantSite');

        RequestLog::create([
            'correlation_id' => $correlationId,
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'merchant_site_id' => $site?->id,
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'duration_ms' => $durationMs,
        ]);

        return $response;
    }
}

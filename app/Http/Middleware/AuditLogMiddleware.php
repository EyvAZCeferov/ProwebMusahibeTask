<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('request_id', (string) Str::uuid());
        $request->attributes->set('start_time', microtime(true));
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        $startTime = $request->attributes->get('start_time', microtime(true));
        $endTime = microtime(true);
        $latency = round(($endTime - $startTime) * 1000);

        Log::channel('audit')->info('API Request', [
            'request_id' => (string) $request->attributes->get('request_id', (string) Str::uuid()),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'latency_ms' => $latency,
        ]);
    }
}

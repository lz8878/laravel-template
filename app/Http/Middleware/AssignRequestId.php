<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) $request->header('X-Request-Id');

        if ($requestId === '') {
            $requestId = (string) Str::uuid();
        }

        Log::withContext([
            'x-request-id' => $requestId,
        ]);

        return tap($next($request), fn ($response) => $response->headers->set('X-Request-Id', $requestId));
    }
}

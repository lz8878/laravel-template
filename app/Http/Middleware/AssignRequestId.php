<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
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
        $requestId = (string) $request->header('X-Request-ID');

        if ($requestId === '') {
            $requestId = Str::uuid()->toString();
        }

        Context::add('X-Request-ID', $requestId);

        return tap($next($request), fn ($response) => $response->headers->set('X-Request-ID', $requestId));
    }
}

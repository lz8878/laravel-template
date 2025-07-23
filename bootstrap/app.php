<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->map(function (ModelNotFoundException $e) {
            $resource = __($key = 'model.'.$e->getModel());

            if ($resource === $key) {
                $resource = class_basename($e->getModel());
            }

            return new NotFoundHttpException(__(':resource not found', ['resource' => $resource]));
        });

        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(
                    data: exception2array($e),
                    status: match (true) {
                        $e instanceof HttpExceptionInterface => $e->getStatusCode(),
                        $e instanceof AuthenticationException => Response::HTTP_UNAUTHORIZED,
                        $e instanceof ValidationException => Response::HTTP_UNPROCESSABLE_ENTITY,
                        default => Response::HTTP_INTERNAL_SERVER_ERROR,
                    },
                    headers: $e instanceof HttpExceptionInterface ? $e->getHeaders() : [],
                    options: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                );
            }
        });
    })->create();

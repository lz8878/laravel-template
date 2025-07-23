<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

if (! function_exists('exception2array')) {
    /**
     * 将异常转换成数组
     */
    function exception2array(Throwable $e): array
    {
        $data = config('app.debug') ? [
            'errcode' => 500,
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => (new Collection($e->getTrace()))->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
        ] : [
            'errcode' => 500,
            'message' => 'Server Error',
        ];

        if ($e instanceof HttpExceptionInterface) {
            $data = array_merge($data, [
                'errcode' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
        } elseif ($e instanceof AuthenticationException) {
            $data = array_merge($data, [
                'errcode' => 401,
                'message' => $e->getMessage(),
            ]);
        } elseif ($e instanceof ValidationException) {
            $message = Arr::first($errors = $e->errors());

            if (is_array($message)) {
                $message = Arr::first($message);
            }

            $data = array_merge($data, [
                'errcode' => 422,
                'message' => $message ?: 'The given data was invalid.',
                'errors' => $errors,
            ]);
        }

        return $data;
    }
}

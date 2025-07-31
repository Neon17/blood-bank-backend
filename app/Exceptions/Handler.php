<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        return response()->json([
            'status' => 'fail',
            'message' => 'Authentication required.'
        ], 401);
    }

    public function render($request, Throwable $exception)
    {
        // Force JSON for validation errors
        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 'fail',
                'message' => 'validation error',
                'errors' => $exception->errors()
            ], 422);
        }

        // Model not found
        if ($exception instanceof ModelNotFoundException) {
            $modelName = class_basename($exception->getModel());

            return response()->json([
                'status' => 'fail',
                'message' => "{$modelName} not found"
            ], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Resource not found',
            ], 404);
        }

        // Handle all other errors as JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500);
        }

        return parent::render($request, $exception);
    }
}

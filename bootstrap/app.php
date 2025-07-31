<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\OptionalSanctum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'optionalSanctum' => OptionalSanctum::class,
            'isAdmin' => IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function (ValidationException $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => 'validation error',
                'errors' => $exception->errors()
            ], 422);
        });
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                $message = $exception->getMessage();

                // "No query results for model [App\Models\Donor]"
                if (str_contains($message, 'No query results for model')) {
                    preg_match('/model \[([^\]]+)\]/', $message, $matches);
                    $modelName = isset($matches[1]) ? class_basename($matches[1]) : 'Resource';

                    return response()->json([
                        'status' => 'fail',
                        'message' => "{$modelName} not found"
                    ], 404);
                }

                // Fallback for other NotFoundHttpException types (e.g., wrong route)
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Resource not found'
                ], 404);
            }
        });
    })->create();

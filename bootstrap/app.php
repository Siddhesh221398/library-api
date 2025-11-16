<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Middleware;



$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'auth.sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'log.request' => \App\Http\Middleware\LogRequest::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e) {
            // Handle ModelNotFoundException
            if ($e instanceof ModelNotFoundException) {
                $model = class_basename($e->getModel());
                return response()->json([
                    'message' => "{$model} not found."
                ], 404);
            }

            // Optionally handle other exceptions
            return response()->json([
                'message' => $e->getMessage() ?: 'Server Error',
            ], method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);
        });
    })

    ->create();

return $app;

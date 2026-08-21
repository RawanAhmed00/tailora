<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )

    ->withProviders()

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'user' => \App\Http\Middleware\IsUser::class,
            'guide' => \App\Http\Middleware\IsTourGuide::class,
            'idempotency' => \App\Http\Middleware\EnsureIdempotency::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (\Throwable $e, Request $request) {

            // 404
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Record not found',
                ], 404);
            }

            // Database errors
            if ($e instanceof QueryException) {
                return response()->json([
                    'message' => 'Database error: ' . $e->getMessage(),
                ], 422);
            }

            // HTTP exceptions
            $status = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : 500;

            return response()->json([
                'message' => $e->getMessage(),
            ], $status);
        });
    })

    ->create();
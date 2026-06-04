<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway (dan load balancer lain) mengakhiri HTTPS di edge; app di dalam container
        // menerima HTTP. Tanpa ini, route()/form action bisa jadi http:// → peringatan browser.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->appendToGroup('web', \App\Http\Middleware\LogWebAuth::class);
        $middleware->alias([
            'staff' => \App\Http\Middleware\EnsureStaff::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            $maxUploadSize = ini_get('post_max_size') ?: 'batas server';
            $message = "Ukuran upload terlalu besar. Total file yang dikirim melebihi batas server ({$maxUploadSize}). Silakan kompres file atau pilih file yang lebih kecil.";

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 413);
            }

            return back()->withErrors(['upload' => $message])->withInput();
        });
    })->create();

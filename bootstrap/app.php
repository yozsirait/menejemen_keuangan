<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Daftarkan alias middleware custom
        $middleware->alias([
            // 'pin.protected' => \App\Http\Middleware\PinProtected::class,
            // 'verify.pin' => \App\Http\Middleware\VerifyPin::class,
        ]);

        // Contoh menambahkan middleware ke grup 'web'
        // $middleware->group('web', [
        //     \App\Http\Middleware\YourMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Konfigurasi handling exception (bisa dikosongkan)
    })
    ->create();

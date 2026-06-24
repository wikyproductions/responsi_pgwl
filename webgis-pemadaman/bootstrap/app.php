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
    ->withMiddleware(function (Middleware $middleware) {
        // Tambahkan baris alias IsAdmin Anda yang kemarin
        $middleware->alias([
            'IsAdmin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        $middleware->redirectTo(
            guests: '/login',
            users: '/map' // Mengubah default HOME dari /dashboard menjadi /map
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

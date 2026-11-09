<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('login.form'));

        // Produksi berjalan di balik reverse proxy (Railway/Heroku/Nginx).
        // Tanpa trusted proxy, Laravel menganggap request masih HTTP sehingga
        // URL bertanda tangan untuk upload Livewire/Filament gagal divalidasi
        // (401) dan file selalu "failed to upload".
        //
        // Penting: callback ini dijalankan SEBELUM bootstrap container selesai,
        // jadi jangan memakai app()/config() di sini. Cukup env() atau nilai statis.
        $trustedProxies = env('TRUSTED_PROXIES') ?: '*';

        $middleware->trustProxies(
            at: $trustedProxies === '*' ? '*' : array_map('trim', explode(',', $trustedProxies)),
        );

        $middleware->alias([
            'contact.verified' => \App\Http\Middleware\EnsureContactVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

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
        if ($trustedProxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(
                at: $trustedProxies === '*' ? '*' : explode(',', $trustedProxies),
            );
        } elseif (app()->environment('production')) {
            $middleware->trustProxies(at: '*');
        }

        $middleware->alias([
            'contact.verified' => \App\Http\Middleware\EnsureContactVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

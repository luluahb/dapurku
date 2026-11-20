<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Route tambahan TANPA middleware web (tanpa sesi & cookie): halaman pemasangan
        then: function () {
            Route::middleware([])->group(base_path('routes/pasang.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Di hosting, request lewat "proxy" dulu. Percayai proxy itu supaya Laravel tahu
        // website diakses lewat https (kalau tidak, link CSS/JS bisa tertulis http dan diblokir).
        $middleware->trustProxies(at: '*');

        // Belum login tapi membuka halaman admin → diarahkan ke form login admin
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        // Sudah login tapi membuka form login → diarahkan ke dashboard admin
        $middleware->redirectUsersTo(fn () => route('admin.resep.index'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

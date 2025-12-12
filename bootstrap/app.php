<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminCheck;
use App\Http\Middleware\AdminCheckLogout;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('adminCheck',[AdminCheck::class]);
        $middleware->group('adminCheckLogout',[AdminCheckLogout::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

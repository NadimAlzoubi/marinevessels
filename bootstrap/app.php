<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php', 
        commands: __DIR__.'/../routes/console.php',  
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        //إذا كنت تريد استخدام الميدل وير فقط في مسارات معينة
        // لا تقم بإضافة هنا 
        // يمكنك إضافة الميدل وير عامة أخرى هنا إذا أردت
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // تخصيص الاستثناءات إن لزم الأمر
    })
    ->create();

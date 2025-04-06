<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // توجيه عام للعديد من الأدوار
        Blade::if('role', function ($roles) {
            if (!is_array($roles)) {
                $roles = [$roles];
            }
            return Auth::check() && in_array(Auth::user()->role, $roles);
        });

        // توجيهات خاصة بكل دور
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->role === 'admin';
        });

        Blade::if('editor', function () {
            return Auth::check() && Auth::user()->role === 'editor';
        });

        Blade::if('contributor', function () {
            return Auth::check() && Auth::user()->role === 'contributor';
        });

        Blade::if('guestuser', function () {
            return Auth::check() && Auth::user()->role === 'guest';
        });

        Blade::if('notguestuser', function () {
            return Auth::check() && Auth::user()->role !== 'guest';
        });

    }
}

// طريقة استخدام الصلاحيات المتعددة

// @role('admin')
//     <!-- عرض المحتوى الخاص بالادمن -->
// @endrole
// @role(['admin', 'editor'])
//     <!-- عرض المحتوى للمستخدمين الذين لديهم دور ادمن او محرر -->
// @endrole



// طريقة استخدام الصلاحيات المحددة

// @admin
//     <p>أنت أدمن</p>
// @endadmin

// @editor
//     <p>أنت محرر</p>
// @endeditor

// @contributor
//     <p>أنت مساهم</p>
// @endcontributor

// @guestuser
//     <p>مرحبًا بالزائر</p>
// @endguestuser

// @notguestuser
//     <p>أنت لست زائرًا</p>
// @endnotguestuser

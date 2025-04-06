<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;

Route::middleware('guest')->group(function () {
    // مسارات التسجيل وتسجيل الدخول
    //    Route::get('register', [RegisteredUserController::class, 'create'])
    //        ->name('register');
    //    Route::post('register', [RegisteredUserController::class, 'store']);
    // جعل مسار التسجيل يؤدي إلى 404
    Route::get('register', function () {
        abort(404);
    })->name('register');

    Route::post('register', function () {
        abort(404);
    });
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // مسارات استعادة كلمة المرور
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {


    // تفعيل المصادقة الثنائية
    Route::post('two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.enable');

    // تعطيل المصادقة الثنائية
    Route::delete('two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
        ->name('two-factor.disable');

    // عرض صفحة التحقق الثنائي
    Route::get('two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
        ->name('two-factor.login');

    // معالجة إدخال رمز التحقق
    Route::post('two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store']);


    // مسارات التحقق من البريد الإلكتروني
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // مسارات تأكيد كلمة المرور
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // مسارات تغيير كلمة المرور
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    // مسار تسجيل الخروج
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

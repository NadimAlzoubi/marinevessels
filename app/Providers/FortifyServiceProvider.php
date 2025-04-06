<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // تسجيل Fortify فقط إذا لم يكن مسجلاً بالفعل
        if (! $this->app->bound(\Laravel\Fortify\FortifyServiceProvider::class)) {
            $this->app->register(\Laravel\Fortify\FortifyServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // مصادقة تسجيل الدخول المخصصة
        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                if (!$user->hasVerifiedEmail()) {
                    return null; // منع الدخول إذا لم يتم التحقق من البريد
                }

                if ($user->two_factor_secret) {
                    session(['two_factor_auth_pending' => $user->id]);
                    return null;
                }

                return $user;
            }

            return null;
        });

        // عرض التحقق الثنائي 2FA
        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        // إعداد عمليات Fortify المختلفة
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));
        
        Fortify::confirmPasswordView(function (){
            return view('auth.confirm-password');
         }); 



        // تحديد معدل محاولات تسجيل الدخول (5 محاولات في الدقيقة)
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        // تحديد معدل محاولات إدخال رمز 2FA (5 محاولات في الدقيقة)
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by(optional($request->session()->get('login.id')));
        });
    }
}

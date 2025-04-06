<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'], // يمكن أن يكون بريدًا إلكترونيًا أو اسم مستخدم
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // تحديد هل الإدخال بريد إلكتروني أم اسم مستخدم
        $fieldType = filter_var($this->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // أولًا تحقق من وجود المستخدم بناءً على البيانات المدخلة
        $user = User::where($fieldType, $this->input('login'))->first();

        // إذا لم يكن المستخدم موجودًا
        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        // التحقق من أن الحساب مفعل (مع التحويل إلى عدد صحيح)
        if ((int)$user->active === 0) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'login' => __('Account inactive.'),
            ]);
        }

        // إذا كان الحساب مفعل، نواصل محاولة تسجيل الدخول
        if (!Auth::attempt([$fieldType => $this->input('login'), 'password' => $this->input('password')], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // في حال فشل البيانات تمامًا
            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }



    /**
     * Ensure the login request is not rate limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')) . '|' . $this->ip());
    }
}

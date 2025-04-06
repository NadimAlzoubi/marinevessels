<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // استخدام الدالة fulfill() للتحقق من البريد الإلكتروني تلقائيًا
        $request->fulfill();

        // إعادة توجيه المستخدم إلى صفحة الـ profile.edit مع رسالة الحالة
        return redirect()->route('profile.edit')->with('status', 'email-verified');
    }
}

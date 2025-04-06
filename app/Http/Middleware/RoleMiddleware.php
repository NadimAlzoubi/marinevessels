<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // إذا كان المستخدم مسجل دخول
        if (Auth::check()) {
            // تحقق إذا كان دور المستخدم يتطابق مع أي من الأدوار المسموح بها
            if (in_array(Auth::user()->role, $roles)) {
                return $next($request); // السماح بالمرور
            }
        }

        // إعادة التوجيه في حال عدم تطابق الدور
        return redirect('/dashboard')->with('error', 'You do not have the required privileges.');
    }
}
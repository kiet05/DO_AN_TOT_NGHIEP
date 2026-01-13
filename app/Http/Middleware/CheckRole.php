<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // Chưa đăng nhập
        if (!$user) {
            abort(403, 'Bạn chưa đăng nhập.');
        }

        /**
         * Trường hợp 1: role là CỘT string (users.role)
         * ví dụ: admin, staff, customer
         */
        if (is_string($user->role)) {
            if (!in_array($user->role, $roles)) {
                abort(403, 'Bạn không có quyền truy cập trang này.');
            }
        }
        /**
         * Trường hợp 2: role là QUAN HỆ (roles table)
         * ví dụ: $user->role->slug
         */
        else {
            if (!$user->role || !in_array($user->role->slug, $roles)) {
                abort(403, 'Bạn không có quyền truy cập trang này.');
            }
        }

        return $next($request);
    }
}

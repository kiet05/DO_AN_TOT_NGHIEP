<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = Auth::user();

        // Nếu chưa đăng nhập
        if (!$user) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // ✅ Điều kiện linh hoạt ✅
        $isAdminById   = ($user->role_id ?? null) === 1;  // nếu 1 là admin
        $isAdminBySlug = strtolower(optional($user->role)->slug ?? '') === 'admin';

        if ($role === 'admin' && ($isAdminById || $isAdminBySlug)) {
            return $next($request);
        }

        // ✅ Giữ nguyên logic cũ nếu có slug bên bảng roles
        if ($user->role && $user->role->slug === $role) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập trang này.');
    }
}

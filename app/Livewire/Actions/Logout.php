<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        // Xóa session 2FA nếu có
        Session::forget(['2fa:user:id', '2fa:user:remember']);
        
        // Logout user
        Auth::guard('web')->logout();

        // Xóa tất cả session
        Session::invalidate();
        
        // Regenerate CSRF token
        Session::regenerateToken();

        // Redirect về trang login
        return redirect()->route('login')->with('status', 'Bạn đã đăng xuất thành công!');
    }
}

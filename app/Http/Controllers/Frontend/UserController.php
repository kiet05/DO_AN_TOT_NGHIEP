<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;

class UserController extends Controller
{
    /** Trang hồ sơ */
    public function index()
    {
        $user = Auth::user();
        return view('frontend.profile.index', compact('user'));
    }

    /** Cập nhật thông tin cơ bản */
    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|regex:/^0[0-9]{9,15}$/',
        ], [
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
        ]);

        $user = Auth::user();
        $user->update($request->only('name', 'phone'));

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /** Cập nhật avatar */
    // Cập nhật avatar
public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
    ]);

    $user = Auth::user();

    try {
        // Tạo thư mục nếu chưa tồn tại
        if (!Storage::disk('public')->exists('avatars')) {
            Storage::disk('public')->makeDirectory('avatars');
        }

        // Xóa ảnh cũ
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Lưu ảnh mới
        $path = $request->file('avatar')->store('avatars', 'public');

        // Lưu vào DB cột 'avatar'
   
        // Lưu vào cột 'avatar_path' cho thống nhất với view
        
$user->avatar_path = $path; 
$user->save();


        return back()->with('success', 'Cập nhật ảnh đại diện thành công!');

    } catch (\Exception $e) {
        \Log::error("Lỗi upload avatar: " . $e->getMessage());
        return back()->with('error', 'Không thể tải ảnh. Kiểm tra storage:link');
    }
}


    /** Đổi mật khẩu */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải ít nhất 8 ký tự.'
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng!');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}

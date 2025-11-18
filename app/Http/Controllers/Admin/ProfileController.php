<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\File; 

class ProfileController extends Controller
{
    
    public function index()
    {
        // Lấy thông tin người dùng đang đăng nhập và tải quan hệ 'role'
        /** @var User|null $user */
        $user = User::with('role')->find(Auth::id()); 
        
        // Truyền biến $user vào view
        return view('admin.profile.index', compact('user')); 
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Validate dữ liệu
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ];

        // Nếu người dùng muốn đổi mật khẩu
        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules, [
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng.',
            'avatar.max' => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
        ]);
        
        // Dữ liệu cần cập nhật
        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // 2. Xử lý File Upload (Avatar)
        if ($request->hasFile('avatar')) {
            $storageDisk = Storage::disk('public');
            $storagePath = 'avatars/';

            // Xóa ảnh cũ nếu có
            if ($user->avatar && $storageDisk->exists($storagePath . $user->avatar)) {
                $storageDisk->delete($storagePath . $user->avatar);
            }

            // Tạo tên file duy nhất (dùng timestamp và extension gốc)
            $file = $request->file('avatar');
            $imageName = time() . '_' . $file->hashName(); // Dùng hashName để tạo tên file duy nhất
            
            // Lưu file mới vào thư mục 'storage/app/public/avatars'
            $file->storeAs($storagePath, $imageName, 'public');
            
            $dataToUpdate['avatar'] = $imageName; // Thêm tên file mới vào mảng cập nhật
        }

        // 3. Xử lý đổi mật khẩu
        if ($request->filled('password')) {
            // Đã pass validation current_password, tiến hành cập nhật
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // 4. Cập nhật dữ liệu
        $user->update($dataToUpdate);

        // 5. Chuyển hướng
        return redirect()->route('admin.profile.index')->with('success', 'Hồ sơ và ảnh đại diện đã được cập nhật thành công!');
    }
}
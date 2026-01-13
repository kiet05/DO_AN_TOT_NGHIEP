<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAccountRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminAccountController extends Controller
{
    public function index()
    {
        $admins = User::with('role')
        ->orderBy('id', 'desc')
        ->paginate(10); // ← PHÂN TRANG + HỖ TRỢ appens()
        return view('admin.accounts.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.accounts.create', compact('roles'));
    }

    public function store(AdminAccountRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => 1,
                'email_verified_at' => Carbon::now(), // 🔥 QUAN TRỌNG

        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Thêm Admin mới thành công!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.accounts.edit', compact('user', 'roles'));
    }

    public function update(AdminAccountRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.accounts.index')->with('success', 'Xóa tài khoản thành công!');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => !$user->status]);

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}

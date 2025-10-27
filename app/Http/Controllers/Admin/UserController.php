<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Hiển thị danh sách khách hàng
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->has('q') && $request->q != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->q.'%')
                  ->orWhere('email', 'like', '%'.$request->q.'%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    // Xem chi tiết khách hàng
    public function show($id)
    {
        $user = User::with('orders')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    // Xóa tài khoản khách hàng
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa khách hàng thành công!');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Danh sách + tìm kiếm khách hàng
    public function index(Request $request)
    {
        $kw = trim((string)$request->get('q'));

        $customers = User::query()
            ->where('role_id', 2) // ✅ chỉ lấy khách hàng (nếu role_id=1 là admin)
            ->when($kw !== '', function ($q) use ($kw) {
                $q->where(function($qq) use ($kw) {
                    $qq->where('name','like',"%{$kw}%")
                       ->orWhere('email','like',"%{$kw}%")
                       ->orWhere('phone','like',"%{$kw}%");
                });
            })
            ->withCount('orders')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers','kw'));
    }

    // Chi tiết + lịch sử mua hàng
    public function show($id)
    {
        $customer = User::with(['orders' => fn($q)=>$q->latest('id')])->findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    // Khoá / mở tài khoản
    public function toggleStatus($id)
    {
        $customer = User::findOrFail($id);
        $customer->status = $customer->status ? 0 : 1;
        $customer->save();

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}



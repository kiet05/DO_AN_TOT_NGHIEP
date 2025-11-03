<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Danh sách khách hàng + tìm kiếm + lọc trạng thái
     * Mặc định: role_id = 3 là khách hàng
     */
    public function index(Request $request)
    {
        $search = (string) $request->input('search');
        $status = (string) $request->input('status'); // 'active' | 'inactive' | ''

        $customers = User::query()
            ->where('role_id', 3)                  // chỉ lấy khách
            ->withCount('orders')                  // đếm số đơn
            ->when($search, function ($q) use ($search) {
                $q->where(function ($x) use ($search) {
                    $x->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            })
            ->when($status === 'active',  fn($q) => $q->where('status', 1))
            ->when($status === 'inactive',fn($q) => $q->where('status', 0))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Chi tiết khách hàng + lịch sử đơn (phân trang)
     */
    public function show($id)
    {
        // chỉ tìm trong nhóm khách hàng
        $customer = User::where('role_id', 3)->findOrFail($id);

        $orders = $customer->orders()
            ->with('orderItems')    // để hiển thị sum(quantity)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    /**
     * Khóa / Mở tài khoản
     */
    public function toggleStatus($id)
    {
        $customer = User::where('role_id', 3)->findOrFail($id);
        $customer->status = (int)!$customer->status;
        $customer->save();

        return back()->with('success', $customer->status ? 'Đã mở khóa tài khoản!' : 'Đã khóa tài khoản!');
    }
}

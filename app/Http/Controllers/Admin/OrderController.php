<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::query();

        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status != '') {
            $query->where('order_status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->order_status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    // Xuất danh sách đơn hàng (demo)
    public function export()
    {
        $orders = Order::all();
        return view('admin.orders.export', compact('orders'));
    }
}

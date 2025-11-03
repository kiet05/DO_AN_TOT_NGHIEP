<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


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

   public function show(\App\Models\Order $order)
{
    // Nếu có quan hệ items thì mới load, tránh lỗi khi dự án chưa khai báo
    if (method_exists($order, 'items')) {
        $order->load(['items.product']);
    }

    return view('admin.orders.show', compact('order')); // ✅ bắt buộc phải có compact('order')
}


    public function invoice(\App\Models\Order $order)
    {
        if (method_exists($order, 'items')) {
            $order->load(['items.product']);
        }

        return view('admin.orders.invoice', compact('order'));
    }

    // public function invoice(Order $order)
    // {
    //     $order->load(['items.product']);
    //     return view('admin.orders.invoice', compact('order'));
    // }


    public function downloadInvoice(\App\Models\Order $order)
    {
        if (method_exists($order, 'items')) {
            $order->load(['items.product']);
        }

        $pdf = Pdf::loadView('admin.orders.invoice_pdf', compact('order'));
        return $pdf->download('invoice_'.$order->id.'.pdf');
    }



    // Xuất danh sách đơn hàng (demo)
    public function export()
    {
        $orders = Order::all();
        return view('admin.orders.export', compact('orders'));
    }
}

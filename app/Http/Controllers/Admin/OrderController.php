<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('status')) {
            // Chuẩn hoá và lấy mọi biến thể (completed<->success, cancelled<->canceled)
            $filter = $this->canonicalStatus($request->status);
            $query->whereIn('order_status', $this->statusSynonyms($filter));
        }
        // Lọc theo ID (chấp nhận #00087 hoặc 87)
    if ($request->filled('id')) {
        $raw = (string) $request->input('id');
        $id  = preg_replace('/\D/', '', $raw); // bỏ mọi ký tự không phải số
        if ($id !== '') {
            $query->where('id', (int) $id);
        }
    }


        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    private function statusMatrix(): array
    {
        return [
            'pending'   => ['shipping', 'cancelled'],
            'shipping'  => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];
    }


    // Trạng thái CHUẨN dùng để hiển thị/validate chính
    private function allowedStatuses(): array
    {
        return ['pending', 'shipping', 'completed', 'cancelled'];
    }


    // Cập nhật trạng thái đơn hàng

    public function updateStatus(Request $request, Order $order)
    {
        // Chấp nhận cả biến thể cũ khi submit
        $valid = array_merge($this->allowedStatuses(), array_keys($this->legacyAliases()));
        $request->validate([
            'status' => 'required|in:' . implode(',', $valid),
        ]);

        // Chuẩn hoá from/to
        $from = $this->canonicalStatus($order->order_status);
        $to   = $this->canonicalStatus($request->input('status'));

        $matrix = $this->statusMatrix();
        if (!in_array($to, $matrix[$from] ?? [], true)) {
            return back()->with('error', "Không thể chuyển trạng thái từ {$from} → {$to}.");
        }

        DB::transaction(function () use ($order, $to) {
            // LUÔN lưu tên chuẩn vào DB
            $order->order_status = $to;
            $order->save();

            // Đồng bộ thanh toán khi đơn 'completed'
            if ($to === 'completed' && method_exists($order, 'payment') && $order->payment) {
                $payment = $order->payment;
                if (in_array($payment->status, ['pending', 'failed', 'canceled'], true)) {
                    $payment->status = 'success';     // trạng thái ở bảng payments vẫn để 'success'
                }
                if (empty($payment->paid_at)) {
                    $payment->paid_at = now();
                }
                $payment->save();
            }
        });

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function show(Order $order)
    {
        $items = method_exists($order, 'items')
            ? $order->items()->with('product')->get()
            : collect();

        $from         = $this->canonicalStatus($order->order_status);
        $allowedNext  = $this->statusMatrix()[$from] ?? [];

        return view('admin.orders.show', compact('order', 'items', 'allowedNext'));
    }



    public function invoice(Order $order)
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


    public function downloadInvoice(Order $order)
{
    if (method_exists($order, 'items')) {
        $order->load(['items.product']);
    }

    // Đổi tên view ở đây
    $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));

    return $pdf->download('invoice_' . $order->id . '.pdf');
}



    // Xuất danh sách đơn hàng (demo)
    public function export()
    {
        $orders = Order::all();
        return view('admin.orders.export', compact('orders'));
    }
    // Thêm vào trong class OrderController

    /** Map các tên cũ -> tên chuẩn dùng trong view */
    private function legacyAliases(): array
    {
        return [
            'success'  => 'completed',
            'canceled' => 'cancelled',
        ];
    }

    /** Trả về tên trạng thái chuẩn */
    private function canonicalStatus(string $status): string
    {
        $aliases = $this->legacyAliases();
        return $aliases[$status] ?? $status;
    }

    /** Lấy tất cả biến thể (chuẩn + cũ) của một trạng thái để whereIn */
    private function statusSynonyms(string $status): array
    {
        $canon = $this->canonicalStatus($status);
        $syn   = [$canon];

        foreach ($this->legacyAliases() as $old => $new) {
            if ($new === $canon) $syn[] = $old;
        }
        return array_values(array_unique($syn));
    }
}

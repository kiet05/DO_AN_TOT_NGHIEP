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
            'pending'    => ['confirmed', 'cancelled'],      // Chờ xử lý -> Xác nhận / Hủy
            'confirmed'  => ['processing', 'cancelled'],     // Xác nhận   -> Chuẩn bị / Hủy
            'processing' => ['shipping', 'cancelled'],       // Chuẩn bị   -> Đang giao / Hủy

            // ĐANG GIAO: chỉ cho phép sang ĐÃ GIAO hoặc HỦY
            'shipping'   => ['shipped', 'cancelled'],

            // ĐÃ GIAO: có thể sang HOÀN THÀNH hoặc HOÀN HÀNG
            'shipped' => ['completed', 'return_pending'], // 👈 chỉ cho yêu cầu hoàn
            'return_pending' => ['returned', 'cancelled'],
            // HOÀN THÀNH: trạng thái cuối (nếu muốn cho phép hoàn sau hoàn thành
            // thì đổi thành ['returned'])
            'completed'  => [],

            // 2 trạng thái cuối còn lại
            'cancelled'  => [],
            'returned'   => [],
        ];
    }
    // Trạng thái CHUẨN dùng để hiển thị/validate chính
    private function allowedStatuses(): array
    {
        return [
            'pending',    // Chờ xử lý
            'confirmed',  // Xác nhận
            'processing', // Chuẩn bị
            'shipping',   // Đang giao
            'shipped',    // Đã giao
            'completed',  // Hoàn thành
            'cancelled',  // Hủy
            'returned',   // Hoàn hàng
            'return_pending',   // Hoàn hàng
            'return_waiting_customer', // Chờ xác nhận hoàn hàng
            // 'returned_completed', // Hoàn thành trả hàng
        ];
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
        $to = $this->canonicalStatus($request->input('status'));

        if ($to === 'completed') {
            return back()->with('error', 'Không thể cập nhật thủ công sang trạng thái Hoàn thành. Hệ thống sẽ tự động xử lý.');
        }


        $matrix = $this->statusMatrix();
        if (!in_array($to, $matrix[$from] ?? [], true)) {
            return back()->with('error', "Không thể chuyển trạng thái từ {$from} → {$to}.");
        }

        DB::transaction(function () use ($order, $to) {
            // Lưu trạng thái cũ để kiểm tra có thay đổi hay không
            $oldStatus = $order->order_status;

            // Luôn lưu tên chuẩn vào DB
            $order->order_status = $to;

            // Nếu có thay đổi trạng thái thì:
            if ($oldStatus !== $to) {
                // 1) cập nhật thời gian đổi trạng thái
                $order->status_changed_at = now();

                // ✅ 1.1) set shipped_at CHỈ KHI chuyển sang shipped (lần đầu)
                if ($to === 'shipped' && empty($order->shipped_at)) {
                    $order->shipped_at = now();
                }

                // ✅ 1.2) nếu admin set completed thủ công
                if ($to === 'completed' && empty($order->completed_at)) {
                    $order->completed_at = now();
                }

                // 2) lưu lịch sử trạng thái (phục vụ hiển thị stepper)
                if (method_exists($order, 'statusHistories')) {
                    $order->statusHistories()->create([
                        'status' => $to,
                        // 'changed_from' => $oldStatus,
                        // 'changed_by'   => auth()->id(),
                    ]);
                }
            }

            // Nếu đơn đã giao hoặc hoàn thành thì coi như đã thanh toán
            if (in_array($to, ['shipped', 'completed'], true)) {
                $order->payment_status = 'paid';
            }

            $order->save();

            // Đồng bộ thanh toán khi đơn 'completed'
            if ($to === 'completed' && method_exists($order, 'payment') && $order->payment) {
                $payment = $order->payment;
                if (in_array($payment->status, ['pending', 'failed', 'canceled'], true)) {
                    $payment->status = 'success';
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
        // Nạp luôn các quan hệ để view dùng cho nhẹ
        $order->load([
            'user',                                       // tài khoản đặt hàng
            'orderItems.product',                         // $it->product
            'orderItems.productVariant',                  // $it->productVariant
            'orderItems.productVariant.attributeValues',  // phân loại biến thể
            'statusHistories',                            // lịch sử trạng thái (thêm)
        ]);

        $from         = $this->canonicalStatus($order->order_status);
        $statusMatrix = $this->statusMatrix();
        $allowedNext  = $statusMatrix[$from] ?? [];

        // Gom thời gian lần đầu đạt từng trạng thái
        $statusTimes = $order->statusHistories
            ->sortBy('created_at')
            ->groupBy('status')
            ->map(function ($group) {
                // lấy lần ĐẦU đạt trạng thái đó
                return $group->first()->created_at;
                // nếu muốn lần CUỐI thì dùng: return $group->last()->created_at;
            });

        return view('admin.orders.show', compact('order', 'allowedNext', 'statusTimes'));
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

            // Nếu trước đây bạn có ghi kiểu khác thì thêm vào đây
            // 'processing_old' => 'processing',
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

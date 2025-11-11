<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $vnpayService;

    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    /**
     * Xử lý thanh toán đơn hàng
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền thanh toán đơn hàng này');
        }

        // Cập nhật phương thức thanh toán cho đơn hàng
        $order->payment_method_id = $paymentMethod->id;
        $order->payment_method = $paymentMethod->slug;
        $order->save();

        // Xử lý theo từng phương thức thanh toán
        if ($paymentMethod->slug === 'cod') {
            // COD: Chỉ cần cập nhật trạng thái
            $order->payment_status = 'pending';
            $order->order_status = 'pending';
            $order->save();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Đơn hàng đã được đặt thành công. Bạn sẽ thanh toán khi nhận hàng.');
        } elseif ($paymentMethod->slug === 'vnpay') {
            // VNPay: Tạo URL thanh toán
            $paymentUrl = $this->vnpayService->createPaymentUrl([
                'order_id' => $order->id,
                'amount' => $order->final_amount,
                'order_info' => 'Thanh toan don hang #' . $order->id,
                'order_type' => 'other',
                'locale' => 'vn',
            ]);

            return redirect($paymentUrl);
        }

        return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ');
    }

    /**
     * Xử lý callback từ VNPay
     */
    public function vnpayReturn(Request $request)
    {
        $data = $request->all();
        
        // Xác thực thanh toán
        $result = $this->vnpayService->verifyPayment($data);

        if ($result['success']) {
            // Cập nhật đơn hàng
            $order = Order::findOrFail($result['order_id']);
            $order->payment_status = 'paid';
            $order->order_status = 'processing';
            $order->save();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Thanh toán thành công! Đơn hàng của bạn đã được xác nhận.');
        } else {
            return redirect()->route('orders.show', $result['order_id'] ?? 0)
                ->with('error', 'Thanh toán thất bại: ' . $result['message']);
        }
    }

    /**
     * Lấy danh sách phương thức thanh toán đang hoạt động
     */
    public function getPaymentMethods()
    {
        $paymentMethods = PaymentMethod::active()->get();
        
        return response()->json([
            'success' => true,
            'data' => $paymentMethods,
        ]);
    }
}

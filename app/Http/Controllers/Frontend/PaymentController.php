<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\VNPayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected $vnpayService;

    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }


    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function createPayment(Request $request): Redirector|RedirectResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền thanh toán đơn hàng này');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Đơn hàng này đã được thanh toán rồi.');
        }

        $paymentUrl = $this->vnpayService->createPaymentUrl([
            'order_id' => $order->id,
            'amount' => $order->final_amount,
            'order_info' => 'Thanh toan don hang #' . $order->id,
            'order_type' => 'other',
            'locale' => 'vn',
        ]);

        $payment = Payment::where('order_id', $order->id)
            ->where('gateway', 'vnpay')
            ->first();

        if ($payment) {
            $payment->update([
                'app_trans_id' => $order->vnp_txn_ref,
                'status' => 'pending',
            ]);
        }

        return redirect($paymentUrl);
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function vnpayReturn(Request $request): RedirectResponse
    {
        $result = $this->vnpayService->verifyPayment($request->all());

        if (!$result['success']) {
            Log::warning('VNPay: Verification failed', [
                'message' => $result['message'],
                'data' => $request->all()
            ]);

            if (isset($result['order_id']) && $result['order_id']) {
                $order = Order::find($result['order_id']);
                if ($order) {
                    $order->update([
                        'payment_status' => 'failed',
                        'vnp_response' => $request->all(),
                    ]);
                }
            }

            return redirect()->route('checkout.failed')
                ->with('error', $result['message']);
        }

        $order = Order::findOrFail($result['order_id']);

        $order->update([
            'vnp_response' => $request->all(),
            'vnp_transaction_no' => $result['transaction_no'] ?? null,
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        $payment = Payment::where('order_id', $order->id)
            ->where('gateway', 'vnpay')
            ->first();

        if ($payment) {
            $payment->update([
                'zp_trans_id' => $result['transaction_no'] ?? null,
                'status' => 'success',
                'paid_at' => now(),
                'meta' => $request->all(),
            ]);
        }

        Log::info('VNPay Payment Success', [
            'order_id' => $order->id,
            'txn_ref' => $result['txn_ref'],
            'amount' => $result['amount'],
            'transaction_no' => $result['transaction_no']
        ]);

        session(['checkout_order_id' => $order->id]);
        session()->save();

        return redirect()->route('checkout.success', ['order_id' => $order->id])
            ->with('success', 'Thanh toán VNPay thành công! Cảm ơn bạn đã đặt hàng.');
    }

    /**
     * @param $code
     * @return string
     */
    private function getResponseMessage($code)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '01' => 'Giao dịch đã tồn tại',
            '02' => 'Merchant không hợp lệ',
            '03' => 'Dữ liệu gửi sang không đúng định dạng',
            '04' => 'Không cho phép thanh toán',
            '05' => 'Giao dịch không thành công do: Quý khách nhập sai mật khẩu xác thực giao dịch (OTP)',
            '06' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP) quá số lần quy định',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '08' => 'Giao dịch không thành công do: Hệ thống Ngân hàng đang bảo trì. Quý khách tạm thời không thể thực hiện giao dịch bằng thẻ/tài khoản của Ngân hàng này.',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP).',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Các lỗi khác',
        ];

        return $messages[$code] ?? "Mã lỗi: {$code}";
    }
}

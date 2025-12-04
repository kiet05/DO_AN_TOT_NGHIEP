<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PaymentController extends Controller
{
    // Callback từ VNPay (người dùng quay lại trang web)
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        // Kiểm tra có chữ ký không
        if (!$request->filled('vnp_SecureHash')) {
            return view('frontend.checkout.failed', ['message' => 'Thiếu chữ ký từ VNPay']);
        }

        $inputData = $request->all();
        $secureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= $key . '=' . $value . '&';
        }
        $hashData = rtrim($hashData, '&');

        $calculatedHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($calculatedHash !== $secureHash) {
            Log::warning('VNPay: Chữ ký không hợp lệ', $request->all());
            return view('frontend.checkout.failed', ['message' => 'Chữ ký không hợp lệ! Có thể bị giả mạo.']);
        }

        $txnRef = $request->vnp_TxnRef; // VD: 123_170254123456
        $amount = $request->vnp_Amount / 100;
        $responseCode = $request->vnp_ResponseCode;

        // Tìm đơn hàng qua vnp_txn_ref
        $order = Order::where('vnp_txn_ref', $txnRef)->first();

        if (!$order) {
            return view('frontend.checkout.failed', ['message' => 'Không tìm thấy đơn hàng!']);
        }

        // Lưu raw response để debug sau này
        $order->vnp_response = $request->all();
        $order->save();

        if ($responseCode == '00') {
            // THANH TOÁN THÀNH CÔNG
            $order->update([
                'payment_status' => 'paid',
                'order_status'   => 'confirmed', // hoặc pending tùy bạn
            ]);

            Log::info('VNPay Payment Success', [
                'order_id' => $order->id,
                'vnp_TxnRef' => $txnRef,
                'amount' => $amount
            ]);

            // Xóa session cũ
            session()->forget(['checkout_order_id', 'vnpay_order_id']);

            return redirect()->route('checkout.success')
                ->with('success', 'Thanh toán VNPay thành công! Cảm ơn bạn.');
        }

        // THẤT BẠI
        $order->update(['payment_status' => 'failed']);

        $message = $this->getResponseMessage($responseCode);
        Log::warning('VNPay Payment Failed', $request->all());

        return view('frontend.checkout.failed', [
            'message' => "Thanh toán thất bại: {$message}"
        ]);
    }

    private function getResponseMessage($code)
    {
        $messages = [
            '00' => 'Thành công',
            '01' => 'Giao dịch đã tồn tại',
            '02' => 'Đơn hàng đã được xác nhận',
            '07' => 'Giao dịch bị nghi ngờ lừa đảo',
            '09' => 'Thẻ/tài khoản chưa đăng ký Internet Banking',
            '10' => 'Xác thực sai quá 3 lần',
            '11' => 'Hết hạn chờ thanh toán',
            '12' => 'Thẻ/tài khoản bị khóa',
            '13' => 'Sai OTP',
            '24' => 'Khách hàng hủy giao dịch',
            '51' => 'Tài khoản không đủ tiền',
            '65' => 'Vượt hạn mức ngày',
            '75' => 'Ngân hàng bảo trì',
            '79' => 'Sai mật khẩu quá số lần',
            '99' => 'Lỗi khác',
        ];

        return $messages[$code] ?? "Mã lỗi: {$code}";
    }
}
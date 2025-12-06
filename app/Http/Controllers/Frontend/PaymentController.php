<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PaymentController extends Controller
{
    /**
     * Callback từ VNPay (người dùng quay lại trang web)
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        // Kiểm tra có chữ ký không
        if (!$request->filled('vnp_SecureHash')) {
            return redirect()->route('checkout.failed')
                ->with('error', 'Thiếu chữ ký từ VNPay');
        }

        $inputData = $request->all();
        $secureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if (!empty($value)) {
                $hashData .= $key . '=' . $value . '&';
            }
        }
        $hashData = rtrim($hashData, '&');

        $calculatedHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($calculatedHash !== $secureHash) {
            Log::warning('VNPay: Chữ ký không hợp lệ', $request->all());
            return redirect()->route('checkout.failed')
                ->with('error', 'Chữ ký không hợp lệ! Có thể bị giả mạo.');
        }

        $txnRef = $request->vnp_TxnRef; // VD: 123_170254123456
        $amount = $request->vnp_Amount / 100;
        $responseCode = $request->vnp_ResponseCode;
        $transactionNo = $request->vnp_TransactionNo ?? null;

        // Tìm đơn hàng qua vnp_TxnRef
        $order = Order::where('vnp_txn_ref', $txnRef)->first();

        if (!$order) {
            Log::error('VNPay: Không tìm thấy đơn hàng', ['vnp_TxnRef' => $txnRef]);
            return redirect()->route('checkout.failed')
                ->with('error', 'Không tìm thấy đơn hàng!');
        }

        // Lưu raw response để debug sau này
        $order->vnp_response = json_encode($request->all());
        $order->vnp_transaction_no = $transactionNo;
        $order->save();

        if ($responseCode == '00') {
            // THANH TOÁN THÀNH CÔNG
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed', // hoặc pending tùy bạn
            ]);

            Log::info('VNPay Payment Success', [
                'order_id' => $order->id,
                'vnp_TxnRef' => $txnRef,
                'amount' => $amount,
                'transaction_no' => $transactionNo
            ]);

            return redirect()->route('checkout.success')
                ->with('success', 'Thanh toán VNPay thành công! Cảm ơn bạn đã đặt hàng.');
        }

        // THANH TOÁN THẤT BẠI
        $order->update([
            'payment_status' => 'failed',
            'order_status' => 'cancelled'
        ]);

        $message = $this->getResponseMessage($responseCode);
        Log::warning('VNPay Payment Failed', [
            'order_id' => $order->id,
            'response_code' => $responseCode,
            'message' => $message,
            'data' => $request->all()
        ]);

        return redirect()->route('checkout.failed')
            ->with('error', "Thanh toán thất bại: {$message}");
    }

    /**
     * Lấy thông báo lỗi từ VNPay response code
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
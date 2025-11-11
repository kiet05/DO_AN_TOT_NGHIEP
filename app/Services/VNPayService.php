<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VNPayService
{
    protected $tmnCode;
    protected $hashSecret;
    protected $url;
    protected $returnUrl;

    public function __construct()
    {
        // Ưu tiên lấy từ database, nếu không có thì lấy từ .env
        $vnpayMethod = \App\Models\PaymentMethod::where('slug', 'vnpay')->first();
        
        if ($vnpayMethod && $vnpayMethod->config) {
            $config = is_string($vnpayMethod->config) ? json_decode($vnpayMethod->config, true) : $vnpayMethod->config;
            $this->tmnCode = $config['merchant_id'] ?? env('VNPAY_TMN_CODE', '');
            $this->hashSecret = $config['secret_key'] ?? env('VNPAY_HASH_SECRET', '');
            $this->url = $config['url'] ?? env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
            $this->returnUrl = $config['return_url'] ?? env('VNPAY_RETURN_URL', url('/payment/vnpay/return'));
        } else {
            // Fallback về .env
            $this->tmnCode = env('VNPAY_TMN_CODE', '');
            $this->hashSecret = env('VNPAY_HASH_SECRET', '');
            $this->url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
            $this->returnUrl = env('VNPAY_RETURN_URL', url('/payment/vnpay/return'));
        }
    }

    /**
     * Tạo URL thanh toán VNPay
     *
     * @param array $params
     * @return string
     */
    public function createPaymentUrl(array $params): string
    {
        $vnp_TxnRef = $params['order_id']; // Mã đơn hàng
        $vnp_OrderInfo = $params['order_info'] ?? 'Thanh toan don hang';
        $vnp_OrderType = $params['order_type'] ?? 'other';
        $vnp_Amount = $params['amount'] * 100; // VNPay yêu cầu số tiền nhân 100
        $vnp_Locale = $params['locale'] ?? 'vn';
        $vnp_IpAddr = request()->ip();
        $vnp_CurrCode = 'VND';
        $vnp_CreateDate = date('YmdHis');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $vnp_CreateDate,
            'vnp_CurrCode' => $vnp_CurrCode,
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        // Sắp xếp lại mảng theo key
        ksort($inputData);
        
        // Tạo query string
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        // Tạo chữ ký
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        $query .= 'vnp_SecureHash=' . $vnp_SecureHash;

        return $this->url . '?' . $query;
    }

    /**
     * Xác thực callback từ VNPay
     *
     * @param array $data
     * @return array
     */
    public function verifyPayment(array $data): array
    {
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash']);

        // Sắp xếp lại mảng
        ksort($data);
        
        // Tạo hashdata
        $i = 0;
        $hashdata = '';
        foreach ($data as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        // Tạo chữ ký
        $secureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);

        // Kiểm tra chữ ký
        if ($secureHash !== $vnp_SecureHash) {
            return [
                'success' => false,
                'message' => 'Chữ ký không hợp lệ',
            ];
        }

        // Kiểm tra response code
        $responseCode = $data['vnp_ResponseCode'] ?? '';
        
        if ($responseCode === '00') {
            return [
                'success' => true,
                'message' => 'Thanh toán thành công',
                'order_id' => $data['vnp_TxnRef'] ?? '',
                'transaction_id' => $data['vnp_TransactionNo'] ?? '',
                'amount' => ($data['vnp_Amount'] ?? 0) / 100, // Chia 100 để lấy số tiền thực
            ];
        } else {
            return [
                'success' => false,
                'message' => $this->getResponseMessage($responseCode),
                'order_id' => $data['vnp_TxnRef'] ?? '',
            ];
        }
    }

    /**
     * Lấy thông báo lỗi từ response code
     *
     * @param string $responseCode
     * @return string
     */
    protected function getResponseMessage(string $responseCode): string
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking',
            '10' => 'Xác thực thông tin thẻ/tài khoản không đúng. Quá 3 lần',
            '11' => 'Đã hết hạn chờ thanh toán. Xin vui lòng thực hiện lại giao dịch',
            '12' => 'Thẻ/Tài khoản bị khóa',
            '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP). Quá 3 lần',
            '51' => 'Tài khoản không đủ số dư để thực hiện giao dịch',
            '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng thanh toán đang bảo trì',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định',
            '99' => 'Lỗi không xác định',
        ];

        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }
}


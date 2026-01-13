<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class VNPayService
{
    protected $tmnCode;
    protected $hashSecret;
    protected $url;
    protected $returnUrl;

    public function __construct()
    {
        $this->tmnCode = config('services.vnpay.tmn_code') ?? env('VNPAY_TMN_CODE');
        $this->hashSecret = config('services.vnpay.hash_secret') ?? env('VNPAY_HASH_SECRET');
        $this->url = config('services.vnpay.url') ?? env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->returnUrl = config('services.vnpay.return_url') ?? env('VNPAY_RETURN_URL', env('APP_URL') . '/payment/vnpay-return');
    }

    /**
     * @param array $params
     * @return string
     */
    public function createPaymentUrl(array $params): string
    {
        $orderId = $params['order_id'] ?? null;
        $amount = $params['amount'] ?? 0;
        $orderInfo = $params['order_info'] ?? 'Thanh toan don hang';
        $orderType = $params['order_type'] ?? 'other';
        $locale = $params['locale'] ?? 'vn';
        $bankCode = $params['bank_code'] ?? '';

        $txnRef = $orderId . '_' . time();

        $vnp_Params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_Amount' => $amount * 100,
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $txnRef,
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => $orderType,
            'vnp_Locale' => $locale,
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_IpAddr' => request()->ip(),
            'vnp_CreateDate' => date('YmdHis'),
        ];

        if (!empty($bankCode)) {
            $vnp_Params['vnp_BankCode'] = $bankCode;
        }

        ksort($vnp_Params);

        $query = '';
        $i = 0;
        $hashData = '';
        foreach ($vnp_Params as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnp_SecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        $query .= 'vnp_SecureHash=' . $vnp_SecureHash;

        if ($orderId) {
            \App\Models\Order::where('id', $orderId)->update([
                'vnp_txn_ref' => $txnRef
            ]);
        }

        $paymentUrl = $this->url . '?' . $query;

        Log::info('VNPay: Created payment URL', [
            'order_id' => $orderId,
            'txn_ref' => $txnRef,
            'amount' => $amount,
        ]);

        return $paymentUrl;
    }

    /**
     * @param array $data
     * @return array
     */
    public function verifyPayment(array $data): array
    {
        $secureHash = $data['vnp_SecureHash'] ?? '';

        if (empty($secureHash)) {
            return [
                'success' => false,
                'message' => 'Thiếu chữ ký từ VNPay',
            ];
        }

        $inputData = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'vnp_') === 0 && $key !== 'vnp_SecureHash' && $key !== 'vnp_SecureHashType') {
                $inputData[$key] = $value;
            }
        }

        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if (strlen($value) > 0) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . '=' . urlencode($value);
                    $i = 1;
                }
            }
        }

        $calculatedHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        if ($calculatedHash !== $secureHash) {
            Log::warning('VNPay: Invalid signature', [
                'calculated' => $calculatedHash,
                'received' => $secureHash,
                'hash_data' => $hashData,
                'input_data' => $inputData,
            ]);

            return [
                'success' => false,
                'message' => 'Chữ ký không hợp lệ! Có thể bị giả mạo.',
            ];
        }

        $txnRef = $data['vnp_TxnRef'] ?? '';
        $responseCode = $data['vnp_ResponseCode'] ?? '';
        $amount = isset($data['vnp_Amount']) ? $data['vnp_Amount'] / 100 : 0;
        $transactionNo = $data['vnp_TransactionNo'] ?? null;

        $orderId = null;
        if (strpos($txnRef, '_') !== false) {
            $parts = explode('_', $txnRef);
            $orderId = (int) $parts[0];
        }

        if ($responseCode === '00') {
            return [
                'success' => true,
                'order_id' => $orderId,
                'txn_ref' => $txnRef,
                'amount' => $amount,
                'transaction_no' => $transactionNo,
                'response_code' => $responseCode,
                'message' => 'Thanh toán thành công',
            ];
        } else {
            $message = $this->getResponseMessage($responseCode);
            return [
                'success' => false,
                'order_id' => $orderId,
                'txn_ref' => $txnRef,
                'amount' => $amount,
                'response_code' => $responseCode,
                'message' => $message,
            ];
        }
    }

    /**
     * @param string $txnRef
     * @return array
     */
    public function query(string $txnRef): array
    {
        $vnp_Params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'querydr',
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_TxnRef' => $txnRef,
            'vnp_OrderInfo' => 'Truy van giao dich',
            'vnp_TransactionDate' => date('YmdHis'),
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_IpAddr' => request()->ip(),
        ];

        ksort($vnp_Params);

        $hashData = '';
        $i = 0;
        foreach ($vnp_Params as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $vnp_SecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        $vnp_Params['vnp_SecureHash'] = $vnp_SecureHash;

        $queryUrl = str_replace('vpcpay.html', 'querydr.html', $this->url);
        $queryUrl = str_replace('/paymentv2/', '/merchant_webapi/', $queryUrl);

        try {
            $response = Http::asForm()->post($queryUrl, $vnp_Params);
            $result = $response->body();
            parse_str($result, $parsed);

            Log::info('VNPay Query Response', [
                'txn_ref' => $txnRef,
                'response' => $parsed,
            ]);

            return [
                'return_code' => $parsed['vnp_ResponseCode'] ?? '99',
                'data' => $parsed,
                'status' => ($parsed['vnp_ResponseCode'] ?? '99') === '00' ? 1 : 0,
            ];
        } catch (\Exception $e) {
            Log::error('VNPay Query Error', [
                'txn_ref' => $txnRef,
                'error' => $e->getMessage(),
            ]);

            return [
                'return_code' => '99',
                'message' => 'Lỗi khi truy vấn: ' . $e->getMessage(),
                'status' => 0,
            ];
        }
    }

    /**
     * @param string $code
     * @return string
     */
    private function getResponseMessage(string $code): string
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


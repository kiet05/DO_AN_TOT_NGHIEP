<?php

namespace App\Services;

class VNPayService
{
    protected $vnp_TmnCode;
    protected $vnp_HashSecret;
    protected $vnp_Url;
    protected $vnp_ReturnUrl;

    public function __construct()
{
    $this->vnp_TmnCode    = config('vnpay.vnp_tmn_code');
    $this->vnp_HashSecret = config('vnpay.vnp_hash_secret');
    $this->vnp_Url        = config('vnpay.vnp_url');
    $this->vnp_ReturnUrl  = config('vnpay.vnp_return_url');
}


    /**
     * Tạo URL thanh toán VNPay
     * Nhận params dạng array:
     * [ 'amount' => ..., 'order_id' => ..., 'app_trans_id' => ... ]
     */
    public function createPaymentUrl($params)
    {
        $amount       = $params['amount'];
        $orderId      = $params['order_id'];
        $appTransId   = $params['app_trans_id'];

        $vnp_Amount    = $amount * 100;

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $this->vnp_TmnCode,
            "vnp_Amount"     => $vnp_Amount,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => request()->ip(),
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => "Thanh toán đơn hàng #$orderId",
            "vnp_OrderType"  => "billpayment",
            "vnp_ReturnUrl"  => $this->vnp_ReturnUrl,
            "vnp_TxnRef" => $orderId
        ];

        // Sort theo key
        ksort($inputData);

        $query = "";
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            $hashdata .= $key . "=" . $value . "&";
            $query    .= urlencode($key) . "=" . urlencode($value) . "&";
        }

        $query    = rtrim($query, '&');
        $hashdata = rtrim($hashdata, '&');

        // Tạo secure hash
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);

        return $this->vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnp_SecureHash;
    }

    /**
     * Query trạng thái giao dịch (DEMO)
     */
    public function query($txnRef)
    {
        return [
            'return_code' => 1,
            'data'        => [
                'status'       => 1,
                'zp_trans_id'  => 'demo12345'
            ]
        ];
    }

    /**
     * Xác minh RETURN từ VNPay
     */
    public function verifyReturn($data)
    {
        if (!isset($data['vnp_SecureHash'])) {
            return ['success' => false, 'message' => 'Missing secure hash'];
        }

        $receivedHash = $data['vnp_SecureHash'];
        unset($data['vnp_SecureHash']);
        unset($data['vnp_SecureHashType']);

        ksort($data);

        $hashString = "";
        foreach ($data as $key => $value) {
            $hashString .= $key . "=" . $value . "&";
        }
        $hashString = rtrim($hashString, "&");

        $calculatedHash = hash_hmac('sha512', $hashString, $this->vnp_HashSecret);

        return [
            'success' => ($calculatedHash === $receivedHash)
        ];
    }
    public function verifyPayment($inputData)
{
    $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

    unset($inputData['vnp_SecureHash']);
    unset($inputData['vnp_SecureHashType']);

    ksort($inputData);

    $hashData = '';
    foreach ($inputData as $key => $value) {
        $hashData .= $key . "=" . $value . "&";
    }
    $hashData = rtrim($hashData, '&');

    // Tạo checksum để so sánh
    $myChecksum = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);

    if ($myChecksum !== $vnp_SecureHash) {
        return [
            'success' => false,
            'message' => 'Sai checksum',
        ];
    }

    // 00 = thanh toán thành công
    $success = ($inputData['vnp_ResponseCode'] ?? null) === '00';

    return [
        'success' => $success,
        'order_id' => explode('_', $inputData['vnp_TxnRef'])[0],
        'message' => $success ? 'Thanh toán thành công' : 'Thanh toán thất bại',
    ];
}

}

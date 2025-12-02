<?php

return [

    // Mã website do VNPay cấp
    'vnp_TmnCode' => env('VNPAY_TMN_CODE', ''),

    // Chuỗi bí mật ký HMAC SHA512
    'vnp_HashSecret' => env('VNPAY_HASH_SECRET', ''),

    // URL thanh toán sandbox (test)
    'vnp_Url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),

    // URL nhận kết quả sau thanh toán
    'vnp_ReturnUrl' => env('VNPAY_RETURN_URL', 'http://localhost:8000/payment/vnpay/return'),

];

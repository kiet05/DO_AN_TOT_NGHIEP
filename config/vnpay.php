<?php

return [

    'vnp_tmn_code'   => env('VNPAY_TMN_CODE', ''),

    'vnp_hash_secret' => env('VNPAY_HASH_SECRET', ''),

    'vnp_url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),

    'vnp_return_url' => env('VNPAY_RETURN_URL', 'http://localhost:8000/payment/vnpay/return'),

];

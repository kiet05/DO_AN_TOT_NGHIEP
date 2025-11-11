<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::insert([
            [
                'name' => 'COD',
                'slug' => 'cod',
                'display_name' => 'Thanh toán khi nhận hàng (COD)',
                'description' => 'Thanh toán bằng tiền mặt khi nhận hàng',
                'icon' => null,
                'is_active' => true,
                'sort_order' => 1,
                'config' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VNPay',
                'slug' => 'vnpay',
                'display_name' => 'Thanh toán qua VNPay',
                'description' => 'Thanh toán trực tuyến qua cổng thanh toán VNPay',
                'icon' => null,
                'is_active' => true,
                'sort_order' => 2,
                'config' => json_encode([
                    'merchant_id' => env('VNPAY_TMN_CODE', ''),
                    'secret_key' => env('VNPAY_HASH_SECRET', ''),
                    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
                    'return_url' => env('VNPAY_RETURN_URL', ''),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

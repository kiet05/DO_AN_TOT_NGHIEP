<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chỉ giữ lại 10 voucher quan trọng nhất
        $vouchers = [
            // ========== VOUCHER CHÀO MỪNG ==========
            [
                'code' => 'WELCOME10',
                'name' => 'Chào mừng khách hàng mới - Giảm 10%',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'type' => 'percent',
                'value' => 10,
                'max_discount' => 50000,
                'min_order_value' => 200000,
                'apply_type' => 'all',
                'usage_limit' => 1000,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(6),
                'expired_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'NEWUSER50K',
                'name' => 'Giảm 50.000đ cho khách hàng mới',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'type' => 'fixed',
                'value' => 50000,
                'max_discount' => null,
                'min_order_value' => 300000,
                'apply_type' => 'all',
                'usage_limit' => 500,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(6),
                'expired_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],

            // ========== VOUCHER GIẢM GIÁ PHẦN TRĂM ==========
            [
                'code' => 'SALE15',
                'name' => 'Giảm 15% cho đơn hàng bất kỳ',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'type' => 'percent',
                'value' => 15,
                'max_discount' => 100000,
                'min_order_value' => 500000,
                'apply_type' => 'all',
                'usage_limit' => 2000,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(6),
                'expired_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'VIP20',
                'name' => 'Giảm 20% cho khách hàng VIP',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'type' => 'percent',
                'value' => 20,
                'max_discount' => 200000,
                'min_order_value' => 1000000,
                'apply_type' => 'all',
                'usage_limit' => 500,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(12),
                'expired_at' => Carbon::now()->addMonths(12),
                'is_active' => true,
            ],

            // ========== VOUCHER GIẢM GIÁ CỐ ĐỊNH ==========
            [
                'code' => 'SAVE100K',
                'name' => 'Tiết kiệm 100.000đ',
                'discount_type' => 'fixed',
                'discount_value' => 100000,
                'type' => 'fixed',
                'value' => 100000,
                'max_discount' => null,
                'min_order_value' => 500000,
                'apply_type' => 'all',
                'usage_limit' => 1000,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(6),
                'expired_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'BIG200K',
                'name' => 'Giảm 200.000đ cho đơn hàng lớn',
                'discount_type' => 'fixed',
                'discount_value' => 200000,
                'type' => 'fixed',
                'value' => 200000,
                'max_discount' => null,
                'min_order_value' => 1500000,
                'apply_type' => 'all',
                'usage_limit' => 300,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(6),
                'expired_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],

            // ========== VOUCHER FLASH SALE ==========
            [
                'code' => 'FLASH25',
                'name' => 'Flash Sale - Giảm 25%',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'type' => 'percent',
                'value' => 25,
                'max_discount' => 250000,
                'min_order_value' => 300000,
                'apply_type' => 'all',
                'usage_limit' => 500,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addDays(7),
                'expired_at' => Carbon::now()->addDays(7),
                'is_active' => true,
            ],

            // ========== VOUCHER ĐẶC BIỆT ==========
            [
                'code' => 'FREESHIP',
                'name' => 'Miễn phí vận chuyển',
                'discount_type' => 'fixed',
                'discount_value' => 40000,
                'type' => 'fixed',
                'value' => 40000,
                'max_discount' => null,
                'min_order_value' => 200000,
                'apply_type' => 'all',
                'usage_limit' => 3000,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(6),
                'expired_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'MINI30K',
                'name' => 'Giảm 30.000đ cho đơn hàng nhỏ',
                'discount_type' => 'fixed',
                'discount_value' => 30000,
                'type' => 'fixed',
                'value' => 30000,
                'max_discount' => null,
                'min_order_value' => 150000,
                'apply_type' => 'all',
                'usage_limit' => 5000,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(12),
                'expired_at' => Carbon::now()->addMonths(12),
                'is_active' => true,
            ],

            // ========== VOUCHER CHO SẢN PHẨM/DANH MỤC ==========
            [
                'code' => 'PRODUCT10',
                'name' => 'Giảm 10% cho sản phẩm đặc biệt',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'type' => 'percent',
                'value' => 10,
                'max_discount' => 100000,
                'min_order_value' => 300000,
                'apply_type' => 'products',
                'usage_limit' => 2000,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addMonths(6),
                'expired_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucherData) {
            // Kiểm tra xem voucher đã tồn tại chưa
            $existing = Voucher::where('code', $voucherData['code'])->first();
            if (!$existing) {
                Voucher::create($voucherData);
                $this->command->info("Đã tạo voucher: {$voucherData['code']} - {$voucherData['name']}");
            } else {
                $this->command->warn("Voucher {$voucherData['code']} đã tồn tại, bỏ qua.");
            }
        }

        $this->command->info('Đã tạo thành công ' . count($vouchers) . ' voucher mẫu!');
    }
}

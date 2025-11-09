<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Sửa các tên cột này cho đúng với migration của bạn:
        $rows = [
            [
                'order_id'       => 1,
                'method'         => 'COD',            // hoặc 'gateway'
                'transaction_id' => 'COD123456',      // hoặc 'txn_code' / 'code'
                'amount'         => 150000,
                'status'         => 'success',        // 'pending' | 'success' | 'failed' | 'canceled'
                'paid_at'        => $now->copy()->subDays(2),
                'created_at'     => $now, 'updated_at' => $now,
            ],
            [
                'order_id'       => 2,
                'method'         => 'VNPay',
                'transaction_id' => 'VNP987654',
                'amount'         => 250000,
                'status'         => 'pending',
                'paid_at'        => null,
                'created_at'     => $now, 'updated_at' => $now,
            ],
            [
                'order_id'       => 3,
                'method'         => 'MOMO',
                'transaction_id' => 'MOMO556677',
                'amount'         => 500000,
                'status'         => 'failed',
                'paid_at'        => null,
                'created_at'     => $now, 'updated_at' => $now,
            ],
        ];

        // Lọc key theo cột thực tế trong DB để tránh lỗi tên cột khác nhau
        $cols = array_flip(Schema::getColumnListing('payments'));
        $rows = array_map(fn ($r) => array_intersect_key($r, $cols), $rows);

        DB::table('payments')->insert($rows);
    }
}


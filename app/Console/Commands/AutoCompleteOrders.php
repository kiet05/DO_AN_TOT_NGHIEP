<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class AutoCompleteOrders extends Command
{
    protected $signature = 'orders:auto-complete';
    protected $description = 'Tự động chuyển đơn đã giao sang hoàn thành sau 3 ngày';

    public function handle()
    {
        $orders = Order::where('order_status', 'shipped')
            ->whereNotNull('shipped_at')
            ->where('shipped_at', '<=', Carbon::now()->subDays(3))
            ->get();

        foreach ($orders as $order) {
            $order->update([
                'order_status' => 'completed',
                'completed_at' => now(),
            ]);

            // (Tuỳ chọn) ghi log lịch sử trạng thái
            // OrderStatusHistory::create([...]);
        }

        $this->info('Đã auto complete ' . $orders->count() . ' đơn hàng.');
    }
}

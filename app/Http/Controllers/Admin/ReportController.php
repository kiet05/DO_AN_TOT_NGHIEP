<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Khoảng thời gian: mặc định 30 ngày gần nhất
        $from = $request->date('from') ?: now()->subDays(30)->toDateString();
        $to   = $request->date('to')   ?: now()->toDateString();

        // 1) Doanh thu theo ngày (chỉ tính completed)
        $revenuePerDay = Order::query()
            ->selectRaw('DATE(created_at) as day, SUM(final_amount) as total')
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->where('order_status', 'completed')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Tổng doanh thu & tổng đơn
        $totals = Order::query()
            ->selectRaw("
                SUM(CASE WHEN order_status = 'completed' THEN final_amount ELSE 0 END) AS revenue,
                COUNT(*) AS orders_count
            ")
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->first();

        // 2) Đơn hàng theo trạng thái
        $ordersByStatus = Order::query()
            ->select('order_status', DB::raw('COUNT(*) as cnt'))
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->groupBy('order_status')
            ->pluck('cnt', 'order_status'); // ['pending'=>10, 'shipping'=>... ]

        // 3) Sản phẩm bán chạy (cần bảng order_items)
        $topProducts = collect();
        if (Schema::hasTable('order_items')) {
            $topProducts = DB::table('order_items as oi')
                ->join('products as p', 'p.id', '=', 'oi.product_id')
                ->join('orders as o', 'o.id', '=', 'oi.order_id')
                ->whereBetween('o.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->where('o.order_status', 'completed')
                ->select('p.id','p.name', DB::raw('SUM(oi.quantity) as qty'), DB::raw('SUM(oi.price * oi.quantity) as amount'))
                ->groupBy('p.id','p.name')
                ->orderByDesc('qty')
                ->limit(10)
                ->get();
        }

        // 4) Tồn kho thấp (dựa vào product_variants.quantity)
        $lowStock = collect();
        if (Schema::hasTable('product_variants')) {
            $lowStock = DB::table('product_variants as pv')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->select('p.id','p.name','pv.sku','pv.quantity')
                ->where('pv.quantity', '<=', 5) // ngưỡng cảnh báo
                ->orderBy('pv.quantity')
                ->limit(20)
                ->get();
        }

        // 5) Mã giảm giá dùng nhiều nhất (nếu có)
        $topCoupons = collect();
        if (Schema::hasTable('coupon_usages') && Schema::hasTable('coupons')) {
            $topCoupons = DB::table('coupon_usages as cu')
                ->join('coupons as c', 'c.id', '=', 'cu.coupon_id')
                ->join('orders as o', 'o.id', '=', 'cu.order_id')
                ->whereBetween('o.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->select('c.code', DB::raw('COUNT(*) as used'))
                ->groupBy('c.code')
                ->orderByDesc('used')
                ->limit(10)
                ->get();
        }

        // Dữ liệu cho chart
        $chartLabels = $revenuePerDay->pluck('day');
        $chartData   = $revenuePerDay->pluck('total');

        return view('admin.reports.index', compact(
            'from','to',
            'totals',
            'ordersByStatus',
            'topProducts',
            'lowStock',
            'topCoupons',
            'chartLabels',
            'chartData'
        ));
    }
        public function revenue(\Illuminate\Http\Request $request)
        {
            $period = $request->get('period', 'day'); // day|week|month
            $from   = $request->date('from') ?: now()->subDays(30)->toDateString();
            $to     = $request->date('to')   ?: now()->toDateString();

            $base = \App\Models\Order::query()
                ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->where('order_status', 'completed');

            if ($period === 'week') {
                // group theo tuần ISO: YYYY-WW
                $rows = $base->selectRaw("DATE_FORMAT(created_at, '%x-W%v') as date, SUM(final_amount) as revenue")
                    ->groupBy('date')->orderBy('date')->get();
            } elseif ($period === 'month') {
                // group theo tháng: YYYY-MM
                $rows = $base->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as date, SUM(final_amount) as revenue")
                    ->groupBy('date')->orderBy('date')->get();
            } else {
                // mặc định theo ngày: YYYY-MM-DD
                $rows = $base->selectRaw("DATE(created_at) as date, SUM(final_amount) as revenue")
                    ->groupBy('date')->orderBy('date')->get();
            }

            // dữ liệu cho chart
            $labels = $rows->pluck('date')->values();
            $data   = $rows->pluck('revenue')->values();

            // bảng dưới chart
            $query = $rows; // để khớp view hiện tại

            return view('admin.reports.revenue', compact('period', 'from', 'to', 'labels', 'data', 'query'));
        }


}

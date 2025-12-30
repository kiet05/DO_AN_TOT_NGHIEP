<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // ===== 1. Khoảng thời gian =====
        $from = $request->filled('from')
            ? $request->date('from')->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->filled('to')
            ? $request->date('to')->endOfDay()
            : now()->endOfDay();

        // base query dùng lại nhiều lần
        $baseOrders = Order::query()
            ->whereBetween('created_at', [$from, $to]);

        // ===== 2. Doanh thu theo ngày (chart) =====
        $revenuePerDay = (clone $baseOrders)
            ->selectRaw('DATE(created_at) as day, SUM(final_amount) as total')
            ->where('order_status', 'completed')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // ===== 3. Tổng quan đơn hàng & doanh thu =====
        $totals = (clone $baseOrders)
            ->selectRaw("
                SUM(CASE WHEN order_status = 'completed' THEN final_amount ELSE 0 END) AS revenue,
                COUNT(*) AS orders_count,
                SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN order_status = 'pending'   THEN 1 ELSE 0 END) AS pending_orders,
                SUM(CASE WHEN order_status = 'shipping'  THEN 1 ELSE 0 END) AS shipping_orders,
                SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
            ")
            ->first();

        // ===== 4. AOV (Average Order Value) =====
        $avgOrderValue = $totals->completed_orders > 0
            ? round($totals->revenue / $totals->completed_orders, 0)
            : 0;

        // ===== 5. So sánh với kỳ trước =====
        $days = $from->diffInDays($to) + 1;
        $prevFrom = (clone $from)->subDays($days);
        $prevTo   = (clone $from)->subDay();

        $prevRevenue = Order::query()
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->where('order_status', 'completed')
            ->sum('final_amount');

        $revenueChangePercent = $prevRevenue > 0
            ? round(($totals->revenue - $prevRevenue) / $prevRevenue * 100, 2)
            : null;

        // ===== 6. Đơn hàng theo trạng thái (cho donut chart + card Đang giao / Chờ xử lý) =====
        $ordersByStatus = (clone $baseOrders)
            ->select('order_status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('order_status')
            ->pluck('cnt', 'order_status');

        // ===== 7. Top sản phẩm bán chạy =====
        $topProducts = collect();
        if (Schema::hasTable('order_items')) {
            $topProducts = DB::table('order_items as oi')
                ->join('products as p', 'p.id', '=', 'oi.product_id')
                ->join('orders as o', 'o.id', '=', 'oi.order_id')
                ->whereBetween('o.created_at', [$from, $to])
                ->where('o.order_status', 'completed')
                ->select(
                    'p.id',
                    'p.name',
                    DB::raw('SUM(oi.quantity) as qty'),
                    DB::raw('SUM(oi.price * oi.quantity) as amount')
                )
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('qty')
                ->limit(10)
                ->get();
        }

        // ===== 8. Sản phẩm tồn kho thấp =====
        $lowStock = collect();
        if (Schema::hasTable('product_variants')) {
            $lowStock = DB::table('product_variants as pv')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->select('p.id', 'p.name', 'pv.sku', 'pv.quantity')
                ->where('pv.quantity', '<=', 5)
                ->orderBy('pv.quantity')
                ->limit(20)
                ->get();
        }

        // ===== 9. Mã giảm giá dùng nhiều =====
        $topCoupons = collect();
        if (Schema::hasTable('coupon_usages') && Schema::hasTable('coupons')) {
            $topCoupons = DB::table('coupon_usages as cu')
                ->join('coupons as c', 'c.id', '=', 'cu.coupon_id')
                ->join('orders as o', 'o.id', '=', 'cu.order_id')
                ->whereBetween('o.created_at', [$from, $to])
                ->select('c.code', DB::raw('COUNT(*) as used'))
                ->groupBy('c.code')
                ->orderByDesc('used')
                ->limit(10)
                ->get();
        }

        // ===== 10. Doanh thu theo phương thức thanh toán =====
        $revenueByPayment = collect();
        if (Schema::hasColumn('orders', 'payment_method')) {
            $revenueByPayment = (clone $baseOrders)
                ->where('order_status', 'completed')
                ->select(
                    'payment_method',
                    DB::raw('SUM(final_amount) as revenue'),
                    DB::raw('COUNT(*) as orders_count')
                )
                ->groupBy('payment_method')
                ->get();
        }

        // ===== 11. Dữ liệu cho chart doanh thu theo ngày =====
        $chartLabels = $revenuePerDay->pluck('day');
        $chartData   = $revenuePerDay->pluck('total');

        // ===== 12. Báo cáo sản phẩm (FULL LIST) =====
        $productReport = collect();
        if (Schema::hasTable('order_items')) {
            $productReport = DB::table('order_items as oi')
                ->join('products as p', 'p.id', '=', 'oi.product_id')
                ->join('orders as o', 'o.id', '=', 'oi.order_id')
                ->whereBetween('o.created_at', [$from, $to])
                ->where('o.order_status', 'completed')
                ->select(
                    'p.id',
                    'p.name',
                    DB::raw('SUM(oi.quantity) as qty'),
                    DB::raw('SUM(oi.quantity * oi.price) as revenue')
                )
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('revenue')
                ->get();

            $totalRevenue = max($totals->revenue, 1);
            $productReport->transform(function ($row) use ($totalRevenue) {
                $row->percent = round($row->revenue / $totalRevenue * 100, 2);
                return $row;
            });
        }

        // ===== 13. Sản phẩm tồn kho cao nhưng bán chậm =====
        $slowMovingProducts = collect();
        if (Schema::hasTable('order_items')) {
            $slowMovingProducts = DB::table('product_variants as pv')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->leftJoin('order_items as oi', 'oi.product_id', '=', 'p.id')
                ->leftJoin('orders as o', function ($join) use ($from, $to) {
                    $join->on('o.id', '=', 'oi.order_id')
                        ->where('o.order_status', 'completed')
                        ->whereBetween('o.created_at', [$from, $to]);
                })
                ->select(
                    'p.id',
                    'p.name',
                    'pv.sku',
                    'pv.quantity',
                    DB::raw('COALESCE(SUM(oi.quantity), 0) as sold_qty')
                )
                ->groupBy('p.id', 'p.name', 'pv.sku', 'pv.quantity')
                ->having('pv.quantity', '>=', 20)
                ->having('sold_qty', '<=', 5)
                ->orderByDesc('pv.quantity')
                ->get();
        }
        // ===== 14. Thời gian xử lý đơn hàng (AVG) =====
        $orderProcessingStats = (clone $baseOrders)
            ->where('order_status', 'completed')
            ->selectRaw('
                COUNT(*) as total_orders,
                AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours_to_complete
            ')
            ->first();

            // ===== 15. Trả dữ liệu sang view =====
        return view('admin.reports.index', compact(
            'from',
            'to',
            'totals',
            'avgOrderValue',
            'revenueChangePercent',
            'ordersByStatus',
            'topProducts',
            'lowStock',

            'revenueByPayment',
            'productReport',
            'slowMovingProducts',
            'topCoupons',
            'orderProcessingStats',
            'chartLabels',
            'chartData'
        ));

    }

    // ========================
    //  BIỂU ĐỒ DOANH THU RIÊNG
    // ========================
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'day'); // day|week|month

        $from = $request->filled('from')
            ? $request->date('from')->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->filled('to')
            ? $request->date('to')->endOfDay()
            : now()->endOfDay();

        $base = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('order_status', 'completed');

        if ($period === 'week') {
            $rows = $base->selectRaw("DATE_FORMAT(created_at, '%x-W%v') as date, SUM(final_amount) as revenue")
                ->groupBy('date')->orderBy('date')->get();
        } elseif ($period === 'month') {
            $rows = $base->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as date, SUM(final_amount) as revenue")
                ->groupBy('date')->orderBy('date')->get();
        } else {
            $rows = $base->selectRaw("DATE(created_at) as date, SUM(final_amount) as revenue")
                ->groupBy('date')->orderBy('date')->get();
        }

        $labels = $rows->pluck('date')->values();
        $data   = $rows->pluck('revenue')->values();
        $query  = $rows;

        return view('admin.reports.revenue', compact(
            'period',
            'from',
            'to',
            'labels',
            'data',
            'query'
        ));
    }
}

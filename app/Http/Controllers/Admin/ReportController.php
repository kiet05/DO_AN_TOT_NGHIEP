<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Trang tổng hợp
    public function index()
    {
        return view('admin.reports.index');
    }

    // 📈 Thống kê doanh thu theo ngày/tuần/tháng
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'day');

        $query = DB::table('orders')
            ->select(
                DB::raw('SUM(total) as revenue'),
                DB::raw('DATE(created_at) as date')
            )
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $labels = $query->pluck('date');
        $data = $query->pluck('revenue');

        return view('admin.reports.revenue', compact('query', 'labels', 'data', 'period'));
    }

    // 🛒 Thống kê sản phẩm bán chạy
    public function topProducts()
    {
        $products = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        return view('admin.reports.top_products', compact('products'));
    }

    // 👥 Thống kê khách hàng mua nhiều nhất
    public function topCustomers()
    {
        $customers = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'customers.name',
                'customers.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent')
            )
            ->where('orders.status', 'completed')
            ->groupBy('customers.id', 'customers.name', 'customers.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return view('admin.reports.top_customers', compact('customers'));
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy banners active
        $banners = Banner::where('status', true)
            ->orderBy('position', 'asc')
            ->get();

        // Lấy danh mục chính
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with('children')
            ->get();

        // Sản phẩm mới
        $newProducts = Product::where('is_new', true)
            ->where('status', 1)
            ->with(['category', 'images'])
            ->latest()
            ->limit(8)
            ->get();

        // Sản phẩm khuyến mãi
        $saleProducts = Product::where('is_on_sale', true)
            ->where('status', 1)
            ->with(['category', 'images'])
            ->latest()
            ->limit(12)
            ->get();

        // Sản phẩm nổi bật (có thể thêm logic riêng)
        $featuredProducts = Product::where('status', 1)
            ->with(['category', 'images'])
            ->inRandomOrder()
            ->limit(12)
            ->get();

        return view('frontend.home', compact('banners', 'categories', 'newProducts', 'saleProducts', 'featuredProducts'));
    }

}


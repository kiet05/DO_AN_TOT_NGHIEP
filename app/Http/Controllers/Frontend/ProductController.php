<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 1)
            ->with(['category', 'images', 'variants']);

        // Filter theo category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter theo search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter theo sale
        if ($request->has('sale') && $request->sale) {
            $query->where('is_on_sale', true);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with('children')
            ->get();

        return view('frontend.products.index', compact('products', 'categories'));
    }

    public function show($id)
{
    $product = Product::findOrFail($id);

    $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->where('status', 1)
            ->latest()
            ->get();

        $avgRating = $reviews->avg('rating') ?? 0;
        $reviewsCount = $reviews->count();


    // Load product 1 lần với các relation cần thiết
    $product = Product::with([
        'category',
        'images',
        'variants.attributes.attribute' // variants -> attribute_values -> attribute (Size/Color/...)
    ])->where('status', 1)->findOrFail($id);

    // Sản phẩm liên quan (cùng category)
    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('status', 1)
        ->with(['category', 'images'])
        ->limit(8)
        ->get();

    // Tổng tồn kho tính từ variants (đã eager-load nên dùng collection sum)
    $totalStock = $product->variants->sum('quantity');

    return view('frontend.products.show', compact('product', 'reviews', 'avgRating', 'reviewsCount', 'relatedProducts', 'totalStock'));
}

}



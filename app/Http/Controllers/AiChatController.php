<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiChatController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->message;

        // ====================================
        // 1. TÁCH GIÁ TỪ CÂU HỎI (nếu có)
        // ====================================
        preg_match('/(\d{3,6})k/i', $message, $matches);
        $minPrice = null;
        $maxPrice = null;

        if (!empty($matches)) {
            $price = (int)$matches[1] * 1000; // "350k" → 350000đ
            $minPrice = $price - 50000;
            $maxPrice = $price + 50000;
        }

        // ====================================
        // 2. QUERY SẢN PHẨM TỪ DB
        // ====================================
        $query = Product::query()
            ->where('status', 1)          // Sản phẩm đang hoạt động
            ->whereNull('deleted_at');    // Không lấy sp bị soft delete

        if ($minPrice && $maxPrice) {
            $query->whereBetween('base_price', [$minPrice, $maxPrice]);
        }

        // Lấy tối đa 10 sản phẩm phù hợp
        $products = $query
            ->with(['category:id,name', 'brand:id,name'])
            ->take(10)
            ->get(['id', 'name', 'description', 'base_price', 'category_id', 'brand_id']);

        // ============================
        // FORMAT GỬI SANG OPENAI
        // ============================
        if ($products->isNotEmpty()) {
            $context = $products->map(function ($p) {
                return "
Tên: {$p->name}
Giá: {$p->base_price} VND
Danh mục: {$p->category->name}
Thương hiệu: {$p->brand->name}
Mô tả: {$p->description}
                ";
            })->implode("\n----------------\n");
        } else {
            $context = "Không tìm thấy sản phẩm nào trong cửa hàng phù hợp với yêu cầu.";
        }

        // ====================================
        // 3. GỌI OPENAI
        // ====================================
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post("https://api.openai.com/v1/chat/completions", [
                "model" => "gpt-4o-mini",
                "messages" => [
                    [
                        "role" => "system",
                        "content" =>
                            "Bạn là trợ lý tư vấn sản phẩm của shop.
                             Bạn CHỈ được tư vấn dựa trên danh sách sản phẩm dưới đây.
                             Nếu không tìm thấy sản phẩm phù hợp, hãy trả lời lịch sự rằng shop không có sản phẩm theo yêu cầu."
                    ],
                    [
                        "role" => "system",
                        "content" => "Danh sách sản phẩm:\n" . $context
                    ],
                    [
                        "role" => "user",
                        "content" => $message
                    ],
                ],
            ]);

        $answer = $response->json('choices.0.message.content');

        return response()->json([
            "answer" => $answer,
            "debug_products" => $products, // m muốn t bỏ thì bảo t
        ]);
    }
}

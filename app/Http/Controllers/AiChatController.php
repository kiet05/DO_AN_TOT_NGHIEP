<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    //     public function chat(Request $request)
    //     {
    //         $message = trim((string) $request->input('message', ''));

    //         if ($message === '') {
    //             return response()->json([
    //                 'answer' => 'Bạn vui lòng nhập nội dung cần tư vấn nhé.',
    //             ], 400);
    //         }

    //         /**
    //          * 1. TÁCH GIÁ (VD: 300k, 500k)
    //          */
    //         preg_match('/(\d{3,6})k/i', $message, $matches);

    //         $minPrice = null;
    //         $maxPrice = null;

    //         if (!empty($matches)) {
    //             $price = ((int) $matches[1]) * 1000;
    //             $minPrice = max(0, $price - 50000);
    //             $maxPrice = $price + 50000;
    //         }

    //         /**
    //          * 2. QUERY SẢN PHẨM
    //          */
    //         $query = Product::query()
    //             ->where('status', 1)
    //             ->whereNull('deleted_at');

    //         if ($minPrice !== null && $maxPrice !== null) {
    //             $query->whereBetween('base_price', [$minPrice, $maxPrice]);
    //         }

    //         $products = $query
    //             ->with(['category:id,name', 'brand:id,name'])
    //             ->take(10)
    //             ->get(['id', 'name', 'description', 'base_price', 'category_id', 'brand_id']);

    //         /**
    //          * 3. TẠO CONTEXT
    //          */
    //         if ($products->isEmpty()) {
    //             $context = "Hiện không có sản phẩm phù hợp trong cửa hàng.";
    //         } else {
    //             $context = $products->map(function ($p) {
    //                 return
    //                     "Tên: {$p->name}\n" .
    //                     "Giá: {$p->base_price} VND\n" .
    //                     "Danh mục: " . optional($p->category)->name . "\n" .
    //                     "Thương hiệu: " . optional($p->brand)->name . "\n" .
    //                     "Mô tả: {$p->description}";
    //             })->implode("\n----------------\n");
    //         }

    //         /**
    //          * 4. GỌI OPENAI (RESPONSES API)
    //          */
    //         try {
    //             $apiKey = config('services.openai.key');

    //             if (!$apiKey) {
    //                 return response()->json([
    //                     'answer' => 'Thiếu OPENAI_API_KEY trong hệ thống.',
    //                 ], 500);
    //             }

    //             $response = Http::withHeaders([
    //                 'Authorization' => 'Bearer ' . $apiKey,
    //                 'Content-Type'  => 'application/json',
    //             ])
    //                 ->timeout(30)
    //                 ->post('https://api.openai.com/v1/responses', [
    //                     'model' => 'gpt-4.1-mini',
    //                     'input' => [
    //                         [
    //                             'role' => 'system',
    //                             'content' => [
    //                                 [
    //                                     'type' => 'text',
    //                                     'text' =>
    //                                         "Bạn là trợ lý tư vấn sản phẩm cho shop thời trang nam.
    // Chỉ tư vấn dựa trên danh sách sản phẩm bên dưới.
    // Gợi ý 3–5 sản phẩm, nêu tên, giá và lý do."
    //                                 ]
    //                             ]
    //                         ],
    //                         [
    //                             'role' => 'system',
    //                             'content' => [
    //                                 [
    //                                     'type' => 'text',
    //                                     'text' => "DANH SÁCH SẢN PHẨM:\n" . $context
    //                                 ]
    //                             ]
    //                         ],
    //                         [
    //                             'role' => 'user',
    //                             'content' => [
    //                                 [
    //                                     'type' => 'text',
    //                                     'text' => $message
    //                                 ]
    //                             ]
    //                         ],
    //                     ],
    //                 ]);

    //             /**
    //              * 5. XỬ LÝ LỖI OPENAI
    //              */
    //             if ($response->failed()) {
    //                 Log::error('OPENAI FAILED', [
    //                     'status' => $response->status(),
    //                     'body'   => $response->body(),
    //                 ]);

    //                 return response()->json([
    //                     'answer' => 'Hệ thống tư vấn AI đang gặp sự cố. Vui lòng thử lại sau.',
    //                 ], 500);
    //             }

    //             /**
    //              * 6. PARSE OUTPUT (ĐÚNG RESPONSES API)
    //              */
    //             $json = $response->json();

    //             $answer = collect($json['output'] ?? [])
    //                 ->flatMap(fn ($item) => $item['content'] ?? [])
    //                 ->firstWhere('type', 'output_text')['text']
    //                 ?? 'Xin lỗi, mình chưa tìm được sản phẩm phù hợp.';

    //             return response()->json([
    //                 'answer' => $answer,
    //             ]);
    //         } catch (\Throwable $e) {
    //             Log::error('OPENAI EXCEPTION', [
    //                 'message' => $e->getMessage(),
    //             ]);

    //             return response()->json([
    //                 'answer' => 'Hệ thống AI đang gặp lỗi. Vui lòng thử lại sau.',
    //             ], 500);
    //         }
    //     }
    public function chat(Request $request)
    {
        // 0️⃣ CHÀO Ở CÂU ĐẦU TIÊN
        if (!session()->has('ai_greeted')) {
            session(['ai_greeted' => true]);

            return response()->json([
                'answer' =>
                "👋 **EGA Shop mua sắm quần áo xin kính chào quý khách!**\n\n" .
                    "🌞 Chúc quý khách một buổi tốt lành.\n\n" .
                    "Quý khách vui lòng nhập nội dung cần tư vấn nhé.?"
            ]);
        }
        $message = mb_strtolower(trim((string) $request->input('message', '')));

        if ($message === '') {
            return response()->json([
                'answer' => 'Bạn vui lòng nhập nội dung cần tư vấn nhé.',
            ], 400);
        }

        /**
         * =========================
         * 1️⃣ TÁCH GIÁ
         * =========================
         */
        $minPrice = null;
        $maxPrice = null;

        // VD: 300k
        if (preg_match('/(\d{2,4})\s?k/i', $message, $m)) {
            $price = (int) $m[1] * 1000;
            $minPrice = max(0, $price - 50000);
            $maxPrice = $price + 50000;
        }

        // VD: dưới 500k
        if (preg_match('/dưới\s?(\d{2,4})\s?k/i', $message, $m)) {
            $maxPrice = (int) $m[1] * 1000;
        }

        // VD: từ 200k đến 400k
        if (preg_match('/(\d{2,4})k\s?-\s?(\d{2,4})k/i', $message, $m)) {
            $minPrice = (int) $m[1] * 1000;
            $maxPrice = (int) $m[2] * 1000;
        }
        /**
 * 1️⃣ DƯỚI 500K
 * VD: dưới 500k
 */
if (preg_match('/dưới\s?(\d{2,4})\s?k/i', $message, $m)) {
    $maxPrice = (int) $m[1] * 1000;
}

/**
 * 2️⃣ TRÊN 500K / TỪ 500K TRỞ LÊN
 * VD: trên 500k, từ 500k trở lên
 */
if (preg_match('/(trên|từ)\s?(\d{2,4})\s?k/i', $message, $m)) {
    $minPrice = (int) $m[2] * 1000;
}

/**
 * 3️⃣ KHOẢNG GIÁ
 * VD: 300k - 500k
 */
if (preg_match('/(\d{2,4})k\s?-\s?(\d{2,4})k/i', $message, $m)) {
    $minPrice = (int) $m[1] * 1000;
    $maxPrice = (int) $m[2] * 1000;
}

/**
 * 4️⃣ GIÁ CỤ THỂ (300k)
 */
if ($minPrice === null && $maxPrice === null &&
    preg_match('/(\d{2,4})\s?k/i', $message, $m)
) {
    $price = (int) $m[1] * 1000;
    $minPrice = max(0, $price - 50000);
    $maxPrice = $price + 50000;
}

        /**
         * =========================
         * 2️⃣ NHẬN DIỆN LOẠI ÁO
         * =========================
         */
        $type = null;

        if (str_contains($message, 'polo')) {
            $type = 'polo';
        }

        if (str_contains($message, 'áo thun') || str_contains($message, 'thun')) {
            $type = 'thun';
        }

        /**
         * =========================
         * 3️⃣ QUERY DATABASE
         * =========================
         */
        $query = Product::query()
            ->where('status', 1)
            ->whereNull('deleted_at');

        // Lọc theo loại áo
        if ($type === 'polo') {
            $query->where('name', 'like', '%polo%');
        }

        if ($type === 'thun') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%áo thun%')
                    ->orWhere('name', 'like', '%thun%');
            });
        }

        // Lọc theo giá
        if ($minPrice !== null) {
            $query->where('base_price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('base_price', '<=', $maxPrice);
        }

        $products = $query
            ->orderBy('base_price')
            ->take(5)
            ->get(['id', 'name', 'base_price']);

        /**
         * =========================
         * 4️⃣ SINH CÂU TRẢ LỜI
         * =========================
         */
        if ($products->isEmpty()) {
            return response()->json([
                'answer' => 'Hiện shop chưa có áo thun hoặc polo phù hợp với mức giá bạn yêu cầu 😥',
            ]);
        }

        $typeText = match ($type) {
            'polo' => 'áo polo',
            'thun' => 'áo thun',
            default => 'sản phẩm',
        };

        $answer = "Mình gợi sẽ gợi ý cho bạn {$products->count()} {$typeText} phù hợp nhất 👇\n\n";

        foreach ($products as $p) {
            $answer .= "🔹 **{$p->name}**\n";
            $answer .= "💰 Giá: " . number_format($p->base_price) . "đ\n";
            $link = url("/products/{$p->id}");

            $answer .= "👉 <a href='{$link}' target='_blank'>Xem trực tiếp sản phẩm</a><br><br>";
        }

        $answer .= "Bạn cần mình lọc thêm theo size, màu sắc hay form áo không 😊?";

        return response()->json([
            'answer' => $answer,
        ]);
    }
}

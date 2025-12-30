<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function chat(Request $request)
    {
        $message = trim((string) $request->input('message', ''));

        if ($message === '') {
            return response()->json([
                'answer' => 'Bạn vui lòng nhập nội dung cần tư vấn nhé.',
            ], 400);
        }

        /**
         * 1. TÁCH GIÁ (VD: 300k, 500k)
         */
        preg_match('/(\d{3,6})k/i', $message, $matches);

        $minPrice = null;
        $maxPrice = null;

        if (!empty($matches)) {
            $price = ((int) $matches[1]) * 1000;
            $minPrice = max(0, $price - 50000);
            $maxPrice = $price + 50000;
        }

        /**
         * 2. QUERY SẢN PHẨM
         */
        $query = Product::query()
            ->where('status', 1)
            ->whereNull('deleted_at');

        if ($minPrice !== null && $maxPrice !== null) {
            $query->whereBetween('base_price', [$minPrice, $maxPrice]);
        }

        $products = $query
            ->with(['category:id,name', 'brand:id,name'])
            ->take(10)
            ->get(['id', 'name', 'description', 'base_price', 'category_id', 'brand_id']);

        /**
         * 3. TẠO CONTEXT
         */
        if ($products->isEmpty()) {
            $context = "Hiện không có sản phẩm phù hợp trong cửa hàng.";
        } else {
            $context = $products->map(function ($p) {
                return
                    "Tên: {$p->name}\n" .
                    "Giá: {$p->base_price} VND\n" .
                    "Danh mục: " . optional($p->category)->name . "\n" .
                    "Thương hiệu: " . optional($p->brand)->name . "\n" .
                    "Mô tả: {$p->description}";
            })->implode("\n----------------\n");
        }

        /**
         * 4. GỌI OPENAI (RESPONSES API)
         */
        try {
            $apiKey = config('services.openai.key');

            if (!$apiKey) {
                return response()->json([
                    'answer' => 'Thiếu OPENAI_API_KEY trong hệ thống.',
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])
                ->timeout(30)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => 'gpt-4.1-mini',
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' =>
                                        "Bạn là trợ lý tư vấn sản phẩm cho shop thời trang nam.
Chỉ tư vấn dựa trên danh sách sản phẩm bên dưới.
Gợi ý 3–5 sản phẩm, nêu tên, giá và lý do."
                                ]
                            ]
                        ],
                        [
                            'role' => 'system',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => "DANH SÁCH SẢN PHẨM:\n" . $context
                                ]
                            ]
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $message
                                ]
                            ]
                        ],
                    ],
                ]);

            /**
             * 5. XỬ LÝ LỖI OPENAI
             */
            if ($response->failed()) {
                Log::error('OPENAI FAILED', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return response()->json([
                    'answer' => 'Hệ thống tư vấn AI đang gặp sự cố. Vui lòng thử lại sau.',
                ], 500);
            }

            /**
             * 6. PARSE OUTPUT (ĐÚNG RESPONSES API)
             */
            $json = $response->json();

            $answer = collect($json['output'] ?? [])
                ->flatMap(fn ($item) => $item['content'] ?? [])
                ->firstWhere('type', 'output_text')['text']
                ?? 'Xin lỗi, mình chưa tìm được sản phẩm phù hợp.';

            return response()->json([
                'answer' => $answer,
            ]);
        } catch (\Throwable $e) {
            Log::error('OPENAI EXCEPTION', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'answer' => 'Hệ thống AI đang gặp lỗi. Vui lòng thử lại sau.',
            ], 500);
        }
    }
    
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

        // ====================================
        // 1. TÁCH GIÁ TỪ CÂU HỎI (nếu có dạng 300k, 450k...)
        // ====================================
        preg_match('/(\d{3,6})k/i', $message, $matches);
        $minPrice = null;
        $maxPrice = null;

        if (!empty($matches)) {
            $price    = (int) $matches[1] * 1000; // "350k" → 350000đ
            $minPrice = max(0, $price - 50000);
            $maxPrice = $price + 50000;
        }

        // ====================================
        // 2. QUERY SẢN PHẨM TỪ DB
        // ====================================
        $query = Product::query()
            ->where('status', 1)          // Sản phẩm đang hoạt động
            ->whereNull('deleted_at');    // Không lấy sp bị soft delete

        if ($minPrice !== null && $maxPrice !== null) {
            $query->whereBetween('base_price', [$minPrice, $maxPrice]);
        }

        // Lấy tối đa 10 sản phẩm phù hợp
        $products = $query
            ->with(['category:id,name', 'brand:id,name'])
            ->take(10)
            ->get(['id', 'name', 'description', 'base_price', 'category_id', 'brand_id']);

        // ============================
        // 3. TẠO CONTEXT GỬI SANG OPENAI
        // ============================
        if ($products->isNotEmpty()) {
            $context = $products->map(function ($p) {
                $categoryName = optional($p->category)->name ?? 'Không có';
                $brandName    = optional($p->brand)->name ?? 'Không có';

                return <<<TXT
Tên: {$p->name}
Giá: {$p->base_price} VND
Danh mục: {$categoryName}
Thương hiệu: {$brandName}
Mô tả: {$p->description}
TXT;
            })->implode("\n----------------\n");
        } else {
            $context = "Không tìm thấy sản phẩm nào trong cửa hàng phù hợp với yêu cầu.";
        }

        // ====================================
        // 4. GỌI OPENAI + XỬ LÝ LỖI RÕ RÀNG
        // ====================================
        try {
            $apiKey = env('OPENAI_API_KEY');

            if (!$apiKey) {
                return response()->json([
                    'answer' => 'Hệ thống chưa cấu hình OPENAI_API_KEY. Vui lòng kiểm tra file .env.',
                    'debug'  => ['env_OPENAI_API_KEY' => $apiKey],
                ], 500);
            }

            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'    => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role'    => 'system',
                            'content' =>
                                "Bạn là trợ lý tư vấn sản phẩm của shop thời trang nam.
                                 Bạn CHỈ được tư vấn dựa trên danh sách sản phẩm dưới đây.
                                 Ưu tiên gợi ý 3–5 sản phẩm phù hợp, nêu rõ tên, mức giá và lý do gợi ý.
                                 Nếu không tìm thấy sản phẩm phù hợp, hãy trả lời lịch sự rằng shop không có sản phẩm theo yêu cầu."
                        ],
                        [
                            'role'    => 'system',
                            'content' => "Danh sách sản phẩm:\n" . $context,
                        ],
                        [
                            'role'    => 'user',
                            'content' => $message,
                        ],
                    ],
                ]);

            if ($response->failed()) {
                $status  = $response->status();
                $body    = $response->json();
                $errMsg  = $body['error']['message'] ?? 'Không rõ nguyên nhân';

                // Thông điệp thân thiện cho khách
                if ($status === 429) {
                    $userMsg = 'Hiện tại hệ thống tư vấn AI đang tạm quá tải hoặc vượt giới hạn sử dụng. '
                             . 'Bạn vui lòng thử lại sau ít phút nhé.';
                } elseif ($status === 401) {
                    $userMsg = 'Hệ thống chưa được phép kết nối tới OpenAI (401 Unauthorized). '
                             . 'Bạn vui lòng báo lại cho quản trị viên để kiểm tra API key.';
                } else {
                    $userMsg = 'Xin lỗi, hiện tại hệ thống tư vấn đang gặp lỗi. Bạn vui lòng thử lại sau ít phút nhé.';
                }

                return response()->json([
                    'answer' => $userMsg,
                    // debug để dev xem khi mở tab Response
                    'debug'  => [
                        'status' => $status,
                        'body'   => $body,
                        'raw'    => $response->body(),
                    ],
                ], 500);
            }

            $answer = $response->json('choices.0.message.content');

            return response()->json([
                'answer'         => $answer,
                'debug_products' => $products, // nếu không muốn trả thì xóa dòng này
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'answer'          => 'Laravel gặp lỗi khi gọi OpenAI. Bạn vui lòng thử lại sau ít phút nhé.',
                'debug_exception' => $e->getMessage(),
            ], 500);
        }
    }
}

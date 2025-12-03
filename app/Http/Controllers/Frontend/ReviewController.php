<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\OrderItem; // nhớ import thêm model này
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Tìm 1 dòng OrderItem mà user hiện tại đã mua sản phẩm này
        $orderItem = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->first();

        // Nếu chưa từng mua, không cho đánh giá
        if (! $orderItem) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể đánh giá những sản phẩm đã mua.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        Review::create([
            'user_id'       => Auth::id(),
            'product_id'    => $product->id,
            'order_id'      => $orderItem->order_id,
            'order_item_id' => $orderItem->id,
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'],
            'image'         => $imagePath,
            // Lưu trạng thái = 0 (chờ duyệt) — admin sẽ duyệt sau
            'status'        => 0,
        ]);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm, đánh giá đang chờ được duyệt!');
    }
}

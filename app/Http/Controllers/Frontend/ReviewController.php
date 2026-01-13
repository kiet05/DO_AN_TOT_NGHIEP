<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        if (!$request->expectsJson()) {
            abort(404);
        }

        $validated = $request->validate([
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'required|string',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'order_item_id' => 'required|exists:order_items,id',
        ]);

        $product = Product::findOrFail($productId);

        // Kiểm tra quyền sở hữu
        $orderItem = OrderItem::where('id', $validated['order_item_id'])
            ->where('product_id', $product->id)
            ->whereHas('order', fn($q) => $q->where('user_id', Auth::id()))
            ->first();

        if (!$orderItem) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua.'
            ], 403);
        }

        // Đã đánh giá chưa
        if (Review::where('user_id', Auth::id())
            ->where('order_item_id', $orderItem->id)
            ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm này đã được đánh giá.'
            ], 422);
        }

        // Upload ảnh
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('reviews', 'public')
            : null;

        Review::create([
            'user_id'       => Auth::id(),
            'product_id'    => $product->id,
            'order_id'      => $orderItem->order_id,
            'order_item_id' => $orderItem->id,
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'],
            'image'         => $imagePath,
            'status'        => 0,
        ]);

        // Kiểm tra đã đánh giá hết đơn
        $order = $orderItem->order;

        $totalItems = $order->items()->count();
        $reviewedItems = Review::where('user_id', Auth::id())
            ->whereIn('order_item_id', $order->items->pluck('id'))
            ->count();

        if ($totalItems === $reviewedItems) {
            $order->update(['is_reviewed' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã đánh giá! Đánh giá đang chờ duyệt.'
        ]);
    }
}

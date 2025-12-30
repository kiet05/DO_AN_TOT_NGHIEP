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

    $orderItem = OrderItem::where('product_id', $product->id)
        ->whereHas('order', function ($q) {
            $q->where('user_id', Auth::id());
        })
        ->latest()
        ->first();

    if (! $orderItem) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn chỉ có thể đánh giá những sản phẩm đã mua.'
        ], 403);
    }

    $imagePath = $request->hasFile('image') ? $request->file('image')->store('reviews', 'public') : null;

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

    return response()->json([
        'success' => true,
        'message' => 'Cảm ơn bạn đã đánh giá sản phẩm, đánh giá đang chờ được duyệt!'
    ]);
}

}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng');
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product.category', 'items.productVariant.product.images', 'items.productVariant.attributeValues', 'voucher'])
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 1,
                'total_price' => 0,
            ]);
        }

        // Tính lại tổng tiền
        $cart->calculateTotal();

        // Lấy sản phẩm tương tự (dựa trên category của các sản phẩm trong giỏ)
        $similarProducts = $this->getSimilarProducts($cart);

        return view('frontend.cart.index', compact('cart', 'similarProducts'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng'
            ], 401);
        }

        $variant = ProductVariant::with('product')->findOrFail($request->product_variant_id);

        // Kiểm tra số lượng tồn kho
        if ($variant->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng sản phẩm không đủ. Còn lại: ' . $variant->quantity . ' sản phẩm'
            ], 400);
        }

        // Kiểm tra trạng thái sản phẩm
        if ($variant->status != 1 || $variant->product->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm hiện không khả dụng'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Lấy hoặc tạo giỏ hàng
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'status' => 1],
                ['total_price' => 0]
            );

            // Kiểm tra sản phẩm đã có trong giỏ chưa
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_variant_id', $request->product_variant_id)
                ->first();

            if ($cartItem) {
                // Cập nhật số lượng
                $newQuantity = $cartItem->quantity + $request->quantity;
                
                if ($variant->quantity < $newQuantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Số lượng vượt quá tồn kho. Tối đa: ' . $variant->quantity . ' sản phẩm'
                    ], 400);
                }

                $cartItem->quantity = $newQuantity;
                $cartItem->calculateSubtotal();
            } else {
                // Tạo mới cart item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_variant_id' => $request->product_variant_id,
                    'quantity' => $request->quantity,
                    'price_at_time' => $variant->price,
                    'subtotal' => $request->quantity * $variant->price,
                ]);
            }

            // Tính lại tổng tiền
            $cart->calculateTotal();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'cart_count' => $cart->items()->sum('quantity'),
                'cart_total' => number_format($cart->total_price, 0, ',', '.') . ' đ'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cart add error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $cartItem = CartItem::with(['cart', 'productVariant'])
            ->whereHas('cart', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);

        // Kiểm tra số lượng tồn kho
        if ($cartItem->productVariant->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng không đủ. Còn lại: ' . $cartItem->productVariant->quantity . ' sản phẩm',
                'max_quantity' => $cartItem->productVariant->quantity
            ], 400);
        }

        DB::beginTransaction();
        try {
            $cartItem->quantity = $request->quantity;
            $cartItem->calculateSubtotal();
            
            $cart = $cartItem->cart;
            $cart->calculateTotal();

            DB::commit();

            $cart->refresh();
            $finalTotal = $cart->total_price - ($cart->discount_amount ?? 0);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng',
                'subtotal' => number_format($cartItem->subtotal, 0, ',', '.') . ' đ',
                'cart_total' => number_format($cart->total_price, 0, ',', '.') . ' đ',
                'cart_count' => $cart->items()->sum('quantity'),
                'discount_amount' => number_format($cart->discount_amount ?? 0, 0, ',', '.') . ' đ',
                'final_total' => number_format($finalTotal, 0, ',', '.') . ' đ'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cart update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'item_id' => $id,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật số lượng. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $cartItem = CartItem::with('cart')
            ->whereHas('cart', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            $cart = $cartItem->cart;
            $cartItem->delete();
            
            $cart->calculateTotal();

            DB::commit();

            $cart->refresh();
            $finalTotal = $cart->total_price - ($cart->discount_amount ?? 0);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                'cart_total' => number_format($cart->total_price, 0, ',', '.') . ' đ',
                'cart_count' => $cart->items()->sum('quantity'),
                'discount_amount' => number_format($cart->discount_amount ?? 0, 0, ',', '.') . ' đ',
                'final_total' => number_format($finalTotal, 0, ',', '.') . ' đ'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cart remove error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'item_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng
     */
    public function getCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with('items')
            ->first();

        $count = $cart ? $cart->items()->sum('quantity') : 0;

        return response()->json(['count' => $count]);
    }

    /**
     * Lấy nội dung giỏ hàng cho sidebar (mini cart)
     */
    public function sidebar()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'html' => '<p class="text-center text-muted">Vui lòng <a href="' . route('login') . '">đăng nhập</a> để xem giỏ hàng</p>'
            ]);
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product.images', 'items.productVariant.attributeValues'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'html' => '<p class="text-center text-muted">Chưa có sản phẩm trong giỏ hàng</p>'
            ]);
        }

        $html = '<div class="cart-sidebar-items" style="max-height: 400px; overflow-y: auto;">';
        foreach ($cart->items->take(5) as $item) {
            $product = $item->productVariant->product;
            $mainImage = $product->image_main 
                ? asset('storage/' . $product->image_main) 
                : ($product->images->first() 
                    ? asset('storage/' . $product->images->first()->image_path) 
                    : asset('img/no-image.png'));
            
            $html .= '<div class="d-flex align-items-center mb-3 pb-3 border-bottom">';
            $html .= '<img src="' . $mainImage . '" alt="' . $product->name . '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; margin-right: 10px;">';
            $html .= '<div class="flex-grow-1">';
            $html .= '<h6 class="mb-1" style="font-size: 14px;">' . $product->name . '</h6>';
            $html .= '<p class="mb-0" style="font-size: 12px; color: #666;">Số lượng: ' . $item->quantity . '</p>';
            $html .= '<p class="mb-0" style="font-size: 14px; font-weight: 600; color: var(--secondary-color);">' . number_format($item->subtotal, 0, ',', '.') . '₫</p>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        if ($cart->items->count() > 5) {
            $html .= '<p class="text-center text-muted" style="font-size: 12px;">Và ' . ($cart->items->count() - 5) . ' sản phẩm khác...</p>';
        }
        
        $html .= '</div>';
        $html .= '<div class="mt-3 pt-3 border-top">';
        $html .= '<div class="d-flex justify-content-between mb-2">';
        $html .= '<strong>Tổng cộng:</strong>';
        $html .= '<strong style="color: var(--secondary-color);">' . number_format($cart->total_price, 0, ',', '.') . '₫</strong>';
        $html .= '</div>';
        $html .= '<a href="' . route('cart.index') . '" class="btn btn-primary w-100 btn-sm">Xem giỏ hàng</a>';
        $html .= '</div>';

        return response()->json(['html' => $html]);
    }

    /**
     * Lấy sản phẩm tương tự dựa trên category
     */
    private function getSimilarProducts($cart)
    {
        if ($cart->items->isEmpty()) {
            return collect([]);
        }

        // Lấy các category_id từ sản phẩm trong giỏ
        $categoryIds = $cart->items->map(function($item) {
            return $item->productVariant->product->category_id;
        })->unique()->filter()->toArray();

        if (empty($categoryIds)) {
            return collect([]);
        }

        // Lấy các product_id đã có trong giỏ để loại trừ
        $productIdsInCart = $cart->items->map(function($item) {
            return $item->productVariant->product_id;
        })->unique()->toArray();

        // Lấy sản phẩm tương tự (cùng category, chưa có trong giỏ, còn hàng)
        $similarProducts = Product::whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $productIdsInCart)
            ->where('status', 1)
            ->whereHas('variants', function($query) {
                $query->where('quantity', '>', 0)
                      ->where('status', 1);
            })
            ->with(['variants' => function($query) {
                $query->where('quantity', '>', 0)
                      ->where('status', 1)
                      ->orderBy('price', 'asc')
                      ->limit(1);
            }, 'images'])
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return $similarProducts;
    }

    /**
     * Áp dụng mã giảm giá
     */
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string|max:50',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product', 'voucher'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng đang trống'
            ], 400);
        }

        // Tìm voucher theo code
        $voucher = Voucher::where('code', $request->voucher_code)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã bị vô hiệu hóa'
            ], 404);
        }

        // Kiểm tra thời gian hiệu lực
        $now = now();
        if ($voucher->start_at && $voucher->start_at->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá chưa có hiệu lực'
            ], 400);
        }

        if ($voucher->end_at && $voucher->end_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn'
            ], 400);
        }

        // Kiểm tra giới hạn sử dụng
        $usageCount = VoucherUsage::where('voucher_id', $voucher->id)
            ->where('user_id', $user->id)
            ->count();

        if ($voucher->usage_limit && $usageCount >= $voucher->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã sử dụng hết số lần áp dụng mã giảm giá này'
            ], 400);
        }

        // Tính tổng tiền giỏ hàng
        $cart->calculateTotal();
        $subtotal = $cart->total_price;

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($voucher->min_order_value && $subtotal < $voucher->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng phải có giá trị tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . '₫'
            ], 400);
        }

        // Kiểm tra áp dụng cho sản phẩm/category
        $canApply = true;
        if ($voucher->apply_type === 'products') {
            $productIds = $voucher->products->pluck('id')->toArray();
            $cartProductIds = $cart->items->map(function($item) {
                return $item->productVariant->product_id;
            })->unique()->toArray();
            
            $canApply = !empty(array_intersect($productIds, $cartProductIds));
        } elseif ($voucher->apply_type === 'categories') {
            $categoryIds = $voucher->categories->pluck('id')->toArray();
            $cartCategoryIds = $cart->items->map(function($item) {
                return $item->productVariant->product->category_id;
            })->unique()->toArray();
            
            $canApply = !empty(array_intersect($categoryIds, $cartCategoryIds));
        }

        if (!$canApply) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không áp dụng cho sản phẩm trong giỏ hàng'
            ], 400);
        }

        // Tính số tiền giảm
        $discountAmount = 0;
        if ($voucher->discount_type === 'percentage') {
            $discountAmount = ($subtotal * $voucher->discount_value) / 100;
            if ($voucher->max_discount && $discountAmount > $voucher->max_discount) {
                $discountAmount = $voucher->max_discount;
            }
        } elseif ($voucher->discount_type === 'fixed') {
            $discountAmount = $voucher->discount_value;
            if ($discountAmount > $subtotal) {
                $discountAmount = $subtotal;
            }
        }

        DB::beginTransaction();
        try {
            $cart->voucher_id = $voucher->id;
            $cart->discount_amount = $discountAmount;
            $cart->save();

            DB::commit();

            $finalTotal = $subtotal - $discountAmount;

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công',
                'voucher' => [
                    'code' => $voucher->code,
                    'name' => $voucher->name,
                    'discount_amount' => number_format($discountAmount, 0, ',', '.') . '₫',
                ],
                'subtotal' => number_format($subtotal, 0, ',', '.') . '₫',
                'discount' => number_format($discountAmount, 0, ',', '.') . '₫',
                'total' => number_format($finalTotal, 0, ',', '.') . '₫',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Apply voucher error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi áp dụng mã giảm giá'
            ], 500);
        }
    }

    /**
     * Xóa mã giảm giá
     */
    public function removeVoucher()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giỏ hàng'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $cart->voucher_id = null;
            $cart->discount_amount = 0;
            $cart->save();

            $cart->calculateTotal();
            $total = $cart->total_price;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa mã giảm giá',
                'total' => number_format($total, 0, ',', '.') . '₫',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Remove voucher error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa mã giảm giá'
            ], 500);
        }
    }

}



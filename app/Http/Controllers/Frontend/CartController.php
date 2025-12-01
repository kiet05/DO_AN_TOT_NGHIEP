<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng
     */
    public function index(VoucherService $voucherService)
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

        // 🔹 Tự động áp dụng voucher tốt nhất nếu:
        //    - Có sản phẩm trong giỏ
        //    - Và người dùng KHÔNG chọn tắt tự động voucher (disable_auto_voucher = false)
        if ($cart->items->count() > 0 && !session('disable_auto_voucher', false)) {
            $this->autoApplyBestVoucher($voucherService);

            // Reload cart để lấy voucher mới
            $cart->refresh();
            $cart->load('voucher');
        }

        // 🔹 Lấy danh sách voucher có thể áp dụng (để hiển thị popup giống Shopee)
        $suggestedVouchers = [];
        if ($cart->items->count() > 0) {
            $suggestedVouchers = $voucherService->getApplicableVouchers($cart, $user->id);
        }

        // Lấy sản phẩm tương tự (dựa trên category của các sản phẩm trong giỏ)
        $similarProducts = $this->getSimilarProducts($cart);

        return view('frontend.cart.index', compact('cart', 'similarProducts', 'suggestedVouchers'));
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
            ->whereHas('cart', function ($query) use ($user) {
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
            $finalTotal = round($cart->total_price - ($cart->discount_amount ?? 0));

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng',
                'subtotal' => number_format($cartItem->subtotal, 0, ',', '.') . ' đ',
                'cart_total' => number_format($cart->total_price, 0, ',', '.') . ' đ',
                'cart_count' => $cart->items()->sum('quantity'),
                'discount_amount' => number_format(round($cart->discount_amount ?? 0), 0, ',', '.') . ' đ',
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
            ->whereHas('cart', function ($query) use ($user) {
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
            $finalTotal = round($cart->total_price - ($cart->discount_amount ?? 0));

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                'cart_total' => number_format($cart->total_price, 0, ',', '.') . ' đ',
                'cart_count' => $cart->items()->sum('quantity'),
                'discount_amount' => number_format(round($cart->discount_amount ?? 0), 0, ',', '.') . ' đ',
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
            ->with([
                'items.productVariant.product.images',
                'items.productVariant.attributeValues.attribute', // load kèm attribute để lấy tên (Color, Size…)
            ])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'html' => '<p class="text-center text-muted py-4">Chưa có sản phẩm trong giỏ hàng</p>'
            ]);
        }

        // Tính lại tổng tiền để đảm bảo chính xác
        $cart->calculateTotal();

        $html = '<div class="cart-sidebar-items" style="max-height: 400px; overflow-y: auto;">';

        foreach ($cart->items->take(5) as $item) {
            $variant = $item->productVariant;
            $product = $variant->product ?? null;

            // ====== CHỌN ẢNH HIỂN THỊ ======
            $imageUrl = null;

            // 1. Ưu tiên ảnh của biến thể
            if ($variant && $variant->image_url) {
                $imageUrl = asset('storage/' . $variant->image_url);
            }
            // 2. Ảnh chính của product
            elseif ($product && $product->image_main) {
                $imageUrl = asset('storage/' . $product->image_main);
            }
            // 3. Ảnh phụ đầu tiên
            elseif ($product && $product->images->first()) {
                $imageUrl = asset('storage/' . $product->images->first()->image_url);
            }
            // 4. Fallback
            else {
                $imageUrl = asset('img/no-image.png');
            }

            $productName = $product->name ?? 'Sản phẩm';

            // ====== GHÉP DÒNG THUỘC TÍNH (Color / Size / Material…) ======
            $variantLine = '';
            if ($variant && $variant->attributeValues && $variant->attributeValues->count()) {

                // nếu muốn dạng "Color: Xanh da trời nhạt / Size: S / Material: Cotton"
                $parts = $variant->attributeValues->map(function ($val) {
                    $attrName = $val->attribute->name ?? null; // cần quan hệ attribute() trong AttributeValue
                    return $attrName
                        ? $attrName . ': ' . $val->value
                        : $val->value;
                })->toArray();

                // nếu chỉ muốn "Xanh da trời nhạt / S / Cotton" thì dùng:
                // $parts = $variant->attributeValues->pluck('value')->toArray();

                $variantLine = implode(' / ', $parts);
            }

            // ====== HTML 1 ITEM ======
            $html .= '<div class="d-flex align-items-center mb-3 pb-3 border-bottom cart-sidebar-item" data-item-id="' . $item->id . '">';

            // ẢNH
            $html .= '<img src="' . $imageUrl . '" alt="' . e($productName) . '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; margin-right: 10px;">';

            // THÔNG TIN BÊN PHẢI
            $html .= '<div class="flex-grow-1">';
            $html .= '<h6 class="mb-1" style="font-size: 14px;">' . e($productName) . '</h6>';

            // dòng thuộc tính
            if ($variantLine !== '') {
                $html .= '<p class="mb-1" style="font-size: 12px; color: #666;">' . e($variantLine) . '</p>';
            }


            // giá
            $html .= '<p class="mb-0" style="font-size: 14px; font-weight: 600; color: var(--secondary-color);">'
                . number_format($item->subtotal, 0, ',', '.') . '₫</p>';

            $html .= '</div>'; // end flex-grow-1

            // nút xóa
            $html .= '<button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2 remove-cart-item" data-item-id="' . $item->id . '" style="font-size: 16px; line-height: 1;" title="Xóa sản phẩm">';
            $html .= '<i class="fas fa-times"></i>';
            $html .= '</button>';

            $html .= '</div>'; // end item wrapper
        }

        if ($cart->items->count() > 5) {
            $html .= '<p class="text-center text-muted" style="font-size: 12px;">Và ' . ($cart->items->count() - 5) . ' sản phẩm khác...</p>';
        }

        $html .= '</div>'; // end cart-sidebar-items

        // Tổng & nút xem giỏ
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
        $categoryIds = $cart->items->map(function ($item) {
            return $item->productVariant->product->category_id;
        })->unique()->filter()->toArray();

        if (empty($categoryIds)) {
            return collect([]);
        }

        // Lấy các product_id đã có trong giỏ để loại trừ
        $productIdsInCart = $cart->items->map(function ($item) {
            return $item->productVariant->product_id;
        })->unique()->toArray();

        // Lấy sản phẩm tương tự (cùng category, chưa có trong giỏ, còn hàng)
        $similarProducts = Product::whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $productIdsInCart)
            ->where('status', 1)
            ->whereHas('variants', function ($query) {
                $query->where('quantity', '>', 0)
                    ->where('status', 1);
            })
            ->with(['variants' => function ($query) {
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
    public function applyVoucher(Request $request, VoucherService $voucherService)
    {
        // Khi người dùng chủ động nhập mã, bật lại cơ chế tự động voucher (nếu trước đó từng tắt)
        Session::forget('disable_auto_voucher');
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
            ->with(['products', 'categories'])
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã bị vô hiệu hóa'
            ], 404);
        }

        // Sử dụng VoucherService để áp dụng voucher
        $result = $voucherService->applyToCart($voucher, $cart, $user->id);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Format response
        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'voucher' => [
                'code' => $result['voucher']['code'],
                'name' => $result['voucher']['name'],
                'discount_amount' => number_format($result['voucher']['discount_amount'], 0, ',', '.') . '₫',
            ],
            'subtotal' => number_format($result['subtotal'], 0, ',', '.') . '₫',
            'discount' => number_format($result['discount'], 0, ',', '.') . '₫',
            'total' => number_format($result['total'], 0, ',', '.') . '₫',
        ]);
    }

    /**
     * Xóa mã giảm giá
     */
    public function removeVoucher(VoucherService $voucherService)
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

        // Sử dụng VoucherService để xóa voucher
        $result = $voucherService->removeFromCart($cart);

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        // Người dùng đã chủ động xóa voucher -> tạm thời tắt auto-apply cho tới khi họ nhập mã mới
        Session::put('disable_auto_voucher', true);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'total' => number_format($result['total'], 0, ',', '.') . '₫',
        ]);
    }

    /**
     * API: Lấy danh sách voucher gợi ý (giống popup khuyến mãi Shopee)
     */
    public function suggestVouchers(VoucherService $voucherService)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống',
            ], 400);
        }

        $vouchers = $voucherService->getApplicableVouchers($cart, $user->id);

        return response()->json([
            'success' => true,
            'vouchers' => $vouchers,
        ]);
    }

    /**
     * Tự động áp dụng voucher tốt nhất cho khách hàng
     * Tìm và áp dụng voucher có discount cao nhất mà khách hàng đủ điều kiện
     */
    public function autoApplyBestVoucher(VoucherService $voucherService)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product', 'voucher'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return false;
        }

        // Sử dụng VoucherService để tìm voucher tốt nhất
        $bestVoucherData = $voucherService->findBestVoucher($cart, $user->id);

        if (!$bestVoucherData) {
            return false;
        }

        // Chỉ áp dụng nếu chưa có voucher hoặc voucher mới tốt hơn
        if (!$cart->voucher_id || $bestVoucherData['discount_amount'] > ($cart->discount_amount ?? 0)) {
            $result = $voucherService->applyToCart($bestVoucherData['voucher'], $cart, $user->id);
            return $result['success'];
        }

        return false;
    }
}

<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VoucherService
{
    /**
     * Kiểm tra voucher có hợp lệ không
     */
    public function validateVoucher(Voucher $voucher, ?int $userId = null, ?float $subtotal = null): array
    {
        $errors = [];

        // Kiểm tra trạng thái active
        if (!$voucher->is_active) {
            $errors[] = 'Mã giảm giá không còn hợp lệ. Vui lòng thử lại.';
            return ['valid' => false, 'errors' => $errors];
        }

        // Kiểm tra thời gian hiệu lực
        $now = now();
        if ($voucher->start_at && $voucher->start_at->isFuture()) {
            $errors[] = 'Mã giảm giá chưa có hiệu lực.';
            return ['valid' => false, 'errors' => $errors];
        }

        if ($voucher->end_at && $voucher->end_at->isPast()) {
            $errors[] = 'Mã giảm giá đã hết hạn.';
            return ['valid' => false, 'errors' => $errors];
        }

        // Kiểm tra số lần sử dụng
        $totalUsageCount = VoucherUsage::where('voucher_id', $voucher->id)->count();
        if ($voucher->usage_limit && $totalUsageCount >= $voucher->usage_limit) {
            $errors[] = 'Mã giảm giá đã hết lượt sử dụng.';
            return ['valid' => false, 'errors' => $errors];
        }

        // Kiểm tra user đã dùng voucher này chưa
        if ($userId) {
            $userUsageCount = VoucherUsage::where('voucher_id', $voucher->id)
                ->where('user_id', $userId)
                ->count();

            if ($userUsageCount > 0) {
                $errors[] = 'Bạn đã sử dụng mã giảm giá này rồi.';
                return ['valid' => false, 'errors' => $errors];
            }
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($subtotal !== null && $voucher->min_order_value && $subtotal < $voucher->min_order_value) {
            $errors[] = 'Đơn hàng phải có giá trị tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . '₫';
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Kiểm tra voucher có áp dụng được cho cart không
     */
    public function canApplyToCart(Voucher $voucher, Cart $cart): bool
    {
        if ($voucher->apply_type === 'all') {
            return true;
        }

        if ($voucher->apply_type === 'products') {
            $voucher->loadMissing('products');
            $productIds = $voucher->products->pluck('id')->toArray();
            $cartProductIds = $cart->items->map(function ($item) {
                return $item->productVariant->product_id ?? null;
            })->filter()->unique()->toArray();

            return !empty(array_intersect($productIds, $cartProductIds));
        }

        if ($voucher->apply_type === 'categories') {
            $voucher->loadMissing('categories');
            $categoryIds = $voucher->categories->pluck('id')->toArray();
            $cartCategoryIds = $cart->items->map(function ($item) {
                return $item->productVariant->product->category_id ?? null;
            })->filter()->unique()->toArray();

            return !empty(array_intersect($categoryIds, $cartCategoryIds));
        }

        return false;
    }

    /**
     * Tính số tiền giảm giá
     */
    public function calculateDiscount(Voucher $voucher, float $subtotal): float
    {
        $discountAmount = 0;

        if ($voucher->discount_type === 'percentage') {
            $discountAmount = ($subtotal * $voucher->discount_value) / 100;
            
            // Áp dụng max_discount nếu có
            if ($voucher->max_discount && $discountAmount > $voucher->max_discount) {
                $discountAmount = $voucher->max_discount;
            }
        } elseif ($voucher->discount_type === 'fixed') {
            $discountAmount = $voucher->discount_value;
            
            // Không được giảm nhiều hơn tổng tiền
            if ($discountAmount > $subtotal) {
                $discountAmount = $subtotal;
            }
        }

        // Làm tròn về số nguyên
        return round($discountAmount);
    }

    /**
     * Tìm voucher tốt nhất cho cart
     */
    public function findBestVoucher(Cart $cart, ?int $userId = null): ?array
    {
        if ($cart->items->isEmpty()) {
            return null;
        }

        $cart->calculateTotal();
        $subtotal = $cart->total_price;

        if ($subtotal <= 0) {
            return null;
        }

        // Lấy tất cả voucher đang active
        $now = now();
        $availableVouchers = Voucher::where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', $now);
            })
            ->with(['products', 'categories'])
            ->get();

        $bestVoucher = null;
        $bestDiscount = 0;

        foreach ($availableVouchers as $voucher) {
            // Validate voucher
            $validation = $this->validateVoucher($voucher, $userId, $subtotal);
            if (!$validation['valid']) {
                continue;
            }

            // Kiểm tra có áp dụng được cho cart không
            if (!$this->canApplyToCart($voucher, $cart)) {
                continue;
            }

            // Tính discount
            $discountAmount = $this->calculateDiscount($voucher, $subtotal);

            // So sánh với voucher tốt nhất hiện tại
            if ($discountAmount > $bestDiscount) {
                $bestVoucher = $voucher;
                $bestDiscount = $discountAmount;
            }
        }

        if ($bestVoucher && $bestDiscount > 0) {
            return [
                'voucher' => $bestVoucher,
                'discount_amount' => $bestDiscount,
            ];
        }

        return null;
    }

    /**
     * Áp dụng voucher vào cart
     */
    public function applyToCart(Voucher $voucher, Cart $cart, ?int $userId = null): array
    {
        try {
            $cart->calculateTotal();
            $subtotal = $cart->total_price;

            // Validate voucher
            $validation = $this->validateVoucher($voucher, $userId, $subtotal);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['errors'][0] ?? 'Mã giảm giá không hợp lệ',
                ];
            }

            // Kiểm tra có áp dụng được cho cart không
            if (!$this->canApplyToCart($voucher, $cart)) {
                return [
                    'success' => false,
                    'message' => 'Mã giảm giá không áp dụng cho sản phẩm trong giỏ hàng',
                ];
            }

            // Tính discount
            $discountAmount = $this->calculateDiscount($voucher, $subtotal);

            if ($discountAmount <= 0) {
                return [
                    'success' => false,
                    'message' => 'Mã giảm giá không thể áp dụng cho đơn hàng này',
                ];
            }

            // Áp dụng vào cart
            $cart->voucher_id = $voucher->id;
            $cart->discount_amount = $discountAmount;
            $cart->save();

            return [
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công',
                'voucher' => [
                    'code' => $voucher->code,
                    'name' => $voucher->name,
                    'discount_amount' => $discountAmount,
                ],
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'total' => round($subtotal - $discountAmount),
            ];
        } catch (\Exception $e) {
            Log::error('Apply voucher to cart error: ' . $e->getMessage(), [
                'voucher_id' => $voucher->id,
                'cart_id' => $cart->id,
                'user_id' => $userId,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi áp dụng mã giảm giá',
            ];
        }
    }

    /**
     * Xóa voucher khỏi cart
     */
    public function removeFromCart(Cart $cart): array
    {
        try {
            $cart->voucher_id = null;
            $cart->discount_amount = 0;
            $cart->save();

            $cart->calculateTotal();

            return [
                'success' => true,
                'message' => 'Đã xóa mã giảm giá',
                'total' => $cart->total_price,
            ];
        } catch (\Exception $e) {
            Log::error('Remove voucher from cart error: ' . $e->getMessage(), [
                'cart_id' => $cart->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa mã giảm giá',
            ];
        }
    }
}


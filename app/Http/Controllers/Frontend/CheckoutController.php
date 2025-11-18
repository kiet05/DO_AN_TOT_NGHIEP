<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product', 'items.productVariant.attributeValues'])
            ->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống');
        }

        if ($cart->items->contains(fn($item) => $item->isOutOfStock())) {
            return redirect()->route('cart.index')
                ->with('error', 'Vui lòng cập nhật lại số lượng sản phẩm trong giỏ trước khi thanh toán');
        }

        $cart->calculateTotal();

        // 🔹 Lấy các phương thức thanh toán đang active
        $paymentMethods = PaymentMethod::active()->get();

        return view('frontend.checkout.index', [
            'cart'           => $cart,
            'user'           => $user,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'receiver_name'    => 'required|string|max:100',
            'receiver_phone'   => 'required|string|max:20',
            'receiver_address' => 'required|string',
            'note'             => 'nullable|string',
            // 🔹 validate theo slug trong bảng payment_methods
            'payment_method'   => 'required|string|exists:payment_methods,slug',
        ]);

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product'])
            ->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống');
        }

        foreach ($cart->items as $item) {
            $variant = $item->productVariant;
            if (!$variant || $item->quantity > $variant->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', 'Số lượng sản phẩm không đủ, vui lòng cập nhật lại giỏ hàng');
            }
        }

        $cart->calculateTotal();

        // 🔹 Lấy thông tin phương thức thanh toán
        $method = PaymentMethod::active()
            ->where('slug', $request->payment_method)
            ->firstOrFail();

        $shippingFee = 0; // sau này bạn có logic phí ship thì sửa ở đây
        $totalPrice  = $cart->total_price;
        $finalAmount = $totalPrice + $shippingFee;

        DB::beginTransaction();

        try {
            // 🔹 Tạo Order
            $order = Order::create([
                'user_id'         => $user->id,
                'customer_id'     => null,
                'receiver_name'   => $request->receiver_name,
                'receiver_phone'  => $request->receiver_phone,
                'receiver_address' => $request->receiver_address,
                'shipping_fee'    => $shippingFee,
                'total_price'     => $totalPrice,
                'final_amount'    => $finalAmount,
                'voucher_id'      => null,
                'payment_method_id' => $method->id,
                'payment_method'  => $method->slug,
                'payment_status'  => 'unpaid',   // hoặc 'pending_cod' với COD
                'order_status'    => 'pending',
                'status'          => 'pending',
            ]);

            // 🔹 Tạo OrderItems + trừ tồn kho
            foreach ($cart->items as $item) {
                $variant = $item->productVariant;
                $product = $variant->product;

                $lineSubtotal = $item->subtotal;

                OrderItem::create([
                    'order_id'          => $order->id,
                    'user_id'           => $user->id,
                    'customer_id'       => null,
                    'product_id'        => $product->id,
                    'product_variant_id' => $variant->id,
                    'receiver_name'     => $order->receiver_name,
                    'receiver_phone'    => $order->receiver_phone,
                    'receiver_address'  => $order->receiver_address,
                    'quantity'          => $item->quantity,
                    'price'             => $item->price_at_time,
                    'discount'          => 0,
                    'subtotal'          => $lineSubtotal,
                    'shipping_fee'      => 0,
                    'total_price'       => $lineSubtotal,
                    'final_amount'      => $lineSubtotal,
                    'voucher_id'        => null,
                    'payment_method_id' => $order->payment_method_id,
                    'payment_method'    => $order->payment_method,
                    'payment_status'    => $order->payment_status,
                    'order_status'      => 'pending',
                    'total'             => $lineSubtotal,
                    'note'              => $request->note,
                    'status'            => 'pending',
                ]);

                $variant->quantity -= $item->quantity;
                $variant->save();
            }

            // 🔹 Đóng giỏ hàng
            $cart->status = 2;
            $cart->save();

            // 🔹 Tạo Payment tương ứng với Order
            $payment = Payment::create([
                'order_id'     => $order->id,
                'gateway'      => $method->slug,    // 'cod' hoặc 'vnpay'
                'app_trans_id' => null,
                'zp_trans_id'  => null,
                'amount'       => $finalAmount,
                'currency'     => 'VND',            // vì bảng có cột currency
                'status'       => 'pending',        // chờ thanh toán
                'meta'         => null,
                'paid_at'      => null,
            ]);

            // 🔹 Log khởi tạo payment (nếu muốn giữ)
            $payment->logs()->create([
                'type'    => 'init',
                'message' => 'Payment record created from checkout.',
                'payload' => null,
            ]);

            DB::commit();

            // Sau này nếu là online (zalopay, momo) thì chỗ này redirect sang cổng thanh toán
            // Hiện tại mình cho về trang chủ / trang thông báo
            return redirect()->route('home')->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // dd($e->getMessage()); // bật khi cần debug
            return redirect()->route('checkout.index')
                ->with('error', 'Có lỗi xảy ra khi đặt hàng, vui lòng thử lại sau.');
        }
    }
}

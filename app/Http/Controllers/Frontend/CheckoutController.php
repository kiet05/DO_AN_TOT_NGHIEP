<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\VoucherUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product', 'items.productVariant.attributeValues', 'voucher'])
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

        // 🔹 Cấu hình thành phố / quận, dùng để tính phí ship
        $locations = $this->locationConfig();
        $selectedCity = session()->getOldInput('receiver_city', array_key_first($locations));
        $districtsOfCity = $locations[$selectedCity]['districts'] ?? [];
        $selectedDistrict = session()->getOldInput('receiver_district', array_key_first($districtsOfCity));

        return view('frontend.checkout.index', [
            'cart'             => $cart,
            'user'             => $user,
            'paymentMethods'   => $paymentMethods,
            'locations'        => $locations,
            'selectedCity'     => $selectedCity,
            'selectedDistrict' => $selectedDistrict,
            'shippingFee'      => $this->calculateShippingFeeByCity($selectedCity),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $locations = $this->locationConfig();
        $cityCodes = array_keys($locations);

        $validator = Validator::make($request->all(), [
            'receiver_name'          => 'required|string|max:100',
            'receiver_phone'         => 'required|string|max:20',
            'receiver_city'          => ['required', 'string', Rule::in($cityCodes)],
            'receiver_district'      => ['required', 'string'],
            'receiver_address_detail'=> 'required|string',
            'note'                   => 'nullable|string',
            // 🔹 validate theo slug trong bảng payment_methods
            'payment_method'         => 'required|string|exists:payment_methods,slug',
        ]);

        // validate quận/huyện thuộc đúng thành phố
        $validator->after(function ($validator) use ($request, $locations) {
            $city = $request->receiver_city;
            if (!$city || !isset($locations[$city])) {
                return;
            }
            $districts = $locations[$city]['districts'] ?? [];
            if (!array_key_exists($request->receiver_district, $districts)) {
                $validator->errors()->add('receiver_district', 'Vui lòng chọn quận/huyện hợp lệ.');
            }
        });

        $validator->validate();

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

        // 🔹 Tính phí ship theo thành phố (Hà Nội nội thành: 30k, tỉnh/thành khác: 40k)
        $shippingFee = $this->calculateShippingFeeByCity($request->receiver_city);
        $totalPrice  = $cart->total_price;
        $discountAmount = $cart->discount_amount ?? 0;
        $finalAmount = $totalPrice - $discountAmount + $shippingFee;

        // Ghép lại địa chỉ đầy đủ để lưu vào đơn
        $cityName      = $locations[$request->receiver_city]['name'] ?? '';
        $districtName  = $locations[$request->receiver_city]['districts'][$request->receiver_district] ?? '';
        $addressDetail = trim($request->receiver_address_detail);
        $fullAddress   = collect([$addressDetail, $districtName, $cityName])
            ->filter()
            ->implode(', ');

        DB::beginTransaction();

        try {
            // 🔹 Tạo Order
            $order = Order::create([
                'user_id'         => $user->id,
                'customer_id'     => null,
                'receiver_name'   => $request->receiver_name,
                'receiver_phone'  => $request->receiver_phone,
                'receiver_address' => $fullAddress,
                'shipping_fee'    => $shippingFee,
                'total_price'     => $totalPrice,
                'final_amount'    => $finalAmount,
                'voucher_id'      => $cart->voucher_id,
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

            // 🔹 Lưu VoucherUsage nếu có voucher
            if ($cart->voucher_id && $discountAmount > 0) {
                VoucherUsage::create([
                    'voucher_id'     => $cart->voucher_id,
                    'order_id'       => $order->id,
                    'user_id'        => $user->id,
                    'discount_amount' => $discountAmount,
                    'used_at'        => now(),
                ]);
            }

            DB::commit();

            session(['checkout_order_id' => $order->id]);

            return redirect()
                ->route('checkout.success')
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // dd($e->getMessage()); // bật khi cần debug
            return redirect()->route('checkout.index')
                ->with('error', 'Có lỗi xảy ra khi đặt hàng, vui lòng thử lại sau.');
        }
    }

    /**
     * Hàm tính phí ship theo mã thành phố
     * - Hà Nội (nội thành): 30.000đ
     * - Tỉnh/thành khác: 40.000đ
     */
    private function calculateShippingFeeByCity(?string $cityCode): int
    {
        if (!$cityCode) {
            return 0;
        }

        return $cityCode === 'hanoi' ? 30000 : 40000;
    }

    /**
     * Cấu hình danh sách thành phố / quận dùng cho form checkout
     */
    private function locationConfig(): array
    {
        return [
            'hanoi' => [
                'name' => 'Hà Nội (nội thành)',
                'districts' => [
                    'ba_dinh'      => 'Quận Ba Đình',
                    'hoan_kiem'    => 'Quận Hoàn Kiếm',
                    'tay_ho'       => 'Quận Tây Hồ',
                    'long_bien'    => 'Quận Long Biên',
                    'cau_giay'     => 'Quận Cầu Giấy',
                    'dong_da'      => 'Quận Đống Đa',
                    'hai_ba_trung' => 'Quận Hai Bà Trưng',
                    'hoang_mai'    => 'Quận Hoàng Mai',
                    'thanh_xuan'   => 'Quận Thanh Xuân',
                    'ha_dong'      => 'Quận Hà Đông',
                    'bac_tu_liem'  => 'Quận Bắc Từ Liêm',
                    'nam_tu_liem'  => 'Quận Nam Từ Liêm',
                ],
            ],
            'ho_chi_minh' => [
                'name' => 'TP. Hồ Chí Minh',
                'districts' => [
                    'quan_1'  => 'Quận 1',
                    'quan_3'  => 'Quận 3',
                    'quan_5'  => 'Quận 5',
                    'quan_7'  => 'Quận 7',
                    'quan_10' => 'Quận 10',
                    'go_vap'  => 'Quận Gò Vấp',
                    'binh_thanh' => 'Quận Bình Thạnh',
                    'phu_nhuan'  => 'Quận Phú Nhuận',
                    'tan_binh'   => 'Quận Tân Bình',
                    'tan_phu'    => 'Quận Tân Phú',
                    'thu_duc'    => 'TP. Thủ Đức',
                    'binh_chanh' => 'Huyện Bình Chánh',
                ],
            ],
            'da_nang' => [
                'name' => 'Đà Nẵng',
                'districts' => [
                    'hai_chau'  => 'Quận Hải Châu',
                    'thanh_khe' => 'Quận Thanh Khê',
                    'son_tra'   => 'Quận Sơn Trà',
                    'ngu_hanh_son' => 'Quận Ngũ Hành Sơn',
                    'lien_chieu'   => 'Quận Liên Chiểu',
                    'cam_le'       => 'Quận Cẩm Lệ',
                    'hoa_vang'     => 'Huyện Hòa Vang',
                ],
            ],
            'hai_phong' => [
                'name' => 'Hải Phòng',
                'districts' => [
                    'hong_bang'  => 'Quận Hồng Bàng',
                    'ngo_quyen'  => 'Quận Ngô Quyền',
                    'le_chan'    => 'Quận Lê Chân',
                    'kien_an'    => 'Quận Kiến An',
                    'hai_an'     => 'Quận Hải An',
                    'duong_kinh' => 'Quận Dương Kinh',
                    'do_son'     => 'Quận Đồ Sơn',
                    'thuy_nguyen'=> 'Huyện Thủy Nguyên',
                ],
            ],
            'binh_duong' => [
                'name' => 'Bình Dương',
                'districts' => [
                    'thu_dau_mot' => 'TP. Thủ Dầu Một',
                    'di_an'       => 'TP. Dĩ An',
                    'thuan_an'    => 'TP. Thuận An',
                    'tan_uyen'    => 'TP. Tân Uyên',
                    'ben_cat'     => 'TP. Bến Cát',
                    'bau_bang'    => 'Huyện Bàu Bàng',
                    'bac_tan_uyen'=> 'Huyện Bắc Tân Uyên',
                    'phu_giao'    => 'Huyện Phú Giáo',
                    'dau_tieng'   => 'Huyện Dầu Tiếng',
                ],
            ],
            'dong_nai' => [
                'name' => 'Đồng Nai',
                'districts' => [
                    'bien_hoa'      => 'TP. Biên Hòa',
                    'long_khanh'    => 'TP. Long Khánh',
                    'nhon_trach'    => 'Huyện Nhơn Trạch',
                    'long_thanh'    => 'Huyện Long Thành',
                    'trang_bom'     => 'Huyện Trảng Bom',
                    'cam_my'        => 'Huyện Cẩm Mỹ',
                    'xuan_loc'      => 'Huyện Xuân Lộc',
                    'tan_phu_dong_nai' => 'Huyện Tân Phú',
                ],
            ],
            'quang_ninh' => [
                'name' => 'Quảng Ninh',
                'districts' => [
                    'ha_long'    => 'TP. Hạ Long',
                    'mong_cai'   => 'TP. Móng Cái',
                    'cam_phe'    => 'TP. Cẩm Phả',
                    'uong_bi'    => 'TP. Uông Bí',
                    'quang_yen'  => 'TX. Quảng Yên',
                    'dong_trieu' => 'TX. Đông Triều',
                    'co_to'      => 'Huyện Cô Tô',
                ],
            ],
            'other' => [
                'name' => 'Tỉnh / thành khác',
                'districts' => [
                    'other' => 'Khu vực khác',
                ],
            ],
        ];
    }

    public function success(Request $request)
    {
        $user = Auth::user();
        $orderId = session('checkout_order_id') ?? $request->query('order_id');

        if (!$orderId) {
            return redirect()->route('home')->with('info', 'Không tìm thấy thông tin đơn hàng.');
        }

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->with(['orderItems.productVariant.product', 'user'])
            ->firstOrFail();

        session()->forget('checkout_order_id');

        return view('frontend.checkout.success', [
            'order' => $order,
        ]);
    }
}

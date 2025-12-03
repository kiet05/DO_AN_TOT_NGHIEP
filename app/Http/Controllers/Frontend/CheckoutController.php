<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['items.productVariant.product', 'items.productVariant.attributeValues', 'voucher'])
            ->first();

        // 🔹 Tự động áp dụng voucher tốt nhất nếu chưa có voucher hoặc muốn tìm voucher tốt hơn
        if ($cart && $cart->items->count() > 0) {
            $voucherService = app(VoucherService::class);
            $bestVoucherData = $voucherService->findBestVoucher($cart, $user->id);

            if ($bestVoucherData && (!$cart->voucher_id || $bestVoucherData['discount_amount'] > ($cart->discount_amount ?? 0))) {
                $voucherService->applyToCart($bestVoucherData['voucher'], $cart, $user->id);
            }

            // Reload cart để lấy voucher mới
            $cart->refresh();
            $cart->load('voucher');
        }

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống');
        }

        // 🔹 Lọc các sản phẩm đã chọn từ query parameter
        $selectedItemIds = [];
        if ($request->has('selected_items') && $request->selected_items) {
            $selectedItemIds = explode(',', $request->selected_items);
            $selectedItemIds = array_filter(array_map('intval', $selectedItemIds));
        }

        // Nếu có danh sách đã chọn, chỉ lấy những items đó
        if (!empty($selectedItemIds)) {
            $cart->setRelation('items', $cart->items->whereIn('id', $selectedItemIds));
        }

        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán');
        }

        if ($cart->items->contains(fn($item) => $item->isOutOfStock())) {
            return redirect()->route('cart.index')
                ->with('error', 'Vui lòng cập nhật lại số lượng sản phẩm trong giỏ trước khi thanh toán');
        }

        // Tính lại tổng tiền chỉ cho các sản phẩm đã chọn
        // Tính lại từ quantity * price_at_time để đảm bảo chính xác
        $selectedSubtotal = 0;
        foreach ($cart->items as $item) {
            $selectedSubtotal += $item->quantity * $item->price_at_time;
        }
        $cart->total_price = $selectedSubtotal;

        // 🔹 Lấy các phương thức thanh toán đang active
        $paymentMethods = PaymentMethod::active()->get();

        // 🔹 Lấy danh sách địa chỉ đã lưu của user
        $savedAddresses = Address::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // 🔹 Lấy địa chỉ mặc định
        $defaultAddress = Address::getDefaultForUser($user->id);

        // 🔹 Cấu hình thành phố / quận, dùng để tính phí ship
        $locations = $this->locationConfig();

        // Nếu có địa chỉ mặc định, dùng nó; nếu không dùng old input hoặc giá trị đầu tiên
        if ($defaultAddress) {
            $selectedCity = $defaultAddress->receiver_city;
            $selectedDistrict = $defaultAddress->receiver_district;
        } else {
            $selectedCity = session()->getOldInput('receiver_city', array_key_first($locations));
            $selectedDistrict = session()->getOldInput('receiver_district', array_key_first($locations[$selectedCity]['districts'] ?? []));
        }

        $districtsOfCity = $locations[$selectedCity]['districts'] ?? [];

        return view('frontend.checkout.index', [
            'cart'             => $cart,
            'user'             => $user,
            'paymentMethods'   => $paymentMethods,
            'locations'        => $locations,
            'selectedCity'     => $selectedCity,
            'selectedDistrict' => $selectedDistrict,
            'shippingFee'      => $this->calculateShippingFeeByCity($selectedCity),
            'savedAddresses'   => $savedAddresses,
            'defaultAddress'   => $defaultAddress,
            'selectedItemIds'  => $selectedItemIds,
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
            'receiver_address_detail' => 'required|string',
            'note'                   => 'nullable|string',
            // 🔹 validate theo slug trong bảng payment_methods
            'payment_method'         => 'required|string|exists:payment_methods,slug',
            'save_address'           => 'nullable|boolean',
            'set_as_default'         => 'nullable|boolean',
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

        // 🔹 Lọc các sản phẩm đã chọn từ request (nếu có)
        $selectedItemIds = [];
        if ($request->has('selected_items') && $request->selected_items) {
            $selectedItemIds = explode(',', $request->selected_items);
            $selectedItemIds = array_filter(array_map('intval', $selectedItemIds));

            if (!empty($selectedItemIds)) {
                $cart->setRelation('items', $cart->items->whereIn('id', $selectedItemIds));
            }
        }

        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán');
        }

        // Kiểm tra sơ bộ số lượng (không lock, chỉ để tránh request không cần thiết)
        foreach ($cart->items as $item) {
            $variant = $item->productVariant;
            if (!$variant || $item->quantity > $variant->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', 'Số lượng sản phẩm không đủ, vui lòng cập nhật lại giỏ hàng');
            }
        }

        $cart->calculateTotal();

        // 🔹 Kiểm tra lại voucher trước khi checkout (đảm bảo voucher vẫn hợp lệ)
        $voucher = null;
        $discountAmount = 0;
        $voucherService = app(VoucherService::class);

        if ($cart->voucher_id) {
            $voucher = Voucher::with(['products', 'categories'])->find($cart->voucher_id);

            if (!$voucher) {
                // Reset voucher trong cart nếu không tồn tại
                $voucherService->removeFromCart($cart);

                return redirect()->route('cart.index')
                    ->with('error', 'Mã giảm giá không còn hợp lệ. Vui lòng thử lại.');
            }

            // Validate voucher
            $validation = $voucherService->validateVoucher($voucher, $user->id);
            if (!$validation['valid']) {
                $voucherService->removeFromCart($cart);
                return redirect()->route('cart.index')
                    ->with('error', $validation['errors'][0] ?? 'Mã giảm giá không hợp lệ.');
            }
        }

        // 🔹 Lấy thông tin phương thức thanh toán
        $method = PaymentMethod::active()
            ->where('slug', $request->payment_method)
            ->firstOrFail();

        // 🔹 Tính phí ship theo thành phố (Hà Nội nội thành: 30k, tỉnh/thành khác: 40k)
        $shippingFee = $this->calculateShippingFeeByCity($request->receiver_city);
        // Tính tổng tiền chỉ cho các sản phẩm đã chọn
        // Tính lại từ quantity * price_at_time để đảm bảo chính xác
        $totalPrice = 0;
        foreach ($cart->items as $item) {
            $totalPrice += $item->quantity * $item->price_at_time;
        }

        // 🔹 Tính lại discount_amount dựa trên voucher hiện tại (nếu có)
        if ($voucher) {
            // Validate với subtotal
            $validation = $voucherService->validateVoucher($voucher, $user->id, $totalPrice);
            if (!$validation['valid']) {
                $voucherService->removeFromCart($cart);
                return redirect()->route('cart.index')
                    ->with('error', $validation['errors'][0] ?? 'Mã giảm giá không hợp lệ.');
            }

            // Kiểm tra có áp dụng được cho cart không
            if (!$voucherService->canApplyToCart($voucher, $cart)) {
                $voucherService->removeFromCart($cart);
                return redirect()->route('cart.index')
                    ->with('error', 'Mã giảm giá không áp dụng cho sản phẩm trong giỏ hàng.');
            }

            // Tính lại số tiền giảm
            $discountAmount = $voucherService->calculateDiscount($voucher, $totalPrice);
        } else {
            $discountAmount = 0;
        }

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
            // 🔹 Kiểm tra lại voucher TRONG transaction để tránh race condition
            if ($voucher) {
                // Lock voucher để kiểm tra lại
                $lockedVoucher = Voucher::where('id', $voucher->id)->lockForUpdate()->first();

                if (!$lockedVoucher) {
                    DB::rollBack();
                    $voucherService->removeFromCart($cart);
                    return redirect()->route('cart.index')
                        ->with('error', 'Mã giảm giá không còn hợp lệ. Vui lòng thử lại.');
                }

                // Validate lại voucher trong transaction
                $validation = $voucherService->validateVoucher($lockedVoucher, $user->id, $totalPrice);
                if (!$validation['valid']) {
                    DB::rollBack();
                    $voucherService->removeFromCart($cart);
                    return redirect()->route('cart.index')
                        ->with('error', $validation['errors'][0] ?? 'Mã giảm giá không hợp lệ.');
                }

                // Kiểm tra lại có áp dụng được cho cart không
                if (!$voucherService->canApplyToCart($lockedVoucher, $cart)) {
                    DB::rollBack();
                    $voucherService->removeFromCart($cart);
                    return redirect()->route('cart.index')
                        ->with('error', 'Mã giảm giá không áp dụng cho sản phẩm trong giỏ hàng.');
                }

                // Tính lại discount với voucher đã lock
                $discountAmount = $voucherService->calculateDiscount($lockedVoucher, $totalPrice);
                $finalAmount = $totalPrice - $discountAmount + $shippingFee;

                $voucher = $lockedVoucher; // Sử dụng voucher đã lock
            }

            // 🔹 Lấy danh sách variant IDs cần lock
            $variantIds = $cart->items->pluck('product_variant_id')->toArray();

            // 🔹 Lock các product variants để tránh race condition
            // Sử dụng lockForUpdate để đảm bảo không có transaction khác có thể cập nhật cùng lúc
            $lockedVariants = ProductVariant::whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 🔹 Kiểm tra lại số lượng sau khi lock (quan trọng để tránh race condition)
            $outOfStockItems = [];
            foreach ($cart->items as $item) {
                $variant = $lockedVariants->get($item->product_variant_id);

                if (!$variant) {
                    $outOfStockItems[] = $item->productVariant->product->name ?? 'Sản phẩm không tồn tại';
                    continue;
                }

                // Kiểm tra số lượng thực tế sau khi lock
                if ($item->quantity > $variant->quantity) {
                    $outOfStockItems[] = $variant->product->name . ' (Còn lại: ' . $variant->quantity . ' sản phẩm)';
                }
            }

            if (!empty($outOfStockItems)) {
                DB::rollBack();
                return redirect()->route('cart.index')
                    ->with('error', 'Một số sản phẩm không còn đủ số lượng: ' . implode(', ', $outOfStockItems) . '. Vui lòng cập nhật lại giỏ hàng.');
            }

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
                'voucher_id'      => $voucher ? $voucher->id : null,
                'payment_method_id' => $method->id,
                'payment_method'  => $method->slug,
                'payment_status'  => 'unpaid',   // hoặc 'pending_cod' với COD
                'order_status'    => 'pending',
                'status'          => 'pending',
                'note'             => $request->input('note'),   // thêm dòng này

            ]);

            // 🔹 Tạo OrderItems + trừ tồn kho (atomic operation)
            foreach ($cart->items as $item) {
                $variant = $lockedVariants->get($item->product_variant_id);

                if (!$variant) {
                    DB::rollBack();
                    return redirect()->route('cart.index')
                        ->with('error', 'Có lỗi xảy ra khi xử lý đơn hàng. Vui lòng thử lại.');
                }

                // Load relationship nếu chưa có
                if (!$variant->relationLoaded('product')) {
                    $variant->load('product');
                }

                $product = $variant->product;

                // Tính lại subtotal để đảm bảo chính xác
                $lineSubtotal = $item->quantity * $item->price_at_time;

                OrderItem::create([
                    'order_id'          => $order->id,
                    'user_id'           => $user->id,
                    'customer_id'       => null,
                    'product_id'        => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name'        => $product->name,

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

                // Trừ số lượng tồn kho (atomic operation trong transaction)
                $variant->decrement('quantity', $item->quantity);
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

            // 🔹 Lưu VoucherUsage nếu có voucher hợp lệ
            if ($voucher && $discountAmount > 0) {
                VoucherUsage::create([
                    'voucher_id'     => $voucher->id,
                    'order_id'       => $order->id,
                    'user_id'        => $user->id,
                    'discount_amount' => $discountAmount,
                    'used_at'        => now(),
                ]);
            }

            // 🔹 Lưu địa chỉ giao hàng nếu user chọn
            if ($request->has('save_address') && $request->save_address) {
                $address = Address::create([
                    'user_id'              => $user->id,
                    'receiver_name'        => $request->receiver_name,
                    'receiver_phone'       => $request->receiver_phone,
                    'receiver_city'        => $request->receiver_city,
                    'receiver_district'    => $request->receiver_district,
                    'receiver_address_detail' => $request->receiver_address_detail,
                    'is_default'           => $request->has('set_as_default') && $request->set_as_default,
                ]);

                // Nếu đặt làm mặc định, cập nhật các địa chỉ khác
                if ($address->is_default) {
                    $address->setAsDefault();
                }
            }

            DB::commit();

            // 🔹 Đảm bảo session được set trước khi redirect
            session(['checkout_order_id' => $order->id]);
            session()->save(); // Force save session

            // 🔹 Redirect đến trang success
            try {
                return redirect()
                    ->route('checkout.success')
                    ->with('success', 'Đặt hàng thành công!');
            } catch (\Throwable $redirectError) {
                // Nếu redirect lỗi, vẫn log nhưng không rollback vì đã commit
                \Log::error('Checkout redirect error: ' . $redirectError->getMessage());
                // Fallback: redirect với query parameter
                return redirect()
                    ->route('checkout.success', ['order_id' => $order->id])
                    ->with('success', 'Đặt hàng thành công!');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
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
                'name' => 'Hà Nội',
                'districts' => [
                    'ba_dinh'      => 'Ba Đình',
                    'hoan_kiem'    => 'Hoàn Kiếm',
                    'tay_ho'       => 'Tây Hồ',
                    'long_bien'    => 'Long Biên',
                    'cau_giay'     => 'Cầu Giấy',
                    'dong_da'      => 'Đống Đa',
                    'hai_ba_trung' => 'Hai Bà Trưng',
                    'hoang_mai'    => 'Hoàng Mai',
                    'thanh_xuan'   => 'Thanh Xuân',
                    'ha_dong'      => 'Hà Đông',
                    'bac_tu_liem'  => 'Bắc Từ Liêm',
                    'nam_tu_liem'  => 'Nam Từ Liêm',
                ],
            ],

            'ho_chi_minh' => [
                'name' => 'TP Hồ Chí Minh',
                'districts' => [
                    'quan_1'      => '1',
                    'quan_3'      => '3',
                    'quan_5'      => '5',
                    'quan_7'      => '7',
                    'quan_10'     => '10',
                    'go_vap'      => 'Gò Vấp',
                    'binh_thanh'  => 'Bình Thạnh',
                    'phu_nhuan'   => 'Phú Nhuận',
                    'tan_binh'    => 'Tân Bình',
                    'tan_phu'     => 'Tân Phú',
                    'thu_duc'     => 'Thủ Đức',
                    'binh_chanh'  => 'Bình Chánh',
                ],
            ],

            'da_nang' => [
                'name' => 'Đà Nẵng',
                'districts' => [
                    'hai_chau'    => 'Hải Châu',
                    'thanh_khe'   => 'Thanh Khê',
                    'son_tra'     => 'Sơn Trà',
                    'ngu_hanh_son' => 'Ngũ Hành Sơn',
                    'lien_chieu'  => 'Liên Chiểu',
                    'cam_le'      => 'Cẩm Lệ',
                    'hoa_vang'    => 'Hòa Vang',
                ],
            ],

            'hai_phong' => [
                'name' => 'Hải Phòng',
                'districts' => [
                    'hong_bang'   => 'Hồng Bàng',
                    'ngo_quyen'   => 'Ngô Quyền',
                    'le_chan'     => 'Lê Chân',
                    'kien_an'     => 'Kiến An',
                    'hai_an'      => 'Hải An',
                    'duong_kinh'  => 'Dương Kinh',
                    'do_son'      => 'Đồ Sơn',
                    'thuy_nguyen' => 'Thủy Nguyên',
                ],
            ],

            'can_tho' => [
                'name' => 'Cần Thơ',
                'districts' => [
                    'ninh_kieu'   => 'Ninh Kiều',
                    'binh_thuy'   => 'Bình Thủy',
                    'cai_rang'    => 'Cái Răng',
                    'omon'        => 'Ô Môn',
                    'thot_not'    => 'Thốt Nốt',
                    'khac'        => 'Khác',
                ],
            ],

            // ===== CÁC TỈNH ĐÃ CÓ CHI TIẾT TRONG CODE CŨ =====
            'binh_duong' => [
                'name' => 'Bình Dương',
                'districts' => [
                    'thu_dau_mot'  => 'Thủ Dầu Một',
                    'di_an'        => 'Dĩ An',
                    'thuan_an'     => 'Thuận An',
                    'tan_uyen'     => 'Tân Uyên',
                    'ben_cat'      => 'Bến Cát',
                    'bau_bang'     => 'Bàu Bàng',
                    'bac_tan_uyen' => 'Bắc Tân Uyên',
                    'phu_giao'     => 'Phú Giáo',
                    'dau_tieng'    => 'Dầu Tiếng',
                ],
            ],

            'dong_nai' => [
                'name' => 'Đồng Nai',
                'districts' => [
                    'bien_hoa'       => 'Biên Hòa',
                    'long_khanh'     => 'Long Khánh',
                    'nhon_trach'     => 'Nhơn Trạch',
                    'long_thanh'     => 'Long Thành',
                    'trang_bom'      => 'Trảng Bom',
                    'cam_my'         => 'Cẩm Mỹ',
                    'xuan_loc'       => 'Xuân Lộc',
                    'tan_phu_dong_nai' => 'Tân Phú',
                ],
            ],

            'quang_ninh' => [
                'name' => 'Quảng Ninh',
                'districts' => [
                    'ha_long'   => 'Hạ Long',
                    'mong_cai'  => 'Móng Cái',
                    'cam_phe'   => 'Cẩm Phả',
                    'uong_bi'   => 'Uông Bí',
                    'quang_yen' => 'Quảng Yên',
                    'dong_trieu' => 'Đông Triều',
                    'co_to'     => 'Cô Tô',
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

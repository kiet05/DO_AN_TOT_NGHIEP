<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\CartController;
use Illuminate\Validation\ValidationException;


class OrderController extends Controller
{


    /**
     * Danh sách đơn hàng đã mua của user đang đăng nhập
     */
    public function index(Request $request)
    {
        $userId  = auth()->id();
        $status  = $request->query('status', 'all');
        $keyword = trim((string) $request->query('q', ''));

        // Tabs trạng thái cho KH xem
        $statusTabs = [
            'all'        => 'Tất cả',
            'pending'    => 'Chờ xác nhận',   // khách vừa đặt
            //  'confirmed'  => 'Chờ chuẩn bị',   // shop đã xác nhận
            'processing' => 'Đang chuẩn bị',  // đang đóng gói
            'shipping'   => 'Đang giao',
            'shipped'    => 'Đã giao',
            'returned'   => 'Hoàn / Trả hàng',
            'return_waiting_customer' => 'Chờ xác nhận hoàn hàng',
            'cancelled'  => 'Đã hủy',
            //'completed'  => 'Hoàn thành',
        ];

        $query = Order::where('user_id', $userId)
            ->with(['items.product', 'items.productVariant', 'returns'])
            ->latest('created_at');

        // Lọc theo tab trạng thái
        if ($status === 'processing') {
            $query->whereIn('order_status', ['processing', 'confirmed']);
        } elseif ($status === 'returned') {
            $query->whereIn('order_status', ['return_pending', 'returned']);
        } elseif ($status === 'shipped') {
            // Hiển thị cả shipped + completed trong tab "Đã giao"
            $query->whereIn('order_status', ['shipped', 'completed']);
        } elseif ($status !== 'all') {
            $query->where('order_status', $status);
        }
        if ($status === 'return_waiting_customer') {
            $query->whereHas('returns', function ($q) {
                $q->whereIn('status', [
                    ReturnModel::PENDING,
                    ReturnModel::WAITING_CUSTOMER_CONFIRM
                ]);
            });
        }
        // 🔍 Tìm kiếm theo ID đơn + tên / ID sản phẩm
        if ($keyword !== '') {
            $isNumeric = ctype_digit($keyword);

            $query->where(function ($orderQ) use ($keyword, $isNumeric) {
                // 1) Nếu là số -> ưu tiên tìm theo ID đơn
                if ($isNumeric) {
                    $orderQ->where('id', (int) $keyword);
                }

                // 2) Tìm theo sản phẩm trong đơn
                $orderQ->orWhereHas('items', function ($itemQ) use ($keyword, $isNumeric) {
                    // theo bảng products
                    $itemQ->whereHas('product', function ($prodQ) use ($keyword, $isNumeric) {
                        // ưu tiên trùng khớp tên
                        $prodQ->where('name', $keyword)
                            ->orWhere('name', 'LIKE', '%' . $keyword . '%');

                        // nếu keyword là số thì có thể là ID sản phẩm
                        if ($isNumeric) {
                            $prodQ->orWhere('id', (int) $keyword);
                        }
                    });

                    // nếu keyword là số thì cho phép match luôn product_id trên order_items
                    if ($isNumeric) {
                        $itemQ->orWhere('product_id', (int) $keyword);
                    }
                });
            });
        }

        $orders = $query->paginate(5)->withQueryString();

        return view('frontend.order.index', compact('orders', 'status', 'statusTabs'));
    }


    /**
     * Chi tiết 1 đơn hàng
     */
    public function show(Order $order)
    {

        // Không cho xem đơn của người khác
        if ($order->user_id !== auth()->id()) { // đổi field nếu khác
            abort(403);
        }

        // Load thêm quan hệ nếu có
        // ví dụ: items, product, histories...
        $order->load([
            'items.product',
            'items.productVariant',   // 👈 thêm dòng này để lấy ảnh biến thể
            'statusHistories',
            'voucherUsage' // ✅ THÊM DÒNG NÀY
        ]);

        return view('frontend.order.show', compact('order'));
    }
    protected function ensureOwner(Order $order): void
    {

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
    }

    /** FORM HỦY ĐƠN */
    public function showCancelForm(Order $order)
    {
        $this->ensureOwner($order);

        if (! $order->canBeCancelledByCustomer()) {
            return redirect()->route('order.index')
                ->with('error', 'Đơn hàng hiện tại không thể hủy.');
        }

        return view('frontend.order.cancel', compact('order'));
    }

    /** XỬ LÝ HỦY ĐƠN */
    public function cancel(Request $request, Order $order)
    {
        $this->ensureOwner($order);

        if (! $order->canBeCancelledByCustomer()) {
            return redirect()->route('order.index')
                ->with('error', 'Đơn hàng hiện tại không thể hủy.');
        }

        $data = $request->validate([
            'cancel_reason' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($order, $data) {
            $order->cancel_reason = $data['cancel_reason'];
            $order->order_status  = 'cancelled';
            $order->status_changed_at = now();
            $order->save();
            // ✅ Cộng lại số lượng vào kho
            foreach ($order->items as $item) {
                $variantId = $item->product_variant_id ?? $item->variant_id ?? null;
                if ($variantId) {
                    ProductVariant::whereKey($variantId)
                        ->increment('quantity', (int) $item->quantity);
                }
            }

            if (method_exists($order, 'statusHistories')) {
                $order->statusHistories()->create([
                    'status'     => 'cancelled',
                    'note'       => 'Khách hàng hủy đơn',
                ]);
            }
        });

        return redirect()->route('order.index')
            ->with('success', 'Đã hủy đơn hàng thành công.');
    }

    /** KHÁCH BẤM "ĐÃ NHẬN HÀNG" */
    public function received(Request $request, Order $order)
    {
        $this->ensureOwner($order);

        // Chỉ cho xác nhận khi đơn đang giao
        if (!in_array($order->order_status, ['shipping', 'shipped'], true)) {
            return redirect()
                ->route('order.index', $order)
                ->with('error', 'Chỉ xác nhận đã nhận hàng với đơn đang giao.');
        }

        DB::transaction(function () use ($order) {
            // Cập nhật trạng thái đơn
            $order->order_status      = 'shipped';
            $order->status_changed_at = now();

            // Nếu thanh toán chưa xong (COD chưa thanh toán) -> đánh dấu đã thanh toán
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
            }

            $order->save();

            // Ghi log lịch sử trạng thái
            if (method_exists($order, 'statusHistories')) {
                $order->statusHistories()->create([
                    'status'   => 'shipped',
                    'note'     => 'Khách xác nhận đã nhận hàng, tự động đánh dấu thanh toán nếu chưa có',
                    'order_id' => $order->id,
                ]);
            }
        });

        return redirect()
            ->route('order.index', $order)
            ->with('success', 'Bạn đã xác nhận đã nhận được hàng. Đơn hàng đã chuyển sang trạng thái "Đã giao".');
    }


    /** FORM TRẢ HÀNG / HOÀN TIỀN */
    public function showReturnForm(Order $order)
    {
        $this->ensureOwner($order);

        if (! $order->canRequestReturnByCustomer()) {
            return redirect()->route('order.index')
                ->with('error', 'Đơn hàng hiện tại không thể yêu cầu trả hàng / hoàn tiền.');
        }

        return view('frontend.order.return', compact('order'));
    }

    /** XỬ LÝ TRẢ HÀNG / HOÀN TIỀN */
    public function submitReturn(Request $request, Order $order)
    {
        $this->ensureOwner($order);

        if (! $order->canRequestReturnByCustomer()) {
            return redirect()->route('order.index')
                ->with('error', 'Đơn hàng hiện không thể yêu cầu trả hàng / hoàn tiền.');
        }

      $data = $request->validate(
    [
        // 1. Hình thức xử lý
        'return_action'
            => 'required|in:refund_full,refund_partial,exchange_product,exchange_variant',

        // 2. Lý do trả hàng / hoàn tiền (được build từ JS)
        'return_reason'
            => 'required|string|max:2000',

        // 3. Ảnh minh chứng (khuyến khích)
        'return_image'
            => 'nullable|image|max:2048',

        // 4. Phương thức hoàn tiền
        'refund_method'
            => 'required|in:wallet,manual',

        // 5. Số tài khoản (chỉ bắt buộc khi hoàn thủ công)
        'refund_account_number'
            => 'required_if:refund_method,manual|string|max:255',

        // 6. Sản phẩm muốn trả
        'return_items'
            => 'nullable|array',

        'return_items.*.checked'
            => 'nullable',

        'return_items.*.quantity'
            => 'nullable|integer|min:1',
    ],
    [
        // ===== MESSAGE TIẾNG VIỆT =====

        'return_action.required'
            => 'Vui lòng chọn hình thức xử lý.',
        'return_action.in'
            => 'Hình thức xử lý không hợp lệ.',

        'return_reason.required'
            => 'Vui lòng chọn lý do trả hàng / hoàn tiền.',
        'return_reason.string'
            => 'Nội dung lý do trả hàng không hợp lệ.',
        'return_reason.max'
            => 'Lý do trả hàng không được vượt quá :max ký tự.',

        'return_image.image'
            => 'Ảnh minh chứng phải là hình ảnh.',
        'return_image.max'
            => 'Ảnh minh chứng không được vượt quá 2MB.',

        'refund_method.required'
            => 'Vui lòng chọn phương thức hoàn tiền.',
        'refund_method.in'
            => 'Phương thức hoàn tiền không hợp lệ.',

        'refund_account_number.required_if'
            => 'Vui lòng nhập số tài khoản nhận tiền hoàn.',
        'refund_account_number.string'
            => 'Số tài khoản hoàn tiền không hợp lệ.',
        'refund_account_number.max'
            => 'Số tài khoản hoàn tiền không được vượt quá :max ký tự.',

        'return_items.array'
            => 'Danh sách sản phẩm trả không hợp lệ.',
        'return_items.*.quantity.integer'
            => 'Số lượng trả phải là số.',
        'return_items.*.quantity.min'
            => 'Số lượng trả phải lớn hơn 0.',
    ],
    [
        // ===== TÊN FIELD TIẾNG VIỆT =====
        'return_action'         => 'hình thức xử lý',
        'return_reason'         => 'lý do trả hàng / hoàn tiền',
        'return_image'          => 'ảnh minh chứng',
        'refund_method'         => 'phương thức hoàn tiền',
        'refund_account_number' => 'số tài khoản hoàn tiền',
        'return_items'          => 'sản phẩm trả',
        'return_items.*.quantity' => 'số lượng trả',
    ]
);

        $path = null;
        if ($request->hasFile('return_image')) {
            $path = $request->file('return_image')->store('order_returns', 'public');
        }

        // Load items của đơn (để kiểm tra số lượng mua và đảm bảo đúng order_item_id)
        $order->load('items');

        // Lấy danh sách dòng user tick
        $rawItems = (array) $request->input('return_items', []);
        $selected = [];

        foreach ($rawItems as $orderItemId => $row) {
            if (!isset($row['checked'])) {
                continue; // không tick => bỏ
            }
            $qty = (int) ($row['quantity'] ?? 0);

            // qty phải >=1
            if ($qty <= 0) {
                continue;
            }

            $selected[(int)$orderItemId] = $qty;
        }

        // ✅ Nếu user không chọn gì:
        // - refund_full: tự động chọn tất cả (full qty)
        // - còn lại: báo lỗi
        if (empty($selected)) {
            if ($data['return_action'] === 'refund_full') {
                foreach ($order->items as $it) {
                    $selected[$it->id] = (int) ($it->quantity ?? 1);
                }
            } else {
                return back()
                    ->withInput()
                    ->with('error', 'Bạn phải chọn ít nhất 1 sản phẩm và số lượng muốn trả.');
            }
        }

        // Key items theo id để check nhanh
        $orderItemsById = $order->items->keyBy('id');

        // Validate: item phải thuộc đơn + qty không vượt quá số đã mua
        foreach ($selected as $orderItemId => $qty) {
            if (!$orderItemsById->has($orderItemId)) {
                return back()->withInput()->with('error', 'Có sản phẩm không thuộc đơn hàng.');
            }

            $boughtQty = (int) ($orderItemsById[$orderItemId]->quantity ?? 0);
            if ($qty > $boughtQty) {
                return back()->withInput()
                    ->with('error', "Số lượng trả của sản phẩm #{$orderItemId} vượt quá số lượng đã mua.");
            }
        }

        DB::transaction(function () use ($order, $data, $path, $selected) {

            // 1) Tạo returns
            $ret = ReturnModel::create([
                'order_id'      => $order->id,
                'user_id'       => $order->user_id,
                'reason'        => $data['return_reason'],
                'proof_image'   => $path,
                'evidence_urls' => null,
                'status'        => ReturnModel::PENDING,
                // ✅ hoàn tiền
    'refund_method'         => $data['refund_method'],
    'refund_account_number' => $data['refund_account_number'] ?? null,
    'refund_amount'         => 0,
                'action_type'   => $data['return_action'],
            ]);

            // 2) Chỉ tạo return_items cho các item được tick
            foreach ($selected as $orderItemId => $qty) {
                ReturnItem::create([
                    'return_id'     => $ret->id,
                    'order_item_id' => $orderItemId,
                    'quantity'      => $qty,
                    'image_proof'   => null,
                    'status'        => 0,
                ]);
            }

            // 3) Update orders status
            $order->return_reason = $data['return_reason'];
            if ($path) {
                $order->return_image_path = $path;
            }
            $order->order_status      = Order::STATUS_RETURN_PENDING;
            $order->status_changed_at = now();
            $order->save();

            if (method_exists($order, 'statusHistories')) {
                $order->statusHistories()->create([
                    'status'   => Order::STATUS_RETURN_PENDING,
                    'note'     => 'Khách hàng yêu cầu trả hàng / hoàn tiền (return #' . $ret->id . ')',
                    'order_id' => $order->id,
                ]);
            }
        });

        return redirect()->route('order.index')
            ->with('success', 'Đã gửi yêu cầu trả hàng / hoàn tiền, vui lòng chờ shop xác nhận.');
    }



    /** MUA LẠI ĐƠN ĐÃ HỦY – THÊM LẠI VÀO GIỎ */
    public function reorder(Request $request, Order $order)
    {
        // Không cho reorder đơn của người khác
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $order->canBeReorderedByCustomer()) {
            return back()->with('error', 'Đơn này hiện không thể mua lại.');
        }

        // Dùng lại CartController
        $cartController = app(CartController::class);

        foreach ($order->orderItems as $item) {
            // tuỳ tên cột của bạn: product_variant_id / variant_id ...
            $variantId = $item->product_variant_id ?? $item->variant_id ?? null;
            if (! $variantId) {
                continue;
            }

            $qty = (int) ($item->quantity ?? 1);

            // ✅ GỌI LẠI LOGIC THÊM GIỎ
            $cartController->addItem($variantId, $qty);
        }

        return redirect()
            ->route('cart.index')   // route hiển thị giỏ ở bước 1
            ->with('success', 'Đã thêm lại các sản phẩm trong đơn vào giỏ hàng.');
    }

    public function confirmRefundReceived($id)
    {
        DB::transaction(function () use ($id) {
            $ret = ReturnModel::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($ret->user_id !== auth()->id()) {
                abort(403);
            }

            if ($ret->status !== ReturnModel::WAITING_CUSTOMER_CONFIRM) {
                return;
            }

            $ret->load(['order', 'items.orderItem']);

            if (!$ret->restocked_at) {
                foreach ($ret->items as $ri) {
                    $orderItem = $ri->orderItem;
                    if (!$orderItem) {
                        continue;
                    }

                    $variantId = $orderItem->product_variant_id ?? $orderItem->variant_id ?? null;
                    if (!$variantId) {
                        continue;
                    }

                    ProductVariant::whereKey($variantId)->increment('quantity', (int) $ri->quantity);
                }

                $ret->restocked_at = now();
            }

            $ret->status = ReturnModel::COMPLETED;
            $ret->save();

            if ($ret->order_id) {
                Order::whereKey($ret->order_id)
                    ->update([
                        'order_status'      => Order::STATUS_RETURNED,
                        'status_changed_at' => now(),
                    ]);
            }

            if ($ret->order && method_exists($ret->order, 'statusHistories')) {
                $ret->order->statusHistories()->create([
                    'status'   => \App\Models\Order::STATUS_RETURNED,
                    'note'     => 'Khách xác nhận đã nhận tiền hoàn (return #' . $ret->id . ')',
                    'order_id' => $ret->order->id,
                ]);
            }
        });

        return redirect()
            ->route('order.index')
            ->with('success', 'Bạn đã xác nhận đã nhận tiền hoàn. Cảm ơn bạn!');
    }
    public function complete(Order $order)
    {
        // chỉ cho chủ đơn xác nhận
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // chỉ cho phép xác nhận khi đã giao
        if ($order->order_status !== 'shipped') {
            return back()->with('error', 'Đơn hàng chưa thể hoàn thành.');
        }

        $order->update([
            'order_status' => 'completed',
            'completed_at' => now(), // nếu có cột
        ]);

        return back()->with('success', 'Đơn hàng đã được hoàn thành.');
    }
    public function track(Order $order)
    {
        // Chỉ chủ đơn mới xem được
        abort_if($order->user_id !== Auth::id(), 403);

        $return = $order->returns()
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if (!$return) {
            abort(404, 'Không tìm thấy yêu cầu hoàn hàng');
        }

        return view('frontend.order.return_track', compact('order', 'return'));
    }
}

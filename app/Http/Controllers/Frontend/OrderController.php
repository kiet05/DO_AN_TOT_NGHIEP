<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Frontend\CartController;
use Illuminate\Support\Facades\Auth;


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
            'confirmed'  => 'Chờ chuẩn bị',   // shop đã xác nhận
            'processing' => 'Đang chuẩn bị',  // đang đóng gói
            'shipping'   => 'Đang giao',
            'shipped'    => 'Đã giao',
            'returned'   => 'Hoàn / Trả hàng',
            'cancelled'  => 'Đã hủy',
        ];

        $query = Order::where('user_id', $userId)
            ->with(['items.product', 'items.productVariant'])
            ->latest('created_at');

        // Lọc theo tab trạng thái
        if ($status === 'confirmed') {
            $query->where('order_status', 'confirmed');
        } elseif ($status === 'returned') {
            $query->whereIn('order_status', ['return_pending', 'returned']);
        } elseif ($status !== 'all') {
            $query->where('order_status', $status);
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
            // chỉnh theo tên relationship thực tế của bạn
            // 'statusHistories', // nếu sau này bạn thêm lịch sử trạng thái
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

        $data = $request->validate([
            'return_reason' => 'required|string|max:1000',
            'return_image'  => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('return_image')) {
            $path = $request->file('return_image')->store('order_returns', 'public');
        }

        DB::transaction(function () use ($order, $data, $path) {
            $order->return_reason = $data['return_reason'];

            if ($path) {
                $order->return_image_path = $path;
            }

            // 👇 chỉ set sang trạng thái "yêu cầu trả hàng", chưa final
            $order->order_status      = 'return_pending';
            $order->status_changed_at = now();
            $order->save();

            if (method_exists($order, 'statusHistories')) {
                $order->statusHistories()->create([
                    'status'   => 'return_pending',
                    'note'     => 'Khách hàng yêu cầu trả hàng / hoàn tiền',
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
}

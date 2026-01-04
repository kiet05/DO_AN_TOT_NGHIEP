<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\CartController;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId  = auth()->id();
        $status  = $request->query('status', 'all');
        $keyword = trim((string) $request->query('q', ''));

        $statusTabs = [
            'all'        => 'Tất cả',
            'pending'    => 'Chờ xác nhận',
            'confirmed'  => 'Chờ chuẩn bị',
            'processing' => 'Đang chuẩn bị',
            'shipping'   => 'Đang giao',
            'shipped'    => 'Đã giao',
            'returned'   => 'Hoàn / Trả hàng',
            'return_waiting_customer' => 'Chờ xác nhận hoàn hàng',
            'cancelled'  => 'Đã hủy',
        ];

        $query = Order::where('user_id', $userId)
            ->with(['items.product', 'items.productVariant', 'returns'])
            ->latest('created_at');

        if ($status === 'confirmed') {
            $query->where('order_status', 'confirmed');
        } elseif ($status === 'returned') {
            $query->whereIn('order_status', ['return_pending', 'returned']);
        } elseif ($status !== 'all') {
            $query->where('order_status', $status);
        }

        if ($status === 'return_waiting_customer') {
            $query->whereHas('returns', function ($q) {
                $q->where('status', \App\Models\ReturnModel::WAITING_CUSTOMER_CONFIRM);
            });
        }

        if ($keyword !== '') {
            $isNumeric = ctype_digit($keyword);

            $query->where(function ($orderQ) use ($keyword, $isNumeric) {
                if ($isNumeric) {
                    $orderQ->where('id', (int) $keyword);
                }

                $orderQ->orWhereHas('items', function ($itemQ) use ($keyword, $isNumeric) {
                    $itemQ->whereHas('product', function ($prodQ) use ($keyword, $isNumeric) {
                        $prodQ->where('name', $keyword)
                            ->orWhere('name', 'LIKE', '%' . $keyword . '%');

                        if ($isNumeric) {
                            $prodQ->orWhere('id', (int) $keyword);
                        }
                    });

                    if ($isNumeric) {
                        $itemQ->orWhere('product_id', (int) $keyword);
                    }
                });
            });
        }

        $orders = $query->paginate(5)->withQueryString();

        return view('frontend.order.index', compact('orders', 'status', 'statusTabs'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load([
            'items.product',
            'items.productVariant',
            'statusHistories',
            'voucherUsage'
        ]);

        return view('frontend.order.show', compact('order'));
    }

    protected function ensureOwner(Order $order): void
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function showCancelForm(Order $order)
    {
        $this->ensureOwner($order);

        if (! $order->canBeCancelledByCustomer()) {
            return redirect()->route('order.index')
                ->with('error', 'Đơn hàng hiện tại không thể hủy.');
        }

        return view('frontend.order.cancel', compact('order'));
    }

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

    public function received(Request $request, Order $order)
    {
        $this->ensureOwner($order);

        if (!in_array($order->order_status, ['shipping', 'shipped'], true)) {
            return redirect()
                ->route('order.index', $order)
                ->with('error', 'Chỉ xác nhận đã nhận hàng với đơn đang giao.');
        }

        DB::transaction(function () use ($order) {
            $order->order_status      = 'shipped';
            $order->status_changed_at = now();

            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
            }

            $order->save();

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

    public function showReturnForm(Order $order)
    {
        $this->ensureOwner($order);

        if (! $order->canRequestReturnByCustomer()) {
            return redirect()->route('order.index')
                ->with('error', 'Đơn hàng hiện tại không thể yêu cầu trả hàng / hoàn tiền.');
        }

        return view('frontend.order.return', compact('order'));
    }

    public function submitReturn(Request $request, Order $order)
    {
        $this->ensureOwner($order);

        if (! $order->canRequestReturnByCustomer()) {
            return redirect()->route('order.index')
                ->with('error', 'Đơn hàng hiện không thể yêu cầu trả hàng / hoàn tiền.');
        }

        $data = $request->validate([
            'return_action'         => 'required|in:refund_full,refund_partial,exchange_product,exchange_variant',
            'return_reason'         => 'required|string|max:1000',
            'return_image'          => 'nullable|image|max:2048',
            'refund_account_number' => 'nullable|string|max:255',

            // ✅ thêm validate cho mảng sản phẩm trả
            'return_items'                      => 'nullable|array',
            'return_items.*.checked'            => 'nullable',
            'return_items.*.quantity'           => 'nullable|integer|min:0',
        ]);

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
                'refund_method' => null,
                'refund_amount' => 0,
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


    public function reorder(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $order->canBeReorderedByCustomer()) {
            return back()->with('error', 'Đơn này hiện không thể mua lại.');
        }

        $cartController = app(CartController::class);

        foreach ($order->orderItems as $item) {
            $variantId = $item->product_variant_id ?? $item->variant_id ?? null;
            if (! $variantId) {
                continue;
            }

            $qty = (int) ($item->quantity ?? 1);
            $cartController->addItem($variantId, $qty);
        }

        return redirect()
            ->route('cart.index')
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
                        'order_status'      => Order::STATUS_RETURNED_COMPLETED,
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
}

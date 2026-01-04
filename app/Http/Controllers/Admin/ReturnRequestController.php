<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Wallet;
use App\Models\ReturnModel;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $q = ReturnModel::with(['user', 'order'])->latest();

        if ($request->filled('status')) {
            $q->where('status', (int) $request->integer('status'));
        }

        if ($request->filled('order_id')) {
            $q->where('order_id', (int) $request->integer('order_id'));
        }

        $returns = $q->paginate(20);

        return view('admin.returns.index', compact('returns'));
    }

    public function show($id)
    {
        $ret = ReturnModel::with([
            'user',
            'order',
            'items.orderItem.product',
            'items.orderItem.productVariant',
            'items.orderItem.productVariant.attributes.attribute',
        ])->findOrFail($id);

        return view('admin.returns.show', compact('ret'));
    }

    public function approve($id, Request $request)
    {
        $data = $request->validate([
            'refund_method' => ['nullable', 'in:manual,wallet'],
        ]);

        $ret = ReturnModel::with([
            'order',
            'order.orderItems',
            'items.orderItem'
        ])->findOrFail($id);

        if ($ret->status !== ReturnModel::PENDING) {
            return back()->with('error', 'Trạng thái không hợp lệ, chỉ duyệt yêu cầu đang chờ.');
        }

        $refundAmount = $this->computeRefundAmountWithVoucher($ret);

        $ret->status        = ReturnModel::APPROVED;
        $ret->approved_by   = Auth::id();
        $ret->decided_at    = now();
        $ret->refund_method = $data['refund_method'] ?? 'wallet';
        $ret->refund_amount = $refundAmount;
        $ret->save();

        if ($ret->order_id) {
            Order::whereKey($ret->order_id)->update([
                'order_status'      => Order::STATUS_RETURN_PENDING,
                'status_changed_at' => now(),
            ]);
        }

        return back()->with('success', 'Đã duyệt yêu cầu. Tiền hoàn đã được tính tự động (đã trừ voucher nếu có).');
    }

    public function reject($id)
    {
        $ret = ReturnModel::with('order')->findOrFail($id);

        if ($ret->status !== ReturnModel::PENDING) {
            return back()->with('error', 'Chỉ được từ chối yêu cầu đang chờ duyệt.');
        }

        $ret->status        = ReturnModel::REJECTED;
        $ret->approved_by   = Auth::id();
        $ret->decided_at    = now();
        $ret->refund_method = null;
        $ret->refund_amount = 0;
        $ret->save();

        $this->setOrderStatusOnReject($ret);

        return back()->with('success', 'Đã từ chối yêu cầu hoàn hàng.');
    }

    public function setRefunding($id)
    {
        $ret = ReturnModel::findOrFail($id);

        if ($ret->status !== ReturnModel::APPROVED) {
            return back()->with('error', 'Chỉ chuyển sang ĐANG HOÀN TIỀN khi yêu cầu đã được duyệt.');
        }

        $ret->status = ReturnModel::REFUNDING;
        $ret->save();

        return back()->with('success', 'Đã chuyển trạng thái sang Đang hoàn tiền.');
    }

    public function refundAuto($id)
    {
        $ret = ReturnModel::with('order')->findOrFail($id);

        if (! in_array($ret->status, [ReturnModel::APPROVED, ReturnModel::REFUNDING], true)) {
            return back()->with('error', 'Trạng thái không hợp lệ (chỉ hoàn khi ĐÃ DUYỆT hoặc ĐANG HOÀN).');
        }

        if ($ret->refund_method !== 'wallet') {
            return back()->with('error', 'Phương thức hoàn không phải ví nội bộ.');
        }

        if ($ret->refund_amount <= 0) {
            return back()->with('error', 'Số tiền hoàn không hợp lệ.');
        }

        DB::transaction(function () use ($ret) {
            $ret->refresh();

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $ret->user_id],
                ['balance' => 0]
            );

            $wallet->balance += $ret->refund_amount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'   => $wallet->id,
                'type'        => 'refund',
                'amount'      => $ret->refund_amount,
                'description' => 'Refund for return #' . $ret->id,
                'order_id'    => $ret->order_id,
                'ref_type'    => 'return',
                'ref_id'      => $ret->id,
                'meta'        => [
                    'approved_by' => $ret->approved_by,
                    'decided_at'  => optional($ret->decided_at)->toDateTimeString(),
                ],
            ]);

            $ret->status     = ReturnModel::WAITING_CUSTOMER_CONFIRM;
            $ret->decided_at = now();
            $ret->save();

            $this->setOrderStatusOnWaitingCustomer($ret);
        });

        return back()->with('success', 'Đã hoàn tiền vào ví. Đang chờ khách xác nhận đã nhận tiền.');
    }

    public function refundManual($id)
    {
        $ret = ReturnModel::with('order')->findOrFail($id);

        if (! in_array($ret->status, [ReturnModel::APPROVED, ReturnModel::REFUNDING], true)) {
            return back()->with('error', 'Trạng thái không hợp lệ (chỉ hoàn khi ĐÃ DUYỆT hoặc ĐANG HOÀN).');
        }

        if ($ret->refund_method !== 'manual') {
            return back()->with('error', 'Phương thức không phải hoàn thủ công.');
        }

        DB::transaction(function () use ($ret) {
            $ret->status     = ReturnModel::WAITING_CUSTOMER_CONFIRM;
            $ret->decided_at = now();
            $ret->save();

            $this->setOrderStatusOnWaitingCustomer($ret);
        });

        return back()->with('success', 'Đã đánh dấu đã hoàn tiền cho khách. Đang chờ khách xác nhận.');
    }

    private function computeRefundAmountWithVoucher(ReturnModel $ret): float
    {
        $order = $ret->order;
        if (! $order) return 0;

        $orderItems = $order->orderItems ?? collect();
        if ($orderItems->isEmpty()) return 0;

        $originalTotal = 0;
        foreach ($orderItems as $oi) {
            $originalTotal += ((float)($oi->price ?? 0)) * ((int)($oi->quantity ?? 0));
        }

        $hasLineFinal = false;
        $sumLineFinal = 0;
        foreach ($orderItems as $oi) {
            if ($oi->final_amount !== null) {
                $hasLineFinal = true;
                $sumLineFinal += (float)$oi->final_amount;
            }
        }

        $finalTotal = $hasLineFinal
            ? $sumLineFinal
            : (float)($order->final_amount ?? $order->grand_total ?? $order->total_price ?? $order->total ?? $originalTotal);

        $voucherDiscountTotal = max(0, (float)$originalTotal - (float)$finalTotal);

        $refund = 0;
        foreach ($ret->items as $ri) {
            $oi = $ri->orderItem;
            if (! $oi) continue;

            $reqQty = (int)($ri->quantity ?? 0);
            if ($reqQty <= 0) continue;

            $unitAfter = $this->unitPriceAfterVoucher($oi, (float)$originalTotal, (float)$voucherDiscountTotal);
            $refund += $unitAfter * $reqQty;
        }

        return round($refund, 2);
    }

    private function unitPriceAfterVoucher($orderItem, float $originalTotal, float $voucherDiscountTotal): float
    {
        $qty = (int)($orderItem->quantity ?? 0);
        if ($qty <= 0) return 0;

        if ($orderItem->final_amount !== null) {
            return (float)$orderItem->final_amount / $qty;
        }

        $lineOriginal = ((float)($orderItem->price ?? 0)) * $qty;

        $proportionalDiscount = 0;
        if ($originalTotal > 0 && $voucherDiscountTotal > 0) {
            $proportionalDiscount = ($lineOriginal / $originalTotal) * $voucherDiscountTotal;
        }

        $lineFinal = max(0, $lineOriginal - $proportionalDiscount);
        return $lineFinal / $qty;
    }

    private function setOrderStatusOnReject(ReturnModel $ret): void
    {
        if (! $ret->order_id) {
            return;
        }

        Order::whereKey($ret->order_id)
            ->where('order_status', Order::STATUS_RETURN_PENDING)
            ->update([
                'order_status'      => Order::STATUS_SHIPPED,
                'status_changed_at' => now(),
            ]);
    }

    private function setOrderStatusOnWaitingCustomer(ReturnModel $ret): void
    {
        if (! $ret->order_id) {
            return;
        }

        Order::whereKey($ret->order_id)->update([
            'order_status'      => Order::STATUS_RETURN_WAITING_CUSTOMER,
            'status_changed_at' => now(),
        ]);
    }
}

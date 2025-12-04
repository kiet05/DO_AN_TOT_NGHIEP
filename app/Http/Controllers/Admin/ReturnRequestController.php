<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnModel;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'items.orderItem.productVariant', // nếu có
        ])->findOrFail($id);

        return view('admin.returns.show', compact('ret'));
    }

    public function approve($id, Request $request)
    {
        $data = $request->validate([
            'refund_amount' => ['nullable', 'numeric', 'min:0'],
            'refund_method' => ['nullable', 'in:manual,wallet'],
        ]);

        $ret = ReturnModel::with('order')->findOrFail($id);

        if ($ret->status !== ReturnModel::PENDING) {
            return back()->with('error', 'Trạng thái không hợp lệ, chỉ duyệt yêu cầu đang chờ.');
        }

        $ret->status        = ReturnModel::APPROVED;
        $ret->approved_by   = Auth::id();
        $ret->decided_at    = now();
        $ret->refund_method = $data['refund_method'] ?? 'wallet';
        $ret->refund_amount = $data['refund_amount'] ?? 0;
        $ret->save();

        if ($ret->order && $ret->order->order_status !== 'returned') {
            $ret->order->order_status      = 'return_pending';
            $ret->order->status_changed_at = now();
            $ret->order->save();
        }

        return back()->with('success', 'Đã duyệt yêu cầu trả hàng / hoàn tiền.');
    }

    public function reject($id)
    {
        $ret = ReturnModel::with('order')->findOrFail($id);

        if (! in_array($ret->status, [ReturnModel::PENDING, ReturnModel::APPROVED], true)) {
            return back()->with('error', 'Trạng thái không hợp lệ để từ chối.');
        }

        $ret->status        = ReturnModel::REJECTED;
        $ret->approved_by   = Auth::id();
        $ret->decided_at    = now();
        $ret->refund_method = null;
        $ret->refund_amount = 0;
        $ret->save();

        if ($ret->order && $ret->order->order_status === 'return_pending') {
            $ret->order->order_status      = 'shipped';
            $ret->order->status_changed_at = now();
            $ret->order->save();
        }

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
        $ret = ReturnModel::findOrFail($id);

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

            $ret->status = ReturnModel::COMPLETED;
            $ret->save();
        });

        return back()->with('success', 'Đã hoàn tiền vào ví và hoàn tất yêu cầu.');
    }

    public function refundManual($id)
    {
        $ret = ReturnModel::findOrFail($id);

        if (! in_array($ret->status, [ReturnModel::APPROVED, ReturnModel::REFUNDING], true)) {
            return back()->with('error', 'Trạng thái không hợp lệ (chỉ hoàn khi ĐÃ DUYỆT hoặc ĐANG HOÀN).');
        }

        if ($ret->refund_method !== 'manual') {
            return back()->with('error', 'Phương thức không phải hoàn thủ công.');
        }

        $ret->status = ReturnModel::COMPLETED;
        $ret->save();

        return back()->with('success', 'Đã đánh dấu hoàn tất hoàn tiền.');
    }
}

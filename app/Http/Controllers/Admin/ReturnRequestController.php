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
            'items.orderItem.productVariant', // nếu có
            'items.orderItem.productVariant.attributes.attribute',
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

        // Nhờ casts 'status' => 'integer' nên so sánh này ổn
        if ($ret->status !== ReturnModel::PENDING) {
            return back()->with('error', 'Trạng thái không hợp lệ, chỉ duyệt yêu cầu đang chờ.');
        }

        $ret->status        = ReturnModel::WAITING_CUSTOMER_CONFIRM;
        $ret->approved_by   = Auth::id();
        $ret->decided_at    = now();
        $ret->refund_method = $data['refund_method'] ?? ($ret->refund_method ?? 'wallet');
        $ret->refund_amount = $data['refund_amount'] ?? ($ret->refund_amount ?? 0);
        $ret->save();

        // Cập nhật trạng thái đơn hàng: chờ hoàn hàng
        $this->setOrderStatusOnApprove($ret);

        return back()->with('success', 'Đã duyệt yêu cầu trả hàng / hoàn tiền.');
    }


    public function reject($id)
    {
        $ret = ReturnModel::with('order')->findOrFail($id);

        // Chỉ cho từ chối khi đang pending
        if ($ret->status !== ReturnModel::PENDING) {
            return back()->with('error', 'Chỉ được từ chối yêu cầu đang chờ duyệt.');
        }

        $ret->status        = ReturnModel::REJECTED;
        $ret->approved_by   = Auth::id();
        $ret->decided_at    = now();
        $ret->refund_method = null;
        $ret->refund_amount = 0;
        $ret->save();

        // Trả đơn về trạng thái đã giao
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

            // ✅ HOÀN VÍ XONG → CHỜ KHÁCH XÁC NHẬN
            $ret->status     = ReturnModel::WAITING_CUSTOMER_CONFIRM;
            $ret->decided_at = now();
            $ret->save();

            // Đơn hàng: chờ khách xác nhận đã nhận tiền / hàng đổi
            $this->setOrderStatusOnWaitingCustomer($ret);
        });

        return back()->with('success', 'Đã hoàn tiền vào ví. Đang chờ khách xác nhận đã nhận tiền.');
    }
    public function refundManual($id)
    {
        $ret = ReturnModel::with('order')->findOrFail($id);

        // Kiểm tra trạng thái trước khi chuyển đổi
        if (! in_array($ret->status, [ReturnModel::APPROVED, ReturnModel::REFUNDING], true)) {
            return back()->with('error', 'Trạng thái không hợp lệ (chỉ hoàn khi ĐÃ DUYỆT hoặc ĐANG HOÀN).');
        }

        if ($ret->refund_method !== 'manual') {
            return back()->with('error', 'Phương thức không phải hoàn thủ công.');
        }

        // Bắt đầu giao dịch DB
        DB::transaction(function () use ($ret) {
            // ✅ Chuyển trạng thái từ đã duyệt hoặc đang hoàn sang "chờ khách xác nhận"
            $ret->status     = ReturnModel::WAITING_CUSTOMER_CONFIRM; // Cập nhật status = 5
            $ret->decided_at = now(); // Lưu thời gian quyết định
            $ret->save(); // Lưu thay đổi

            // Đơn hàng: chờ khách xác nhận
            $this->setOrderStatusOnWaitingCustomer($ret); // Hàm cập nhật trạng thái cho đơn hàng nếu cần
        });

        // Trả về thông báo thành công
        return back()->with('success', 'Đã đánh dấu đã hoàn tiền cho khách. Đang chờ khách xác nhận.');
    }

    private function setOrderStatusOnApprove(ReturnModel $ret): void
    {
        if (! $ret->order_id) {
            return;
        }

        // ✅ Khi admin bấm DUYỆT → đơn chuyển sang trạng thái CHỜ KHÁCH XÁC NHẬN
        Order::whereKey($ret->order_id)
            ->update([
                'order_status'      => Order::STATUS_RETURN_WAITING_CUSTOMER,
                'status_changed_at' => now(),
            ]);
    }

    private function setOrderStatusOnReject(ReturnModel $ret): void
    {
        if (! $ret->order_id) {
            return;
        }

        // Chỉ trả về shipped nếu hiện tại đang ở return_pending
        Order::whereKey($ret->order_id)
            ->where('order_status', Order::STATUS_RETURN_PENDING)
            ->update([
                'order_status'      => Order::STATUS_SHIPPED,
                'status_changed_at' => now(),
            ]);
    }

    private function setOrderStatusOnCompleted(ReturnModel $ret): void
    {
        if (! $ret->order_id) {
            return;
        }

        // Tuỳ 4 loại action_type
        $newStatus = in_array($ret->action_type, ['refund_full', 'refund_partial'], true)
            ? Order::STATUS_RETURNED     // Hoàn tiền (toàn bộ / 1 phần) -> đơn coi như Hoàn/Trả hàng
            : Order::STATUS_SHIPPED;     // Đổi sản phẩm / đổi size màu -> xử lý xong coi như ĐÃ GIAO

        Order::whereKey($ret->order_id)
            ->update([
                'order_status'      => $newStatus,
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

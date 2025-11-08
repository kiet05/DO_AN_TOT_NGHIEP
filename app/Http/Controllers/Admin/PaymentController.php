<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Services\ZaloPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /** Danh sách + lọc */
    public function index(Request $r): View
    {
        $q = Payment::with('order')
            ->when($r->status, fn($x) => $x->where('status', $r->status))
            ->when($r->gateway, fn($x) => $x->where('gateway', $r->gateway))
            ->when($r->app_trans_id, fn($x) => $x->where('app_trans_id', 'like', '%' . $r->app_trans_id . '%'))
            ->when($r->date_from, fn($x) => $x->whereDate('created_at', '>=', $r->date_from))
            ->when($r->date_to, fn($x) => $x->whereDate('created_at', '<=', $r->date_to))
            ->latest();

        $payments = $q->paginate(20)->withQueryString();
        return view('admin.payments.index', compact('payments'));
    }

    /** Chi tiết giao dịch */
    public function show(Payment $payment): View
    {
        $payment->load('order', 'logs');

        // Ma trận trạng thái cho dropdown (UI)
        $matrix = [
            'pending'  => ['success', 'failed', 'canceled'],
            'failed'   => ['pending', 'canceled'],
            'canceled' => ['pending'],
            'success'  => ['refunded'],
            'refunded' => [], // cuối cùng
        ];

        $allowed = $matrix[$payment->status] ?? [];
        return view('admin.payments.show', compact('payment', 'allowed'));
    }

    /** Cập nhật thủ công trạng thái */
    public function updateStatus(Request $r, Payment $payment): RedirectResponse
    {
        $r->validate(['status' => 'required|string|in:pending,success,failed,canceled,refunded']);

        // Ma trận chuyển đổi
        $matrix = [
            'pending'  => ['success', 'failed', 'canceled'],
            'failed'   => ['pending', 'canceled'],
            'canceled' => ['pending'],
            'success'  => ['refunded'],
            'refunded' => [],
        ];

        $from = $payment->status;
        $to   = $r->status;

        if (!in_array($to, $matrix[$from] ?? [])) {
            return back()->with('error', "Không thể chuyển từ trạng thái [$from] sang [$to].");
        }

        DB::transaction(function () use ($payment, $to) {
            $old = $payment->status;

            // logic cập nhật paid_at
            if ($to === 'success') {
                $payment->update(['status' => $to, 'paid_at' => $payment->paid_at ?? now()]);
                $payment->order?->update([
                    'payment_status' => 'paid',
                    'paid_at'        => $payment->order->paid_at ?? $payment->paid_at,
                ]);
            } elseif ($to === 'refunded') {
                $payment->update(['status' => 'refunded']);
                $payment->order?->update(['payment_status' => 'refunded']);
            } else {
                $payment->update(['status' => $to, 'paid_at' => null]);
                $payment->order?->update(['payment_status' => 'unpaid', 'paid_at' => null]);
            }

            PaymentLog::create([
                'payment_id' => $payment->id,
                'type'       => 'STATUS_MANUAL',
                'message'    => "Admin thay đổi trạng thái từ $old → $to",
            ]);
        });

        return back()->with('status', 'Đã cập nhật trạng thái thành công.');
    }

    /** Đồng bộ trạng thái từ ZaloPay (nút Query) */
    public function query(Payment $payment, ZaloPayService $zp): RedirectResponse
    {
        if (strtolower($payment->gateway) !== 'zalopay') {
            return back()->with('error', 'Chỉ hỗ trợ query ZaloPay.');
        }


        $res = $zp->query($payment->app_trans_id);

        DB::transaction(function () use ($payment, $res) {
            PaymentLog::create([
                'payment_id' => $payment->id,
                'type'       => 'QUERY',
                'payload'    => $res,
            ]);

            $ok = isset($res['return_code']) && (int)$res['return_code'] === 1
                && in_array((int)($res['data']['status'] ?? $res['status'] ?? -1), [1, 2], true);

            if ($ok) {
                $payment->update([
                    'zp_trans_id' => $res['data']['zp_trans_id'] ?? ($res['zp_trans_id'] ?? null),
                    'status'      => 'success',
                    'paid_at'     => $payment->paid_at ?? now(),
                ]);
                $payment->order?->update([
                    'payment_status' => 'paid',
                    'paid_at'        => $payment->order->paid_at ?? $payment->paid_at,
                ]);
            } elseif ($payment->status !== 'success') {
                $payment->update(['status' => 'failed', 'paid_at' => null]);
                $payment->order?->update(['payment_status' => 'unpaid', 'paid_at' => null]);
            }
        });

        return back()->with('status', 'Đã đồng bộ trạng thái.');
    }

    /** Nhật ký */
    public function logs(Payment $payment): View
    {
        $logs = $payment->logs()->latest()->paginate(50);
        return view('admin.payments.logs', compact('payment', 'logs'));
    }

    /** (Tuỳ chọn) Refund */
    public function refund(Payment $payment, ZaloPayService $zp): RedirectResponse
    {
        return back()->with('status', '(Demo) Chưa triển khai refund.');
    }
}

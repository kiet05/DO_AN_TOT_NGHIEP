<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Services\VNPayService;
use Illuminate\Support\Facades\DB;

class VNPayController extends Controller
{
    /** Tạo URL thanh toán */
    public function createPayment(Request $r, VNPayService $vnpay)
    {
        $amount = $r->amount ?? 0;

        if ($amount <= 0) {
            return back()->with('error', 'Số tiền không hợp lệ.');
        }

        DB::beginTransaction();

        try {
            // 1. Tạo Payment record
            $payment = Payment::create([
                'order_id'      => null,        
                'amount'        => $amount,
                'status'        => 'pending',
                'gateway'       => 'vnpay',
                'app_trans_id'  => 'VNP' . time(),
            ]);

            // 2. Tạo link redirect sang VNPay
            $paymentUrl = $vnpay->createPaymentUrl([
                'amount'       => $amount,
                'order_id'     => $payment->id,
                'app_trans_id' => $payment->app_trans_id,
            ]);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'type'       => 'CREATE',
                'payload'    => ['url' => $paymentUrl],
            ]);

            DB::commit();
            return redirect($paymentUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Không tạo được thanh toán!');
        }
    }

    /** VNPay Redirect / Return URL */
    public function return(Request $request, VNPayService $vnpay)
    {
        $response = $vnpay->verifyReturn($request->all());

        $transId = $request->vnp_TxnRef ?? null;

        if (!$transId) {
            return redirect()->route('home')->with('error', 'Không tìm thấy mã giao dịch.');
        }

        $payment = Payment::where('app_trans_id', $transId)->first();

        if (!$payment) {
            return redirect()->route('home')->with('error', 'Giao dịch không tồn tại.');
        }

        PaymentLog::create([
            'payment_id' => $payment->id,
            'type'       => 'RETURN',
            'payload'    => $request->all(),
        ]);

        // Nếu xác minh OK
        if ($response['success']) {
            $payment->update([
                'status'  => 'success',
                'paid_at' => now(),
            ]);

            return redirect()->route('home')
                ->with('success', 'Thanh toán thành công!');
        }

        // Nếu thất bại
        $payment->update(['status' => 'failed']);

        return redirect()->route('home')
            ->with('error', 'Thanh toán thất bại!');
    }
}
 
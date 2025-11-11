<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $paymentMethods = PaymentMethod::withCount('orders')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->paginate(10);

            \Log::info('PaymentMethods loaded: ' . $paymentMethods->count() . ' items');
            
            // Debug: dump data để kiểm tra
            if (config('app.debug')) {
                \Log::info('PaymentMethods data: ' . json_encode($paymentMethods->toArray()));
            }

            return view('admin.payment-methods.index', compact('paymentMethods'));
        } catch (\Exception $e) {
            \Log::error('PaymentMethodController@index error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.payment-methods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:50|unique:payment_methods,slug',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'config' => 'nullable|array',
        ]);

        // Tự động tạo slug nếu không có
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Xử lý config cho VNPay
        if ($validated['slug'] === 'vnpay' && isset($validated['config'])) {
            $validated['config'] = json_encode($validated['config']);
        } else {
            $validated['config'] = null;
        }

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Thêm phương thức thanh toán thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paymentMethod = PaymentMethod::with('orders')->findOrFail($id);
        return view('admin.payment-methods.show', compact('paymentMethod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        
        // Decode config nếu là JSON
        if ($paymentMethod->config && is_string($paymentMethod->config)) {
            $paymentMethod->config = json_decode($paymentMethod->config, true);
        }

        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:50|unique:payment_methods,slug,' . $id,
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'config' => 'nullable|array',
        ]);

        // Tự động tạo slug nếu không có
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Xử lý config cho VNPay
        if ($validated['slug'] === 'vnpay' && isset($validated['config'])) {
            $validated['config'] = json_encode($validated['config']);
        } elseif (!isset($validated['config'])) {
            $validated['config'] = $paymentMethod->config;
        } else {
            $validated['config'] = null;
        }

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Cập nhật phương thức thanh toán thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        // Kiểm tra xem có đơn hàng nào đang sử dụng phương thức này không
        if ($paymentMethod->orders()->count() > 0) {
            return redirect()->route('admin.payment-methods.index')
                ->with('error', 'Không thể xóa phương thức thanh toán này vì đã có đơn hàng sử dụng!');
        }

        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Xóa phương thức thanh toán thành công!');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(string $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->is_active = !$paymentMethod->is_active;
        $paymentMethod->save();

        return redirect()->back()
            ->with('success', 'Cập nhật trạng thái thành công!');
    }
}

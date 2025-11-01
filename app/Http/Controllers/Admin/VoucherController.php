<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\Category;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = Voucher::query()
            ->when($request->keyword, function ($query) use ($request) {
                $k = $request->keyword;
                $query->where('code', 'like', "%$k%")
                    ->orWhere('name', 'like', "%$k%");
            })
            ->orderByDesc('id');

        $vouchers = $q->paginate(15);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('admin.vouchers.create', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'           => 'required|string|max:50|unique:vouchers,code',
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:percent,fixed',
            'value'          => 'required|numeric|min:0',
            'max_discount'   => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'apply_type'     => 'required|in:all,products,categories',
            'usage_limit'    => 'nullable|integer|min:0',
            'start_at'       => 'nullable|date',
            'end_at'         => 'nullable|date|after_or_equal:start_at',
            'is_active'      => 'boolean',
            'products'       => 'array',
            'categories'     => 'array',
        ]);

        $data['discount_type']  = $data['type'];        // 'percent' hoặc 'fixed'
        $data['discount_value'] = $data['value'];       // cùng giá trị

        // ưu tiên end_at, không có thì +30 ngày
        $data['expired_at'] = $data['end_at'] ?? now()->addDays(30);

        $voucher = Voucher::create($data);

        if ($data['apply_type'] === 'products') {
            $voucher->products()->sync($request->input('products', []));
        }

        if ($data['apply_type'] === 'categories') {
            $voucher->categories()->sync($request->input('categories', []));
        }

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Tạo mã khuyến mãi thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voucher $voucher)
    {
        $products = Product::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('admin.vouchers.edit', compact('voucher', 'products', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voucher $voucher)
    {
        $data = $request->validate([
            'code'            => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'name'            => 'nullable|string|max:255',
            'type'            => 'required|in:percent,fixed',
            'value'           => 'required|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'apply_type'      => 'required|in:all,products,categories',
            'usage_limit'     => 'nullable|integer|min:1',
            'start_at'        => 'nullable|date',
            'end_at'          => 'nullable|date|after_or_equal:start_at',
            'is_active'       => 'boolean',
            'products'        => 'array',
            'categories'      => 'array',
        ]);

        $voucher->update($data);

        if ($data['apply_type'] === 'products') {
            $voucher->products()->sync($request->input('products', []));
        } else {
            $voucher->products()->detach();
        }

        if ($data['apply_type'] === 'categories') {
            $voucher->categories()->sync($request->input('categories', []));
        } else {
            $voucher->categories()->detach();
        }

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Cập nhật mã khuyến mãi thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Đã xóa mã khuyến mãi');
    }

    //báo cáo lượt sử dụng
    public function report(Voucher $voucher)
    {
        $usages = $voucher->usages()
            ->with('voucher')
            ->orderByDesc('used_at')
            ->paginate(30);

        return view('admin.vouchers.report', compact('voucher', 'usages'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $q = Voucher::query()
            ->when($request->keyword, function ($query) use ($request) {
                $k = $request->keyword;
                $query->where('code', 'LIKE', "%$k%")
                    ->orWhere('name', 'LIKE', "%$k%");
            })
            ->orderByDesc('id');

        $vouchers = $q->paginate(15);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $products   = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();

        return view('admin.vouchers.create', compact('products', 'categories'));
    }
    public function edit(Voucher $voucher)
    {
        $isUsed = $voucher->used_count > 0;

        $products   = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();

        // Lấy danh sách product/category đang áp dụng
        $selectedProducts   = $voucher->products()->pluck('product_id')->toArray();
        $selectedCategories = $voucher->categories()->pluck('category_id')->toArray();

        return view('admin.vouchers.edit', [
            'voucher'            => $voucher,
            'products'           => $products,
            'categories'         => $categories,
            'selectedProducts'   => $selectedProducts,
            'selectedCategories' => $selectedCategories,
        ]);
    }

    // ========================================================================
    // STORE
    // ========================================================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'  => 'required|string|max:50|unique:vouchers,code',
            'name'  => 'required|string|max:255',

            'type'  => 'required|in:percent,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0',
                Rule::when(
                    $request->type === 'percent',
                    'max:100'
                ),
            ],

            'max_discount'    => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',

            'apply_type'  => 'required|in:all,products,categories',
            'usage_limit' => 'required|integer|min:1',

            'start_at' => 'nullable|date',
            'end_at'   => 'nullable|date|after_or_equal:start_at',

            'is_active' => 'boolean',

            'products' => Rule::when(
                $request->apply_type === 'products',
                ['required', 'array', 'min:1'],
                ['nullable', 'array']
            ),

            'categories' => Rule::when(
                $request->apply_type === 'categories',
                ['required', 'array', 'min:1'],
                ['nullable', 'array']
            ),

        ], [

            // CODE
            'code.required' => 'Mã voucher không được để trống.',
            'code.unique'   => 'Mã voucher đã tồn tại.',

            // NAME
            'name.required' => 'Tên voucher không được để trống.',

            // TYPE & VALUE
            'type.required' => 'Vui lòng chọn kiểu giảm giá.',
            'type.in'       => 'Kiểu giảm giá không hợp lệ.',

            'value.required' => 'Giá trị giảm không được để trống.',
            'value.numeric'  => 'Giá trị giảm phải là số.',
            'value.min'      => 'Giá trị giảm không được nhỏ hơn 0.',
            'value.max'      => 'Giảm phần trăm tối đa là 100%.',

            // APPLY TYPE
            'apply_type.required' => 'Vui lòng chọn phạm vi áp dụng.',
            'apply_type.in'       => 'Phạm vi áp dụng không hợp lệ.',

            // PRODUCTS
            'products.required' => 'Bạn phải chọn ít nhất 1 sản phẩm.',
            'products.array'    => 'Dữ liệu sản phẩm không hợp lệ.',

            // CATEGORIES
            'categories.required' => 'Bạn phải chọn ít nhất 1 danh mục.',
            'categories.array'    => 'Dữ liệu danh mục không hợp lệ.',

            // DATE
            'end_at.after_or_equal' => 'Thời gian kết thúc phải sau hoặc bằng thời gian bắt đầu.',

            // USAGE LIMIT
            'usage_limit.required' => 'Lượt sử dụng không được để trống.',
            'usage_limit.integer' => 'Lượt sử dụng phải là số nguyên.',
            'usage_limit.min'     => 'Lượt sử dụng tối thiểu là 1.',
        ]);

        $voucher = Voucher::create($data);

        if ($data['apply_type'] === 'products') {
            $voucher->products()->sync($request->products);
        }
        if ($data['apply_type'] === 'categories') {
            $voucher->categories()->sync($request->categories);
        }

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Tạo voucher thành công');
    }



    // ========================================================================
    // UPDATE
    // ========================================================================
    public function update(Request $request, Voucher $voucher)
    {
        if ($voucher->used_count > 0) {
            return redirect()->route('admin.vouchers.index')
                ->with('error', 'Mã giảm giá đã được áp dụng, không thể chỉnh sửa!');
        }

        $data = $request->validate([
            'code'  => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'name'  => 'required|string|max:255',

            'type'  => 'required|in:percent,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0',
                Rule::when(
                    $request->type === 'percent',
                    'max:100'
                ),
            ],

            'max_discount'    => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',

            'apply_type'  => 'required|in:all,products,categories',
            'usage_limit' => 'required|integer|min:1',

            'start_at' => 'nullable|date',
            'end_at'   => 'nullable|date|after_or_equal:start_at',

            'is_active' => 'boolean',

            'products' => Rule::when(
                $request->apply_type === 'products',
                ['required', 'array', 'min:1'],
                ['nullable', 'array']
            ),

            'categories' => Rule::when(
                $request->apply_type === 'categories',
                ['required', 'array', 'min:1'],
                ['nullable', 'array']
            ),

        ], [
            // thông báo lỗi giống STORE
            // CODE
            'code.required' => 'Mã voucher không được để trống.',
            'code.unique'   => 'Mã voucher đã tồn tại.',

            // NAME
            'name.required' => 'Tên voucher không được để trống.',

            // TYPE & VALUE
            'type.required' => 'Vui lòng chọn kiểu giảm giá.',
            'type.in'       => 'Kiểu giảm giá không hợp lệ.',

            'value.required' => 'Giá trị giảm không được để trống.',
            'value.numeric'  => 'Giá trị giảm phải là số.',
            'value.min'      => 'Giá trị giảm không được nhỏ hơn 0.',
            'value.max'      => 'Giảm phần trăm tối đa là 100%.',

            // APPLY TYPE
            'apply_type.required' => 'Vui lòng chọn phạm vi áp dụng.',
            'apply_type.in'       => 'Phạm vi áp dụng không hợp lệ.',

            // PRODUCTS
            'products.required' => 'Bạn phải chọn ít nhất 1 sản phẩm.',
            'products.array'    => 'Dữ liệu sản phẩm không hợp lệ.',

            // CATEGORIES
            'categories.required' => 'Bạn phải chọn ít nhất 1 danh mục.',
            'categories.array'    => 'Dữ liệu danh mục không hợp lệ.',

            // DATE
            'end_at.after_or_equal' => 'Thời gian kết thúc phải sau hoặc bằng thời gian bắt đầu.',

            // USAGE LIMIT
            'usage_limit.required' => 'Lượt sử dụng không được để trống.',
            'usage_limit.integer' => 'Lượt sử dụng phải là số nguyên.',
            'usage_limit.min'     => 'Lượt sử dụng tối thiểu là 1.',
        ]);

        $voucher->update($data);

        if ($data['apply_type'] === 'products') {
            $voucher->products()->sync($request->products);
            $voucher->categories()->sync([]);
        } elseif ($data['apply_type'] === 'categories') {
            $voucher->categories()->sync($request->categories);
            $voucher->products()->sync([]);
        } else {
            $voucher->products()->sync([]);
            $voucher->categories()->sync([]);
        }

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Cập nhật voucher thành công');
    }



    public function destroy(Voucher $voucher)
    {
        if ($voucher->used_count > 0) {
            return redirect()->route('admin.vouchers.index')
                ->with('error', 'Mã giảm giá đã được áp dụng, không thể xóa!');
        }

        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Xóa mã giảm giá thành công!');
    }
    public function toggle(Voucher $voucher)
    {
        $voucher->is_active = !$voucher->is_active;
        $voucher->save();

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Cập nhật trạng thái voucher thành công!');
    }


    public function report(Voucher $voucher)
    {
        $usages = $voucher->usages()
            ->with(['voucher', 'user', 'order'])
            ->orderByDesc('used_at')
            ->paginate(30);

        return view('admin.vouchers.report', compact('voucher', 'usages'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $categories = Category::withCount('products')
            ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:categories,name']);
        Category::create($request->only('name'));
        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thàng công!');
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
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
        ]);

        $slug = $request->slug ?: Str::slug($request->name);

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        if ($category->slug == 'empty') {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục trống!');
        }

        $emptyCategory = Category::firstOrCreate(
            ['slug' => 'empty'],
            ['name' => 'Trống']
        );

        Product::where('category_id', $category->id)
            ->update(['category_id' => $emptyCategory->id]);

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công. Các sản phẩm đã được chuyển về danh mục "Trống".');
    }
}

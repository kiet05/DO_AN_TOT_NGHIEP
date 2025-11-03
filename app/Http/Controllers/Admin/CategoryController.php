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
        $keyword = $request->input('s');

        $categories = Category::withCount('products')
            ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Category::create($request->only(['name', 'status', 'parent_id']));
        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công');
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
        $categories = Category::where('id', '!=', $id)->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->only(['name', 'status', 'parent_id']));
        return redirect()->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Không xóa danh mục "Trống"
        if ($category->name == 'Trống') {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục trống!');
        }

        // Lấy hoặc tạo danh "Trống"
        $emptyCategory = Category::firstOrCreate(
            ['name' => 'Trống'],
            ['status' => 1, 'parent_id' => null]
        );

        // Chuyển sản phẩm sang danh "Trống"
        Product::where('category_id', $category->id)
            ->update(['category_id' => $emptyCategory->id]);

        // Nếu category có con, chuyển cha của danh mục con về "Trống"
        Category::where('parent_id', $category->id)
            ->update(['parent_id' => $emptyCategory->id]);

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công. Các sản phẩm và danh mục con đã được chuyển về "Trống".');
    }
}

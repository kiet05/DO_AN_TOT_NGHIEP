<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProductVariantAttribute;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category', 'variants', 'brand');

        // Filter theo từ khóa
        if ($request->filled('s')) {
            $query->where('name', 'like', '%' . $request->s . '%');
        }

        // Filter theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter theo thương hiệu
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        switch ($request->sort) {
            case 'new':
                $query->where('is_new', 1);
                break;
            case 'sale':
                $query->where('is_on_sale', 1);
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        // Lấy danh sách sidebar
        $sizes = AttributeValue::where('type', 'size')->get();
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands', 'sizes'));
    }

    public function showByCategory($id)
    {
        $categories = Category::withCount('products')->get();
        $currentCategory = Category::findOrFail($id);
        $products = $currentCategory->products()->with('images')->get();

        return view('admin.products.index', compact('categories', 'currentCategory', 'products'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        // Tách riêng size / màu / chất liệu theo attribute_id
        $sizes = AttributeValue::where('attribute_id', 2)->get();     // ví dụ: 2 = Size
        $colors = AttributeValue::where('attribute_id', 1)->get();    // ví dụ: 1 = Color
        $materials = AttributeValue::where('attribute_id', 3)->get(); // ví dụ: 3 = Material

        return view('admin.products.create', compact(
            'categories',
            'brands',
            'attributes',
            'sizes',
            'colors',
            'materials'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'image_main' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_new' => 'boolean',
            'is_on_sale' => 'boolean',
            'status' => 'nullable|string',

            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.status' => 'nullable|in:0,1',
            'variants.*.attribute_value_ids' => 'nullable|array',

            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Ảnh chính
            $imageMainPath = null;
            if ($request->hasFile('image_main')) {
                $imageMainPath = $request->file('image_main')->store('products', 'public');
            }

            // Tạo sản phẩm
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'base_price' => $validated['base_price'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'image_main' => $imageMainPath,
                'is_new' => $validated['is_new'] ?? false,
                'is_on_sale' => $validated['is_on_sale'] ?? false,
                'status' => $validated['status'] ?? '1',
            ]);

            // Ảnh phụ
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imgFile) {
                    $imgPath = $imgFile->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $imgPath,
                    ]);
                }
            }

            // Biến thể
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    // SKU unique
                    $sku = $variantData['sku'] ?? null;
                    if (empty($sku) || ProductVariant::where('sku', $sku)->exists()) {
                        $sku = 'SKU-' . strtoupper(Str::random(8));
                    }

                    $status = isset($variantData['status'])
                        ? (int) $variantData['status']   // 1 = Hiện, 0 = Ẩn
                        : 1;

                    $productVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $sku,
                        'price' => $variantData['price'],
                        'quantity' => $variantData['quantity'],
                        'status' => $status,
                    ]);

                    // Gắn thuộc tính (size, màu, chất liệu...)
                    $attributeValueIds = $variantData['attribute_value_ids'] ?? [];
                    $productVariant->attributes()->sync($attributeValueIds);
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'variants', 'images'])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with([
            'images',
            'variants.sizes',
            'variants.colors',
            'variants.materials',
            'variants.attributes', // để sau này cần cũng có luôn
        ])->findOrFail($id);

        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        $sizes = AttributeValue::where('attribute_id', 2)->get();
        $colors = AttributeValue::where('attribute_id', 1)->get();
        $materials = AttributeValue::where('attribute_id', 3)->get();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'brands',
            'attributes',
            'sizes',
            'colors',
            'materials'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'is_new' => 'boolean',
            'is_on_sale' => 'boolean',
            'image_main' => 'nullable|file|image|max:2048',

            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.status' => 'nullable|in:0,1',
            'variants.*.attribute_value_ids' => 'nullable|array',

            'images' => 'nullable|array',
            'images.*' => 'nullable|file|image|max:2048',
        ]);

        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product, $validated, $request) {

            // Cập nhật thông tin cơ bản
            $product->update([
                'name' => $validated['name'],
                'base_price' => $validated['base_price'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'is_new' => $validated['is_new'] ?? 0,
                'is_on_sale' => $validated['is_on_sale'] ?? 0,
                'description' => $validated['description'] ?? null,
            ]);

            // Ảnh chính
            if ($request->hasFile('image_main')) {
                $path = $request->file('image_main')->store('products', 'public');
                $product->update(['image_main' => $path]);
            }

            // Ảnh phụ
            if (!empty($request->file('images'))) {
                $product->images()->delete();

                foreach ($request->file('images') as $img) {
                    $path = $img->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $path,
                    ]);
                }
            }

            // Biến thể
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    $sku = $variantData['sku'] ?? null;

                    // SKU unique (không trùng variant khác)
                    if (
                        empty($sku) ||
                        ProductVariant::where('sku', $sku)
                        ->where('id', '!=', $variantData['id'] ?? 0)
                        ->exists()
                    ) {
                        $sku = 'SKU-' . strtoupper(Str::random(8));
                    }

                    $status = isset($variantData['status'])
                        ? (int) $variantData['status']   // 1 = Hiện, 0 = Ẩn
                        : 1;

                    if (!empty($variantData['id'])) {
                        // Cập nhật biến thể cũ
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant && $variant->product_id == $product->id) {
                            $variant->update([
                                'sku' => $sku,
                                'price' => $variantData['price'] ?? $variant->price,
                                'quantity' => $variantData['quantity'] ?? $variant->quantity,
                                'status' => $status,
                            ]);
                            $variant->attributes()->sync($variantData['attribute_value_ids'] ?? []);
                        }
                    } else {
                        // Tạo biến thể mới
                        $newVariant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $sku,
                            'price' => $variantData['price'] ?? $product->base_price,
                            'quantity' => $variantData['quantity'] ?? 0,
                            'status' => $status,
                        ]);
                        $newVariant->attributes()->sync($variantData['attribute_value_ids'] ?? []);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công!');
    }
}

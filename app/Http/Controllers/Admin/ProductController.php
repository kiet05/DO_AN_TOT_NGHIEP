<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\ProductImage;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('variants', 'category');

        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $products = $query->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
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
            'image_main' => 'nullable|string',
            'is_new' => 'boolean',
            'is_on_sale' => 'boolean',
            'status' => 'nullable|string',
            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.status' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'base_price' => $validated['base_price'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'image_main' => $validated['image_main'] ?? null,
                'is_new' => $validated['is_new'] ?? false,
                'is_on_sale' => $validated['is_on_sale'] ?? false,
                'status' => $validated['status'] ?? '1',
            ]);

            if (!empty($validated['images'])) {
                foreach ($validated['images'] as $img) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $img,
                    ]);
                }
            }

            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variant) {
                    $productVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variant['sku'] ?? null,
                        'price' => $variant['price'],
                        'quantity' => $variant['quantity'],
                        'status' => $variant['status'] ?? 'active',
                    ]);

                    if (!empty($varriant['attribute_value_ids'])) {
                        foreach ($variant['attribute_value_ids'] as $attrValueId) {
                            ProductVariantAttribute::create([
                                'product_variant_id' => $productVariant->id,
                                'attribute_value_id' => $attrValueId,
                            ]);
                        }
                    }
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with(['images', 'variants.attributes'])->findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes'));
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
            'image_main' => 'nullable|string',
            'variants' => 'nullable|array',
            'image' => 'nullable|array',
        ]);

        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product, $validated) {
            // Cập nhật thông tin cơ bản
            $product->update([
                'name' => $validated['name'],
                'base_price' => $validated['base_price'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'image_main' => $validated['image_main'] ?? null,
            ]);

            // Cập nhật ảnh phụ (nếu có)
            if (!empty($validated['image'])) {
                $product->images()->delete();

                foreach ($validated['image'] as $url) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $url,
                    ]);
                }
            }

            // Cập nhật hoặc thêm biến thể
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    if (!empty($variantData['id'])) {
                        // Update biến thể cũ
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant && $variant->product_id == $product->id) {
                            $variant->update([
                                'sku' => $variantData['sku'] ?? $variant->sku,
                                'price' => $variantData['price'] ?? $variant->price,
                                'quantity' => $variantData['quantity'] ?? $variant->quantity,
                                'status' => $variantData['status'] ?? $variant->status,
                            ]);
                            $variant->attributes()->sync($variantData['attribute_value_ids'] ?? []);
                        }
                    } else {
                        // Tạo biến thể mới
                        $newVariant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'] ?? $product->base_price,
                            'quantity' => $variantData['quantity'] ?? 0,
                            'status' => $variantData['status'] ?? 'active',
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
        // $product = Product::findOrFail($id);

        // $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Xóa sản phẩm thành công!');
    }
}

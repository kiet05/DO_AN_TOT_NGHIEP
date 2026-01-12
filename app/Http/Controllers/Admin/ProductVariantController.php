<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('product_variants', 'sku')->ignore($variant->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in([0, 1])],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        DB::transaction(function () use ($request, $validated, $variant) {
            $sku = $validated['sku'] ?? $variant->sku;

            if (empty($sku) || ProductVariant::where('sku', $sku)->where('id', '!=', $variant->id)->exists()) {
                $sku = 'SKU-' . strtoupper(Str::random(8));
            }

            $updateData = [
                'sku' => $sku,
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'status' => (int) $validated['status'],
            ];

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products/variants', 'public');
                $updateData['image_url'] = $path;
            }

            $variant->update($updateData);
        });

        return redirect()
            ->route('admin.products.show', $variant->product_id)
            ->with('success', 'Cập nhật biến thể thành công!');
    }

    public function destroy(ProductVariant $variant)
    {
        $productId = $variant->product_id;

        DB::transaction(function () use ($variant) {
            $variant->attributes()->detach();
            $variant->delete();
        });

        return redirect()
            ->route('admin.products.show', $productId)
            ->with('success', 'Đã xóa biến thể!');
    }
}

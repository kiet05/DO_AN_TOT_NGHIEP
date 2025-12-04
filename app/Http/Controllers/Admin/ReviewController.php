<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $q = Review::with(['user', 'product'])->latest();

        if ($request->filled('status')) {
            $q->where('status', (int)$request->integer('status'));
        }

        if ($request->filled('product_id')) {
            $q->where('product_id', (int)$request->integer('product_id'));
        }

        $reviews = $q->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function show($id)
    {
        $rev = Review::with(['user', 'product', 'order', 'orderItem'])->findOrFail($id);
        return view('admin.reviews.show', compact('rev'));
    }

    public function approve($id)
    {
        $rev = Review::findOrFail($id);
        $rev->status = 1; // 1 = Duyệt
        $rev->save();

        return back()->with('success', 'Đã duyệt đánh giá.');
    }

    public function reject($id)
    {
        $rev = Review::findOrFail($id);
        $rev->status = 2; // 2 = Từ chối
        $rev->save();

        return back()->with('success', 'Đã từ chối đánh giá.');
    }

    public function destroy($id)
    {
        $rev = Review::findOrFail($id);
        $rev->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Đã xoá đánh giá.');
    }
}

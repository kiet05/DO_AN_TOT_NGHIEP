<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class BannerController extends Controller
{
     public function index()
    {
        $banners = Banner::all();
        return view('admin.banners.index', compact('banners'));
    }
    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'image' => $path,
            'link' => $request->link,
            'position' => $request->position,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm banner thành công!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'image|nullable',
        ]);

        $data = $request->only(['title', 'link', 'position', 'status']);

       if ($request->hasFile('image')) {
        // Xóa ảnh cũ (nếu có)
        if ($banner->image && \Storage::disk('public')->exists($banner->image)) {
            \Storage::disk('public')->delete($banner->image);
        }

        // Lưu ảnh mới vào storage/app/public/banners
        $path = $request->file('image')->store('banners', 'public');
        $data['image'] = $path;
    }


        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công!');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Xóa banner thành công!');
    }
}

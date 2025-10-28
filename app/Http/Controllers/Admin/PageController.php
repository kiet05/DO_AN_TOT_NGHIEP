<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        return view('admin.pages.index', compact('pages'));
    }

    // ✅ Thêm hàm create()
    public function create()
    {
        return view('admin.pages.create');
    }

    // ✅ Thêm hàm store() để lưu dữ liệu khi nhấn "Thêm trang"
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'required',
            'published' => 'nullable|boolean'
        ]);

        Page::create([
            'key' => strtoupper($request->slug ?: \Str::slug($request->title)),
            'slug' => $request->slug ?: \Str::slug($request->title),
            'title' => $request->title,
            'content' => $request->content,
            'published' => $request->has('published') ? 1 : 0,
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Thêm trang mới thành công!');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $id,
            'content' => 'required',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => $request->slug ?: \Str::slug($request->title),
            'content' => $request->content,
            'published' => $request->has('published') ? 1 : 0,
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Cập nhật trang thành công!');
    }

    public function destroy($id)
    {
        Page::findOrFail($id)->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Xóa trang thành công!');
    }
    public function show($id)
{
    $page = Page::findOrFail($id);
    return view('admin.pages.show', compact('page'));
}


}

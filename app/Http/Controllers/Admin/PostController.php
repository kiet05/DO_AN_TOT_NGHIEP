<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Danh sách bài viết
     */
    public function index(Request $request)
    {
        $q = Post::query();

        // Đếm trước để gắn badge
        $total     = Post::count();
        $published = Post::whereNotNull('published_at')->count();
        $draft     = Post::whereNull('published_at')->count();

        // Lọc theo ?status=published|draft
        if ($request->status === 'published') {
            $q->whereNotNull('published_at');
        } elseif ($request->status === 'draft') {
            $q->whereNull('published_at');
        }

        $posts = $q->orderByDesc('id')->get();

        return view('admin.posts.index', compact('posts', 'total', 'published', 'draft'));
    }


    /**
     * Form tạo mới
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Lưu bài viết mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required',
            'image'        => 'nullable|image|max:2048',
            // checkbox xuất bản, không lưu cột riêng; chỉ dùng để set published_at
            'is_published' => 'nullable',
        ]);

        // Chỉ lấy field cần lưu (không có slug, không có is_published)
        $data = $request->only(['title', 'content']);

        // Ảnh (nếu có)
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // Trạng thái: published_at = now() nếu checkbox được bật
        $data['published_at'] = $request->boolean('is_published') ? now() : null;

        Post::create($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Bài viết đã được tạo.');
    }

    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required',
            'image'        => 'nullable|image|max:2048',
            'is_published' => 'nullable',
        ]);

        $data = $request->only(['title', 'content']);

        // Nếu có upload ảnh mới -> lưu, và (tùy) xóa ảnh cũ
        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('posts', 'public');

            // Xóa ảnh cũ nếu tồn tại và cùng disk 'public'
            if (!empty($post->image) && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $newPath;
        }

        // Cập nhật trạng thái xuất bản
        $data['published_at'] = $request->boolean('is_published') ? now() : null;

        $post->update($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Bài viết đã được cập nhật.');
    }

    /**
     * Xóa bài viết
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Xóa file ảnh nếu có
        if (!empty($post->image) && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Bài viết đã được xóa.');
    }
}

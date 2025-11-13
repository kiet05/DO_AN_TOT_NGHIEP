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

        // Đếm để hiển thị badge
        $total     = Post::count();
        $published = Post::where('is_published', 1)->count();   // đã xuất bản
        $draft     = Post::where('is_published', 0)->count();   // nháp

        // Lọc theo ?status=published|draft
        if ($request->status === 'published') {
            $q->where('is_published', 1);
        } elseif ($request->status === 'draft') {
            $q->where('is_published', 0);
        }

        // Sắp xếp ID tăng dần
        $posts = $q->orderBy('id', 'asc')->get();

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
            'is_published' => 'nullable',
        ]);

        $data = $request->only(['title', 'content']);

        // Ảnh
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // Trạng thái xuất bản
        $isPublished = $request->boolean('is_published');
        $data['is_published'] = $isPublished;
        $data['published_at'] = $isPublished ? now() : null;

        // Nếu có dùng status để bật/tắt bài viết thì set mặc định = 1
        $data['status'] = 1;

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

        // Ảnh
        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('posts', 'public');

            if (!empty($post->image) && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }

            $data['image'] = $newPath;
        }

        // Trạng thái xuất bản
        $isPublished = $request->boolean('is_published');
        $data['is_published'] = $isPublished;

        // published_at: chỉ set lại khi đổi trạng thái
        if ($isPublished && !$post->published_at) {
            // từ nháp -> xuất bản lần đầu
            $data['published_at'] = now();
        } elseif (!$isPublished) {
            // chuyển về nháp
            $data['published_at'] = null;
        }
        // nếu đã xuất bản trước đó và vẫn giữ xuất bản thì giữ nguyên published_at cũ

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

        if (!empty($post->image) && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Bài viết đã được xóa.');
    }
}

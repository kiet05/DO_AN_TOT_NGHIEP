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
        $status = $request->query('status', 'all');

        // Lọc theo status: all | published | draft | trash
        if ($status === 'trash') {
            $q = Post::onlyTrashed();
        } else {
            $q = Post::query();
            if ($status === 'published') {
                $q->whereNotNull('published_at');
            } elseif ($status === 'draft') {
                $q->whereNull('published_at');
            }
        }

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

        $published = Post::whereNotNull('published_at')->count();
        $draft     = Post::whereNull('published_at')->count();
        $trash     = Post::onlyTrashed()->count();

        // Sắp xếp ID tăng dần
        $posts = $q->orderBy('id', 'asc')->get();

        return view('admin.posts.index', compact('posts', 'total', 'published', 'draft', 'trash'));
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
     * Xóa mềm (chuyển vào thùng rác)
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Bài viết đã được chuyển vào thùng rác.');
    }

    /**
     * Khôi phục từ thùng rác
     */
    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return redirect()
            ->route('admin.posts.index', ['status' => 'trash'])
            ->with('success', 'Bài viết đã được khôi phục.');
    }

    /**
     * Xóa vĩnh viễn
     */
    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);

        if (!empty($post->image) && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->forceDelete();

        return redirect()
            ->route('admin.posts.index', ['status' => 'trash'])
            ->with('success', 'Bài viết đã được xóa vĩnh viễn.');
    }
}

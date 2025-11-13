<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Query dùng chung: chỉ lấy bài ĐÃ XUẤT BẢN
     */
    protected function publishedPosts()
    {
        return Post::query()
            ->where('is_published', 1)  // chỉ bài đã publish
            ->where('status', 1);       // và đang bật (nếu bạn dùng status để tắt/bật)
    }

    public function index(Request $request)
    {
        $query = $this->publishedPosts();

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        $posts = $query
            ->orderBy('published_at', 'desc')  // ưu tiên ngày xuất bản
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('frontend.blog.index', compact('posts'));
    }

    public function show($id)
    {
        // chỉ cho xem bài đã publish + đang bật
        $post = $this->publishedPosts()
            ->where('id', $id)
            ->with(['comments.user'])
            ->firstOrFail();

        return view('frontend.blog.show', compact('post'));
    }

    public function storeComment(Request $request, $id)
    {
        $post = $this->publishedPosts()
            ->where('id', $id)
            ->firstOrFail();

        $data = $request->validate(
            [
                'content' => 'required|string|min:5|max:2000',
            ],
            [
                'content.required' => 'Vui lòng nhập nội dung bình luận.',
                'content.min'      => 'Bình luận tối thiểu 5 ký tự.',
            ]
        );

        $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $data['content'],
            'status'  => 'approved',
        ]);


        return redirect()
            ->route('blog.show', $post->id)
            ->with('success', 'Đã gửi bình luận của bạn.');
    }
}

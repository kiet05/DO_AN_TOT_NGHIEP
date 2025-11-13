@extends('frontend.layouts.app')

@section('title', $post->title)

@push('styles')
    <style>
        .blog-detail-title {
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .blog-detail-meta {
            font-size: 13px;
            color: #777;
        }

        .blog-detail-meta span+span::before {
            content: "•";
            margin: 0 6px;
            color: #b3b3b3;
        }

        .blog-detail-hero-img {
            border-radius: 12px;
            max-height: 420px;
            object-fit: cover;
            width: 100%;
        }

        .blog-detail-content {
            font-size: 15px;
            line-height: 1.7;
            color: #333;
        }

        .blog-detail-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .blog-comment-author {
            font-weight: 600;
            font-size: 14px;
        }

        .blog-comment-time {
            font-size: 12px;
            color: #999;
        }

        .blog-comment-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #111;
            color: #fff;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .blog-sidebar-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .12em;
            padding-bottom: 8px;
            margin-bottom: 12px;
            border-bottom: 2px solid #111827;
        }

        .blog-sidebar-link {
            font-size: 14px;
        }

        .blog-sidebar-time {
            font-size: 12px;
            color: #999;
        }

        /* Sidebar kiểu Kenta: Bài viết mới nhất bên trái */
        .news-sidebar-card {
            background: #fff;
            border-right: 1px solid #e5e7eb;
            padding-right: 20px;
        }

        .news-sidebar-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .news-sidebar-item {
            display: flex;
            gap: 10px;
        }

        .news-sidebar-thumb {
            width: 70px;
            height: 70px;
            object-fit: cover;
            flex-shrink: 0;
            border-radius: 4px;
        }

        .news-sidebar-text-title {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 4px;
            color: #111827;
        }

        .news-sidebar-text-title:hover {
            color: #f97316;
        }

        .news-sidebar-meta {
            font-size: 11px;
            color: #6b7280;
        }

        @media (max-width: 991.98px) {
            .news-sidebar-card {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 18px;
                margin-bottom: 18px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row g-4">

            {{-- Sidebar trái: Bài viết mới nhất --}}
            <div class="col-lg-3 order-2 order-lg-1">
                {{-- Ô tìm kiếm nhanh --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3" style="font-size: 14px;">Tìm kiếm bài viết</h6>
                        <form method="GET" action="{{ route('blog.index') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Nhập từ khóa..." value="{{ request('search') }}">
                                <button class="btn btn-sm btn-outline-dark" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Bài viết mới nhất (kiểu Kenta) --}}
                <aside class="news-sidebar-card">
                    <h6 class="blog-sidebar-title">Bài viết mới nhất</h6>

                    @php
                        $latestPosts = \App\Models\Post::where('is_published', 1)
                            ->where('status', 1)
                            ->orderBy('published_at', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp




                    @if ($latestPosts->isEmpty())
                        <p class="text-muted mb-0">Chưa có bài viết.</p>
                    @else
                        <div class="news-sidebar-list">
                            @foreach ($latestPosts as $item)
                                @php
                                    $thumb = $item->thumbnail ?? ($item->image ?? null);
                                @endphp
                                <div class="news-sidebar-item">
                                    @if ($thumb)
                                        <a href="{{ route('blog.show', $item) }}">
                                            <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $item->title }}"
                                                class="news-sidebar-thumb">
                                        </a>
                                    @endif
                                    <div>
                                        <a href="{{ route('blog.show', $item) }}" class="text-decoration-none">
                                            <div class="news-sidebar-text-title">
                                                {{ \Illuminate\Support\Str::limit($item->title, 70) }}
                                            </div>
                                        </a>
                                        <div class="news-sidebar-meta">
                                            {{ $item->created_at?->format('d.m.Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </aside>
            </div>

            {{-- Nội dung chính bên phải --}}
            <div class="col-lg-9 order-1 order-lg-2">

                {{-- Breadcrumb nhỏ --}}
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb small mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">Trang chủ</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('blog.index') }}">Tin tức</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ \Illuminate\Support\Str::limit($post->title, 40) }}
                        </li>
                    </ol>
                </nav>

                {{-- Tiêu đề và meta --}}
                <header class="mb-4">
                    <h1 class="blog-detail-title mb-2">{{ $post->title }}</h1>
                    <div class="blog-detail-meta">
                        <span>{{ $post->created_at?->format('d.m.Y') }}</span>
                        @php $commentCount = $post->comments()->count(); @endphp
                        <span>{{ $commentCount }} bình luận</span>
                    </div>
                </header>

                {{-- Ảnh hero --}}
                @php $thumb = $post->thumbnail ?? $post->image ?? null; @endphp
                @if ($thumb)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $post->title }}" class="blog-detail-hero-img">
                    </div>
                @endif

                {{-- Nội dung bài viết --}}
                <article class="blog-detail-content mb-5">
                    {!! $post->content !!}
                </article>

                {{-- Bình luận --}}
                <section class="mb-5">
                    <h4 class="mb-3">Bình luận</h4>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Form bình luận --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            @auth
                                <form action="{{ route('blog.comments.store', $post) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Nội dung bình luận</label>
                                        <textarea name="content" rows="3" class="form-control" placeholder="Chia sẻ suy nghĩ của bạn về bài viết này"
                                            required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-dark">
                                        Gửi bình luận
                                    </button>
                                </form>
                            @else
                                <p class="text-muted mb-0">
                                    Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để gửi bình luận.
                                </p>
                            @endauth
                        </div>
                    </div>

                    {{-- Danh sách bình luận --}}
                    @if ($post->comments->isEmpty())
                        <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ cảm nhận.</p>
                    @else
                        <ul class="list-unstyled">
                            @foreach ($post->comments as $comment)
                                <li class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <div class="blog-comment-badge">
                                                @php
                                                    $name = $comment->user->name ?? 'User';
                                                    $initial = mb_substr($name, 0, 1, 'UTF-8');
                                                @endphp
                                                {{ mb_strtoupper($initial, 'UTF-8') }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="blog-comment-author">
                                                    {{ $comment->user->name ?? 'Người dùng' }}
                                                </span>
                                                <span class="blog-comment-time">
                                                    {{ $comment->created_at?->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            <p class="mb-0">
                                                {!! nl2br(e($comment->content)) !!}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

            </div>
        </div>
    </div>
@endsection

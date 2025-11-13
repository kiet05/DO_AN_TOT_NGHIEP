@extends('frontend.layouts.app')

@section('title', 'Tin tức')

@push('styles')
    <style>
        .news-page-wrapper {
            background-color: #ffffff;
        }

        .news-main-heading {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 22px;
            color: #111827;
        }

        /* SIDEBAR TRÁI */

        .news-sidebar-card {
            background: #fff;
            border-right: 1px solid #e5e7eb;
            padding-right: 20px;
        }

        .news-sidebar-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .12em;
            padding-bottom: 8px;
            margin-bottom: 12px;
            border-bottom: 2px solid #111827;
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

        /* CỘT PHẢI – BÀI CHÍNH + DANH SÁCH */

        .news-main-article {
            display: grid;
            grid-template-columns: 1.1fr 1.2fr;
            gap: 20px;
            margin-bottom: 28px;
        }

        .news-main-img {
            width: 100%;
            height: 320px;
            object-fit: cover;
        }

        .news-main-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #111827;
        }

        .news-main-meta {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .news-main-excerpt {
            font-size: 14px;
            color: #4b5563;
        }

        /* CÁC BÀI CÒN LẠI BÊN DƯỚI */

        .news-list-item {
            display: grid;
            grid-template-columns: 0.9fr 1.7fr;
            gap: 20px;
            padding: 20px 0;
            border-top: 1px solid #e5e7eb;
        }

        .news-list-thumb {
            width: 100%;
            height: 210px;
            object-fit: cover;
        }

        .news-list-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #111827;
        }

        .news-list-title:hover {
            color: #f97316;
        }

        .news-list-meta {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 6px;
        }

        .news-list-excerpt {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 0;
        }

        .news-pagination {
            margin-top: 24px;
        }

        @media (max-width: 991.98px) {
            .news-sidebar-card {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 18px;
                margin-bottom: 18px;
            }

            .news-main-article,
            .news-list-item {
                grid-template-columns: 1fr;
            }

            .news-main-img {
                height: 260px;
            }

            .news-list-thumb {
                height: 200px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="news-page-wrapper py-4">
        <div class="container">

            @php
                use Illuminate\Support\Str;

                $featured = $posts->first();
                $others = $posts->slice(1);
            @endphp

            @if (!$featured)
                <p class="text-muted">Chưa có bài viết nào.</p>
            @else
                <div class="row g-4">
                    {{-- Cột trái: Bài viết mới nhất --}}
                    <div class="col-lg-3">
                        <aside class="news-sidebar-card">
                            <h3 class="news-sidebar-title">Bài viết mới nhất</h3>

                            <div class="news-sidebar-list">
                                @foreach ($posts->take(5) as $post)
                                    @php
                                        $thumb = $post->thumbnail ?? ($post->image ?? null);
                                    @endphp
                                    <div class="news-sidebar-item">
                                        @if ($thumb)
                                            <a href="{{ route('blog.show', $post->id) }}">
                                                <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $post->title }}"
                                                    class="news-sidebar-thumb">
                                            </a>
                                        @endif
                                        <div>
                                            <a href="{{ route('blog.show', $post->id) }}" class="text-decoration-none">
                                                <div class="news-sidebar-text-title">
                                                    {{ Str::limit($post->title, 70) }}
                                                </div>
                                            </a>
                                            <div class="news-sidebar-meta">
                                                {{ $post->created_at?->format('d.m.Y') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </aside>
                    </div>

                    {{-- Cột phải: Tin tức --}}
                    <div class="col-lg-9">
                        <h1 class="news-main-heading">EGA Blog</h1>

                        {{-- Bài nổi bật --}}
                        <article class="news-main-article">
                            @php
                                $thumb = $featured->thumbnail ?? ($featured->image ?? null);
                            @endphp

                            @if ($thumb)
                                <a href="{{ route('blog.show', $featured->id) }}">
                                    <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $featured->title }}"
                                        class="news-main-img">
                                </a>
                            @endif

                            <div>
                                <a href="{{ route('blog.show', $featured->id) }}" class="text-decoration-none">
                                    <h2 class="news-main-title">
                                        {{ $featured->title }}
                                    </h2>
                                </a>
                                <div class="news-main-meta">
                                    {{ $featured->created_at?->format('d.m.Y') }}
                                </div>
                                <p class="news-main-excerpt">
                                    {{ Str::limit($featured->excerpt ?? strip_tags($featured->content), 260) }}
                                </p>
                            </div>
                        </article>

                        {{-- Các bài còn lại --}}
                        @foreach ($others as $post)
                            @php
                                $thumb = $post->thumbnail ?? ($post->image ?? null);
                            @endphp
                            <article class="news-list-item">
                                @if ($thumb)
                                    <a href="{{ route('blog.show', $post->id) }}">
                                        <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $post->title }}"
                                            class="news-list-thumb">
                                    </a>
                                @endif

                                <div>
                                    <a href="{{ route('blog.show', $post->id) }}" class="text-decoration-none">
                                        <h3 class="news-list-title">
                                            {{ $post->title }}
                                        </h3>
                                    </a>
                                    <div class="news-list-meta">
                                        {{ $post->created_at?->format('d.m.Y') }}
                                    </div>
                                    <p class="news-list-excerpt">
                                        {{ Str::limit($post->excerpt ?? strip_tags($post->content), 220) }}
                                    </p>
                                </div>
                            </article>
                        @endforeach

                        {{-- Phân trang --}}
                        <div class="news-pagination">
                            {{ $posts->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

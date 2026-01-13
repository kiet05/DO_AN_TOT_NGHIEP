@extends('layouts.admin.master')

@section('title', 'Chi tiết đánh giá')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    <div class="sherah-dsinner">

                        <div class="row mg-top-30">
                            <div class="col-12 sherah-flex-between">
                                <div class="sherah-breadcrumb">
                                    <h2 class="sherah-breadcrumb__title">Chi tiết đánh giá</h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                        <li><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
                                        <li class="active">{{ $rev->id }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-20 p-4">
                            <h4>ID sản phẩm: {{ $rev->id }}</h4>
                            <p><strong>Sản phẩm:</strong> {{ optional($rev->product)->name ?? '(đã xóa)' }}</p>
                            <p><strong>Người đánh giá:</strong> {{ optional($rev->user)->name ?? 'Khách' }}</p>
                            <p><strong>Đánh giá:</strong> {{ $rev->rating }}/5</p>
                            <p><strong>Bình luận:</strong></p>
                            <div class="mb-3">{{ $rev->comment }}</div>

                            @if ($rev->image)
                                <div class="mb-3">
                                    <strong>Ảnh kèm theo:</strong>
                                    <div>
                                        <img src="{{ asset('storage/' . $rev->image) }}" alt="review image"
                                            style="max-width:400px" />
                                    </div>
                                </div>
                            @endif

                            <p><strong>Trạng thái:</strong>
                                @php
                                    $labels = [0 => 'Chờ', 1 => 'Duyệt', 2 => 'Ẩn', 3 => 'Hiện'];
                                    $classes = [
                                        0 => 'badge bg-secondary',
                                        1 => 'badge bg-success',
                                        2 => 'badge bg-danger',
                                        3 => 'badge bg-info',
                                    ];
                                @endphp
                                <span
                                    class="{{ $classes[$rev->status] ?? 'badge bg-secondary' }}">{{ $labels[$rev->status] ?? $rev->status }}</span>
                            </p>

                            <div class="mt-3">
                                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary ms-2">
                                    <i class="fa fa-arrow-left me-1"></i> Trở về
                                </a>
                                @if($rev->status == 0)
                                <form action="{{ route('admin.reviews.approve', $rev->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    <button class="btn btn-success ms-2">Duyệt</button>
                                </form>
                                @endif

                                @if(in_array($rev->status, [0,1,3]))
                                <form action="{{ route('admin.reviews.reject', $rev->id) }}" method="POST"
                                    style="display:inline" class="ms-2">
                                    @csrf
                                    <button class="btn btn-warning">Ẩn</button>
                                </form>
                                @endif

                                @if($rev->status == 2)
                                <form action="{{ route('admin.reviews.unhide', $rev->id) }}" method="POST"
                                    style="display:inline" class="ms-2">
                                    @csrf
                                    <button class="btn btn-info">Hiện</button>
                                </form>
                                @endif

                                <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST"
                                    style="display:inline" class="ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger"
                                        onclick="return confirm('Bạn chắc muốn xoá đánh giá này?')">Xoá</button>
                                </form>

                                
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

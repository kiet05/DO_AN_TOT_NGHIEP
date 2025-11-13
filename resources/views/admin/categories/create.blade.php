@extends('layouts.admin.master')

@section('title', isset($category) ? 'Sửa danh mục' : 'Thêm danh mục')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="sherah-breadcrumb mg-top-30">
                                        <h2 class="sherah-breadcrumb__title">
                                            {{ isset($category) ? 'Sửa danh mục' : 'Thêm danh mục' }}</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a
                                                    href="#">{{ isset($category) ? 'Sửa danh mục' : 'Thêm danh mục' }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="sherah-page-inner sherah-border sherah-basic-page sherah-default-bg mg-top-25 p-0">
                                <form class="sherah-wc__form-main"
                                    action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}"
                                    method="POST">
                                    @csrf
                                    @if (isset($category))
                                        @method('PUT')
                                    @endif

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="product-form-box sherah-border mg-top-30">
                                                <h4 class="form-title m-0">Thông tin danh mục</h4>
                                                <div class="row">
                                                    <!-- Tên danh mục -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Tên danh mục</label>
                                                            <div class="form-group__input">
                                                                <input type="text" name="name"
                                                                    class="sherah-wc__form-input"
                                                                    placeholder="Nhập tên danh mục"
                                                                    value="{{ old('name', $category->name ?? '') }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Parent danh mục -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Danh mục cha</label>
                                                            <select name="parent_id" class="form-group__input">
                                                                <option value="">-- Không có --</option>
                                                                @foreach ($categories as $parent)
                                                                    @if (!isset($category) || $category->id != $parent->id)
                                                                        <option value="{{ $parent->id }}"
                                                                            {{ isset($category) && $category->parent_id == $parent->id ? 'selected' : '' }}>
                                                                            {{ $parent->name }}
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Trạng thái -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Trạng thái</label>
                                                            <select name="status" class="form-group__input">
                                                                <option value="1"
                                                                    {{ isset($category) && $category->status == 1 ? 'selected' : '' }}>
                                                                    Hiển thị</option>
                                                                <option value="0"
                                                                    {{ isset($category) && $category->status == 0 ? 'selected' : '' }}>
                                                                    Ẩn</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mg-top-40 sherah-dflex sherah-dflex-gap-30 justify-content-end">
                                                <button type="submit" class="sherah-btn sherah-btn__primary">
                                                    {{ isset($category) ? 'Cập nhật' : 'Thêm danh mục' }}
                                                </button>
                                                <a href="{{ route('admin.categories.index') }}"
                                                    class="sherah-btn sherah-btn__third">Hủy</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
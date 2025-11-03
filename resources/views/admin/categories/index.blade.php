@extends('layouts.admin.master')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <!-- Dashboard Inner -->
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Danh sách danh mục</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="order-list.html">Danh mục</a></li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                    <a href="{{ route('admin.categories.create') }}" class="sherah-btn sherah-gbcolor">Update danh mục</a>
                                </div>
                            </div>
                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <table id="sherah-table__vendor" class="sherah-table__main sherah-table__main-v3">
                                    <!-- sherah Table Head -->
                                    <thead class="sherah-table__head">
                                        <tr>
                                            <th class="sherah-table__column-1 sherah-table__h1">ID</th>
                                            <th class="sherah-table__column-2 sherah-table__h2">Tên danh mục</th>
                                            <th class="sherah-table__column-3 sherah-table__h3">Số sản phẩm</th>
                                            <th class="sherah-table__column-4 sherah-table__h4">Trạng thái</th>
                                            <th class="sherah-table__column-5 sherah-table__h5">Ngày tạo</th>
                                            <th class="sherah-table__column-6 sherah-table__h6">Ngày cập nhật</th>
                                            <th class="sherah-table__column-7 sherah-table__h7">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sherah-table__body">
                                        @forelse ($categories as $category)
                                            <tr>
                                                <!-- ID -->
                                                <td class="sherah-table__column-1 sherah-table__data-1">
                                                    <div class="sherah-language-form__input">
                                                        {{-- <input class="sherah-language-form__check" type="checkbox"> --}}
                                                        <p class="crany-table__product--number"><a href="#"
                                                                class="sherah-color1">#{{ $category->id }}</a></p>
                                                    </div>
                                                </td>

                                                <!-- Tên danh mục -->
                                                <td class="sherah-table__column-2 sherah-table__data-2">
                                                    <div class="sherah-table__product-content">
                                                        <p class="sherah-table__product-desc">{{ $category->name }}</p>
                                                    </div>
                                                </td>

                                                <!-- Số sản phẩm -->
                                                <td class="sherah-table__column-3 sherah-table__data-3">
                                                    <p class="sherah-table__product-desc">{{ $category->products_count }}
                                                    </p>
                                                </td>

                                                <!-- Trạng thái -->
                                                <td class="sherah-table__column-4 sherah-table__data-4">
                                                    <div class="sherah-table__product-content">
                                                        <div
                                                            class="sherah-table__status {{ $category->status ? 'sherah-color3' : 'sherah-color2' }}">
                                                            {{ $category->status ? 'Hoạn động' : 'Ẩn' }}
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Ngày tạo -->
                                                <td class="sherah-table__column-5 sherah-table__data-5">
                                                    <div class="sherah-table__product-content">
                                                        <p class="sherah-table__product-desc">
                                                            {{ $category->created_at->format('d M, Y H:i') }}</p>
                                                    </div>
                                                </td>

                                                <!-- Ngày cập nhật -->
                                                <td class="sherah-table__column-6 sherah-table__data-6">
                                                    <div class="sherah-table__product-content">
                                                        <p class="sherah-table__product-desc">
                                                            {{ $category->updated_at->format('d M, Y H:i') }}</p>
                                                    </div>
                                                </td>

                                                <!-- Hành động -->
                                                <td class="sherah-table__column-7 sherah-table__data-7">
                                                    <div class="sherah-table__status__group d-flex gap-1">
                                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                            class="sherah-table__action"
                                                            style="background-color: #3b82f6; color: white; border-radius: 4px; padding: 4px 8px;">
                                                            Sửa
                                                        </a>

                                                        <form
                                                            action="{{ route('admin.categories.destroy', $category->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="sherah-table__action"
                                                                style="background-color: #ef4444; color: white; border-radius: 4px; padding: 4px 8px;">
                                                                Xóa
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Không có danh mục nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="row mg-top-40">
                                    <div class="sherah-pagination">
                                        <ul class="sherah-pagination__list">
                                            {{-- Trang trước --}}
                                            <li
                                                class="sherah-pagination__button {{ $categories->onFirstPage() ? 'disabled' : '' }}">
                                                <a href="{{ $categories->previousPageUrl() }}"><i
                                                        class="fas fa-angle-left"></i></a>
                                            </li>

                                            {{-- Các số trang --}}
                                            @for ($i = 1; $i <= $categories->lastPage(); $i++)
                                                <li class="{{ $categories->currentPage() == $i ? 'active' : '' }}">
                                                    <a
                                                        href="{{ $categories->url($i) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</a>
                                                </li>
                                            @endfor

                                            {{-- Trang tiếp --}}
                                            <li
                                                class="sherah-pagination__button {{ $categories->currentPage() == $categories->lastPage() ? 'disabled' : '' }}">
                                                <a href="{{ $categories->nextPageUrl() }}"><i
                                                        class="fas fa-angle-right"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Dashboard Inner -->
                    </div>
                </div>


            </div>
        </div>
    </section>
@endsection

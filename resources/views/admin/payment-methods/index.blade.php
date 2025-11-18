@extends('layouts.admin.master')

@section('content')
    <style>
        .sherah-table__main {
            table-layout: fixed;
            border-collapse: collapse;
            width: 100%;
        }

        .sherah-table__main th,
        .sherah-table__main td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        /* Nếu theme set block cho thead/tbody -> ép về mặc định để không lệch cột */
        .sherah-table__head {
            display: table-header-group !important;
        }

        .sherah-table__body {
            display: table-row-group !important;
        }
    </style>

    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Phương thức thanh toán</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="#">Phương thức thanh toán</a></li>
                                        </ul>
                                    </div>
                                    <a href="{{ route('admin.payment-methods.create') }}" class="sherah-btn sherah-gbcolor">Thêm phương thức</a>
                                </div>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success mg-top-20">{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger mg-top-20">{{ session('error') }}</div>
                            @endif

                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <table id="payment-methods-table" class="sherah-table__main sherah-table__main-v3">
                                    <thead class="sherah-table__head">
                                        <tr>
                                            <th class="sherah-table__column-1 sherah-table__h1">ID</th>
                                            <th class="sherah-table__column-2 sherah-table__h2">Tên</th>
                                            <th class="sherah-table__column-3 sherah-table__h3">Slug</th>
                                            <th class="sherah-table__column-4 sherah-table__h4">Số đơn hàng</th>
                                            <th class="sherah-table__column-5 sherah-table__h5">Trạng thái</th>
                                            <th class="sherah-table__column-6 sherah-table__h6">Thứ tự</th>
                                            <th class="sherah-table__column-7 sherah-table__h7">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sherah-table__body">
                                        @forelse ($paymentMethods as $method)
                                            <tr>
                                                <td class="sherah-table__column-1 sherah-table__data-1">
                                                    <p class="crany-table__product--number">
                                                        <a href="#" class="sherah-color1">#{{ $method->id }}</a>
                                                    </p>
                                                </td>
                                                <td class="sherah-table__column-2 sherah-table__data-2">
                                                    <div class="sherah-table__product-content">
                                                        <p class="sherah-table__product-desc">{{ $method->display_name }}</p>
                                                        @if($method->description)
                                                            <small class="text-muted">
                                                                {{ mb_strlen($method->description) > 50 ? mb_substr($method->description, 0, 50) . '...' : $method->description }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="sherah-table__column-3 sherah-table__data-3">
                                                    <p class="sherah-table__product-desc">
                                                        <code>{{ $method->slug }}</code>
                                                    </p>
                                                </td>
                                                <td class="sherah-table__column-4 sherah-table__data-4">
                                                    <p class="sherah-table__product-desc">{{ $method->orders_count ?? 0 }}</p>
                                                </td>
                                                <td class="sherah-table__column-5 sherah-table__data-5">
                                                    <div class="sherah-table__product-content">
                                                        <div class="sherah-table__status {{ $method->is_active ? 'sherah-color3' : 'sherah-color2' }}">
                                                            {{ $method->is_active ? 'Hoạt động' : 'Tắt' }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="sherah-table__column-6 sherah-table__data-6">
                                                    <p class="sherah-table__product-desc">{{ $method->sort_order }}</p>
                                                </td>
                                                <td class="sherah-table__column-7 sherah-table__data-7">
                                                    <div class="sherah-table__status__group d-flex gap-1">
                                                        <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="sherah-table__action" style="background-color: #3b82f6; color: white; border-radius: 4px; padding: 4px 8px; text-decoration: none;">
                                                            Sửa
                                                        </a>
                                                        <form action="{{ route('admin.payment-methods.toggle-status', $method->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="sherah-table__action" style="background-color: {{ $method->is_active ? '#f59e0b' : '#10b981' }}; color: white; border-radius: 4px; padding: 4px 8px; border: none; cursor: pointer;">
                                                                {{ $method->is_active ? 'Tắt' : 'Bật' }}
                                                            </button>
                                                        </form>
                                                        @if(($method->orders_count ?? 0) == 0)
                                                            <form action="{{ route('admin.payment-methods.destroy', $method->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="sherah-table__action" style="background-color: #ef4444; color: white; border-radius: 4px; padding: 4px 8px; border: none; cursor: pointer;">
                                                                    Xóa
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Không có phương thức thanh toán nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                @if($paymentMethods->hasPages())
                                    <div class="row mg-top-40">
                                        <div class="sherah-pagination">
                                            <ul class="sherah-pagination__list">
                                                @if($paymentMethods->onFirstPage())
                                                    <li class="sherah-pagination__button disabled">
                                                        <span><i class="fas fa-angle-left"></i></span>
                                                    </li>
                                                @else
                                                    <li class="sherah-pagination__button">
                                                        <a href="{{ $paymentMethods->previousPageUrl() }}"><i class="fas fa-angle-left"></i></a>
                                                    </li>
                                                @endif

                                                @for ($i = 1; $i <= $paymentMethods->lastPage(); $i++)
                                                    <li class="{{ $paymentMethods->currentPage() == $i ? 'active' : '' }}">
                                                        <a href="{{ $paymentMethods->url($i) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</a>
                                                    </li>
                                                @endfor

                                                @if($paymentMethods->hasMorePages())
                                                    <li class="sherah-pagination__button">
                                                        <a href="{{ $paymentMethods->nextPageUrl() }}"><i class="fas fa-angle-right"></i></a>
                                                    </li>
                                                @else
                                                    <li class="sherah-pagination__button disabled">
                                                        <span><i class="fas fa-angle-right"></i></span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // Prevent DataTable from auto-initializing on this page
        $(document).ready(function() {
            // Override DataTable if it exists
            if (typeof $.fn.DataTable !== 'undefined') {
                var originalDataTable = $.fn.DataTable;
                $.fn.DataTable = function() {
                    // Only initialize if it's not our payment methods table
                    if (this.selector && this.selector.includes('payment-methods-table')) {
                        return this;
                    }
                    return originalDataTable.apply(this, arguments);
                };
            }
        });
    </script>
    @endpush
@endsection

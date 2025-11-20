@extends('frontend.layouts.app')

@section('title', 'Sản phẩm - ' . config('app.name'))

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}">
                        <!-- Category Filter -->
                        @if(isset($categories) && $categories->count() > 0)
                        <div class="mb-4">
                            <h6>Danh mục</h6>
                            @foreach($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" 
                                    value="{{ $category->id }}" 
                                    id="cat-{{ $category->id }}"
                                    onchange="this.form.submit()"
                                    {{ request('category') == $category->id ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat-{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        <!-- Sale Filter -->
                        <div class="mb-4">
                            <h6>Khuyến mãi</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sale" value="1" 
                                    id="sale-filter"
                                    {{ request('sale') ? 'checked' : '' }}>
                                <label class="form-check-label" for="sale-filter">
                                    Chỉ sản phẩm giảm giá
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-dark w-100">Áp dụng</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Xóa bộ lọc</a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products List -->
        <div class="col-md-9">
            <!-- Sort & View -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span>Tìm thấy {{ $products->total() }} sản phẩm</span>
                </div>
                <div>
                    <select class="form-select form-select-sm" onchange="window.location.href=this.value">
                        <option value="{{ route('products.index', array_merge(request()->all(), ['sort' => 'latest'])) }}" 
                            {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ route('products.index', array_merge(request()->all(), ['sort' => 'price_asc'])) }}" 
                            {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                        <option value="{{ route('products.index', array_merge(request()->all(), ['sort' => 'price_desc'])) }}" 
                            {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                        <option value="{{ route('products.index', array_merge(request()->all(), ['sort' => 'name'])) }}" 
                            {{ request('sort') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                    </select>
                </div>
            </div>
            
            <!-- Products Grid -->
            @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-6 col-md-4">
                    @include('frontend.partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-5">
                {{ $products->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Không tìm thấy sản phẩm nào</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection


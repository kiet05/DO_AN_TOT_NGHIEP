@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Mã *</label>
        <input type="text" name="code" class="form-control"
               value="{{ old('code', $voucher->code ?? '') }}" required>
    </div>
    <div class="col-md-8">
        <label class="form-label">Tên hiển thị</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $voucher->name ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Kiểu giảm</label>
        <select name="type" class="form-select">
            <option value="percent" @selected(old('type', $voucher->type ?? '') == 'percent')>Giảm %</option>
            <option value="fixed" @selected(old('type', $voucher->type ?? '') == 'fixed')>Giảm tiền</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Giá trị *</label>
        <input type="number" step="0.01" min="0" name="value" class="form-control"
               value="{{ old('value', $voucher->value ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Giảm tối đa</label>
        <input type="number" step="0.01" min="0" name="max_discount" class="form-control"
               value="{{ old('max_discount', $voucher->max_discount ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Đơn tối thiểu</label>
        <input type="number" step="0.01" min="0" name="min_order_value" class="form-control"
               value="{{ old('min_order_value', $voucher->min_order_value ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Áp dụng cho</label>
        <select name="apply_type" id="apply_type" class="form-select">
            <option value="all" @selected(old('apply_type', $voucher->apply_type ?? '') == 'all')>Tất cả</option>
            <option value="products" @selected(old('apply_type', $voucher->apply_type ?? '') == 'products')>Sản phẩm chọn</option>
            <option value="categories" @selected(old('apply_type', $voucher->apply_type ?? '') == 'categories')>Danh mục chọn</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Số lượt tối đa</label>
        <input type="number" name="usage_limit" min="1" class="form-control"
               value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Bắt đầu</label>
        <input type="datetime-local" name="start_at" class="form-control"
               value="{{ old('start_at', isset($voucher->start_at) ? $voucher->start_at->format('Y-m-d\TH:i') : '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Kết thúc</label>
        <input type="datetime-local" name="end_at" class="form-control"
               value="{{ old('end_at', isset($voucher->end_at) ? $voucher->end_at->format('Y-m-d\TH:i') : '') }}">
    </div>

    <div class="col-md-2 d-flex align-items-center">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $voucher->is_active ?? 1))>
            <label class="form-check-label">Kích hoạt</label>
        </div>
    </div>

    {{-- chỗ này sau này bạn gắn select2 sản phẩm / danh mục vào --}}
</div>

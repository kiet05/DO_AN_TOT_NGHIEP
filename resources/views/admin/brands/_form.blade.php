<style>
    .brand-form .form-label {
        font-weight: 500;
        margin-bottom: 6px;
    }

    .brand-form .form-control,
    .brand-form .form-select {
        border-radius: 8px;
        height: 44px;
    }

    .brand-form textarea.form-control {
        height: auto;
        min-height: 100px;
        resize: vertical;
    }

    .brand-logo-preview {
        width: 120px;
        height: 120px;
        object-fit: contain;
        border-radius: 10px;
        border: 1px dashed #ddd;
        background: #fafafa;
        padding: 8px;
    }
</style>

<div class="brand-form">

    {{-- Tên brand --}}
    <div class="mb-4">
        <label class="form-label">Tên brand</label>
        <input type="text"
               name="name"
               class="form-control"
               placeholder="Nhập tên brand"
               value="{{ old('name', $brand->name ?? '') }}"
               required>
    </div>

    {{-- Logo --}}
    <div class="mb-4">
        <label class="form-label">Logo</label>
        <input type="file"
               name="logo"
               class="form-control"
               accept="image/*">

        @if(!empty($brand->logo))
            <div class="mt-3">
                <img src="{{ asset('storage/'.$brand->logo) }}"
                     class="brand-logo-preview"
                     alt="Brand logo">
            </div>
        @endif
    </div>

    {{-- Mô tả --}}
    <div class="mb-4">
        <label class="form-label">Mô tả</label>
        <textarea name="description"
                  class="form-control"
                  placeholder="Mô tả brand">{{ old('description', $brand->description ?? '') }}</textarea>
    </div>

    {{-- Trạng thái --}}
    <div class="mb-4">
        <label class="form-label">Trạng thái</label>
        <select name="status" class="form-select">
            <option value="1" @selected(($brand->status ?? 1) == 1)>Hoạt động</option>
            <option value="0" @selected(($brand->status ?? 1) == 0)>Tạm tắt</option>
        </select>
    </div>

</div>

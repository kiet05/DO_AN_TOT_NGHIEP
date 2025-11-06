@extends('layouts.admin.master')

@section('content')
<div class="container mt-4">
    <h3>Chỉnh sửa Admin</h3>

    <form action="{{ route('admin.accounts.update', $user->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Vai trò</label>
            <select name="role_id" class="form-select">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $role->id == $user->role_id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" {{ $user->status ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ !$user->status ? 'selected' : '' }}>Khóa</option>
            </select>
        </div>

        <button class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection

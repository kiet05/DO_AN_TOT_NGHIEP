@extends('layouts.admin.master')

@section('content')
<section class="sherah-adashboard sherah-show">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sherah-body">
                    <div class="sherah-dsinner">

                        {{-- Breadcrumb --}}
                        <div class="sherah-breadcrumb mb-4">
                            <h2 class="sherah-breadcrumb__title">Cập nhật Brand</h2>
                            <ul class="sherah-breadcrumb__list">
                                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li><a href="{{ route('admin.brands.index') }}">Brands</a></li>
                                <li class="active">Chỉnh sửa</li>
                            </ul>
                        </div>

                        {{-- Alert --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Form --}}
                        <div class="sherah-default-bg sherah-border p-4 rounded">
                            <form method="POST"
                                  action="{{ route('admin.brands.update', $brand) }}"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                @include('admin.brands._form')

                                <div class="mt-4 d-flex gap-2">
                                    <button class="btn btn-success">
                                        <i class="bi bi-save me-1"></i> Cập nhật
                                    </button>

                                    <a href="{{ route('admin.brands.index') }}"
                                       class="btn btn-secondary">
                                        Quay lại
                                    </a>
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

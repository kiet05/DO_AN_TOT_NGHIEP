@extends('layouts.admin.master')

@section('content')
<section class="sherah-adashboard sherah-show">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sherah-body">
                    <div class="sherah-dsinner">

                        <h2 class="mb-4">Thêm Brand</h2>

                        <form method="POST"
                              action="{{ route('admin.brands.store') }}"
                              enctype="multipart/form-data">
                            @csrf

                            @include('admin.brands._form')

                            <button class="btn btn-success">
                                <i class="bi bi-save me-1"></i> Lưu
                            </button>
                            <a href="{{ route('admin.brands.index') }}"
                               class="btn btn-secondary ms-2">
                                Quay lại
                            </a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

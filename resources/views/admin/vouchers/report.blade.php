@extends('admin.layouts.app')

@section('title', 'Báo cáo sử dụng voucher')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-3">Báo cáo sử dụng voucher: {{ $voucher->code }} – {{ $voucher->name }}</h1>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Người dùng</th>
                            <th>Đơn hàng</th>
                            <th>Thời gian dùng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usages as $index => $usage)
                            <tr>
                                <td>{{ $usages->firstItem() + $index }}</td>
                                <td>
                                    @if($usage->user)
                                        {{ $usage->user->name }} ({{ $usage->user->email }})
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($usage->order)
                                        ĐH #{{ $usage->order->id }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ optional($usage->used_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-4">Chưa có lượt sử dụng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $usages->links() }}
            </div>
        </div>
    </div>
@endsection
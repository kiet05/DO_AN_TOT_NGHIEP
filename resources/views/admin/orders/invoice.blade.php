<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn #{{ $order->id }}</title>

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #333;
            background: #f5f5f5;
            margin: 0;
            padding: 24px;
        }

        .invoice-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 24px 28px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
        }

        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .company-info,
        .order-info {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .muted {
            color: #777;
            font-size: 12px;
        }

        .order-info {
            text-align: right;
        }

        .order-info h2 {
            margin: 0 0 8px;
            font-size: 18px;
        }

        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            text-transform: uppercase;
            background: #f5f5f5;
        }

        .badge-status.completed {
            color: #1e8449;
            background: #eafaf1;
        }

        .badge-status.pending {
            color: #b9770e;
            background: #fcf3cf;
        }

        .badge-status.canceled {
            color: #c0392b;
            background: #fdecea;
        }

        .section-title {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 24px 0 8px;
            font-weight: bold;
        }

        .box {
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            padding: 12px 14px;
            font-size: 13px;
            background: #fcfcfc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        th, td {
            border: 1px solid #e5e5e5;
            padding: 8px 10px;
        }

        th {
            background: #fafafa;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: .4px;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }

        .no-items-row td {
            font-style: italic;
            color: #777;
        }

        tfoot th {
            font-weight: 600;
        }

        .total-row th {
            font-size: 15px;
        }

        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #d35400;
        }

        .mt-3 { margin-top: 18px; }

        .note {
            font-size: 11px;
            color: #777;
            margin-top: 16px;
        }
    </style>
</head>
<body>
<div class="invoice-wrapper">

    {{-- Header hóa đơn --}}
    <div class="invoice-header">
        <div class="company-info">
            <div class="company-name">EGA SHOP</div>
            <div class="muted">
                Địa chỉ cửa hàng<br>
                SĐT: 0123 456 789<br>
                Email: support@example.com
            </div>
        </div>

        <div class="order-info">
            <h2>Hóa đơn #{{ $order->id }}</h2>
            <div>Ngày tạo: {{ optional($order->created_at)->format('d/m/Y H:i') }}</div>
            <div>Mã khách hàng: {{ $order->user_id ?? 'N/A' }}</div>

            <div class="mt-3">
                <span class="badge-status {{ $order->order_status }}">
                    {{ ucfirst($order->order_status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Thông tin khách hàng --}}
    <div class="section-title">Thông tin khách hàng</div>
    <div class="box">
        <strong>{{ $order->receiver_name }}</strong><br>
        Điện thoại: {{ $order->receiver_phone }}<br>
        Địa chỉ: {{ $order->receiver_address }}
    </div>

    {{-- Bảng sản phẩm --}}
    <div class="section-title">Chi tiết sản phẩm</div>

    @php
        $subtotal = 0;
        $items    = $order->items ?? [];
    @endphp

    <table>
        <thead>
        <tr>
            <th style="width: 45%;">Sản phẩm</th>
            <th style="width: 15%;" class="text-end">Đơn giá</th>
            <th style="width: 10%;" class="text-end">SL</th>
            <th style="width: 15%;" class="text-end">Thành tiền</th>
        </tr>
        </thead>

        <tbody>
        @forelse($items as $item)
            @php
                $lineTotal = $item->price * $item->quantity;
                $subtotal += $lineTotal;
            @endphp
            <tr>
                <td>
                    {{ $item->product->name ?? 'Sản phẩm đã xóa' }}
                </td>
                <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                <td class="text-end">{{ $item->quantity }}</td>
                <td class="text-end">{{ number_format($lineTotal, 0, ',', '.') }}đ</td>
            </tr>
        @empty
            <tr class="no-items-row">
                <td colspan="4" class="text-center">
                    Đơn hàng này chưa có sản phẩm.
                </td>
            </tr>
        @endforelse
        </tbody>

        @php
            $shipping = $order->shipping_fee ?? 0;
            $total    = $subtotal + $shipping;
        @endphp

        <tfoot>
        <tr>
            <th colspan="3" class="text-end">Tạm tính</th>
            <th class="text-end">{{ number_format($subtotal, 0, ',', '.') }}đ</th>
        </tr>
        <tr>
            <th colspan="3" class="text-end">Phí ship</th>
            <th class="text-end">{{ number_format($shipping, 0, ',', '.') }}đ</th>
        </tr>
        <tr class="total-row">
            <th colspan="3" class="text-end">Tổng thanh toán</th>
            <th class="text-end total-amount">
                {{ number_format($total, 0, ',', '.') }}đ
            </th>
        </tr>
        </tfoot>
    </table>

    <div class="note">
        Hóa đơn được tạo tự động từ hệ thống. Nếu có thắc mắc về nội dung hóa đơn,
        vui lòng liên hệ bộ phận chăm sóc khách hàng để được hỗ trợ.
    </div>
</div>
</body>
</html>

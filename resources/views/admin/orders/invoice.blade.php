<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn #{{ $order->id }}</title>

    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; background: #fff; margin: 24px; }
        h2, h3 { margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td {
            border: 1px solid #ddd; padding: 8px; font-size: 14px;
        }
        th { background: #f3f3f3; }
        .text-end { text-align: right; }
        .total {
            font-size: 18px; font-weight: bold; color: #d35400;
        }
        .header-box, .info-box {
            border: 1px solid #ddd; padding: 12px; margin-bottom: 14px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="header-box">
        <h2>🧾 HÓA ĐƠN BÁN HÀNG</h2>
        <strong>Mã đơn hàng:</strong> #{{ $order->id }}<br>
        <strong>Ngày tạo:</strong> {{ optional($order->created_at)->format('d/m/Y H:i') }}<br>
        <strong>Trạng thái:</strong> {{ $order->order_status }}
    </div>

    <div class="info-box">
        <h3>📌 Thông tin khách hàng</h3>
        <strong>Họ tên:</strong> {{ $order->receiver_name }}<br>
        <strong>Điện thoại:</strong> {{ $order->receiver_phone }}<br>
        <strong>Địa chỉ:</strong> {{ $order->receiver_address }}
    </div>

    <h3>📦 Sản phẩm</h3>
    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th class="text-end">Giá</th>
                <th class="text-end">SL</th>
                <th class="text-end">Thành tiền</th>
            </tr>
        </thead>

        <tbody>
        @php $subtotal = 0; @endphp

        @foreach($order->items as $item)
            @php
                $lineTotal = $item->price * $item->quantity;
                $subtotal += $lineTotal;
            @endphp
            <tr>
                <td>{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</td>
                <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                <td class="text-end">{{ $item->quantity }}</td>
                <td class="text-end">{{ number_format($lineTotal, 0, ',', '.') }}đ</td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Tạm tính</th>
                <th class="text-end">{{ number_format($subtotal, 0, ',', '.') }}đ</th>
            </tr>
            <tr>
                <th colspan="3" class="text-end">Phí ship</th>
                <th class="text-end">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</th>
            </tr>
            <tr>
                <th colspan="3" class="text-end">Tổng thanh toán</th>
                <th class="text-end total">
                    {{ number_format($order->final_amount, 0, ',', '.') }}đ
                </th>
            </tr>
        </tfoot>
    </table>

</body>
</html>

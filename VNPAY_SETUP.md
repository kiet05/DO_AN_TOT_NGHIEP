# Hướng dẫn cấu hình VNPay

## Thông tin cần cung cấp từ VNPay

Để tích hợp VNPay vào hệ thống, bạn cần cung cấp các thông tin sau từ tài khoản VNPay của bạn:

### 1. Thông tin tài khoản VNPay

Bạn cần đăng ký tài khoản tại: https://sandbox.vnpayment.vn/ (cho môi trường test) hoặc https://www.vnpayment.vn/ (cho môi trường production)

### 2. Các thông tin cần cung cấp:

#### a) TMN Code (Terminal ID)
- Đây là mã định danh cửa hàng của bạn trên hệ thống VNPay
- Ví dụ: `ABC12345`

#### b) Hash Secret

- Đây là khóa bí mật để tạo chữ ký xác thực giao dịch
- Được cung cấp trong phần cài đặt tài khoản VNPay
- Ví dụ: `ABCDEFGHIJKLMNOPQRSTUVWXYZ123456`

#### c) URL thanh toán
- **Sandbox (Test)**: `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html`
- **Production**: `https://www.vnpayment.vn/paymentv2/vpcpay.html`

#### d) Return URL
- URL mà VNPay sẽ redirect về sau khi thanh toán xong
- Ví dụ: `http://yourdomain.com/payment/vnpay/return`
- Hoặc: `https://yourdomain.com/payment/vnpay/return`

## Cách cấu hình

### Bước 1: Thêm vào file `.env`

Mở file `.env` và thêm các dòng sau:

```env
# VNPay Configuration
VNPAY_TMN_CODE=YOUR_TMN_CODE_HERE
VNPAY_HASH_SECRET=YOUR_HASH_SECRET_HERE
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://127.0.0.1:8000/payment/vnpay/return
VNPAY_SANDBOX=true
```

**Lưu ý:**
- Thay `YOUR_TMN_CODE_HERE` bằng TMN Code thực tế của bạn
- Thay `YOUR_HASH_SECRET_HERE` bằng Hash Secret thực tế của bạn
- Đối với môi trường production, thay đổi:
  - `VNPAY_URL=https://www.vnpayment.vn/paymentv2/vpcpay.html`
  - `VNPAY_RETURN_URL=https://yourdomain.com/payment/vnpay/return`
  - `VNPAY_SANDBOX=false`

### Bước 2: Chạy migration và seeder

```bash
php artisan migrate
php artisan db:seed --class=PaymentMethodSeeder
```

### Bước 3: Kiểm tra

Sau khi cấu hình xong, bạn có thể:
1. Kiểm tra danh sách phương thức thanh toán: `GET /payment/methods`
2. Test thanh toán với đơn hàng

## Các trạng thái thanh toán

- **pending**: Chờ thanh toán (COD)
- **paid**: Đã thanh toán thành công
- **failed**: Thanh toán thất bại

## Mã lỗi VNPay thường gặp

- **00**: Giao dịch thành công
- **07**: Giao dịch bị nghi ngờ
- **09**: Thẻ/Tài khoản chưa đăng ký dịch vụ
- **10**: Xác thực thông tin không đúng
- **51**: Tài khoản không đủ số dư
- **65**: Vượt quá hạn mức giao dịch
- **75**: Ngân hàng đang bảo trì
- **99**: Lỗi không xác định

## Lưu ý quan trọng

1. **Bảo mật**: Không commit file `.env` lên Git
2. **Test**: Luôn test trên môi trường sandbox trước khi chuyển sang production
3. **HTTPS**: Trong production, đảm bảo website sử dụng HTTPS
4. **Return URL**: Return URL phải khớp với URL đã đăng ký trong tài khoản VNPay

## Hỗ trợ

Nếu gặp vấn đề, vui lòng liên hệ:
- VNPay Support: https://www.vnpayment.vn/contact
- Tài liệu VNPay: https://sandbox.vnpayment.vn/apis/


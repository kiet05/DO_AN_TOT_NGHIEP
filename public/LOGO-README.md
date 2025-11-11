# 🎩 EGA Gentlemen's Fashion - Logo Files

**Premium Logo cho Shop Quần Áo Quý Ông**

## 📁 Các file logo đã tạo:

### 1. **logo-ega-horizontal.svg** (Khuyến nghị)
- **Kích thước:** 280x75px
- **Sử dụng:** Header, Navbar, Email signature
- **Đặc điểm:** Logo ngang với icon **Cà vạt (Tie)** - Premium, Nam tính, Sang trọng
- **Tagline:** "GENTLEMEN'S FASHION"

### 2. **logo-ega-simple.svg**
- **Kích thước:** 200x65px
- **Sử dụng:** Mobile, Compact spaces
- **Đặc điểm:** Logo Ultra Bold, Premium với accent line vàng gold

### 3. **logo-ega-icon.svg**
- **Kích thước:** 140x140px
- **Sử dụng:** Favicon, App icon, Social media profile
- **Đặc điểm:** Logo tròn Premium với icon **Cà vạt (Tie)** ở giữa, text "EGA" bên dưới

### 4. **logo-ega.svg**
- **Kích thước:** 220x85px
- **Sử dụng:** General purpose
- **Đặc điểm:** Logo Elegant với icon **Cà vạt (Tie)** bên trái, text "EGA" bên phải

## 🎨 Màu sắc Premium:
- **Đen Premium:** #1a1a1a, #2d2d2d - Sang trọng, Nam tính
- **Vàng Gold:** #d4af37, #f4d03f - Premium, Cao cấp
- **Màu text:** #666, #888 (Gray)
- **Style:** Phù hợp shop quần áo quý ông, nam tính, sang trọng

## 📝 Cách sử dụng:

### Trong Blade Template:
```blade
{{-- Logo trong navbar --}}
<img src="{{ asset('logo-ega-horizontal.svg') }}" alt="EGA Fashion Shop" style="max-height: 50px;">

{{-- Logo trong header --}}
<img src="{{ asset('logo-ega-horizontal.svg') }}" alt="EGA Fashion Shop">

{{-- Favicon --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('logo-ega-icon.svg') }}">
```

### Trong HTML thuần:
```html
<img src="/logo-ega-horizontal.svg" alt="EGA Fashion Shop">
```

### Làm Favicon:
```html
<link rel="icon" type="image/svg+xml" href="/logo-ega-icon.svg">
```

## ✅ Đã tích hợp:
- ✅ **Navbar** (`resources/views/partials/navbar.blade.php`) - Đã cập nhật dùng `logo-ega-horizontal.svg`

## 🔍 Xem preview:
Mở file `logo-preview.html` trong trình duyệt để xem tất cả các logo:
```
http://127.0.0.1:8000/logo-preview.html
```

## 💡 Gợi ý:
- Logo horizontal phù hợp nhất cho header/navbar
- Logo icon phù hợp cho favicon và social media
- Tất cả logo đều là SVG, có thể scale mà không mất chất lượng
- **Icon Cà vạt (Tie)** thể hiện phong cách quý ông, sang trọng
- **Màu đen + vàng gold** tạo cảm giác premium, cao cấp
- Phù hợp shop quần áo nam, quý ông, thời trang cao cấp


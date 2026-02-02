# TÀI LIỆU TRANG INDEX (TRANG CHỦ) - DỰ ÁN FOOD ORDERING

## TỔNG QUAN
Tài liệu này tổng hợp tất cả các file liên quan đến trang chủ (index) của dự án đặt món ăn FoodOrderingLaravel.

---

## 1. ROUTING - ĐỊNH TUYẾN

### File: `routes/web.php`
- **Dòng 16**: Route trang chủ chính
```php
Route::get('/', [MonAnController::class, 'menu'])->name('foods.index');
```

**Mô tả**: Route này xử lý URL gốc "/" và gọi đến method `menu()` trong `MonAnController`. Tên route là `foods.index`.

---

## 2. CONTROLLER - BỘ ĐIỀU KHIỂN

### File: `app/Http/Controllers/MonAnController.php`

#### Method: `menu()` (Dòng 20-31)
```php
public function menu(Request $request)
{
    $foods = MonAn::paginate(12);

    $restaurants = NhaHang::withCount(['monAn' => function ($q) {
        $q->where('TrangThai', 'Còn bán');
    }])
        ->orderBy('TenNhaHang')
        ->get();

    return view('foods.index', compact('foods', 'restaurants'));
}
```

**Chức năng**:
- Lấy danh sách món ăn với phân trang (12 món/trang)
- Lấy danh sách nhà hàng kèm số lượng món ăn đang còn bán
- Trả về view `foods.index` với dữ liệu `$foods` và `$restaurants`

**Models sử dụng**:
- `App\Models\MonAn`
- `App\Models\NhaHang`

---

## 3. VIEW - GIAO DIỆN

### 3.1. File chính: `resources/views/foods/index.blade.php`
**Số dòng**: 1062 dòng

**Cấu trúc**:
- **Dòng 1**: Extends từ layout master
```php
@extends('foods.master')
```

- **Dòng 3-40**: Hiển thị thông báo (success/error messages)

- **Dòng 120-275**: Floating Cart Button & Cart Modal (giỏ hàng nổi)

- **Dòng 277-438**: Header section với:
  - Logo và navbar
  - Địa chỉ giao hàng (header + main location input)
  - Thanh tìm kiếm
  - Menu người dùng (đăng nhập/đăng ký)

- **Dòng 440-480**: Section khuyến mãi (Offer section)

- **Dòng 482-561**: Danh sách món ăn chính
  - Filter menu theo loại (Cơm, Bún, Phở, Mì, Trà)
  - Grid hiển thị món ăn với hình ảnh, tên, giá, rating
  - Nút thêm vào giỏ hàng

- **Dòng 563-611**: Section nhà hàng (Restaurant section)

- **Dòng 615-660**: About section (Giới thiệu về CabaFood)

- **Dòng 664-714**: Client section (Bình luận khách hàng)

- **Dòng 716-1062**: Styles & Scripts
  - CSS cho floating cart, cart modal
  - JavaScript cho cart functionality

**Các section chính**:
1. **Hero Area**: Banner chính với địa chỉ giao hàng
2. **Offer Section**: Khuyến mãi
3. **Food Section**: Danh sách món ăn
4. **Restaurant Section**: Danh sách nhà hàng
5. **About Section**: Giới thiệu
6. **Client Section**: Đánh giá khách hàng

---

### 3.2. File layout: `resources/views/foods/master.blade.php`
**Số dòng**: 225 dòng

**Chức năng**: Template chính cho trang foods

**Cấu trúc**:
- **Dòng 1-83**: Head section
  - Meta tags (CSRF, user info)
  - CSS files (Bootstrap, Font Awesome, Owl Carousel, Leaflet)
  - Custom styles cho header

- **Dòng 85**: Yield content area
```php
@yield('content')
```

- **Dòng 87-136**: Footer section
  - Thông tin liên hệ
  - Liên kết mạng xã hội
  - Link tải ứng dụng

- **Dòng 138-225**: JavaScript section
  - jQuery, Bootstrap
  - Owl Carousel, Isotope
  - Custom.js
  - Google Maps API
  - Leaflet maps
  - Script xử lý địa chỉ giao hàng

---

### 3.3. File partial: `resources/views/foods/_cart-scripts.blade.php`
**Số dòng**: 332 dòng

**Chức năng**: Scripts xử lý giỏ hàng cho trang index

**Các function chính**:

1. **`api(url, options)`** (Dòng 18-49): Helper cho API calls
   - Xử lý fetch với CSRF token
   - Parse JSON response
   - Error handling

2. **`renderCart(data)`** (Dòng 51-91): Render giỏ hàng
   - Cập nhật số lượng items
   - Hiển thị danh sách món
   - Tính tổng tiền

3. **`Quantity(id, input)`** (Dòng 93-113): Validate số lượng
   - Kiểm tra input rỗng
   - Kiểm tra số nguyên dương
   - Xử lý xóa khi số lượng = 0

4. **`cartInputChange(id, input)`** (Dòng 148-178): Xử lý nhập số lượng
   - Update số lượng món
   - Xóa món khi số lượng = 0

5. **`toggleCartModal()`** (Dòng 181-193): Hiển thị/ẩn modal giỏ hàng

6. **Event listeners** (Dòng 195-258):
   - Nút "Thêm vào giỏ"
   - Kiểm tra trạng thái món (còn bán/ngừng bán)
   - Hiển thị thông báo

7. **Cart operations** (Dòng 260-287):
   - `cartIncrease()`: Tăng số lượng
   - `cartDecrease()`: Giảm số lượng
   - `cartRemove()`: Xóa món
   - `clearCart()`: Xóa toàn bộ giỏ

8. **`showAlert(type, message)`** (Dòng 289-332): Hiển thị toast notification

---

## 4. JAVASCRIPT FILES

### 4.1. File: `public/js/cart-integration.js`
**Số dòng**: 472 dòng

**Class chính**: `CartManager`

**Chức năng**:
- Quản lý giỏ hàng với localStorage
- Đồng bộ với backend khi đăng nhập
- Xử lý thêm/sửa/xóa món
- Tích hợp với các route Laravel

**Methods chính**:
- `checkLoginStatus()`: Kiểm tra trạng thái đăng nhập
- `syncWithBackend()`: Đồng bộ giỏ hàng với server
- `addToBackend()`: Thêm món vào giỏ (backend)
- `bindAddToCartButtons()`: Gắn sự kiện cho nút thêm giỏ

### 4.2. File: `public/js/custom.js`
**Chức năng**: JavaScript tùy chỉnh cho các tính năng chung
- Owl Carousel
- Isotope filtering
- Nice Select
- Animation effects

---

## 5. CSS FILES

### 5.1. File: `public/css/style.css`
**Số dòng**: 1252 dòng

**Các class chính cho trang index**:

1. **Layout**:
   - `.layout_padding`: 90px padding
   - `.layout_padding-bottom`: 50px padding bottom
   - `.hero_area`: Hero section chính

2. **Header**:
   - `.header_section`: Fixed header
   - `.header_section.scrolled`: Header khi scroll
   - `.navbar`: Navigation bar

3. **Food Section**:
   - `.food_section`: Section món ăn
   - `.filters_menu`: Menu filter loại món
   - `.box`: Card món ăn
   - `.img-box`: Container hình ảnh
   - `.detail-box`: Chi tiết món

4. **Restaurant Section**:
   - `.restaurant_section`: Section nhà hàng
   - `.restaurant-card`: Card nhà hàng
   - `.rest-avatar`: Avatar nhà hàng

5. **Footer**:
   - `.footer_section`: Footer chính
   - `.footer_contact`: Thông tin liên hệ

### 5.2. File: `public/css/responsive.css`
**Chức năng**: CSS responsive cho mobile/tablet

---

## 6. MODELS - DỮ LIỆU

### 6.1. File: `app/Models/MonAn.php`
**Chức năng**: Model món ăn

**Attributes**:
- `MaMonAn`: Primary key
- `MaNhaHang`: Foreign key
- `TenMonAn`: Tên món
- `DanhMuc`: Loại món (Cơm, Bún, Phở, Mì, Trà)
- `MoTa`: Mô tả
- `Gia`: Giá tiền
- `HinhAnh`: Đường dẫn hình ảnh
- `TrangThai`: Còn bán/Ngừng bán

**Relationships**:
- `nhaHang()`: Belongs to NhaHang
- `binhLuans()`: Has many BinhLuan
- `gioHangChiTiets()`: Has many GioHangChiTiet

**Accessors**:
- `loai_mon_class`: Convert danh mục sang class CSS
- `diem_trung_binh`: Điểm đánh giá trung bình
- `tong_binh_luan`: Tổng số bình luận

### 6.2. File: `app/Models/NhaHang.php`
**Chức năng**: Model nhà hàng

**Attributes**:
- `MaNhaHang`: Primary key
- `TenNhaHang`: Tên nhà hàng
- `DiaChi`: Địa chỉ
- `SoDienThoai`: Số điện thoại
- `MaNguoiDung`: Foreign key (owner)

**Relationships**:
- `monAn()`: Has many MonAn
- `owner()`: Belongs to User

---

## 7. ASSETS - TÀI NGUYÊN

### Hình ảnh (thư mục `public/images/`):
- `bg2.jpg`: Background hero section
- `o1.jpg`, `o2.jpg`: Hình khuyến mãi
- `about-img.png`: Hình giới thiệu
- `client1.jpg`, `client2.jpg`, `client3.png`: Avatar khách hàng
- `apple.png`, `android.png`: Icon download app
- `favicon.png`: Icon trang web
- `no-image.png`: Hình mặc định khi món không có ảnh

### Fonts:
- **Dancing Script**: Font chữ cho tiêu đề
- **Open Sans**: Font chữ cho nội dung
- **Font Awesome**: Icons

---

## 8. DEPENDENCIES - THƯ VIỆN

### Frontend Libraries:
1. **Bootstrap 4.6.2**: Framework CSS
2. **jQuery 3.6.0**: JavaScript library
3. **Owl Carousel 2.3.4**: Slider/Carousel
4. **Isotope Layout 3**: Grid filtering
5. **Nice Select 1.1.0**: Custom select dropdown
6. **Bootstrap Icons**: Icon set
7. **Leaflet 1.9.4**: Maps library
8. **Google Maps API**: Geolocation

### Backend:
- **Laravel Framework**: PHP framework
- **Laravel Pagination**: Phân trang dữ liệu

---

## 9. API ENDPOINTS - TRANG INDEX

### Cart APIs (được gọi từ trang index):
```
GET    /cart              - Lấy giỏ hàng
POST   /cart              - Thêm món vào giỏ
PUT    /cart/{id}         - Cập nhật số lượng
DELETE /cart/{id}         - Xóa món khỏi giỏ
DELETE /cart              - Xóa toàn bộ giỏ
```

### Other APIs:
```
GET /api/check-username   - Kiểm tra username
GET /api/check-email      - Kiểm tra email
```

---

## 10. FEATURES - TÍNH NĂNG TRANG INDEX

### 10.1. Hiển thị món ăn
- ✅ Phân trang 12 món/trang
- ✅ Filter theo loại (Cơm, Bún, Phở, Mì, Trà)
- ✅ Hiển thị rating và số lượng bình luận
- ✅ Hiển thị giá và trạng thái món
- ✅ Xem chi tiết món

### 10.2. Giỏ hàng
- ✅ Floating cart button
- ✅ Thêm món vào giỏ
- ✅ Cập nhật số lượng
- ✅ Xóa món
- ✅ Xóa toàn bộ giỏ
- ✅ Hiển thị tổng tiền
- ✅ Chuyển đến checkout

### 10.3. Địa chỉ giao hàng
- ✅ Input địa chỉ chính (main location)
- ✅ Input địa chỉ header (khi scroll)
- ✅ Lấy vị trí hiện tại (geolocation)
- ✅ Đồng bộ 2 input

### 10.4. Authentication
- ✅ Đăng nhập/Đăng ký
- ✅ Dropdown user menu
- ✅ Hiển thị avatar
- ✅ Đăng xuất

### 10.5. Restaurant Section
- ✅ Hiển thị danh sách nhà hàng
- ✅ Số lượng món ăn của nhà hàng
- ✅ Thông tin liên hệ
- ✅ Xem chi tiết nhà hàng

### 10.6. Notifications
- ✅ Toast messages
- ✅ Auto-hide sau 2-5 giây
- ✅ Success/Error/Warning alerts

---

## 11. LUỒNG HOẠT ĐỘNG

### Luồng xem trang chủ:
```
1. User truy cập "/"
2. Route gọi MonAnController@menu
3. Controller lấy dữ liệu:
   - Danh sách món ăn (paginate 12)
   - Danh sách nhà hàng (with count món)
4. Render view foods.index
5. Load master.blade.php
6. Load scripts (cart-scripts, custom.js)
7. Initialize cart từ localStorage/backend
8. Hiển thị trang
```

### Luồng thêm món vào giỏ:
```
1. User click nút "Thêm giỏ"
2. Kiểm tra trạng thái món (còn bán?)
3. Gọi API POST /cart với MaMonAn
4. Backend thêm vào database
5. Trả về dữ liệu giỏ mới
6. Update UI:
   - Số lượng badge
   - Render lại cart items
7. Hiển thị toast success
```

---

## 12. DATABASE SCHEMA LIÊN QUAN

### Bảng `MonAn` (mon_ans):
```
- MaMonAn (PK)
- MaNhaHang (FK)
- TenMonAn
- DanhMuc
- MoTa
- Gia
- HinhAnh
- TrangThai
- created_at
- updated_at
```

### Bảng `NhaHang` (nha_hangs):
```
- MaNhaHang (PK)
- TenNhaHang
- DiaChi
- SoDienThoai
- MaNguoiDung (FK)
- created_at
- updated_at
```

### Bảng `GioHang` (gio_hangs):
```
- MaGioHang (PK)
- MaNguoiDung (FK)
- created_at
- updated_at
```

### Bảng `GioHangChiTiet` (gio_hang_chi_tiets):
```
- MaGioHangChiTiet (PK)
- MaGioHang (FK)
- MaMonAn (FK)
- SoLuong
- created_at
- updated_at
```

---

## 13. VALIDATION RULES

### Thêm món vào giỏ:
- MaMonAn: required, exists:mon_ans
- SoLuong: required, integer, min:1
- Món phải có TrangThai = "Còn bán"

### Cập nhật số lượng:
- SoLuong: required, integer, min:0
- Nếu = 0 thì xóa khỏi giỏ

---

## 14. SECURITY - BẢO MẬT

### CSRF Protection:
- Tất cả POST/PUT/DELETE requests yêu cầu CSRF token
- Token được embed trong meta tag và gửi kèm mỗi request

### Authentication:
- Cart APIs yêu cầu đăng nhập (`auth` middleware)
- Guest có thể xem nhưng không thể thêm giỏ

### XSS Prevention:
- Blade tự động escape output
- User input được validate

---

## 15. PERFORMANCE OPTIMIZATION

### Database:
- Sử dụng pagination (12 items/page)
- Eager loading với `withCount()`
- Index trên các foreign keys

### Frontend:
- Lazy load images (có thể cải thiện)
- Minify CSS/JS (production)
- CDN cho libraries

### Caching:
- Có thể cache danh sách món/nhà hàng
- Cache API responses

---

## 16. RESPONSIVE DESIGN

### Breakpoints:
- Desktop: > 992px
- Tablet: 768px - 991px
- Mobile: < 767px

### Mobile Features:
- Hamburger menu
- Touch-friendly cart modal
- Responsive grid (col-sm-6, col-lg-4)
- Mobile-optimized input fields

---

## 17. BROWSER COMPATIBILITY

### Supported Browsers:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

### Required Features:
- ES6 JavaScript
- Fetch API
- LocalStorage
- Geolocation API

---

## 18. TESTING CHECKLIST

### Functional Testing:
- [ ] Hiển thị danh sách món đúng
- [ ] Filter hoạt động
- [ ] Thêm giỏ thành công
- [ ] Cập nhật số lượng đúng
- [ ] Xóa món khỏi giỏ
- [ ] Tính tổng tiền chính xác
- [ ] Toast notifications hiển thị

### UI/UX Testing:
- [ ] Responsive trên mobile
- [ ] Hover effects hoạt động
- [ ] Modal open/close đúng
- [ ] Scroll behavior mượt

### Security Testing:
- [ ] CSRF token valid
- [ ] Auth middleware hoạt động
- [ ] XSS prevention
- [ ] SQL injection prevention

---

## 19. KNOWN ISSUES & IMPROVEMENTS

### Cần cải thiện:
1. Lazy loading cho hình ảnh
2. Infinite scroll thay vì pagination
3. Real-time cart updates
4. Search autocomplete
5. Filter theo giá, rating
6. Wishlist feature
7. Compare products

---

## 20. LIÊN HỆ & HỖ TRỢ

### Team Development:
- **Team**: CabaFoodTeam9
- **Email**: cabafoods@gmail.com
- **Phone**: +84 123456789
- **Address**: 175 Tây Sơn, Đống Đa, Hà Nội

---

## PHỤ LỤC: DANH SÁCH FILE ĐẦY ĐỦ

### Backend Files:
1. `routes/web.php` - Route definitions
2. `app/Http/Controllers/MonAnController.php` - Food controller
3. `app/Http/Controllers/GioHangController.php` - Cart controller
4. `app/Models/MonAn.php` - Food model
5. `app/Models/NhaHang.php` - Restaurant model
6. `app/Models/GioHang.php` - Cart model
7. `app/Models/GioHangChiTiet.php` - Cart detail model

### Frontend Files:
8. `resources/views/foods/index.blade.php` - Main index view
9. `resources/views/foods/master.blade.php` - Master layout
10. `resources/views/foods/_cart-scripts.blade.php` - Cart scripts partial

### JavaScript Files:
11. `public/js/cart-integration.js` - Cart integration
12. `public/js/custom.js` - Custom JavaScript
13. `public/js/jquery-3.4.1.min.js` - jQuery library

### CSS Files:
14. `public/css/style.css` - Main stylesheet
15. `public/css/responsive.css` - Responsive styles
16. `public/css/bootstrap.css` - Bootstrap framework

### Assets:
17. `public/images/` - Image directory
18. `public/fonts/` - Font files

---

**Ngày tạo tài liệu**: 02/02/2026  
**Phiên bản**: 1.0  
**Tác giả**: GitHub Copilot  
**Dự án**: FoodOrderingLaravel

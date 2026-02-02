# API Authentication Documentation

## Cấu hình ban đầu

### 1. Chạy migration (sau khi cấu hình database)
```bash
php artisan migrate
```

### 2. Cấu hình CORS (nếu cần)
Trong file `config/cors.php`, đảm bảo cấu hình phù hợp với frontend:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:3000'], // URL frontend của bạn
'supports_credentials' => true,
```

## API Endpoints

### Base URL
```
http://your-domain.com/api/v1
```

---

## 1. Đăng ký tài khoản (Register)

### Endpoint
```
POST /api/v1/register
```

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Request Body
```json
{
    "username": "john_doe",
    "password": "123456",
    "password_confirmation": "123456",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "0123456789",
    "device_name": "web-browser"
}
```

### Response Success (201)
```json
{
    "success": true,
    "message": "Đăng ký tài khoản thành công",
    "data": {
        "user": {
            "id": 1,
            "username": "john_doe",
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "0123456789",
            "role": "khach_hang"
        },
        "token": "1|abc123def456...",
        "token_type": "Bearer"
    }
}
```

### Response Error (422)
```json
{
    "success": false,
    "message": "Dữ liệu không hợp lệ",
    "errors": {
        "email": ["Email đã được sử dụng"],
        "username": ["Tên đăng nhập đã tồn tại"]
    }
}
```

---

## 2. Đăng nhập (Login)

### Endpoint
```
POST /api/v1/login
```

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Request Body
```json
{
    "login": "john_doe",
    "password": "123456",
    "device_name": "web-browser"
}
```

**Lưu ý:** `login` có thể là **email** hoặc **username**

### Response Success (200)
```json
{
    "success": true,
    "message": "Đăng nhập thành công",
    "data": {
        "user": {
            "id": 1,
            "username": "john_doe",
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "0123456789",
            "role": "khach_hang"
        },
        "token": "2|xyz789abc123...",
        "token_type": "Bearer"
    }
}
```

### Response Error (401)
```json
{
    "success": false,
    "message": "Tài khoản không tồn tại trong hệ thống"
}
```

hoặc

```json
{
    "success": false,
    "message": "Mật khẩu không chính xác"
}
```

---

## 3. Lấy thông tin user hiện tại

### Endpoint
```
GET /api/v1/user
```

### Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

### Response Success (200)
```json
{
    "success": true,
    "data": {
        "MaNguoiDung": 1,
        "TenDangNhap": "john_doe",
        "HoTen": "John Doe",
        "Email": "john@example.com",
        "SoDienThoai": "0123456789",
        "VaiTro": "khach_hang"
    }
}
```

---

## 4. Đăng xuất (Logout)

### Endpoint
```
POST /api/v1/logout
```

### Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

### Response Success (200)
```json
{
    "success": true,
    "message": "Đăng xuất thành công"
}
```

**Lưu ý:** Chỉ xóa token hiện tại, các token khác vẫn còn hiệu lực.

---

## 5. Đăng xuất khỏi tất cả thiết bị

### Endpoint
```
POST /api/v1/logout-all
```

### Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

### Response Success (200)
```json
{
    "success": true,
    "message": "Đã đăng xuất khỏi tất cả các thiết bị"
}
```

**Lưu ý:** Xóa tất cả tokens của user.

---

## Cách sử dụng với Frontend

### JavaScript/Fetch API
```javascript
// Login
const login = async (username, password) => {
    const response = await fetch('http://your-domain.com/api/v1/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            login: username,
            password: password,
            device_name: 'web-browser'
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Lưu token
        localStorage.setItem('token', data.data.token);
        localStorage.setItem('user', JSON.stringify(data.data.user));
    }
    
    return data;
};

// Gọi API có authentication
const getUser = async () => {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://your-domain.com/api/v1/user', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
        }
    });
    
    return await response.json();
};

// Logout
const logout = async () => {
    const token = localStorage.getItem('token');
    
    await fetch('http://your-domain.com/api/v1/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
        }
    });
    
    // Xóa token khỏi localStorage
    localStorage.removeItem('token');
    localStorage.removeItem('user');
};
```

### Axios
```javascript
import axios from 'axios';

// Cấu hình axios instance
const api = axios.create({
    baseURL: 'http://your-domain.com/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Interceptor để tự động thêm token
api.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Login
const login = async (username, password) => {
    const response = await api.post('/login', {
        login: username,
        password: password,
        device_name: 'web-browser'
    });
    
    if (response.data.success) {
        localStorage.setItem('token', response.data.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.data.user));
    }
    
    return response.data;
};

// Get User
const getUser = async () => {
    const response = await api.get('/user');
    return response.data;
};

// Logout
const logout = async () => {
    await api.post('/logout');
    localStorage.removeItem('token');
    localStorage.removeItem('user');
};
```

---

## Testing với Postman/Thunder Client

### 1. Login Request
- **Method:** POST
- **URL:** `http://localhost:8000/api/v1/login`
- **Headers:**
  - Content-Type: application/json
  - Accept: application/json
- **Body (raw JSON):**
```json
{
    "login": "john_doe",
    "password": "123456",
    "device_name": "postman"
}
```

### 2. Lưu token từ response
Copy token từ response: `data.token`

### 3. Test authenticated endpoint
- **Method:** GET
- **URL:** `http://localhost:8000/api/v1/user`
- **Headers:**
  - Content-Type: application/json
  - Accept: application/json
  - Authorization: Bearer {paste-token-here}

---

## Mã lỗi HTTP

| Code | Ý nghĩa |
|------|---------|
| 200  | Success - Request thành công |
| 201  | Created - Tạo resource thành công (register) |
| 401  | Unauthorized - Không có quyền truy cập (sai mật khẩu, token không hợp lệ) |
| 422  | Unprocessable Entity - Dữ liệu validation không hợp lệ |
| 500  | Internal Server Error - Lỗi server |

---

## Bảo mật

1. **HTTPS:** Luôn sử dụng HTTPS trong production
2. **Token Storage:** Lưu token trong localStorage hoặc httpOnly cookie
3. **Token Expiration:** Cấu hình trong `config/sanctum.php`
4. **Rate Limiting:** Áp dụng rate limiting cho endpoints quan trọng

---

## 2. HOME PAGE APIs

### 2.1. Lấy danh sách món ăn
- **Endpoint**: `GET /api/v1/foods`
- **Query Parameters**:
  - `category` (optional): Filter theo danh mục (Cơm, Bún, Phở, Mì, Trà)
  - `search` (optional): Tìm kiếm theo tên món
  - `per_page` (optional, default: 12): Số món mỗi trang

- **Example**: `GET /api/v1/foods?category=Cơm&per_page=12`

### 2.2. Chi tiết món ăn
- **Endpoint**: `GET /api/v1/foods/{id}`
- **Returns**: Thông tin món ăn + related foods + bình luận

### 2.3. Danh sách nhà hàng
- **Endpoint**: `GET /api/v1/restaurants`
- **Query**: `search`, `per_page`

### 2.4. Chi tiết nhà hàng
- **Endpoint**: `GET /api/v1/restaurants/{id}`
- **Returns**: Thông tin nhà hàng + danh sách món ăn

### 2.5. Món ăn của nhà hàng
- **Endpoint**: `GET /api/v1/restaurants/{id}/foods`
- **Query**: `category`, `search`, `per_page`

### 2.6. Khuyến mãi
- **Endpoint**: `GET /api/v1/offers`

### 2.7. Đánh giá khách hàng
- **Endpoint**: `GET /api/v1/reviews`

---

## 3. CART APIs (Giỏ hàng)

**Lưu ý**: Hỗ trợ cả guest users (session) và authenticated users (database).

### 3.1. Xem giỏ hàng
- **Endpoint**: `GET /api/v1/cart`
- **Auth**: No (hỗ trợ guest)

### 3.2. Thêm món vào giỏ
- **Endpoint**: `POST /api/v1/cart`
- **Body**: `{ "MaMonAn": 1, "SoLuong": 2 }`

### 3.3. Cập nhật số lượng
- **Endpoint**: `PUT /api/v1/cart/{id}`
- **Body**: `{ "SoLuong": 3 }`

### 3.4. Xóa món
- **Endpoint**: `DELETE /api/v1/cart/{id}`

### 3.5. Xóa toàn bộ giỏ
- **Endpoint**: `DELETE /api/v1/cart`

---

## 4. REVIEW APIs

### 4.1. Thêm đánh giá
- **Endpoint**: `POST /api/v1/foods/{id}/reviews`
- **Auth**: Yes (Bearer Token)
- **Body** (multipart/form-data):
  - `diem_danh_gia`: 1-5
  - `noi_dung`: string
  - `hinh_anh`: file (optional)

---

## TỔNG KẾT API ENDPOINTS

### Authentication (2 endpoints)
- ✅ POST `/api/v1/login`
- ✅ POST `/api/v1/register`
- ✅ GET `/api/v1/user` (protected)
- ✅ POST `/api/v1/logout` (protected)
- ✅ POST `/api/v1/logout-all` (protected)

### Home Page (7 endpoints)
- ✅ GET `/api/v1/foods` - Danh sách món ăn
- ✅ GET `/api/v1/foods/{id}` - Chi tiết món ăn
- ✅ GET `/api/v1/restaurants` - Danh sách nhà hàng
- ✅ GET `/api/v1/restaurants/{id}` - Chi tiết nhà hàng
- ✅ GET `/api/v1/restaurants/{id}/foods` - Món của nhà hàng
- ✅ GET `/api/v1/offers` - Khuyến mãi
- ✅ GET `/api/v1/reviews` - Đánh giá

### Cart (5 endpoints)
- ✅ GET `/api/v1/cart` - Xem giỏ hàng
- ✅ POST `/api/v1/cart` - Thêm món
- ✅ PUT `/api/v1/cart/{id}` - Cập nhật số lượng
- ✅ DELETE `/api/v1/cart/{id}` - Xóa món
- ✅ DELETE `/api/v1/cart` - Xóa toàn bộ

### Reviews (1 endpoint)
- ✅ POST `/api/v1/foods/{id}/reviews` - Thêm đánh giá (protected)

### Checkout & Orders (6 endpoints)
- ✅ GET `/api/v1/checkout` - Lấy thông tin checkout (protected)
- ✅ POST `/api/v1/checkout/apply-voucher` - Áp dụng mã giảm giá (protected)
- ✅ POST `/api/v1/checkout/create-order` - Tạo đơn hàng (protected)
- ✅ GET `/api/v1/orders` - Lịch sử đơn hàng (protected)
- ✅ GET `/api/v1/orders/{id}` - Chi tiết đơn hàng (protected)
- ✅ POST `/api/v1/orders/{id}/payment` - Xử lý thanh toán (protected)

### Profile (4 endpoints)
- ✅ GET `/api/v1/profile` - Xem thông tin cá nhân (protected)
- ✅ PUT `/api/v1/profile` - Cập nhật thông tin cá nhân (protected)
- ✅ PUT `/api/v1/profile/password` - Đổi mật khẩu (protected)
- ✅ DELETE `/api/v1/profile` - Xóa tài khoản (protected)

**Tổng cộng**: 30 API endpoints đã hoàn thiện

---

## 5. CHECKOUT & ORDERS APIs

### 5.1. Lấy thông tin checkout
- **Endpoint**: `GET /api/v1/checkout`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Lấy thông tin giỏ hàng, tổng tiền và danh sách voucher khả dụng

**Request Headers**:
```
Authorization: Bearer {token}
Accept: application/json
```

**Response Success (200)**:
```json
{
    "success": true,
    "data": {
        "cart_items": [
            {
                "id": 1,
                "MaMonAn": 5,
                "TenMonAn": "Cơm tấm sườn",
                "HinhAnh": "https://example.com/image.jpg",
                "Gia": 45000,
                "SoLuong": 2,
                "ThanhTien": 90000,
                "MaNhaHang": 3,
                "TenNhaHang": "Nhà hàng ABC"
            }
        ],
        "subtotal": 90000,
        "available_vouchers": [
            {
                "MaGiamGia": 1,
                "Code": "FREESHIP",
                "MoTa": "Miễn phí vận chuyển",
                "LoaiGiamGia": "percent",
                "GiaTriGiam": 100,
                "GiaTriDonHangToiThieu": 50000,
                "SoLuongConLai": 100,
                "NgayBatDau": "2026-01-01",
                "NgayKetThuc": "2026-12-31"
            }
        ]
    }
}
```

**Response Error (401)**:
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

---

### 5.2. Áp dụng mã giảm giá
- **Endpoint**: `POST /api/v1/checkout/apply-voucher`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Kiểm tra và áp dụng mã giảm giá

**Request Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body**:
```json
{
    "voucher_code": "FREESHIP",
    "subtotal": 90000
}
```

**Validation Rules**:
- `voucher_code`: required, string
- `subtotal`: required, numeric, min:0

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Áp dụng mã giảm giá thành công",
    "data": {
        "voucher": {
            "MaGiamGia": 1,
            "Code": "FREESHIP",
            "MoTa": "Miễn phí vận chuyển",
            "LoaiGiamGia": "percent",
            "GiaTriGiam": 100
        },
        "discount_amount": 30000,
        "total": 60000
    }
}
```

**Response Error (400)**:
```json
{
    "success": false,
    "message": "Mã giảm giá không hợp lệ hoặc đã hết hạn"
}
```

**Response Error (422)**:
```json
{
    "success": false,
    "message": "Giá trị đơn hàng tối thiểu phải từ 50000đ"
}
```

---

### 5.3. Tạo đơn hàng
- **Endpoint**: `POST /api/v1/checkout/create-order`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Tạo đơn hàng mới từ giỏ hàng

**Request Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body**:
```json
{
    "DiaChiGiaoHang": "123 Nguyễn Văn A, Quận 1, TP.HCM",
    "PhuongThucThanhToan": "COD",
    "GhiChu": "Giao hàng ngoài giờ hành chính",
    "MaGiamGia": 1
}
```

**Validation Rules**:
- `DiaChiGiaoHang`: required, string, max:500
- `PhuongThucThanhToan`: required, in:COD,Online,VNPay,Momo
- `GhiChu`: nullable, string, max:1000
- `MaGiamGia`: nullable, integer, exists:giam_gia,MaGiamGia

**Response Success (201)**:
```json
{
    "success": true,
    "message": "Đơn hàng đã được tạo thành công",
    "data": {
        "order": {
            "MaDonHang": 100,
            "MaNguoiDung": 5,
            "TongTien": 90000,
            "PhiVanChuyen": 30000,
            "TienGiamGia": 0,
            "TongThanhToan": 120000,
            "DiaChiGiaoHang": "123 Nguyễn Văn A, Quận 1, TP.HCM",
            "TrangThai": "Chờ xác nhận",
            "PhuongThucThanhToan": "COD",
            "GhiChu": "Giao hàng ngoài giờ hành chính",
            "NgayDatHang": "2026-02-02 14:30:00",
            "items": [
                {
                    "MaChiTietDonHang": 150,
                    "MaMonAn": 5,
                    "TenMonAn": "Cơm tấm sườn",
                    "SoLuong": 2,
                    "DonGia": 45000,
                    "ThanhTien": 90000
                }
            ]
        },
        "payment": {
            "MaThanhToan": 80,
            "MaDonHang": 100,
            "SoTien": 120000,
            "PhuongThucThanhToan": "COD",
            "TrangThai": "Chờ thanh toán",
            "NgayThanhToan": null
        }
    }
}
```

**Response Error (400)**:
```json
{
    "success": false,
    "message": "Giỏ hàng trống. Vui lòng thêm món ăn trước khi đặt hàng"
}
```

**Response Error (422)**:
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "DiaChiGiaoHang": ["Địa chỉ giao hàng là bắt buộc"],
        "PhuongThucThanhToan": ["Phương thức thanh toán không hợp lệ"]
    }
}
```

**Response Error (500)**:
```json
{
    "success": false,
    "message": "Không thể tạo đơn hàng. Vui lòng thử lại",
    "error": "Database transaction failed"
}
```

---

### 5.4. Lịch sử đơn hàng
- **Endpoint**: `GET /api/v1/orders`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Lấy danh sách đơn hàng của người dùng (có phân trang)

**Request Headers**:
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters**:
- `page` (optional, default: 1): Số trang
- `per_page` (optional, default: 10): Số đơn hàng mỗi trang
- `status` (optional): Lọc theo trạng thái (Chờ xác nhận, Đang chuẩn bị, Đang giao, Hoàn thành, Đã hủy)

**Example**: `GET /api/v1/orders?page=1&per_page=10&status=Hoàn thành`

**Response Success (200)**:
```json
{
    "success": true,
    "data": {
        "orders": [
            {
                "MaDonHang": 100,
                "TongThanhToan": 120000,
                "TrangThai": "Hoàn thành",
                "PhuongThucThanhToan": "COD",
                "NgayDatHang": "2026-02-02 14:30:00",
                "items_count": 2,
                "payment_status": "Đã thanh toán"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 10,
            "total": 25,
            "last_page": 3,
            "from": 1,
            "to": 10
        }
    }
}
```

---

### 5.5. Chi tiết đơn hàng
- **Endpoint**: `GET /api/v1/orders/{id}`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Lấy thông tin chi tiết một đơn hàng

**Request Headers**:
```
Authorization: Bearer {token}
Accept: application/json
```

**Response Success (200)**:
```json
{
    "success": true,
    "data": {
        "order": {
            "MaDonHang": 100,
            "MaNguoiDung": 5,
            "TongTien": 90000,
            "PhiVanChuyen": 30000,
            "TienGiamGia": 0,
            "TongThanhToan": 120000,
            "DiaChiGiaoHang": "123 Nguyễn Văn A, Quận 1, TP.HCM",
            "TrangThai": "Hoàn thành",
            "PhuongThucThanhToan": "COD",
            "GhiChu": "Giao hàng ngoài giờ hành chính",
            "NgayDatHang": "2026-02-02 14:30:00",
            "user": {
                "MaNguoiDung": 5,
                "HoTen": "Nguyễn Văn A",
                "SoDienThoai": "0123456789",
                "Email": "nguyenvana@example.com"
            },
            "items": [
                {
                    "MaChiTietDonHang": 150,
                    "MaMonAn": 5,
                    "TenMonAn": "Cơm tấm sườn",
                    "HinhAnh": "https://example.com/image.jpg",
                    "SoLuong": 2,
                    "DonGia": 45000,
                    "ThanhTien": 90000,
                    "restaurant": {
                        "MaNhaHang": 3,
                        "TenNhaHang": "Nhà hàng ABC",
                        "DiaChi": "456 Lê Văn B, Quận 2"
                    }
                }
            ],
            "payment": {
                "MaThanhToan": 80,
                "SoTien": 120000,
                "PhuongThucThanhToan": "COD",
                "TrangThai": "Đã thanh toán",
                "NgayThanhToan": "2026-02-02 15:00:00"
            },
            "voucher": null
        }
    }
}
```

**Response Error (403)**:
```json
{
    "success": false,
    "message": "Bạn không có quyền xem đơn hàng này"
}
```

**Response Error (404)**:
```json
{
    "success": false,
    "message": "Không tìm thấy đơn hàng"
}
```

---

### 5.6. Xử lý thanh toán
- **Endpoint**: `POST /api/v1/orders/{id}/payment`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Xử lý thanh toán cho đơn hàng (Online/VNPay/Momo)

**Request Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body**:
```json
{
    "payment_method": "VNPay"
}
```

**Validation Rules**:
- `payment_method`: required, in:Online,VNPay,Momo

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Thanh toán thành công",
    "data": {
        "payment": {
            "MaThanhToan": 80,
            "MaDonHang": 100,
            "SoTien": 120000,
            "PhuongThucThanhToan": "VNPay",
            "TrangThai": "Đã thanh toán",
            "NgayThanhToan": "2026-02-02 15:00:00"
        },
        "order": {
            "MaDonHang": 100,
            "TrangThai": "Đang chuẩn bị",
            "TongThanhToan": 120000
        }
    }
}
```

**Response Error (400)**:
```json
{
    "success": false,
    "message": "Đơn hàng không thể thanh toán (đã thanh toán hoặc đã hủy)"
}
```

**Response Error (403)**:
```json
{
    "success": false,
    "message": "Bạn không có quyền thanh toán đơn hàng này"
}
```

**Response Error (404)**:
```json
{
    "success": false,
    "message": "Không tìm thấy đơn hàng"
}
```

---

## 6. PROFILE APIs

### 6.1. Xem thông tin cá nhân
- **Endpoint**: `GET /api/v1/profile`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Lấy thông tin chi tiết của người dùng đang đăng nhập

**Request Headers**:
```
Authorization: Bearer {token}
Accept: application/json
```

**Response Success (200)**:
```json
{
    "success": true,
    "data": {
        "MaNguoiDung": 5,
        "TenDangNhap": "nta2004",
        "HoTen": "Nguyễn Văn A",
        "Email": "nta2004@example.com",
        "SoDienThoai": "0123456789",
        "DiaChi": "Xóm Trong, xã Đông Anh, Hà Nội",
        "VaiTro": "khach_hang",
        "NgayTao": "2026-01-15 10:30:00"
    }
}
```

**Response Error (401)**:
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

---

### 6.2. Cập nhật thông tin cá nhân
- **Endpoint**: `PUT /api/v1/profile`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Cập nhật thông tin cá nhân của người dùng

**Request Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body**:
```json
{
    "HoTen": "Nguyễn Văn B",
    "Email": "nta2004@example.com",
    "SoDienThoai": "0987654321",
    "DiaChi": "123 Nguyễn Trãi, Thanh Xuân, Hà Nội"
}
```

**Validation Rules**:
- `HoTen`: required, string, max:255
- `Email`: required, email, max:255, unique (trừ email hiện tại)
- `SoDienThoai`: nullable, string, max:20
- `DiaChi`: nullable, string, max:500

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Cập nhật thông tin thành công",
    "data": {
        "MaNguoiDung": 5,
        "TenDangNhap": "nta2004",
        "HoTen": "Nguyễn Văn B",
        "Email": "nta2004@example.com",
        "SoDienThoai": "0987654321",
        "DiaChi": "123 Nguyễn Trãi, Thanh Xuân, Hà Nội",
        "VaiTro": "khach_hang"
    }
}
```

**Response Error (422)**:
```json
{
    "success": false,
    "message": "Dữ liệu không hợp lệ",
    "errors": {
        "HoTen": ["Họ và tên là bắt buộc"],
        "Email": ["Email đã được sử dụng"]
    }
}
```

---

### 6.3. Đổi mật khẩu
- **Endpoint**: `PUT /api/v1/profile/password`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Thay đổi mật khẩu người dùng

**Request Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body**:
```json
{
    "current_password": "123456",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Validation Rules**:
- `current_password`: required, string
- `password`: required, confirmed, min:6
- `password_confirmation`: required, same as password

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Đổi mật khẩu thành công"
}
```

**Response Error (422) - Wrong Current Password**:
```json
{
    "success": false,
    "message": "Mật khẩu hiện tại không đúng",
    "errors": {
        "current_password": ["Mật khẩu hiện tại không đúng"]
    }
}
```

**Response Error (422) - Validation Error**:
```json
{
    "success": false,
    "message": "Dữ liệu không hợp lệ",
    "errors": {
        "password": ["Xác nhận mật khẩu không khớp"]
    }
}
```

---

### 6.4. Xóa tài khoản
- **Endpoint**: `DELETE /api/v1/profile`
- **Auth**: Yes (Bearer Token)
- **Mô tả**: Xóa vĩnh viễn tài khoản người dùng (cần xác nhận mật khẩu)

**Request Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body**:
```json
{
    "password": "123456"
}
```

**Validation Rules**:
- `password`: required, string (phải khớp với mật khẩu hiện tại)

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Xóa tài khoản thành công"
}
```

**Response Error (422)**:
```json
{
    "success": false,
    "message": "Mật khẩu không đúng",
    "errors": {
        "password": ["Mật khẩu không đúng"]
    }
}
```

**Lưu ý**: Sau khi xóa tài khoản, tất cả API tokens của người dùng sẽ bị thu hồi.

---

**Ngày cập nhật**: 02/02/2026  
**Phiên bản**: 4.0.0

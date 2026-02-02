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

**Tổng cộng**: 20 API endpoints đã hoàn thiện

---

**Ngày cập nhật**: 02/02/2026  
**Phiên bản**: 2.0.0

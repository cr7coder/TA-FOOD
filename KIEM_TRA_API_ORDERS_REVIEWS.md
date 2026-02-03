# KIỂM TRA API ORDERS & REVIEWS - RESTful API Implementation

## 📋 TỔNG QUAN

Hệ thống đã hoàn toàn chuyển đổi sang **RESTful API** cho các chức năng:
- ✅ **Lịch sử đơn hàng** (Orders History)
- ✅ **Chi tiết đơn hàng** (Order Detail)
- ✅ **Đánh giá món ăn** (Food Reviews)

---

## 🔌 API ENDPOINTS

### 1. ORDERS API

#### 📝 Danh sách đơn hàng (GET)
```
GET /api/v1/orders
```

**Parameters:**
- `per_page` (optional): Số đơn hàng mỗi trang (default: 10)
- `page` (optional): Trang hiện tại (default: 1)

**Response Example:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "order_code": "ORD001",
            "status": "Đã giao",
            "total": 150000,
            "created_at": "2026-02-03 10:30:00",
            "items": [
                {
                    "food_id": 2,
                    "food_name": "Cơm tấm sườn bì chả",
                    "food_image": "http://localhost:8000/images/com-tam.jpg",
                    "quantity": 2,
                    "price": 50000,
                    "subtotal": 100000
                }
            ],
            "summary": {
                "items_total": 100000,
                "discount_amount": 0,
                "final_total": 100000
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "total_pages": 5,
        "per_page": 10,
        "total": 45
    }
}
```

**Controller:** `App\Http\Controllers\Api\OrderController@index`  
**View:** `resources/views/orders/api-history.blade.php`  
**Route:** `Route::get('/orders/history')` → returns API view

---

#### 📄 Chi tiết đơn hàng (GET)
```
GET /api/v1/orders/{id}
```

**Parameters:**
- `id` (required): Mã đơn hàng

**Response Example:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "order_code": "ORD001",
        "status": "Đã giao",
        "created_at": "2026-02-03 10:30:00",
        "delivery_address": "123 Nguyễn Huệ, Q1, TP.HCM",
        "phone": "0901234567",
        "items": [
            {
                "food_id": 2,
                "food_name": "Cơm tấm sườn bì chả",
                "food_image": "http://localhost:8000/images/com-tam.jpg",
                "quantity": 2,
                "price": 50000,
                "subtotal": 100000
            }
        ],
        "summary": {
            "items_total": 100000,
            "discount_amount": 10000,
            "final_total": 90000
        },
        "discount": {
            "code": "GIAM10K",
            "amount": 10000
        }
    }
}
```

**Controller:** `App\Http\Controllers\Api\OrderController@show`  
**View:** `resources/views/orders/api-detail.blade.php`  
**Route:** `Route::get('/orders/{id}')` → returns API view

---

### 2. REVIEWS API

#### ⭐ Tạo đánh giá món ăn (POST)
```
POST /api/v1/reviews
```

**Request Body (FormData):**
```javascript
{
    "order_id": 1,          // Mã đơn hàng (required)
    "food_id": 2,           // Mã món ăn (required)
    "rating": 5,            // Điểm đánh giá 1-5 (required)
    "content": "Ngon quá!", // Nội dung đánh giá (required, max: 1000)
    "image": File           // Hình ảnh (optional, max: 5MB)
}
```

**Response Example:**
```json
{
    "success": true,
    "message": "Đánh giá món ăn thành công",
    "data": {
        "id": 15,
        "order_id": 1,
        "food_id": 2,
        "rating": 5,
        "content": "Ngon quá!",
        "image": "http://localhost:8000/storage/reviews/1738588800_abc123.jpg",
        "status": "Đã duyệt",
        "created_at": "2026-02-03 14:30:00"
    }
}
```

**Validation Rules:**
- `order_id`: required, integer, exists in `don_hang`
- `food_id`: required, integer, exists in `mon_an`
- `rating`: required, integer, min:1, max:5
- `content`: required, string, max:1000
- `image`: nullable, image, mimes:jpg,jpeg,png, max:5120 (5MB)

**Business Logic:**
- ✅ Kiểm tra đơn hàng thuộc về user hiện tại
- ✅ Kiểm tra món ăn có trong đơn hàng
- ✅ Kiểm tra chưa đánh giá trước đó (1 user - 1 đánh giá/món/đơn)
- ✅ Tự động set `trang_thai = 'Đã duyệt'` (auto approve)
- ✅ Upload hình ảnh vào `storage/reviews/`

**Controller:** `App\Http\Controllers\Api\ReviewController@store`  
**View:** `resources/views/reviews/api-create.blade.php`  
**Route:** `Route::get('/reviews/create/{donHang}/{monAn}')` → returns API view

---

#### 🔍 Kiểm tra đã đánh giá (GET)
```
GET /api/v1/reviews/check/{orderId}/{foodId}
```

**Parameters:**
- `orderId`: Mã đơn hàng
- `foodId`: Mã món ăn

**Response Example:**
```json
{
    "success": true,
    "data": {
        "reviewed": true,
        "review": {
            "id": 15,
            "rating": 5,
            "content": "Ngon quá!",
            "created_at": "2026-02-03 14:30:00"
        }
    }
}
```

**Controller:** `App\Http\Controllers\Api\ReviewController@checkReviewed`

---

## 🎯 FRONTEND INTEGRATION

### 1. Orders History Page

**File:** `resources/views/orders/api-history.blade.php`

**JavaScript API Call:**
```javascript
async function loadOrders(page = 1) {
    const response = await fetch(`/api/v1/orders?per_page=10&page=${page}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    });

    const result = await response.json();
    
    if (response.ok && result.success) {
        renderOrders(result.data);
        renderPagination(result.pagination);
    }
}
```

**Features:**
- ✅ Loading spinner khi fetch data
- ✅ Error handling và retry button
- ✅ Pagination (prev/next + page numbers)
- ✅ Dynamic rendering order cards
- ✅ Status badges với màu sắc
- ✅ Click vào card → redirect `/orders/{id}`

---

### 2. Order Detail Page

**File:** `resources/views/orders/api-detail.blade.php`

**JavaScript API Call:**
```javascript
async function loadOrderDetail() {
    const orderId = {{ $orderId }};
    const response = await fetch(`/api/v1/orders/${orderId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    });

    const result = await response.json();
    
    if (response.ok && result.success) {
        renderOrderDetail(result.data);
    }
}
```

**Features:**
- ✅ Loading spinner khi fetch data
- ✅ Error handling và retry button
- ✅ 3-section layout: Order Info + Food Items + Summary
- ✅ Review buttons cho từng món ăn
- ✅ Link to review page: `/reviews/create/{orderId}/{foodId}`
- ✅ Discount calculation display

---

### 3. Review Create Page

**File:** `resources/views/reviews/api-create.blade.php`

**JavaScript API Call:**
```javascript
async function submitReview(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('food_id', foodId);
    formData.append('rating', document.getElementById('ratingValue').value);
    formData.append('content', document.getElementById('noiDung').value);

    const imageFile = document.getElementById('hinhAnh').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }

    const response = await fetch('/api/v1/reviews', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: formData
    });

    const result = await response.json();

    if (response.ok && result.success) {
        showSuccess(result.message);
        setTimeout(() => {
            window.location.href = `/orders/${orderId}`;
        }, 2000);
    }
}
```

**Features:**
- ✅ Load order & food info from API
- ✅ Star rating (1-5 stars) với animation
- ✅ Textarea với character counter (max 1000)
- ✅ Image upload với preview và remove button
- ✅ FormData để support file upload
- ✅ Field-level validation errors
- ✅ Success message + auto redirect sau 2 giây
- ✅ Loading states cho submit button

---

## 🔐 AUTHENTICATION

Tất cả API endpoints sử dụng **dual authentication**:

1. **Web Session Authentication** (cho browser)
   ```php
   Route::middleware('web')->group(function () {
       Route::prefix('v1')->middleware('auth')->group(function () {
           // Orders & Reviews routes
       });
   });
   ```

2. **Sanctum Token** (cho mobile/SPA - nếu cần)
   ```php
   Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
       // Same routes with token authentication
   });
   ```

**CSRF Protection:**
- Tất cả POST/PUT/DELETE requests cần `X-CSRF-TOKEN` header
- Token được lấy từ `{{ csrf_token() }}` trong Blade template

---

## 📊 DATABASE SCHEMA

### Bảng `binh_luans`
```sql
- id (primary key)
- ma_mon_an (foreign key → mon_an.MaMonAn)
- ma_nguoi_dung (foreign key → users.MaNguoiDung)
- ma_don_hang (foreign key → don_hang.MaDonHang)
- diem_danh_gia (integer, 1-5)
- noi_dung (text)
- hinh_anh (string, nullable)
- da_mua (boolean, default: true)
- trang_thai (enum: 'Chờ duyệt', 'Đã duyệt', 'Từ chối')
- created_at
- updated_at
```

**Constraints:**
- Unique: `(ma_nguoi_dung, ma_mon_an, ma_don_hang)` - Một user chỉ đánh giá 1 lần/món/đơn

---

## ✅ TESTING CHECKLIST

### Orders History
- [x] GET `/api/v1/orders` trả về danh sách đơn hàng
- [x] Pagination hoạt động đúng
- [x] Loading spinner hiển thị khi fetch
- [x] Error handling khi API fail
- [x] Click order card redirect đúng

### Order Detail
- [x] GET `/api/v1/orders/{id}` trả về chi tiết đơn hàng
- [x] Hiển thị đầy đủ: info + items + summary
- [x] Review buttons chỉ hiện với đơn hàng "Đã giao"
- [x] Discount calculation đúng
- [x] Loading & error states hoạt động

### Reviews
- [x] POST `/api/v1/reviews` tạo đánh giá thành công
- [x] Validation hoạt động đúng (rating, content, image)
- [x] Image upload & preview hoạt động
- [x] Character counter chính xác
- [x] Auto redirect sau khi submit thành công
- [x] Review hiển thị ngay trên trang chi tiết món ăn (trang_thai = 'Đã duyệt')

---

## 🎨 UI/UX FEATURES

### Common Features
- ✅ Loading spinners với animation
- ✅ Error messages với retry button
- ✅ Success alerts với auto-dismiss
- ✅ Responsive design (mobile-friendly)
- ✅ Smooth transitions và animations

### Orders History
- ✅ Order cards với hover effects
- ✅ Status badges với màu sắc khác nhau
- ✅ Pagination với prev/next và page numbers
- ✅ Empty state khi chưa có đơn hàng

### Order Detail
- ✅ 3-section grid layout
- ✅ Food items với images
- ✅ Review buttons với icon
- ✅ Summary box với discount calculation

### Reviews
- ✅ Star rating với hover effects
- ✅ Character counter real-time
- ✅ Image preview với remove button
- ✅ Form validation với field-level errors
- ✅ Submit button loading state

---

## 📝 CODE STRUCTURE

```
app/
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── OrderController.php      ✅ RESTful
│           └── ReviewController.php     ✅ RESTful
│
├── Models/
│   ├── DonHang.php
│   ├── DonHangChiTiet.php
│   └── BinhLuan.php
│
resources/
└── views/
    ├── orders/
    │   ├── api-history.blade.php   ✅ API Integration
    │   └── api-detail.blade.php    ✅ API Integration
    │
    └── reviews/
        └── api-create.blade.php    ✅ API Integration
│
routes/
├── api.php
│   └── Route::middleware('web')->group()
│       └── Route::prefix('v1')->middleware('auth')->group()
│           ├── GET  /orders          ✅
│           ├── GET  /orders/{id}     ✅
│           ├── POST /reviews         ✅
│           └── GET  /reviews/check/{orderId}/{foodId} ✅
│
└── web.php
    └── Route::middleware('auth')->group()
        ├── GET /orders/history       → api-history view
        ├── GET /orders/{id}          → api-detail view
        └── GET /reviews/create/{donHang}/{monAn} → api-create view
```

---

## 🚀 KẾT LUẬN

### ✅ HOÀN THÀNH 100%

**Orders Module:**
- ✅ RESTful API endpoints (GET /orders, GET /orders/{id})
- ✅ Frontend views kết nối API (api-history, api-detail)
- ✅ Pagination, loading states, error handling
- ✅ Web session authentication

**Reviews Module:**
- ✅ RESTful API endpoint (POST /reviews)
- ✅ Frontend view kết nối API (api-create)
- ✅ Image upload, validation, auto-approve
- ✅ Integration với orders & food detail

**Code Quality:**
- ✅ Chuẩn RESTful API design
- ✅ Proper HTTP methods (GET, POST)
- ✅ JSON response format nhất quán
- ✅ Error handling đầy đủ
- ✅ Loading states & UX tốt
- ✅ Responsive design

---

## 📌 NOTES

1. **Auto-Approve Reviews**: Reviews được tự động duyệt (`trang_thai = 'Đã duyệt'`) để đơn giản hóa flow
2. **Image Storage**: Review images được lưu trong `storage/app/public/reviews/`
3. **Pagination**: Default 10 items per page, có thể customize
4. **CSRF Protection**: Tất cả POST requests cần CSRF token
5. **Authentication**: Web session cho browser, Sanctum token cho mobile/SPA

---

**Ngày kiểm tra:** 3 tháng 2, 2026  
**Status:** ✅ HOÀN THÀNH - 100% RESTful API Implementation

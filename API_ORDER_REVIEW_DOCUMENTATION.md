# API Documentation - Order & Review Management

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
Tất cả các API endpoint đều yêu cầu authentication token (Bearer Token) trong header:
```
Authorization: Bearer {your_token}
```

---

## 📦 Order APIs

### 1. Lấy danh sách đơn hàng
**Endpoint:** `GET /orders`

**Query Parameters:**
- `per_page` (optional): Số lượng đơn hàng mỗi trang (default: 10)

**Response Success (200):**
```json
{
  "success": true,
  "message": "Lấy danh sách đơn hàng thành công",
  "data": [
    {
      "id": 1,
      "order_code": "ORD001",
      "total_amount": 150000,
      "status": "Hoàn thành",
      "delivery_address": "123 Đường ABC, Quận 1, TP.HCM",
      "customer_name": "Nguyễn Văn A",
      "phone": "0901111111",
      "payment_method": "COD",
      "note": "Giao hàng buổi chiều",
      "created_at": "2026-02-02 10:30:00",
      "items": [
        {
          "food_id": 5,
          "food_name": "Phở gà",
          "quantity": 1,
          "price": 50000,
          "subtotal": 50000
        }
      ]
    }
  ],
  "pagination": {
    "total": 25,
    "per_page": 10,
    "current_page": 1,
    "last_page": 3
  }
}
```

---

### 2. Lấy chi tiết đơn hàng
**Endpoint:** `GET /orders/{id}`

**URL Parameters:**
- `id` (required): Mã đơn hàng

**Response Success (200):**
```json
{
  "success": true,
  "message": "Lấy chi tiết đơn hàng thành công",
  "data": {
    "id": 1,
    "order_code": "ORD001",
    "status": "Hoàn thành",
    "total_amount": 150000,
    "delivery_address": "123 Đường ABC, Quận 1, TP.HCM",
    "customer_name": "Nguyễn Văn A",
    "phone": "0901111111",
    "payment_method": "COD",
    "note": "Giao hàng buổi chiều",
    "created_at": "2026-02-02 10:30:00",
    "items": [
      {
        "food_id": 5,
        "food_name": "Phở gà",
        "food_image": "http://localhost:8000/images/pho-ga.jpg",
        "quantity": 1,
        "price": 50000,
        "subtotal": 50000
      }
    ],
    "summary": {
      "subtotal": 150000,
      "discount": 0,
      "shipping_fee": 0,
      "total": 150000
    },
    "voucher": null
  }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Không tìm thấy đơn hàng"
}
```

---

## ⭐ Review APIs

### 3. Tạo đánh giá món ăn
**Endpoint:** `POST /reviews`

**Request Body (multipart/form-data):**
```json
{
  "order_id": 1,
  "food_id": 5,
  "rating": 5,
  "content": "Món ăn rất ngon, đóng gói cẩn thận",
  "image": "file" // Optional, JPG/JPEG/PNG, max 5MB
}
```

**Validation Rules:**
- `order_id`: required, integer, phải tồn tại trong DB
- `food_id`: required, integer, phải tồn tại trong DB
- `rating`: required, integer, từ 1-5
- `content`: required, string, max 1000 ký tự
- `image`: optional, image, JPG/JPEG/PNG, max 5MB

**Response Success (201):**
```json
{
  "success": true,
  "message": "Đánh giá món ăn thành công",
  "data": {
    "id": 10,
    "order_id": 1,
    "food_id": 5,
    "rating": 5,
    "content": "Món ăn rất ngon, đóng gói cẩn thận",
    "image": "http://localhost:8000/storage/reviews/1738491234_abc123.jpg",
    "status": "Chờ duyệt",
    "created_at": "2026-02-02 14:30:00"
  }
}
```

**Response Error (422):**
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "rating": ["Điểm đánh giá phải từ 1 đến 5 sao"],
    "content": ["Vui lòng nhập nội dung đánh giá"]
  }
}
```

**Response Error (400):**
```json
{
  "success": false,
  "message": "Bạn đã đánh giá món ăn này rồi"
}
```

---

### 4. Kiểm tra đã đánh giá chưa
**Endpoint:** `GET /reviews/check/{orderId}/{foodId}`

**URL Parameters:**
- `orderId` (required): Mã đơn hàng
- `foodId` (required): Mã món ăn

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "has_reviewed": true
  }
}
```

---

### 5. Lấy danh sách đánh giá của user
**Endpoint:** `GET /reviews`

**Query Parameters:**
- `per_page` (optional): Số lượng đánh giá mỗi trang (default: 10)

**Response Success (200):**
```json
{
  "success": true,
  "message": "Lấy danh sách đánh giá thành công",
  "data": [
    {
      "id": 10,
      "order_id": 1,
      "food_id": 5,
      "food_name": "Phở gà",
      "food_image": "http://localhost:8000/images/pho-ga.jpg",
      "rating": 5,
      "content": "Món ăn rất ngon, đóng gói cẩn thận",
      "image": "http://localhost:8000/storage/reviews/1738491234_abc123.jpg",
      "status": "Đã duyệt",
      "created_at": "2026-02-02 14:30:00"
    }
  ],
  "pagination": {
    "total": 15,
    "per_page": 10,
    "current_page": 1,
    "last_page": 2
  }
}
```

---

## 🔐 Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Đơn hàng không tồn tại hoặc không thuộc về bạn"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Đã có lỗi xảy ra, vui lòng thử lại sau"
}
```

---

## 📝 Notes

1. **Authentication**: Tất cả API đều yêu cầu Bearer Token
2. **Pagination**: Sử dụng query parameter `per_page` để điều chỉnh số lượng items
3. **Image Upload**: Chỉ hỗ trợ JPG, JPEG, PNG với kích thước tối đa 5MB
4. **Status Values**: 
   - Order: "Chờ xác nhận", "Đang xử lý", "Hoàn thành", "Đã hủy"
   - Review: "Chờ duyệt", "Đã duyệt", "Từ chối"
5. **Date Format**: YYYY-MM-DD HH:mm:ss

---

## 🧪 Testing với Postman

### 1. Login để lấy token
```
POST http://localhost:8000/api/v1/login
Body: {
  "email": "user@example.com",
  "password": "password"
}
```

### 2. Set Bearer Token
Copy token từ response và add vào Header:
```
Authorization: Bearer {token}
```

### 3. Test các endpoint
```
GET http://localhost:8000/api/v1/orders
GET http://localhost:8000/api/v1/orders/1
POST http://localhost:8000/api/v1/reviews
GET http://localhost:8000/api/v1/reviews
```

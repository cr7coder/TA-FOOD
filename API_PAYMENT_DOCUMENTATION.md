# API PAYMENT DOCUMENTATION

## Tổng quan
API thanh toán cho hệ thống TA-Food, tuân thủ chuẩn RESTful API.

**Base URL**: `http://localhost:8000/api/v1`

**Authentication**: Bearer Token (Sanctum)

---

## Endpoints

### 1. Lấy danh sách thanh toán
**GET** `/payments`

Lấy danh sách các thanh toán của người dùng đã đăng nhập.

#### Headers
```
Authorization: Bearer {token}
```

#### Response Success (200)
```json
{
    "success": true,
    "data": [
        {
            "payment_id": 1,
            "order_id": 15,
            "amount": 250000,
            "method": "MoMo",
            "status": "Đã thanh toán",
            "payment_date": "2026-02-03 10:30:00",
            "created_at": "2026-02-03 10:25:00"
        },
        {
            "payment_id": 2,
            "order_id": 16,
            "amount": 150000,
            "method": "COD",
            "status": "Đã thanh toán",
            "payment_date": "2026-02-02 14:20:00",
            "created_at": "2026-02-02 14:20:00"
        }
    ],
    "pagination": {
        "current_page": 1,
        "total_pages": 3,
        "total_items": 25,
        "per_page": 10
    }
}
```

---

### 2. Lấy chi tiết thanh toán
**GET** `/payments/{id}`

Lấy thông tin chi tiết một thanh toán.

#### Headers
```
Authorization: Bearer {token}
```

#### Parameters
- `id` (required): ID của thanh toán

#### Response Success (200)
```json
{
    "success": true,
    "data": {
        "payment_id": 1,
        "order_id": 15,
        "amount": 250000,
        "method": "MoMo",
        "status": "Đã thanh toán",
        "payment_date": "2026-02-03 10:30:00",
        "order": {
            "customer_name": "Nguyễn Văn A",
            "phone": "0123456789",
            "address": "123 Đường ABC, Quận 1, TP.HCM",
            "total_amount": 250000,
            "status": "Đã thanh toán",
            "items": [
                {
                    "food_name": "Phở Bò",
                    "quantity": 2,
                    "price": 50000,
                    "total": 100000
                },
                {
                    "food_name": "Cơm Tấm",
                    "quantity": 3,
                    "price": 50000,
                    "total": 150000
                }
            ]
        },
        "created_at": "2026-02-03 10:25:00"
    }
}
```

#### Response Error (404)
```json
{
    "success": false,
    "message": "Không tìm thấy thanh toán"
}
```

---

### 3. Tạo thanh toán mới
**POST** `/payments`

Tạo thanh toán cho đơn hàng.

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Request Body
```json
{
    "order_id": 15,
    "payment_method": "MoMo",
    "success": true
}
```

#### Fields
- `order_id` (required, integer): ID đơn hàng
- `payment_method` (required, string): Phương thức thanh toán
  - Giá trị hợp lệ: `COD`, `Online`, `MoMo`, `ZaloPay`, `VNPay`
- `success` (optional, boolean): Mô phỏng kết quả thanh toán online (mặc định: true)

#### Response Success (201)
```json
{
    "success": true,
    "message": "Thanh toán thành công",
    "data": {
        "payment_id": 1,
        "order_id": 15,
        "amount": 250000,
        "method": "MoMo",
        "status": "Đã thanh toán",
        "order_status": "Đã thanh toán",
        "payment_date": "2026-02-03 10:30:00"
    }
}
```

#### Response Error (404)
```json
{
    "success": false,
    "message": "Không tìm thấy đơn hàng"
}
```

#### Response Error (400)
```json
{
    "success": false,
    "message": "Đơn hàng đã được thanh toán"
}
```

---

### 4. Cập nhật trạng thái thanh toán
**PUT** `/payments/{id}`

Cập nhật trạng thái thanh toán (dùng cho callback từ cổng thanh toán).

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Parameters
- `id` (required): ID của thanh toán

#### Request Body
```json
{
    "status": "Đã thanh toán"
}
```

#### Fields
- `status` (required, string): Trạng thái thanh toán
  - Giá trị hợp lệ: `Chờ thanh toán`, `Đã thanh toán`, `Thất bại`, `Đã hoàn tiền`

#### Response Success (200)
```json
{
    "success": true,
    "message": "Cập nhật trạng thái thanh toán thành công",
    "data": {
        "payment_id": 1,
        "status": "Đã thanh toán",
        "order_status": "Đã thanh toán"
    }
}
```

#### Response Error (404)
```json
{
    "success": false,
    "message": "Không tìm thấy thanh toán"
}
```

---

### 5. Kiểm tra trạng thái thanh toán
**GET** `/payments/check/{orderId}`

Kiểm tra trạng thái thanh toán của đơn hàng.

#### Headers
```
Authorization: Bearer {token}
```

#### Parameters
- `orderId` (required): ID của đơn hàng

#### Response Success (200)
```json
{
    "success": true,
    "data": {
        "order_id": 15,
        "payment_exists": true,
        "payment_id": 1,
        "payment_status": "Đã thanh toán",
        "payment_method": "MoMo",
        "amount": 250000,
        "order_status": "Đã thanh toán",
        "is_paid": true
    }
}
```

#### Response (Chưa có thanh toán)
```json
{
    "success": true,
    "data": {
        "order_id": 15,
        "payment_exists": false,
        "payment_id": null,
        "payment_status": "Chưa có thanh toán",
        "payment_method": null,
        "amount": null,
        "order_status": "Chờ xử lý",
        "is_paid": false
    }
}
```

---

## Quy trình thanh toán

### 1. Thanh toán COD (Tiền mặt khi nhận hàng)
```
1. Tạo đơn hàng: POST /checkout/create-order (payment_method: "COD")
2. Hệ thống tự động tạo thanh toán với trạng thái "Đã thanh toán"
3. Không cần thêm bước nào khác
```

### 2. Thanh toán Online (MoMo, ZaloPay, VNPay)
```
1. Tạo đơn hàng: POST /checkout/create-order (payment_method: "MoMo")
2. Hệ thống tạo thanh toán với trạng thái "Chờ thanh toán"
3. Redirect user đến cổng thanh toán (mô phỏng)
4. User xác nhận thanh toán
5. Callback cập nhật trạng thái: PUT /payments/{id}
6. Kiểm tra trạng thái: GET /payments/check/{orderId}
```

### 3. Mô phỏng thanh toán (Development)
```
POST /payments
{
    "order_id": 15,
    "payment_method": "MoMo",
    "success": true  // true = thành công, false = thất bại
}
```

---

## Trạng thái thanh toán

| Trạng thái | Mô tả |
|------------|-------|
| Chờ thanh toán | Đang chờ xử lý thanh toán online |
| Đã thanh toán | Thanh toán thành công |
| Thất bại | Thanh toán thất bại |
| Đã hoàn tiền | Đã hoàn tiền cho khách hàng |

---

## Phương thức thanh toán

| Phương thức | Mô tả |
|-------------|-------|
| COD | Tiền mặt khi nhận hàng |
| Online | Thanh toán online (chung) |
| MoMo | Ví điện tử MoMo |
| ZaloPay | Ví điện tử ZaloPay |
| VNPay | Cổng thanh toán VNPay |

---

## Error Codes

| HTTP Status | Message | Mô tả |
|------------|---------|-------|
| 200 | OK | Thành công |
| 201 | Created | Tạo mới thành công |
| 400 | Bad Request | Dữ liệu không hợp lệ |
| 401 | Unauthorized | Chưa đăng nhập |
| 404 | Not Found | Không tìm thấy |
| 500 | Internal Server Error | Lỗi server |

---

## Ví dụ sử dụng

### Ví dụ 1: Thanh toán COD
```bash
# 1. Tạo đơn hàng với COD
curl -X POST http://localhost:8000/api/v1/checkout/create-order \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Nguyễn Văn A",
    "phone": "0123456789",
    "address": "123 Đường ABC",
    "payment_method": "COD",
    "voucher_id": null,
    "note": "Giao hàng buổi chiều"
  }'

# Response: Đơn hàng và thanh toán tự động được tạo với trạng thái "Đã thanh toán"
```

### Ví dụ 2: Thanh toán MoMo
```bash
# 1. Tạo đơn hàng
curl -X POST http://localhost:8000/api/v1/checkout/create-order \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Nguyễn Văn A",
    "phone": "0123456789",
    "address": "123 Đường ABC",
    "payment_method": "MoMo"
  }'

# Response: order_id = 15

# 2. Xử lý thanh toán (mô phỏng)
curl -X POST http://localhost:8000/api/v1/payments \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": 15,
    "payment_method": "MoMo",
    "success": true
  }'

# 3. Kiểm tra trạng thái
curl -X GET http://localhost:8000/api/v1/payments/check/15 \
  -H "Authorization: Bearer {token}"
```

### Ví dụ 3: Xem lịch sử thanh toán
```bash
# Lấy danh sách thanh toán
curl -X GET http://localhost:8000/api/v1/payments \
  -H "Authorization: Bearer {token}"

# Xem chi tiết thanh toán
curl -X GET http://localhost:8000/api/v1/payments/1 \
  -H "Authorization: Bearer {token}"
```

---

## Lưu ý
1. Tất cả endpoints đều yêu cầu authentication (Bearer Token)
2. Chỉ có thể thao tác với thanh toán của chính mình
3. Phương thức COD tự động tạo thanh toán với trạng thái "Đã thanh toán"
4. Phương thức Online cần thêm bước xử lý thanh toán
5. Sử dụng tham số `success` trong môi trường development để mô phỏng kết quả thanh toán

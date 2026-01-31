# 🍔 Food Ordering System

Hệ thống đặt món ăn trực tuyến được xây dựng bằng Laravel Framework.

## 📋 Mục lục

- [Giới thiệu](#giới-thiệu)
- [Tính năng](#tính-năng)
- [Công nghệ sử dụng](#công-nghệ-sử-dụng)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Cài đặt](#cài-đặt)
- [Cấu hình](#cấu-hình)
- [Chạy ứng dụng](#chạy-ứng-dụng)
- [Testing](#testing)
- [Cấu trúc dự án](#cấu-trúc-dự-án)
- [Đóng góp](#đóng-góp)
- [License](#license)

---

## 📖 Giới thiệu

Food Ordering System là một nền tảng đặt món ăn trực tuyến toàn diện, cho phép người dùng:
- Duyệt và tìm kiếm món ăn từ nhiều nhà hàng
- Đặt hàng và thanh toán trực tuyến
- Theo dõi trạng thái đơn hàng
- Đánh giá và nhận xét món ăn

---

## ✨ Tính năng

### 👥 Người dùng (Customer)
- ✅ Đăng ký và đăng nhập
- ✅ Duyệt danh sách món ăn và nhà hàng
- ✅ Thêm món vào giỏ hàng
- ✅ Áp dụng mã giảm giá
- ✅ Đặt hàng và thanh toán
- ✅ Xem lịch sử đơn hàng
- ✅ Đánh giá và nhận xét món ăn

### 🏪 Người bán (Seller)
- ✅ Quản lý thông tin nhà hàng
- ✅ Quản lý danh sách món ăn
- ✅ Xem và xử lý đơn hàng
- ✅ Thống kê doanh thu

### 👨‍💼 Quản trị viên (Admin)
- ✅ Quản lý người dùng
- ✅ Quản lý mã giảm giá
- ✅ Quản lý đối tác vận chuyển
- ✅ Xem báo cáo thống kê

---

## 🛠 Công nghệ sử dụng

### Backend
- **Framework:** Laravel 12.x
- **PHP:** 8.2+
- **Database:** MySQL 8.0
- **Authentication:** Laravel Sanctum
- **Cache:** Redis (optional)

### Frontend
- **Template Engine:** Blade
- **CSS Framework:** Tailwind CSS 4.x
- **JavaScript:** Vanilla JS + Axios
- **Build Tool:** Vite

### Testing
- **PHP Testing:** PHPUnit
- **JavaScript Testing:** Jest

---

## 💻 Yêu cầu hệ thống

- **PHP** >= 8.2
- **Composer** >= 2.x
- **Node.js** >= 18.x
- **NPM** >= 9.x
- **MySQL** >= 8.0
- **Git**

### Extensions PHP cần thiết
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
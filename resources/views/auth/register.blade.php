<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng ký - TAFOOD</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* Copy toàn bộ CSS từ login, chỉ thay đổi một số điều chỉnh */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Copy tất cả CSS từ login page */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/><circle cx="900" cy="800" r="80" fill="url(%23a)"/></svg>');
            animation: float 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-30px) rotate(120deg);
            }

            66% {
                transform: translateY(30px) rotate(240deg);
            }
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            margin: 20px;
            display: flex;
            min-height: auto;
            align-items: stretch;
        }

        .auth-left {
            flex: 1;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            position: relative;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            animation: sparkle 3s ease-in-out infinite;
        }

        @keyframes sparkle {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .auth-left h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .auth-left p {
            font-size: 1rem;
            opacity: 0.9;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .feature-list {
            list-style: none;
            text-align: left;
        }

        .feature-list li {
            margin: 12px 0;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }

        .feature-list li i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .auth-right {
            flex: 1.2;
            padding: 40px 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-height: none;
            overflow-y: visible;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-header h2 {
            color: #333;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .auth-header p {
            color: #666;
            font-size: 1rem;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
            background: white;
            outline: none;
        }

        .form-icon {
            position: absolute;
            left: 15px;
            top: 25px;
            transform: translateY(-50%);
            color: #999;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .form-control:focus+.form-icon {
            color: #ff6b35;
        }

        .form-control.is-invalid {
            background-image: none !important;
            padding-right: 15px !important;
        }

        #password, #password_confirmation {
            padding-right: 45px !important;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 25px;
            transform: translateY(-50%);
            color: #999;
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #ff6b35;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-google-login {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background-color: #fff;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            color: #555;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .btn-google-login:hover {
            background-color: #f8f9fa !important;
            border-color: #ff6b35 !important;
            color: #ff6b35 !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }

        .btn-google-login img {
            width: 22px;
            height: 22px;
            margin-right: 12px;
        }

        .btn-register:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .loading-spinner {
            display: none;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .auth-links {
            text-align: center;
            margin-top: 15px;
        }

        .auth-links a {
            color: #ff6b35;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #e55a2b;
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 15px;
            border: none;
            padding: 12px;
            font-size: 0.9rem;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                margin: 10px;
                max-width: 100%;
            }

            .auth-left {
                padding: 30px 20px;
                min-height: auto;
            }

            .auth-left h1 {
                font-size: 2rem;
                margin-bottom: 5px;
            }

            .auth-left p {
                margin-bottom: 0;
            }

            .feature-list {
                display: none; /* Ẩn bớt tính năng trên mobile để form hiển thị cao hơn */
            }

            .auth-right {
                padding: 30px 20px;
            }

            .auth-header h2 {
                font-size: 1.8rem;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-row .form-group {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 480px) {
            .auth-container {
                margin: 5px;
                border-radius: 15px;
            }

            .auth-left {
                padding: 30px 15px;
            }

            .auth-right {
                padding: 30px 15px;
            }

            .form-control {
                padding: 10px 12px 10px 40px;
            }
        }
        /* Toggle Switch Role Style */
        .role-selector {
            display: flex;
            background: #f3f4f6; /* Nền xám nhạt */
            border-radius: 30px;
            padding: 5px;
            margin-bottom: 25px;
            position: relative;
        }

        .role-selector input[type="radio"] {
            display: none;
        }

        .role-selector label {
            flex: 1;
            text-align: center;
            padding: 12px 15px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            font-size: 1rem;
            color: #6b7280; /* Chữ xám cho mục chưa chọn */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 0;
            user-select: none;
        }

        /* Màu icon mặc định */
        .role-selector label i {
            font-size: 1.1rem;
        }

        /* Màu riêng cho icon khách hàng */
        .role-selector input[type="radio"]#role_khachhang + label i {
            color: #6b7280; 
        }

        /* Màu riêng cho icon nhà cung cấp */
        .role-selector input[type="radio"]#role_nhacungcap + label i {
            color: #6b7280;
        }

        /* Trạng thái được chọn */
        .role-selector input[type="radio"]:checked + label {
            background: white;
            color: #ff6b35; /* Chữ màu cam TA-FOOD khi chọn */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            font-weight: 600;
            transform: scale(1.02);
        }

        /* Màu icon cam khi được chọn */
        .role-selector input[type="radio"]:checked + label i {
            color: #ff6b35;
        }

    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Left Panel -->
        <div class="auth-left">
            <h1>TAFOOD</h1>
            <p>Tham gia cộng đồng đặt món ăn lớn nhất</p>

            <ul class="feature-list">
                <li>
                    <i class="fas fa-gift"></i>
                    <span>Ưu đãi độc quyền cho thành viên</span>
                </li>
                <li>
                    <i class="fas fa-heart"></i>
                    <span>Lưu món ăn yêu thích</span>
                </li>
                <li>
                    <i class="fas fa-history"></i>
                    <span>Theo dõi lịch sử đơn hàng</span>
                </li>
                <li>
                    <i class="fas fa-bell"></i>
                    <span>Nhận thông báo khuyến mãi</span>
                </li>
            </ul>
        </div>

        <!-- Right Panel -->
        <div class="auth-right">
            <div class="auth-header">
                <h2>Đăng ký</h2>
                <p>Tạo tài khoản để bắt đầu hành trình ẩm thực!</p>
            </div>

            <!-- Alert Messages -->
            <div id="alertContainer"></div>

            <form id="registerForm" novalidate>
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Tên đăng nhập">
                        <i class="fas fa-user form-icon"></i>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Họ và tên">
                        <i class="fas fa-id-card form-icon"></i>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Địa chỉ email">
                        <i class="fas fa-envelope form-icon"></i>
                    </div>
                    <div class="form-group">
                        <input type="tel" class="form-control" id="phone" name="phone"
                            placeholder="Số điện thoại (tùy chọn)">
                        <i class="fas fa-phone form-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Mật khẩu (tối thiểu 6 ký tự)">
                    <i class="fas fa-lock form-icon"></i>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        placeholder="Xác nhận mật khẩu">
                    <i class="fas fa-lock form-icon"></i>
                    <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
                </div>

                <div class="role-selector">
                    <input type="radio" id="role_khachhang" name="role" value="KhachHang" checked>
                    <label for="role_khachhang">
                        <i class="fas fa-user"></i> Khách hàng
                    </label>

                    <input type="radio" id="role_nhacungcap" name="role" value="NguoiBan">
                    <label for="role_nhacungcap">
                        <i class="fas fa-building"></i> Người bán
                    </label>
                </div>

                <button type="submit" class="btn-register">
                    <span class="btn-text">Đăng ký tài khoản</span>
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                </button>
            </form>

            <div class="my-3 d-flex align-items-center justify-content-center">
                <hr class="flex-grow-1" style="border-color: #ccc; margin: 0;">
                <span class="px-3 text-muted" style="font-size: 0.9rem; font-weight: 500;">Hoặc</span>
                <hr class="flex-grow-1" style="border-color: #ccc; margin: 0;">
            </div>

            <a href="{{ route('auth.google') }}" class="btn-google-login mb-3">
                <img src="https://developers.google.com/static/identity/images/g-logo.png" alt="Google Logo">
                Đăng ký với Google
            </a>

            <div class="auth-links">
                <p>Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a></p>
                <p><a href="{{ route('foods.index') }}">← Về trang chủ</a></p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const registerForm = document.getElementById('registerForm');
            const alertContainer = document.getElementById('alertContainer');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            // Toggle password visibility
            document.querySelectorAll('.password-toggle').forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const input = this.previousElementSibling.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);

                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            });

            // Password confirmation validation
            confirmPasswordInput.addEventListener('input', function () {
                if (this.value && this.value !== passwordInput.value) {
                    // this.setCustomValidity('Mật khẩu không khớp');
                } else {
                    this.setCustomValidity('');
                }
            });

            // Handle form submission
            registerForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('.btn-register');
                const btnText = submitBtn.querySelector('.btn-text');
                const loadingSpinner = submitBtn.querySelector('.loading-spinner');

                submitBtn.disabled = true;
                btnText.style.opacity = '0';
                loadingSpinner.style.display = 'block';

                // Validate client-side trước
                const valid = await validateRegisterForm();
                if (!valid) {
                    submitBtn.disabled = false;
                    btnText.style.opacity = '1';
                    loadingSpinner.style.display = 'none';
                    return;
                }

                try {
                    // Chuẩn bị data theo RESTful API format (JSON)
                    const registerData = {
                        username: document.getElementById('username').value,
                        password: document.getElementById('password').value,
                        password_confirmation: document.getElementById('password_confirmation').value,
                        name: document.getElementById('fullname').value,
                        email: document.getElementById('email').value,
                        phone: document.getElementById('phone').value,
                        role: document.querySelector('input[name="role"]:checked').value,
                        device_name: 'web-browser'
                    };

                    // Gọi RESTful API endpoint
                    const response = await fetch('/api/v1/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(registerData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert('success', data.message);

                        // Lưu thông tin user và token vào localStorage
                        if (data.data) {
                            localStorage.setItem('user', JSON.stringify(data.data.user));
                            localStorage.setItem('auth_token', data.data.token);
                            localStorage.setItem('token_type', data.data.token_type);
                        }

                        // Tự động kiểm tra giỏ hàng để chuyển hướng thông minh
                        fetch('/cart', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(cartData => {
                            const count = cartData.count || 0;
                            setTimeout(() => {
                                if (count > 0) {
                                    window.location.href = '/checkout';
                                } else {
                                    window.location.href = '{{ route("foods.index") }}';
                                }
                            }, 1200);
                        })
                        .catch(() => {
                            setTimeout(() => {
                                window.location.href = '{{ route("foods.index") }}';
                            }, 1200);
                        });
                    } else {
                        if (data.errors) {
                            let hasAlert = false;
                            for (const [field, messages] of Object.entries(data.errors)) {
                                const input = document.getElementById(field);
                                if (input) {
                                    if (typeof setInvalid === 'function') {
                                        setInvalid(input, messages[0]);
                                    } else {
                                        input.classList.add('is-invalid');
                                    }
                                } else {
                                    hasAlert = true;
                                    showAlert('danger', messages[0]);
                                }
                            }
                            if (!hasAlert) {
                                // No general alert if all errors were mapped to fields
                                // But if there are unmapped errors, they will show.
                            }
                        } else {
                            showAlert('danger', data.message || 'Có lỗi xảy ra');
                        }
                        submitBtn.disabled = false;
                        btnText.style.opacity = '1';
                        loadingSpinner.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Register error:', error);
                    showAlert('danger', 'Có lỗi xảy ra. Vui lòng thử lại!');
                    submitBtn.disabled = false;
                    btnText.style.opacity = '1';
                    loadingSpinner.style.display = 'none';
                }
            });

            function showAlert(type, message) {
                if (window.showAlert) {
                    window.showAlert(type, message);
                } else {
                    const alertContainer = document.getElementById('alertContainer');
                    alertContainer.innerHTML = `
                        <div class="alert alert-${type}" role="alert">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                            ${message}
                        </div>
                    `;
                    setTimeout(() => {
                        alertContainer.innerHTML = '';
                    }, 4000);
                }
            }

            // Input focus effects
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function () {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function () {
                    this.parentElement.classList.remove('focused');
                });
            });
        });

    </script>
    <script src="{{ asset('js/register-validator.js') }}"></script>
    @include('client.partials._global-alert')
</body>

</html>
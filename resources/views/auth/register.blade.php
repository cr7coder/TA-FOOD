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
            position: absolute;
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
            max-width: 1000px;
            margin: 20px;
            display: flex;
            min-height: 650px;
        }

        .auth-left {
            flex: 1;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            padding: 60px 40px;
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
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .auth-left p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .feature-list {
            list-style: none;
            text-align: left;
        }

        .feature-list li {
            margin: 15px 0;
            display: flex;
            align-items: center;
            font-size: 1rem;
        }

        .feature-list li i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .auth-right {
            flex: 1.2;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-height: 650px;
            overflow-y: auto;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-header h2 {
            color: #333;
            font-size: 2.2rem;
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
            margin-bottom: 20px;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
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
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .form-control:focus+.form-icon {
            color: #ff6b35;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
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
            padding: 15px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
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
                padding: 40px 20px;
                min-height: 250px;
            }

            .auth-left h1 {
                font-size: 2.2rem;
            }

            .auth-right {
                padding: 40px 20px;
            }

            .auth-header h2 {
                font-size: 1.8rem;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-row .form-group {
                margin-bottom: 20px;
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

            <form id="registerForm">
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
                        placeholder="Mật khẩu (tối thiểu 6 ký tự)" minlength="6">
                    <i class="fas fa-lock form-icon"></i>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        placeholder="Xác nhận mật khẩu">
                    <i class="fas fa-lock form-icon"></i>
                    <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
                </div>

                <button type="submit" class="btn-register">
                    <span class="btn-text">Đăng ký tài khoản</span>
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                </button>
            </form>

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

                        // Redirect về trang chủ sau 1.5 giây
                        setTimeout(() => {
                            window.location.href = '{{ route("foods.index") }}';
                        }, 1500);
                    } else {
                        if (data.errors) {
                            let errorMessage = '';
                            Object.values(data.errors).forEach(errors => {
                                errors.forEach(error => {
                                    errorMessage += error + '<br>';
                                });
                            });
                            showAlert('danger', errorMessage);
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
        async function validateRegisterForm() {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const alertContainer = document.getElementById('alertContainer');

            alertContainer.innerHTML = '';

            // Tên đăng nhập
            const username = usernameInput.value.trim();
            if (!username) {
                showAlert('danger', 'Tên đăng nhập không được bỏ trống');
                usernameInput.focus();
                return false;
            }
            if (username.length > 50) {
                showAlert('danger', 'Tên đăng nhập không quá 50 ký tự');
                usernameInput.focus();
                return false;
            }
            if (!/^[A-Za-z0-9_]+$/.test(username)) {
                showAlert('danger', 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới');
                usernameInput.focus();
                return false;
            }
            if (await checkExists('username', username)) {
                showAlert('danger', 'Tên đăng nhập đã tồn tại');
                usernameInput.focus();
                return false;
            }

            // Mật khẩu
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            if (!password) {
                showAlert('danger', 'Mật khẩu không được bỏ trống');
                passwordInput.focus();
                return false;
            }
            if (password.length < 6) {
                showAlert('danger', 'Mật khẩu phải từ 6 ký tự trở lên');
                passwordInput.focus();
                return false;
            }
            if (confirmPassword !== password) {
                showAlert('danger', 'Xác nhận mật khẩu không khớp');
                confirmPasswordInput.focus();
                return false;
            }

            // Email
            const email = emailInput.value.trim();
            if (!email) {
                showAlert('danger', 'Email không được bỏ trống');
                emailInput.focus();
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showAlert('danger', 'Email không hợp lệ');
                emailInput.focus();
                return false;
            }
            if (await checkExists('email', email)) {
                showAlert('danger', 'Email đã được sử dụng');
                emailInput.focus();
                return false;
            }

            // Số điện thoại (nếu có nhập)
            const phone = phoneInput.value.trim();
            if (phone) {
                if (!/^0\d{8,10}$/.test(phone)) {
                    showAlert('danger', 'Số điện thoại không hợp lệ');
                    phoneInput.focus();
                    return false;
                }
            }

            return true;
        }
    </script>
    <script src="{{ asset('js/register-validator.js') }}"></script>
</body>

</html>
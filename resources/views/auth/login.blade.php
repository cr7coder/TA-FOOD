<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập - CabaFood</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* Sử dụng lại CSS từ trước, chỉ cập nhật một số điều chỉnh nhỏ */
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
            max-width: 900px;
            margin: 20px;
            display: flex;
            min-height: 600px;
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
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
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

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 1rem;
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
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .form-control:focus+.form-icon {
            color: #ff6b35;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #ff6b35;
        }

        .form-check {
            margin: 20px 0;
            display: flex;
            align-items: center;
        }

        .form-check-input {
            margin-right: 10px;
            accent-color: #ff6b35;
        }

        .form-check-label {
            color: #666;
            font-size: 0.95rem;
        }

        .btn-login {
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
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
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
            margin-top: 20px;
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
            margin-bottom: 20px;
            border: none;
            padding: 15px;
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
                min-height: 300px;
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
                padding: 12px 15px 12px 45px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Left Panel -->
        <div class="auth-left">
            <h1>CabaFood</h1>
            <p>Nền tảng đặt món ăn hàng đầu Việt Nam</p>

            <ul class="feature-list">
                <li>
                    <i class="fas fa-utensils"></i>
                    <span>Hàng ngàn món ăn ngon</span>
                </li>
                <li>
                    <i class="fas fa-shipping-fast"></i>
                    <span>Giao hàng nhanh chóng</span>
                </li>
                <li>
                    <i class="fas fa-star"></i>
                    <span>Đánh giá chất lượng cao</span>
                </li>
                <li>
                    <i class="fas fa-shield-alt"></i>
                    <span>Thanh toán an toàn</span>
                </li>
            </ul>
        </div>

        <!-- Right Panel -->
        <div class="auth-right">
            <div class="auth-header">
                <h2>Đăng nhập</h2>
                <p>Chào mừng bạn quay trở lại!</p>
            </div>

            <!-- Alert Messages -->
            <div id="alertContainer"></div>

            <form id="loginForm" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" id="login" name="login"
                        placeholder="Tên đăng nhập hoặc Email">
                    <i class="fas fa-user form-icon"></i>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Nhập mật khẩu">
                    <i class="fas fa-lock form-icon"></i>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <span class="btn-text">Đăng nhập</span>
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                </button>
            </form>

            <div class="auth-links">
                <p>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
                <p><a href="#">Quên mật khẩu?</a></p>
                <p><a href="{{ route('foods.index') }}">← Về trang chủ</a></p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/login-validator.js') }}"></script>
    {{--
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('loginForm');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const alertContainer = document.getElementById('alertContainer');

            // Toggle password visibility
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            // Handle form submission
            loginForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Client-side validate
                if (!validateLoginForm()) {
                    return;
                }

                const submitBtn = this.querySelector('.btn-login');
                const btnText = submitBtn.querySelector('.btn-text');
                const loadingSpinner = submitBtn.querySelector('.loading-spinner');

                // Show loading state
                submitBtn.disabled = true;
                btnText.style.opacity = '0';
                loadingSpinner.style.display = 'block';

                try {
                    const formData = new FormData(this);
                    const response = await fetch('{{ route("login.post") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    // Kiểm tra lỗi từ server và hiện thông báo đúng mã lỗi
                    if (data.success) {
                        showAlert('success', data.message);

                        if (data.user) {
                            localStorage.setItem('user', JSON.stringify(data.user));
                        }

                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        // Hiển thị lỗi server trả về đúng như 1E.2 hoặc 1E.4
                        // Server cần trả về message là "Tên đăng nhập không tồn tại (1E.2)" hoặc "Mật khẩu không đúng (1E.4)"
                        showAlert('danger', data.message);
                        resetSubmitButton();
                    }
                } catch (error) {
                    showAlert('danger', 'Có lỗi xảy ra. Vui lòng thử lại!');
                    resetSubmitButton();
                }

                function resetSubmitButton() {
                    submitBtn.disabled = false;
                    btnText.style.opacity = '1';
                    loadingSpinner.style.display = 'none';
                }
            });

            function showAlert(type, message) {
                alertContainer.innerHTML = `
                    <div class="alert alert-${type}" role="alert">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        ${message}
                    </div>
                `;

                // Auto hide after 5 seconds
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 5000);
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

        function validateLoginForm() {
            const loginInput = document.getElementById('login');
            const passwordInput = document.getElementById('password');
            const alertContainer = document.getElementById('alertContainer');
            let ok = true;

            // Xóa lỗi cũ
            alertContainer.innerHTML = '';

            // Tên đăng nhập - bỏ trống
            if (!loginInput.value.trim()) {
                showAlert('danger', 'Tên đăng nhập không được bỏ trống (1E.1)');
                loginInput.focus();
                ok = false;
            }

            // Mật khẩu - bỏ trống
            if (!passwordInput.value.trim()) {
                showAlert('danger', 'Mật khẩu không được bỏ trống (1E.3)');
                passwordInput.focus();
                ok = false;
            }

            return ok;
        }

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
        <div class="alert alert-${type}" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
        </div>
    `;
            setTimeout(() => { alertContainer.innerHTML = ''; }, 5000);
        }
    </script> --}}
</body>

</html>
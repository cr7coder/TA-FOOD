<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập - TAFOOD</title>

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
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

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
            max-width: 850px;
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
            flex: 1.1;
            padding: 40px 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
            left: 18px;
            top: 25px;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1rem;
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
            right: 18px;
            top: 25px;
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
            margin: 15px 0;
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
            padding: 12px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
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
            <h1>TAFOOD</h1>
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

            <div class="mt-2 mb-4 d-flex align-items-center justify-content-center">
                <hr class="flex-grow-1" style="border-color: #ccc; margin: 0;">
                <span class="px-3 text-muted" style="font-size: 0.9rem; font-weight: 500;">Hoặc</span>
                <hr class="flex-grow-1" style="border-color: #ccc; margin: 0;">
            </div>

            <a href="{{ route('auth.google') }}" class="btn-google-login mb-4">
                <img src="https://developers.google.com/static/identity/images/g-logo.png" alt="Google Logo">
                Đăng nhập với Google
            </a>

            <div class="auth-links">
                @if(App\Services\SettingService::check('app_allow_register', true))
                <p>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
                @endif
                <p><a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Quên mật khẩu?</a></p>
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

            // nhan dang nhap
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
                    // Chuẩn bị data theo RESTful API format (JSON)
                    const loginData = {
                        login: document.getElementById('login').value,
                        password: document.getElementById('password').value,
                        remember: document.getElementById('remember').checked,
                        device_name: 'web-browser'
                    };

                    // Gọi RESTful API endpoint
                    const response = await fetch('/api/v1/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(loginData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast(data.message);

                        // Lưu thông tin user và token vào localStorage
                        if (data.data) {
                            localStorage.setItem('user', JSON.stringify(data.data.user));
                            localStorage.setItem('auth_token', data.data.token);
                            localStorage.setItem('token_type', data.data.token_type);
                        }

                        // Redirect về trang chủ sau 1 giây
                        setTimeout(() => {
                            window.location.href = '{{ route("foods.index") }}';
                        }, 1000);
                    } else {
                        showAlert('danger', data.message);
                        resetSubmitButton();
                    }
                } catch (error) {
                    console.error('Login error:', error);
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
    @include('client.partials._global-alert')

    <!-- Forgot Password Modal Flow -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-header" style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; border-bottom: none;">
                    <h5 class="modal-title" id="forgotPasswordModalLabel" style="font-weight: 600;">Khôi phục mật khẩu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background: #f8f9fa;">
                    
                    <!-- Step 1: Request OTP -->
                    <div id="step-request-otp">
                        <p class="text-muted mb-4 text-center">Vui lòng nhập Email đã đăng ký. Chúng tôi sẽ gửi mã OTP để bạn đặt lại mật khẩu.</p>
                        <div id="fpAlert1" class="alert d-none" style="padding: 10px; font-size: 14px; border-radius: 8px;"></div>
                        <div class="form-group mb-4">
                            <input type="email" class="form-control" id="fpEmail" placeholder="Nhập địa chỉ Email của bạn">
                        </div>
                        <button type="button" class="btn-login" id="btnSendOtp" onclick="sendOtp()" style="margin-bottom: 0;">
                            <span class="btn-text">Gửi mã OTP</span>
                            <div class="loading-spinner"><div class="spinner"></div></div>
                        </button>
                    </div>

                    <!-- Step 2: Verify OTP -->
                    <div id="step-verify-otp" class="d-none">
                        <p class="text-muted mb-4 text-center">Mã OTP đã được gửi tới <strong id="displayEmail"></strong>. Mã có hiệu lực 5 phút.</p>
                        <div id="fpAlert2" class="alert d-none" style="padding: 10px; font-size: 14px; border-radius: 8px;"></div>
                        <div class="form-group mb-4">
                            <input type="text" class="form-control text-center" id="fpOtp" placeholder="Nhập 6 số OTP" maxlength="6" style="font-size: 24px; letter-spacing: 5px; font-weight: bold;">
                        </div>
                        <button type="button" class="btn-login" id="btnVerifyOtp" onclick="verifyOtp()" style="margin-bottom: 0;">
                            <span class="btn-text">Xác nhận mã</span>
                            <div class="loading-spinner"><div class="spinner"></div></div>
                        </button>
                        <div class="text-center mt-3">
                            <a href="#" id="btnResendOtp" onclick="resendOtp(event)" class="text-muted" style="text-decoration: none; font-size: 14px; pointer-events: none; opacity: 0.6;">
                                Gửi lại mã (<span id="resendCountdown">60</span>s)
                            </a>
                        </div>
                    </div>

                    <!-- Step 3: Reset Password -->
                    <div id="step-reset-password" class="d-none">
                        <p class="text-muted mb-4 text-center">Xác thực thành công! Vui lòng nhập mật khẩu mới của bạn.</p>
                        <div id="fpAlert3" class="alert d-none" style="padding: 10px; font-size: 14px; border-radius: 8px;"></div>
                        <div class="form-group mb-3">
                            <input type="password" class="form-control" id="fpNewPassword" placeholder="Mật khẩu mới (tối thiểu 6 ký tự)">
                        </div>
                        <div class="form-group mb-4">
                            <input type="password" class="form-control" id="fpNewPasswordConfirm" placeholder="Nhập lại mật khẩu mới">
                        </div>
                        <button type="button" class="btn-login" id="btnResetPwd" onclick="resetPassword()" style="margin-bottom: 0;">
                            <span class="btn-text">Đổi mật khẩu</span>
                            <div class="loading-spinner"><div class="spinner"></div></div>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password JS -->
    <script>
        let currentEmail = '';
        let resendTimer = null;
        let countdown = 60;

        function showFpAlert(step, type, message) {
            const alertEl = document.getElementById('fpAlert' + step);
            alertEl.className = `alert alert-${type}`;
            alertEl.innerHTML = message;
            alertEl.classList.remove('d-none');
        }

        function setBtnLoading(btnId, isLoading) {
            const btn = document.getElementById(btnId);
            const text = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.loading-spinner');
            btn.disabled = isLoading;
            if(isLoading) {
                text.style.opacity = '0';
                spinner.style.display = 'block';
            } else {
                text.style.opacity = '1';
                spinner.style.display = 'none';
            }
        }

        function startResendCountdown() {
            countdown = 60;
            const btnResend = document.getElementById('btnResendOtp');
            const spanCount = document.getElementById('resendCountdown');
            
            btnResend.style.pointerEvents = 'none';
            btnResend.style.opacity = '0.6';
            btnResend.classList.remove('text-primary');
            btnResend.classList.add('text-muted');
            
            btnResend.innerHTML = `Gửi lại mã (<span id="resendCountdown">${countdown}</span>s)`;

            if(resendTimer) clearInterval(resendTimer);
            
            resendTimer = setInterval(() => {
                countdown--;
                const currentSpan = document.getElementById('resendCountdown');
                if(currentSpan) currentSpan.innerText = countdown;
                
                if(countdown <= 0) {
                    clearInterval(resendTimer);
                    btnResend.style.pointerEvents = 'auto';
                    btnResend.style.opacity = '1';
                    btnResend.classList.remove('text-muted');
                    btnResend.classList.add('text-primary');
                    btnResend.innerHTML = 'Gửi lại mã';
                }
            }, 1000);
        }

        async function sendOtp() {
            const email = document.getElementById('fpEmail').value.trim();
            if(!email) return showFpAlert(1, 'danger', 'Vui lòng nhập Email');
            
            setBtnLoading('btnSendOtp', true);
            try {
                const res = await fetch('/api/v1/forgot-password/send-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                if(data.success) {
                    currentEmail = email;
                    document.getElementById('displayEmail').innerText = email;
                    document.getElementById('step-request-otp').classList.add('d-none');
                    document.getElementById('step-verify-otp').classList.remove('d-none');
                    showFpAlert(2, 'success', data.message);
                    startResendCountdown();
                } else {
                    showFpAlert(1, 'danger', data.message || 'Có lỗi xảy ra');
                }
            } catch (e) {
                showFpAlert(1, 'danger', 'Lỗi kết nối. Vui lòng thử lại!');
            }
            setBtnLoading('btnSendOtp', false);
        }

        async function resendOtp(e) {
            e.preventDefault();
            if(countdown > 0) return;
            
            showFpAlert(2, 'info', 'Đang gửi lại mã...');
            try {
                const res = await fetch('/api/v1/forgot-password/send-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: currentEmail })
                });
                const data = await res.json();
                if(data.success) {
                    showFpAlert(2, 'success', 'Mã OTP mới đã được gửi lại vào Email của bạn.');
                    startResendCountdown();
                } else {
                    showFpAlert(2, 'danger', data.message || 'Có lỗi xảy ra khi gửi lại mã');
                }
            } catch (e) {
                showFpAlert(2, 'danger', 'Lỗi kết nối. Vui lòng thử lại!');
            }
        }

        async function verifyOtp() {
            const otp = document.getElementById('fpOtp').value.trim();
            if(!otp || otp.length !== 6) return showFpAlert(2, 'danger', 'Vui lòng nhập đủ 6 số OTP');
            
            setBtnLoading('btnVerifyOtp', true);
            try {
                const res = await fetch('/api/v1/forgot-password/verify-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: currentEmail, otp })
                });
                const data = await res.json();
                if(data.success) {
                    document.getElementById('step-verify-otp').classList.add('d-none');
                    document.getElementById('step-reset-password').classList.remove('d-none');
                    showFpAlert(3, 'success', 'Vui lòng nhập mật khẩu mới');
                } else {
                    showFpAlert(2, 'danger', data.message || 'OTP không hợp lệ');
                }
            } catch (e) {
                showFpAlert(2, 'danger', 'Lỗi kết nối. Vui lòng thử lại!');
            }
            setBtnLoading('btnVerifyOtp', false);
        }

        async function resetPassword() {
            const otp = document.getElementById('fpOtp').value.trim();
            const password = document.getElementById('fpNewPassword').value;
            const password_confirmation = document.getElementById('fpNewPasswordConfirm').value;

            if(!password || password.length < 6) return showFpAlert(3, 'danger', 'Mật khẩu phải từ 6 ký tự');
            if(password !== password_confirmation) return showFpAlert(3, 'danger', 'Mật khẩu nhập lại không khớp');
            
            setBtnLoading('btnResetPwd', true);
            try {
                const res = await fetch('/api/v1/forgot-password/reset', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: currentEmail, otp, password, password_confirmation })
                });
                const data = await res.json();
                if(data.success) {
                    showFpAlert(3, 'success', data.message + ' Đang chuyển hướng...');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showFpAlert(3, 'danger', data.message || 'Có lỗi xảy ra');
                }
            } catch (e) {
                showFpAlert(3, 'danger', 'Lỗi kết nối. Vui lòng thử lại!');
            }
            setBtnLoading('btnResetPwd', false);
        }

        // Reset modal state when hidden
        document.getElementById('forgotPasswordModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('step-request-otp').classList.remove('d-none');
            document.getElementById('step-verify-otp').classList.add('d-none');
            document.getElementById('step-reset-password').classList.add('d-none');
            document.getElementById('fpEmail').value = '';
            document.getElementById('fpOtp').value = '';
            document.getElementById('fpNewPassword').value = '';
            document.getElementById('fpNewPasswordConfirm').value = '';
            currentEmail = '';
            ['1','2','3'].forEach(step => document.getElementById('fpAlert'+step).classList.add('d-none'));
        });
    </script>
</body>

</html>
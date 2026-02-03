<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thông tin cá nhân - TA-FOOD</title>
    <link href="https://fonts.googleapis.com/css2?family=Arimo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arimo', sans-serif;
            background-color: #f9fafb;
            min-height: 100vh;
        }

        .page-container {
            background-color: #f9fafb;
            min-height: 100vh;
            padding-bottom: 40px;
        }

        .header-container {
            background-color: white;
            padding: 16px 166px;
            box-shadow: 0px 1px 3px 0px rgba(0,0,0,0.1), 0px 1px 2px 0px rgba(0,0,0,0.1);
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #4a5565;
            font-size: 16px;
            line-height: 24px;
            cursor: pointer;
        }

        .back-button:hover {
            color: #FF6900;
        }

        .back-icon {
            width: 20px;
            height: 20px;
        }

        .profile-container {
            max-width: 864px;
            margin: 40px auto;
            background-color: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0px 4px 6px -1px rgba(0,0,0,0.1), 0px 2px 4px -2px rgba(0,0,0,0.1);
        }

        .profile-header {
            background: linear-gradient(to right, #ff6900, #f54900);
            padding: 48px 32px;
        }

        .profile-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .avatar-circle {
            width: 96px;
            height: 96px;
            background-color: white;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            line-height: 40px;
            color: #ff6900;
            font-weight: 400;
        }

        .user-details h1 {
            font-size: 30px;
            line-height: 36px;
            color: white;
            margin-bottom: 8px;
            font-weight: 400;
        }

        .user-details p {
            font-size: 14px;
            color: #0a0a0a;
            margin: 0;
        }

        .edit-button {
            background-color: white;
            color: #ff6900;
            border: none;
            border-radius: 20px;
            padding: 8px 24px;
            font-size: 16px;
            line-height: 24px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            height: 40px;
        }

        .edit-button:hover {
            background-color: #fff5f0;
            color: #ff6900;
        }

        .edit-icon {
            width: 16px;
            height: 16px;
        }

        .profile-body {
            padding: 32px;
        }

        .section-title {
            font-size: 20px;
            line-height: 28px;
            color: #101828;
            margin-bottom: 24px;
            font-weight: 400;
        }

        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-row.two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .info-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-label {
            font-size: 14px;
            line-height: 20px;
            color: #4a5565;
        }

        .info-value {
            font-size: 16px;
            line-height: 24px;
            color: #101828;
            padding-left: 48px;
        }

        .info-value a {
            color: #0a0a0a;
            font-size: 14px;
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 300px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            color: #ff6900;
        }

        .error-container {
            text-align: center;
            padding: 48px 32px;
        }

        .error-icon {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 16px;
        }

        .error-message {
            font-size: 18px;
            color: #4a5565;
            margin-bottom: 24px;
        }

        .retry-button {
            background-color: #ff6900;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
        }

        .retry-button:hover {
            background-color: #f54900;
        }

        @media (max-width: 992px) {
            .header-container {
                padding: 16px 24px;
            }

            .profile-container {
                margin: 24px 16px;
            }

            .profile-header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .edit-button {
                width: 100%;
                justify-content: center;
            }

            .info-row.two-columns {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .profile-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .avatar-circle {
                width: 80px;
                height: 80px;
                font-size: 30px;
            }

            .user-details h1 {
                font-size: 24px;
            }

            .info-value {
                padding-left: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="header-container">
            <a href="{{ route('foods.index') }}" class="back-button">
                <svg class="back-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Quay lại</span>
            </a>
        </div>

        <div id="loadingSpinner" class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
        </div>

        <div id="errorContainer" class="error-container" style="display: none;">
            <i class="fas fa-exclamation-triangle error-icon"></i>
            <div class="error-message" id="errorMessage"></div>
            <button class="retry-button" onclick="loadProfile()">
                <i class="fas fa-redo me-2"></i>Thử lại
            </button>
        </div>

        <div id="profileContainer" class="profile-container" style="display: none;">
            <div class="profile-header">
                <div class="profile-header-content">
                    <div class="profile-info">
                        <div class="avatar-circle" id="avatarInitial"></div>
                        <div class="user-details">
                            <h1 id="userName"></h1>
                            <p id="userEmail"></p>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="edit-button">
                        <svg class="edit-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.3333 2.00004C11.5084 1.82494 11.7163 1.68605 11.9451 1.59129C12.1739 1.49653 12.4191 1.44775 12.6667 1.44775C12.9142 1.44775 13.1594 1.49653 13.3882 1.59129C13.617 1.68605 13.8249 1.82494 14 2.00004C14.1751 2.17513 14.314 2.383 14.4088 2.61178C14.5036 2.84055 14.5523 3.08575 14.5523 3.33337C14.5523 3.58099 14.5036 3.82619 14.4088 4.05497C14.314 4.28374 14.1751 4.49161 14 4.66671L5 13.6667L1.33333 14.6667L2.33333 11L11.3333 2.00004Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Chỉnh sửa
                    </a>
                </div>
            </div>

            <div class="profile-body">
                <h2 class="section-title">Thông tin cá nhân</h2>

                <div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
                    <span id="successMessage"></span>
                    <button type="button" class="btn-close" onclick="closeAlert('successAlert')"></button>
                </div>

                <div class="info-grid">
                    <div class="info-row two-columns">
                        <div class="info-field">
                            <label class="info-label">Họ và tên</label>
                            <div class="info-value" id="fullName">Chưa cập nhật</div>
                        </div>
                        <div class="info-field">
                            <label class="info-label">Tên đăng nhập</label>
                            <div class="info-value" id="username"></div>
                        </div>
                    </div>

                    <div class="info-row two-columns">
                        <div class="info-field">
                            <label class="info-label">Email</label>
                            <div class="info-value" id="email"></div>
                        </div>
                        <div class="info-field">
                            <label class="info-label">Mật khẩu</label>
                            <div class="info-value">*********</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-field">
                            <label class="info-label">Số điện thoại</label>
                            <div class="info-value" id="phone">Chưa cập nhật</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-field">
                            <label class="info-label">Địa chỉ</label>
                            <div class="info-value" id="address">Chưa cập nhật</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load profile data from API
        async function loadProfile() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const errorContainer = document.getElementById('errorContainer');
            const profileContainer = document.getElementById('profileContainer');

            // Show loading
            loadingSpinner.style.display = 'flex';
            errorContainer.style.display = 'none';
            profileContainer.style.display = 'none';

            try {
                const response = await fetch('/api/v1/profile', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    const data = result.data;

                    // Update avatar initial
                    document.getElementById('avatarInitial').textContent = data.TenDangNhap ? data.TenDangNhap.charAt(0).toUpperCase() : '?';
                    
                    // Update header
                    document.getElementById('userName').textContent = data.TenDangNhap || 'N/A';
                    document.getElementById('userEmail').textContent = data.Email || 'N/A';

                    // Update profile fields
                    document.getElementById('fullName').textContent = data.HoTen || 'Chưa cập nhật';
                    document.getElementById('username').textContent = data.TenDangNhap || 'N/A';
                    
                    const emailElement = document.getElementById('email');
                    if (data.Email) {
                        emailElement.innerHTML = `<a href="mailto:${data.Email}">${data.Email}</a>`;
                    } else {
                        emailElement.textContent = 'Chưa cập nhật';
                    }
                    
                    document.getElementById('phone').textContent = data.SoDienThoai || 'Chưa cập nhật';
                    document.getElementById('address').textContent = data.DiaChi || 'Chưa cập nhật';

                    // Show success message if exists
                    const urlParams = new URLSearchParams(window.location.search);
                    const successMsg = urlParams.get('success');
                    if (successMsg) {
                        showSuccess(successMsg);
                        // Remove query parameter from URL
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }

                    // Show profile
                    loadingSpinner.style.display = 'none';
                    profileContainer.style.display = 'block';

                } else {
                    throw new Error(result.message || 'Không thể tải thông tin người dùng');
                }

            } catch (error) {
                console.error('Error loading profile:', error);
                loadingSpinner.style.display = 'none';
                errorContainer.style.display = 'block';
                document.getElementById('errorMessage').textContent = error.message || 'Đã xảy ra lỗi khi tải thông tin';
            }
        }

        function showSuccess(message) {
            const alert = document.getElementById('successAlert');
            const messageElement = document.getElementById('successMessage');
            messageElement.textContent = message;
            alert.style.display = 'block';
            alert.classList.add('show');
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                closeAlert('successAlert');
            }, 5000);
        }

        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            alert.classList.remove('show');
            setTimeout(() => {
                alert.style.display = 'none';
            }, 150);
        }

        // Load profile when page loads
        document.addEventListener('DOMContentLoaded', loadProfile);
    </script>
</body>
</html>

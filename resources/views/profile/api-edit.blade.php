<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chỉnh sửa thông tin - TA-FOOD</title>
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
            color: #ff6900;
        }

        .user-details h1 {
            font-size: 30px;
            color: white;
            margin-bottom: 8px;
        }

        .user-details p {
            font-size: 14px;
            color: #0a0a0a;
        }

        .profile-body {
            padding: 32px;
        }

        .section-title {
            font-size: 20px;
            color: #101828;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 14px;
            color: #4a5565;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            font-size: 16px;
            padding: 10px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #ff6900;
            box-shadow: 0 0 0 0.2rem rgba(255, 105, 0, 0.25);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 14px;
            margin-top: 4px;
        }

        .btn-primary {
            background-color: #ff6900;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #f54900;
        }

        .btn-primary:disabled {
            background-color: #ffa366;
            cursor: not-allowed;
        }

        .btn-secondary {
            background-color: #6b7280;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 16px;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .password-section {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e5e7eb;
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

        @media (max-width: 992px) {
            .header-container {
                padding: 16px 24px;
            }

            .profile-container {
                margin: 24px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="header-container">
            <a href="{{ route('foods.index') }}" class="back-button">
                <svg class="back-icon" viewBox="0 0 20 20" fill="none">
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
                </div>
            </div>

            <div class="profile-body">
                <h2 class="section-title">Chỉnh sửa thông tin cá nhân</h2>

                <div id="errorAlert" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none;">
                    <div id="errorMessage"></div>
                    <button type="button" class="btn-close" onclick="closeAlert('errorAlert')"></button>
                </div>

                <div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
                    <span id="successMessage"></span>
                    <button type="button" class="btn-close" onclick="closeAlert('successAlert')"></button>
                </div>

                <!-- Profile Information Form -->
                <form id="profileForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="HoTen" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="HoTen" name="HoTen" required>
                            <div class="invalid-feedback" id="HoTen-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="TenDangNhap" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="TenDangNhap" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="Email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="Email" name="Email" required>
                            <div class="invalid-feedback" id="Email-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="SoDienThoai" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="SoDienThoai" name="SoDienThoai">
                            <div class="invalid-feedback" id="SoDienThoai-error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="DiaChi" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" id="DiaChi" name="DiaChi" rows="3"></textarea>
                        <div class="invalid-feedback" id="DiaChi-error"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="saveButton">
                            <i class="fas fa-save me-2"></i>Lưu thay đổi
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('profile.index') }}'">Hủy</button>
                    </div>
                </form>

                <!-- Change Password Section -->
                <div class="password-section">
                    <h2 class="section-title">Đổi mật khẩu</h2>

                    <div id="passwordErrorAlert" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none;">
                        <div id="passwordErrorMessage"></div>
                        <button type="button" class="btn-close" onclick="closeAlert('passwordErrorAlert')"></button>
                    </div>

                    <div id="passwordSuccessAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
                        <span id="passwordSuccessMessage"></span>
                        <button type="button" class="btn-close" onclick="closeAlert('passwordSuccessAlert')"></button>
                    </div>

                    <form id="passwordForm">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <div class="invalid-feedback" id="current_password-error"></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback" id="password-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <div class="invalid-feedback" id="password_confirmation-error"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="changePasswordButton">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load profile data from API
        async function loadProfile() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const profileContainer = document.getElementById('profileContainer');

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

                    // Update avatar and header
                    document.getElementById('avatarInitial').textContent = data.TenDangNhap ? data.TenDangNhap.charAt(0).toUpperCase() : '?';
                    document.getElementById('userName').textContent = data.TenDangNhap || 'N/A';
                    document.getElementById('userEmail').textContent = data.Email || 'N/A';

                    // Fill form fields
                    document.getElementById('HoTen').value = data.HoTen || '';
                    document.getElementById('TenDangNhap').value = data.TenDangNhap || '';
                    document.getElementById('Email').value = data.Email || '';
                    document.getElementById('SoDienThoai').value = data.SoDienThoai || '';
                    document.getElementById('DiaChi').value = data.DiaChi || '';

                    loadingSpinner.style.display = 'none';
                    profileContainer.style.display = 'block';

                } else {
                    throw new Error(result.message || 'Không thể tải thông tin người dùng');
                }

            } catch (error) {
                console.error('Error loading profile:', error);
                showError(error.message || 'Đã xảy ra lỗi khi tải thông tin');
            }
        }

        // Handle profile form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const saveButton = document.getElementById('saveButton');
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

            // Clear previous errors
            clearFormErrors('profileForm');

            const formData = {
                HoTen: document.getElementById('HoTen').value,
                Email: document.getElementById('Email').value,
                SoDienThoai: document.getElementById('SoDienThoai').value,
                DiaChi: document.getElementById('DiaChi').value
            };

            try {
                const response = await fetch('/api/v1/profile', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showSuccess(result.message || 'Cập nhật thông tin thành công');
                    
                    // Update header if email changed
                    document.getElementById('userEmail').textContent = formData.Email;
                    
                    // Redirect to profile page after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route('profile.index') }}?success=' + encodeURIComponent(result.message);
                    }, 1500);

                } else {
                    if (result.errors) {
                        displayFormErrors(result.errors, 'profileForm');
                    } else {
                        showError(result.message || 'Không thể cập nhật thông tin');
                    }
                }

            } catch (error) {
                console.error('Error updating profile:', error);
                showError('Đã xảy ra lỗi khi cập nhật thông tin');
            } finally {
                saveButton.disabled = false;
                saveButton.innerHTML = '<i class="fas fa-save me-2"></i>Lưu thay đổi';
            }
        });

        // Handle password form submission
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const changePasswordButton = document.getElementById('changePasswordButton');
            changePasswordButton.disabled = true;
            changePasswordButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

            // Clear previous errors
            clearFormErrors('passwordForm');

            const formData = {
                current_password: document.getElementById('current_password').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            try {
                const response = await fetch('/api/v1/profile/password', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showPasswordSuccess(result.message || 'Đổi mật khẩu thành công');
                    document.getElementById('passwordForm').reset();

                } else {
                    if (result.errors) {
                        displayFormErrors(result.errors, 'passwordForm');
                    } else {
                        showPasswordError(result.message || 'Không thể đổi mật khẩu');
                    }
                }

            } catch (error) {
                console.error('Error changing password:', error);
                showPasswordError('Đã xảy ra lỗi khi đổi mật khẩu');
            } finally {
                changePasswordButton.disabled = false;
                changePasswordButton.innerHTML = '<i class="fas fa-key me-2"></i>Đổi mật khẩu';
            }
        });

        function displayFormErrors(errors, formId) {
            for (const [field, messages] of Object.entries(errors)) {
                const input = document.getElementById(field);
                const errorDiv = document.getElementById(`${field}-error`);
                
                if (input && errorDiv) {
                    input.classList.add('is-invalid');
                    errorDiv.textContent = messages[0];
                }
            }
        }

        function clearFormErrors(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('.is-invalid');
            inputs.forEach(input => input.classList.remove('is-invalid'));
            
            const errors = form.querySelectorAll('.invalid-feedback');
            errors.forEach(error => error.textContent = '');
        }

        function showError(message) {
            const alert = document.getElementById('errorAlert');
            const messageElement = document.getElementById('errorMessage');
            messageElement.textContent = message;
            alert.style.display = 'block';
            alert.classList.add('show');
        }

        function showSuccess(message) {
            const alert = document.getElementById('successAlert');
            const messageElement = document.getElementById('successMessage');
            messageElement.textContent = message;
            alert.style.display = 'block';
            alert.classList.add('show');
        }

        function showPasswordError(message) {
            const alert = document.getElementById('passwordErrorAlert');
            const messageElement = document.getElementById('passwordErrorMessage');
            messageElement.textContent = message;
            alert.style.display = 'block';
            alert.classList.add('show');
        }

        function showPasswordSuccess(message) {
            const alert = document.getElementById('passwordSuccessAlert');
            const messageElement = document.getElementById('passwordSuccessMessage');
            messageElement.textContent = message;
            alert.style.display = 'block';
            alert.classList.add('show');
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

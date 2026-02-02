<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <a href="{{ route('profile.index') }}" class="back-button">
                <svg class="back-icon" viewBox="0 0 20 20" fill="none">
                    <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Quay lại</span>
            </a>
        </div>

        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-header-content">
                    <div class="profile-info">
                        <div class="avatar-circle">
                            {{ substr($user->TenDangNhap, 0, 1) }}
                        </div>
                        <div class="user-details">
                            <h1>{{ $user->TenDangNhap }}</h1>
                            <p>{{ $user->Email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-body">
                <h2 class="section-title">Chỉnh sửa thông tin cá nhân</h2>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Profile Information Form -->
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="HoTen" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="HoTen" name="HoTen" 
                                   value="{{ old('HoTen', $user->HoTen) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="TenDangNhap" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="TenDangNhap" 
                                   value="{{ $user->TenDangNhap }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="Email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="Email" name="Email" 
                                   value="{{ old('Email', $user->Email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="SoDienThoai" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="SoDienThoai" name="SoDienThoai" 
                                   value="{{ old('SoDienThoai', $user->SoDienThoai) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="DiaChi" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" id="DiaChi" name="DiaChi" rows="3">{{ old('DiaChi', $user->DiaChi) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu thay đổi
                        </button>
                        <a href="{{ route('profile.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>

                <!-- Change Password Section -->
                <div class="password-section">
                    <h2 class="section-title">Đổi mật khẩu</h2>

                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="password" 
                                       name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

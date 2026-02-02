<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Header with back button */
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

        /* Main profile card */
        .profile-container {
            max-width: 864px;
            margin: 40px auto;
            background-color: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0px 4px 6px -1px rgba(0,0,0,0.1), 0px 2px 4px -2px rgba(0,0,0,0.1);
        }

        /* Header section with gradient */
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

        /* Profile body section */
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

        /* Responsive */
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
        <!-- Header with back button -->
        <div class="header-container">
            <a href="{{ url()->previous() }}" class="back-button">
                <svg class="back-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Quay lại</span>
            </a>
        </div>

        <!-- Profile Card -->
        <div class="profile-container">
            <!-- Profile Header with Gradient -->
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
                    <a href="{{ route('profile.edit') }}" class="edit-button">
                        <svg class="edit-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.3333 2.00004C11.5084 1.82494 11.7163 1.68605 11.9451 1.59129C12.1739 1.49653 12.4191 1.44775 12.6667 1.44775C12.9142 1.44775 13.1594 1.49653 13.3882 1.59129C13.617 1.68605 13.8249 1.82494 14 2.00004C14.1751 2.17513 14.314 2.383 14.4088 2.61178C14.5036 2.84055 14.5523 3.08575 14.5523 3.33337C14.5523 3.58099 14.5036 3.82619 14.4088 4.05497C14.314 4.28374 14.1751 4.49161 14 4.66671L5 13.6667L1.33333 14.6667L2.33333 11L11.3333 2.00004Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Chỉnh sửa
                    </a>
                </div>
            </div>

            <!-- Profile Body -->
            <div class="profile-body">
                <h2 class="section-title">Thông tin cá nhân</h2>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="info-grid">
                    <!-- Row 1: Full Name and Username -->
                    <div class="info-row two-columns">
                        <div class="info-field">
                            <label class="info-label">Họ và tên</label>
                            <div class="info-value">{{ $user->HoTen ?? 'Chưa cập nhật' }}</div>
                        </div>
                        <div class="info-field">
                            <label class="info-label">Tên đăng nhập</label>
                            <div class="info-value">{{ $user->TenDangNhap }}</div>
                        </div>
                    </div>

                    <!-- Row 2: Email and Password -->
                    <div class="info-row two-columns">
                        <div class="info-field">
                            <label class="info-label">Email</label>
                            <div class="info-value">
                                <a href="mailto:{{ $user->Email }}">{{ $user->Email }}</a>
                            </div>
                        </div>
                        <div class="info-field">
                            <label class="info-label">Mật khẩu</label>
                            <div class="info-value">*********</div>
                        </div>
                    </div>

                    <!-- Row 3: Phone Number -->
                    <div class="info-row">
                        <div class="info-field">
                            <label class="info-label">Số điện thoại</label>
                            <div class="info-value">{{ $user->SoDienThoai ?? 'Chưa cập nhật' }}</div>
                        </div>
                    </div>

                    <!-- Row 4: Address -->
                    <div class="info-row">
                        <div class="info-field">
                            <label class="info-label">Địa chỉ</label>
                            <div class="info-value">{{ $user->DiaChi ?? 'Chưa cập nhật' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

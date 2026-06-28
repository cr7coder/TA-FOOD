@extends('client.layouts.master')

@section('title', 'Thông tin cá nhân')

@section('content')
    <div class="hero_area">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>
        @include('client.partials._header')
    </div>

    <style>
        /* Optimize header area height on PC to prevent excessive scrolling */
        @media (min-width: 992px) {
            .hero_area {
                min-height: 125px !important;
            }
        }

        .profile_section {
            padding: 50px 0;
            background-color: #f8fafc;
            min-height: 100vh;
        }

        .profile-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06), 0 5px 15px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-header {
            background: linear-gradient(135deg, #222831 0%, #1a1c20 100%);
            padding: 50px 40px;
            position: relative;
            border-bottom: 4px solid #ffbe33;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 80% 20%, rgba(255, 190, 51, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }

        .profile-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
            position: relative;
            z-index: 2;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 28px;
            flex-wrap: wrap;
        }

        .avatar-circle {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ffbe33 0%, #ff9f43 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            color: #222831;
            font-weight: 800;
            border: 4px solid #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            text-transform: uppercase;
        }

        .user-details h1 {
            font-size: 32px;
            color: white;
            margin-bottom: 6px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .user-details p {
            font-size: 15px;
            color: #94a3b8;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: #ffbe33;
            color: #222831;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 20px rgba(255, 190, 51, 0.25);
            border: none;
            cursor: pointer;
        }

        .edit-button:hover {
            background-color: #e69c00;
            color: #222831;
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(255, 190, 51, 0.35);
            text-decoration: none !important;
        }

        .edit-button:active {
            transform: translateY(-1px);
        }

        .profile-body {
            padding: 40px;
        }

        .section-title {
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 30px;
            font-weight: 800;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -8px;
            width: 40px;
            height: 4px;
            background-color: #ffbe33;
            border-radius: 2px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 18px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
            border-color: rgba(255, 190, 51, 0.4);
        }

        .info-icon-wrapper {
            width: 52px;
            height: 52px;
            background: rgba(255, 190, 51, 0.1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #ffbe33;
            transition: all 0.3s ease;
        }

        .info-card:hover .info-icon-wrapper {
            background: #ffbe33;
            color: #222831;
            transform: scale(1.1) rotate(5deg);
        }

        .info-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .info-value {
            font-size: 16.5px;
            color: #1e293b;
            font-weight: 600;
        }

        .info-card.full-width {
            grid-column: span 2;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }

        .spinner-border {
            width: 3.5rem;
            height: 3.5rem;
            color: #ffbe33;
        }

        .error-container {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        .error-icon {
            font-size: 56px;
            color: #ef4444;
            margin-bottom: 20px;
        }

        .retry-button {
            background-color: #ffbe33;
            color: #222831;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 700;
            margin-top: 20px;
            transition: all 0.2s;
            box-shadow: 0 8px 20px rgba(255, 190, 51, 0.2);
        }

        .retry-button:hover {
            background-color: #e69c00;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .info-card.full-width {
                grid-column: span 1;
            }
            .profile-header {
                padding: 40px 24px;
            }
            .profile-body {
                padding: 30px 20px;
            }
        }

        /* Override global template handwriting font for clean sans-serif */
        .profile_section h1,
        .profile_section h2,
        .profile_section h3,
        .profile_section h4,
        .profile_section .user-details h1,
        .profile_section .section-title,
        .profile_section .breadcrumb-item,
        .profile_section .info-value,
        .profile_section .info-label {
            font-family: 'Open Sans', sans-serif !important;
        }
    </style>

    <section class="profile_section">
        <div class="container">
            <!-- Breadcrumb & Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap" style="gap: 15px;">
                <nav aria-label="breadcrumb" class="mb-0">
                    <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="{{ route('foods.index') }}" style="color: #64748b; text-decoration: none;">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #1e293b; font-weight: 600;">Thông tin cá nhân</li>
                    </ol>
                </nav>
                <a href="{{ route('foods.index') }}" class="btn btn-warning rounded-pill px-4" style="background-color: #ffbe33; border: none; font-weight: 700; transition: all 0.3s; color: #222831; box-shadow: 0 4px 15px rgba(255, 190, 51, 0.2);" onmouseover="this.style.backgroundColor='#e69c00'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='#ffbe33'; this.style.transform='translateY(0)';">
                    <i class="fa fa-arrow-left mr-2"></i> Quay lại
                </a>
            </div>

            <div id="loadingSpinner" class="loading-spinner">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>

            <div id="errorContainer" class="error-container" style="display: none;">
                <i class="fas fa-exclamation-triangle error-icon"></i>
                <h4 class="fw-bold mb-2">Không thể tải thông tin</h4>
                <div class="error-message text-muted mb-3" id="errorMessage"></div>
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
                                <p><i class="fa fa-envelope" style="color: #ffbe33;"></i> <span id="userEmail"></span></p>
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="edit-button">
                            <i class="fa fa-edit"></i> Chỉnh sửa thông tin
                        </a>
                    </div>
                </div>

                <div class="profile-body">
                    <h2 class="section-title">Thông tin tài khoản</h2>

                    <div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none; border-radius: 12px; border: none; background: #dcfce7; color: #15803d; font-weight: 600; padding: 16px 20px;">
                        <span id="successMessage"></span>
                        <button type="button" class="close" onclick="closeAlert('successAlert')" style="color: #15803d; outline: none; text-shadow: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="info-grid">
                        <!-- Card 1: Họ tên -->
                        <div class="info-card">
                            <div class="info-icon-wrapper">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Họ và tên</span>
                                <span class="info-value" id="fullName">Chưa cập nhật</span>
                            </div>
                        </div>

                        <!-- Card 2: Tên đăng nhập -->
                        <div class="info-card">
                            <div class="info-icon-wrapper">
                                <i class="fa fa-id-badge"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Tên đăng nhập</span>
                                <span class="info-value" id="username">N/A</span>
                            </div>
                        </div>

                        <!-- Card 3: Email -->
                        <div class="info-card">
                            <div class="info-icon-wrapper">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Địa chỉ Email</span>
                                <span class="info-value" id="email">N/A</span>
                            </div>
                        </div>

                        <!-- Card 4: Mật khẩu -->
                        <div class="info-card">
                            <div class="info-icon-wrapper">
                                <i class="fa fa-lock"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Mật khẩu</span>
                                <span class="info-value">*********</span>
                            </div>
                        </div>

                        <!-- Card 5: Số điện thoại -->
                        <div class="info-card">
                            <div class="info-icon-wrapper">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Số điện thoại</span>
                                <span class="info-value" id="phone">Chưa cập nhật</span>
                            </div>
                        </div>

                        <!-- Card 6: Địa chỉ mặc định (Full width) -->
                        <div class="info-card full-width">
                            <div class="info-icon-wrapper">
                                <i class="fa fa-map-marker-alt"></i>
                            </div>
                            <div class="info-details" style="flex: 1;">
                                <span class="info-label">⭐ Địa chỉ mặc định</span>
                                <span class="info-value" id="address">Chưa cập nhật</span>
                            </div>
                        </div>

                        <!-- Card 7: Nhà riêng (Full width) -->
                        <div class="info-card full-width" id="cardNhaRieng" style="display:none;">
                            <div class="info-icon-wrapper">
                                🏠
                            </div>
                            <div class="info-details" style="flex: 1;">
                                <span class="info-label">Nhà riêng</span>
                                <span class="info-value" id="addressNhaRieng">Chưa cập nhật</span>
                            </div>
                        </div>

                        <!-- Card 8: Văn phòng (Full width) -->
                        <div class="info-card full-width" id="cardVanPhong" style="display:none;">
                            <div class="info-icon-wrapper">
                                🏢
                            </div>
                            <div class="info-details" style="flex: 1;">
                                <span class="info-label">Văn phòng</span>
                                <span class="info-value" id="addressVanPhong">Chưa cập nhật</span>
                            </div>
                        </div>

                        <!-- Card 9: Trường học (Full width) -->
                        <div class="info-card full-width" id="cardTruongHoc" style="display:none;">
                            <div class="info-icon-wrapper">
                                🏫
                            </div>
                            <div class="info-details" style="flex: 1;">
                                <span class="info-label">Trường học</span>
                                <span class="info-value" id="addressTruongHoc">Chưa cập nhật</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    const data = result.data;

                    // Update avatar initial
                    const avatarInitial = document.getElementById('avatarInitial');
                    if (data.AnhDaiDien) {
                        avatarInitial.innerHTML = `<img src="${data.avatar_url}" alt="avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                        avatarInitial.style.padding = '0';
                        avatarInitial.style.overflow = 'hidden';
                    } else {
                        avatarInitial.textContent = data.TenDangNhap ? data.TenDangNhap.charAt(0).toUpperCase() : '?';
                    }
                    
                    // Update header
                    document.getElementById('userName').textContent = data.HoTen || data.TenDangNhap || 'N/A';
                    document.getElementById('userEmail').textContent = data.Email || 'N/A';

                    // Update profile fields
                    document.getElementById('fullName').textContent = data.HoTen || 'Chưa cập nhật';
                    document.getElementById('username').textContent = data.TenDangNhap || 'N/A';
                    
                    const emailElement = document.getElementById('email');
                    if (data.Email) {
                        emailElement.innerHTML = `<a href="mailto:${data.Email}" style="color: #ffbe33; text-decoration: none; font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='#e69c00';" onmouseout="this.style.color='#ffbe33';">${data.Email}</a>`;
                    } else {
                        emailElement.textContent = 'Chưa cập nhật';
                    }
                    
                    document.getElementById('phone').textContent = data.SoDienThoai || 'Chưa cập nhật';
                    document.getElementById('address').textContent = data.DiaChi || 'Chưa cập nhật';

                    // Show/populate tag address cards
                    if (data.DiaChiNhaRieng) {
                        document.getElementById('addressNhaRieng').textContent = data.DiaChiNhaRieng;
                        document.getElementById('cardNhaRieng').style.display = 'flex';
                    }
                    if (data.DiaChiVanPhong) {
                        document.getElementById('addressVanPhong').textContent = data.DiaChiVanPhong;
                        document.getElementById('cardVanPhong').style.display = 'flex';
                    }
                    if (data.DiaChiTruongHoc) {
                        document.getElementById('addressTruongHoc').textContent = data.DiaChiTruongHoc;
                        document.getElementById('cardTruongHoc').style.display = 'flex';
                    }

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
@endsection

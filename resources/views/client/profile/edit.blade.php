@extends('client.layouts.master')

@section('title', 'Chỉnh sửa thông tin')

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

        .form-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01);
            margin-bottom: 35px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control, .form-select {
            font-size: 15.5px;
            padding: 12px 18px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #ffbe33;
            box-shadow: 0 0 0 4px rgba(255, 190, 51, 0.15);
            outline: none;
        }

        .form-control:disabled {
            background-color: #f8fafc;
            color: #94a3b8;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
        }

        .invalid-feedback {
            display: block;
            color: #ef4444;
            font-size: 13.5px;
            font-weight: 500;
            margin-top: 6px;
        }

        .btn-primary {
            background-color: #ffbe33;
            color: #222831;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 15.5px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 20px rgba(255, 190, 51, 0.25);
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #e69c00;
            color: #222831;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 190, 51, 0.35);
        }

        .btn-primary:disabled {
            background-color: #ffe8b3;
            color: #888888;
            box-shadow: none;
            transform: none;
            cursor: not-allowed;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 15.5px;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
            color: #334155;
            transform: translateY(-2px);
        }

        .password-section {
            margin-top: 40px;
            padding-top: 40px;
            border-top: 1px solid #f1f5f9;
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

        @media (max-width: 768px) {
            .profile-header {
                padding: 40px 24px;
            }
            .profile-body {
                padding: 30px 20px;
            }
            .form-card {
                padding: 24px 16px;
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
        .profile_section .form-label,
        .profile_section .btn {
            font-family: 'Open Sans', sans-serif !important;
        }

        /* Suggestions list dropdown styling */
        .address-suggestions-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 5px;
            display: none;
            padding: 5px 0;
        }
        
        .address-suggestion-item {
            padding: 10px 15px;
            color: #333333;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .address-suggestion-item:last-child {
            border-bottom: none;
        }

        .address-suggestion-item:hover {
            background: #f8f9fa;
            color: #ffbe33;
        }

        .address-suggestion-item i {
            color: #ffbe33;
            font-size: 14px;
        }
    </style>

    <section class="profile_section">
        <div class="container">
            <!-- Breadcrumb & Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap" style="gap: 15px;">
                <nav aria-label="breadcrumb" class="mb-0">
                    <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="{{ route('foods.index') }}" style="color: #64748b; text-decoration: none;">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('profile.index') }}" style="color: #64748b; text-decoration: none;">Thông tin cá nhân</a></li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #1e293b; font-weight: 600;">Chỉnh sửa</li>
                    </ol>
                </nav>
                <a href="{{ route('profile.index') }}" class="btn btn-warning rounded-pill px-4" style="background-color: #ffbe33; border: none; font-weight: 700; transition: all 0.3s; color: #222831; box-shadow: 0 4px 15px rgba(255, 190, 51, 0.2);" onmouseover="this.style.backgroundColor='#e69c00'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='#ffbe33'; this.style.transform='translateY(0)';">
                    <i class="fa fa-arrow-left mr-2"></i> Quay lại
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
                                <p><i class="fa fa-envelope" style="color: #ffbe33;"></i> <span id="userEmail"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-body">
                    <h2 class="section-title">Cấu hình tài khoản</h2>

                    <div id="errorAlert" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none; border-radius: 12px; border: none; background: #fee2e2; color: #b91c1c; font-weight: 600; padding: 16px 20px;">
                        <div id="errorMessage"></div>
                        <button type="button" class="btn-close" onclick="closeAlert('errorAlert')" style="box-shadow: none;"></button>
                    </div>

                    <div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none; border-radius: 12px; border: none; background: #dcfce7; color: #15803d; font-weight: 600; padding: 16px 20px;">
                        <span id="successMessage"></span>
                        <button type="button" class="btn-close" onclick="closeAlert('successAlert')" style="box-shadow: none;"></button>
                    </div>

                    <!-- Profile Information Card -->
                    <div class="form-card">
                        <h4 class="mb-4 fw-bold" style="color: #1e293b;"><i class="fa fa-user-cog mr-2" style="color: #ffbe33;"></i> Thông tin cá nhân</h4>
                        <form id="profileForm" enctype="multipart/form-data">
                            <!-- Avatar Upload Section -->
                            <div class="mb-4 d-flex align-items-center gap-3 flex-wrap" id="avatarUploadGroup" style="background: #f8fafc; padding: 15px; border-radius: 14px; border: 1px dashed #e2e8f0;">
                                <div class="avatar-circle" id="formAvatarPreview" style="width: 80px; height: 80px; font-size: 32px; overflow: hidden; padding: 0; flex-shrink: 0; background: linear-gradient(135deg, #ffbe33 0%, #ff9f43 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #222831; font-weight: 800; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);"></div>
                                <div>
                                    <label class="form-label d-block mb-1" style="font-size: 13.5px; font-weight: 700; color: #1e293b;">Ảnh đại diện</label>
                                    <input type="file" class="form-control form-control-sm" id="AnhDaiDien" name="AnhDaiDien" accept="image/*" style="max-width: 250px; padding: 6px 12px; height: auto; font-size: 13px;">
                                    <small class="text-muted d-block mt-1" id="avatarHint" style="font-size: 12px;">Hỗ trợ JPG, PNG, GIF. Kích thước tối đa 2MB.</small>
                                    <div class="invalid-feedback" id="AnhDaiDien-error" style="display: block;"></div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="HoTen" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="HoTen" name="HoTen" required placeholder="Nhập họ và tên đầy đủ">
                                    <div class="invalid-feedback" id="HoTen-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="TenDangNhap" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="TenDangNhap" disabled>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="Email" class="form-label">Email liên hệ</label>
                                    <input type="email" class="form-control" id="Email" name="Email" required placeholder="example@domain.com">
                                    <div class="invalid-feedback" id="Email-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="SoDienThoai" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" id="SoDienThoai" name="SoDienThoai" placeholder="Nhập số điện thoại">
                                    <div class="invalid-feedback" id="SoDienThoai-error"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="DiaChi" class="form-label">⭐ Địa chỉ giao hàng mặc định</label>
                                <textarea class="form-control" id="DiaChi" name="DiaChi" rows="2" placeholder="Địa chỉ nhận hàng mặc định của bạn"></textarea>
                                <div class="invalid-feedback" id="DiaChi-error"></div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="DiaChiNhaRieng" class="form-label">🏠 Nhà riêng</label>
                                    <textarea class="form-control" id="DiaChiNhaRieng" name="DiaChiNhaRieng" rows="2" placeholder="Địa chỉ nhà riêng"></textarea>
                                    <div class="invalid-feedback" id="DiaChiNhaRieng-error"></div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="DiaChiVanPhong" class="form-label">🏢 Văn phòng</label>
                                    <textarea class="form-control" id="DiaChiVanPhong" name="DiaChiVanPhong" rows="2" placeholder="Địa chỉ văn phòng"></textarea>
                                    <div class="invalid-feedback" id="DiaChiVanPhong-error"></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="DiaChiTruongHoc" class="form-label">🏫 Trường học</label>
                                    <textarea class="form-control" id="DiaChiTruongHoc" name="DiaChiTruongHoc" rows="2" placeholder="Địa chỉ trường học"></textarea>
                                    <div class="invalid-feedback" id="DiaChiTruongHoc-error"></div>
                                </div>
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary" id="saveButton">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('profile.index') }}'">Hủy</button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password Section -->
                    <div class="form-card password-section" style="border-top: none; margin-top: 0;">
                        <h4 class="mb-4 fw-bold" style="color: #1e293b;"><i class="fa fa-key mr-2" style="color: #ffbe33;"></i> Bảo mật & Đổi mật khẩu</h4>

                        <div id="passwordErrorAlert" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none; border-radius: 12px; border: none; background: #fee2e2; color: #b91c1c; font-weight: 600; padding: 16px 20px;">
                            <div id="passwordErrorMessage"></div>
                            <button type="button" class="btn-close" onclick="closeAlert('passwordErrorAlert')" style="box-shadow: none;"></button>
                        </div>

                        <div id="passwordSuccessAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none; border-radius: 12px; border: none; background: #dcfce7; color: #15803d; font-weight: 600; padding: 16px 20px;">
                            <span id="passwordSuccessMessage"></span>
                            <button type="button" class="btn-close" onclick="closeAlert('passwordSuccessAlert')" style="box-shadow: none;"></button>
                        </div>

                        <form id="passwordForm">
                            <div class="mb-4">
                                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="Nhập mật khẩu đang sử dụng">
                                <div class="invalid-feedback" id="current_password-error"></div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Tối thiểu 6 ký tự">
                                    <div class="invalid-feedback" id="password-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Nhập lại mật khẩu mới">
                                    <div class="invalid-feedback" id="password_confirmation-error"></div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" id="changePasswordButton">
                                <i class="fas fa-key me-2"></i>Cập nhật mật khẩu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Load profile data from API
        // Preview avatar on change
        document.getElementById('AnhDaiDien').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    document.getElementById('formAvatarPreview').innerHTML = `<img src="${evt.target.result}" alt="preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                };
                reader.readAsDataURL(file);
            }
        });

        async function loadProfile() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const profileContainer = document.getElementById('profileContainer');

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

                    // Update avatar and header
                    const avatarInitial = document.getElementById('avatarInitial');
                    const formAvatarPreview = document.getElementById('formAvatarPreview');
                    
                    if (data.AnhDaiDien) {
                        const imgHtml = `<img src="${data.avatar_url}" alt="avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                        avatarInitial.innerHTML = imgHtml;
                        avatarInitial.style.padding = '0';
                        avatarInitial.style.overflow = 'hidden';
                        
                        formAvatarPreview.innerHTML = imgHtml;
                    } else {
                        const initialText = data.TenDangNhap ? data.TenDangNhap.charAt(0).toUpperCase() : '?';
                        avatarInitial.textContent = initialText;
                        formAvatarPreview.textContent = initialText;
                    }

                    if (data.google_id) {
                        document.getElementById('avatarHint').innerHTML = `<span class="badge badge-success" style="background-color: #28a745; color: white;"><i class="fab fa-google" style="margin-right: 4px;"></i> Liên kết Google</span> Đang hiển thị ảnh từ tài khoản Gmail của bạn.`;
                    }

                    document.getElementById('userName').textContent = data.HoTen || data.TenDangNhap || 'N/A';
                    document.getElementById('userEmail').textContent = data.Email || 'N/A';

                    // Fill form fields
                    document.getElementById('HoTen').value = data.HoTen || '';
                    document.getElementById('TenDangNhap').value = data.TenDangNhap || '';
                    document.getElementById('Email').value = data.Email || '';
                    document.getElementById('SoDienThoai').value = data.SoDienThoai || '';
                    document.getElementById('DiaChi').value = data.DiaChi || '';
                    document.getElementById('DiaChiNhaRieng').value = data.DiaChiNhaRieng || '';
                    document.getElementById('DiaChiVanPhong').value = data.DiaChiVanPhong || '';
                    document.getElementById('DiaChiTruongHoc').value = data.DiaChiTruongHoc || '';

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

            // Using FormData to support file upload with Laravel spoofing
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('HoTen', document.getElementById('HoTen').value);
            formData.append('Email', document.getElementById('Email').value);
            formData.append('SoDienThoai', document.getElementById('SoDienThoai').value);
            formData.append('DiaChi', document.getElementById('DiaChi').value);
            formData.append('DiaChiNhaRieng', document.getElementById('DiaChiNhaRieng').value);
            formData.append('DiaChiVanPhong', document.getElementById('DiaChiVanPhong').value);
            formData.append('DiaChiTruongHoc', document.getElementById('DiaChiTruongHoc').value);

            const fileInput = document.getElementById('AnhDaiDien');
            if (fileInput.files[0]) {
                formData.append('AnhDaiDien', fileInput.files[0]);
            }

            try {
                const response = await fetch('/api/v1/profile', {
                    method: 'POST', // Use POST with _method PUT spoofing to support file uploads
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin',
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showSuccess(result.message || 'Cập nhật thông tin thành công');
                    
                    // Update header if email changed
                    document.getElementById('userEmail').textContent = document.getElementById('Email').value;
                    
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
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                changePasswordButton.innerHTML = '<i class="fas fa-key me-2"></i>Cập nhật mật khẩu';
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
        document.addEventListener('DOMContentLoaded', function() {
            loadProfile();

            const GOONG_API_KEY = @json(config('services.goong.api_key'));

            function setupProfileAutocomplete(inputElement) {
                if (!inputElement) return;

                // Ensure parent has position relative
                if (inputElement.parentNode) {
                    inputElement.parentNode.style.position = "relative";
                }

                let suggestionsBox = document.createElement("div");
                suggestionsBox.className = "address-suggestions-list";
                inputElement.parentNode.appendChild(suggestionsBox);

                let autocompleteTimeout = null;

                document.addEventListener("click", function(e) {
                    if (e.target !== inputElement && e.target !== suggestionsBox && !suggestionsBox.contains(e.target)) {
                        suggestionsBox.style.display = "none";
                    }
                });

                inputElement.addEventListener("input", function() {
                    const query = this.value.trim();
                    clearTimeout(autocompleteTimeout);

                    if (query.length < 3) {
                        suggestionsBox.style.display = "none";
                        suggestionsBox.innerHTML = "";
                        return;
                    }

                    autocompleteTimeout = setTimeout(async () => {
                        try {
                            if (GOONG_API_KEY && GOONG_API_KEY.trim() !== '' && !GOONG_API_KEY.includes('GOONG_API_KEY')) {
                                const res = await fetch(`https://rsapi.goong.io/Place/AutoComplete?api_key=${GOONG_API_KEY}&input=${encodeURIComponent(query)}`);
                                const data = await res.json();
                                if (inputElement.value.trim() !== query) return;
                                if (data && data.predictions) {
                                    renderProfileSuggestions(data.predictions, suggestionsBox, inputElement, true);
                                }
                            } else {
                                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=vn&accept-language=vi`);
                                const data = await res.json();
                                if (inputElement.value.trim() !== query) return;
                                if (data) {
                                    renderProfileSuggestions(data, suggestionsBox, inputElement, false);
                                }
                            }
                        } catch (err) {
                            console.warn("Autocomplete error:", err);
                        }
                    }, 350);
                });

                inputElement.addEventListener("focus", function() {
                    if (this.value.trim().length >= 3 && suggestionsBox.children.length > 0) {
                        suggestionsBox.style.display = "block";
                    }
                });
            }

            function renderProfileSuggestions(results, box, input, isGoong) {
                box.innerHTML = "";
                if (results.length === 0) {
                    box.style.display = "none";
                    return;
                }

                results.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "address-suggestion-item";
                    const description = isGoong ? item.description : item.display_name;
                    div.innerHTML = `<i class="fa fa-map-marker-alt"></i> <span>${description}</span>`;
                    
                    div.addEventListener("click", function() {
                        input.value = description;
                        box.style.display = "none";
                    });
                    box.appendChild(div);
                });
                box.style.display = "block";
            }

            // Setup autocomplete for all four address inputs on the profile edit form
            setupProfileAutocomplete(document.getElementById("DiaChi"));
            setupProfileAutocomplete(document.getElementById("DiaChiNhaRieng"));
            setupProfileAutocomplete(document.getElementById("DiaChiVanPhong"));
            setupProfileAutocomplete(document.getElementById("DiaChiTruongHoc"));
        });
    </script>
@endsection

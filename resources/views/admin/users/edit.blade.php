@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-edit text-primary fs-4"></i>
                        <h2 class="h4 mb-0 fw-bold">Chỉnh sửa User</h2>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form id="editUserForm" autocomplete="off">
                            <div class="row g-3">
                                <!-- Họ và tên -->
                                <div class="col-md-6">
                                    <label for="HoTen" class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-3" id="HoTen" name="HoTen" placeholder="Nhập họ và tên">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Tên đăng nhập -->
                                <div class="col-md-6">
                                    <label for="TenDangNhap" class="form-label fw-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-3" id="TenDangNhap" name="TenDangNhap" placeholder="Nhập tên đăng nhập">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="Email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control rounded-3" id="Email" name="Email" placeholder="Nhập email" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Số điện thoại -->
                                <div class="col-md-6">
                                    <label for="SoDienThoai" class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-3" id="SoDienThoai" name="SoDienThoai" placeholder="Nhập số điện thoại">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Vai trò -->
                                <div class="col-md-6">
                                    <label for="VaiTro" class="form-label fw-bold">Vai trò <span class="text-danger">*</span></label>
                                    <select class="form-select rounded-3" id="VaiTro" name="VaiTro">
                                        <option value="">Chọn vai trò</option>
                                        <option value="QuanTri">Admin</option>
                                        <option value="NguoiBan">Seller</option>
                                        <option value="KhachHang">Customer</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Mật khẩu -->
                                <div class="col-md-6">
                                    <label for="MatKhau" class="form-label fw-bold">Mật khẩu</label>
                                    <input type="password" class="form-control rounded-3" id="MatKhau" name="MatKhau" placeholder="Bỏ trống nếu không muốn đổi" autocomplete="new-password">
                                    <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu.</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4 rounded-3">Hủy</a>
                                <button type="submit" class="btn btn-primary px-4 rounded-3" id="saveBtn">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to fetch data and handle submit -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = "{{ $id }}";
            const form = document.getElementById('editUserForm');
            
            // Fetch current user data
            fetch(`/api/v1/admin/users/${userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    
                    // Fill form
                    document.getElementById('HoTen').value = user.HoTen || '';
                    document.getElementById('TenDangNhap').value = user.TenDangNhap || '';
                    document.getElementById('Email').value = user.Email || '';
                    document.getElementById('SoDienThoai').value = user.SoDienThoai || '';
                    document.getElementById('VaiTro').value = user.VaiTro || '';
                } else {
                    window.showAdminToast('Không thể lấy thông tin người dùng.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showAdminToast('Đã xảy ra lỗi kết nối.', 'error');
            });

            // Handle form submit
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                const inputs = form.querySelectorAll('.form-control, .form-select');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });

                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    if (key !== 'MatKhau' || value !== '') {
                        data[key] = value;
                    }
                });

                // Fetch request
                fetch(`/api/v1/admin/users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        localStorage.setItem('admin_success', result.message || 'Cập nhật người dùng thành công!');
                        window.location.href = "{{ route('admin.users.index') }}";
                    } else if (result.errors) {
                        // Show validation errors
                        const errors = result.errors;
                        for (const key in errors) {
                            const input = document.getElementById(key);
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = input.nextElementSibling;
                                if (feedback && feedback.classList.contains('invalid-feedback')) {
                                    feedback.textContent = errors[key][0];
                                }
                            }
                        }
                    } else {
                        window.showAdminToast('Có lỗi xảy ra: ' + result.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showAdminToast('Đã xảy ra lỗi kết nối.', 'error');
                });
            });

            // Clear error on input
            const inputs = form.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endsection

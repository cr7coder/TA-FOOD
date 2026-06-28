@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-eye text-primary fs-4"></i>
                        <h2 class="h4 mb-0 fw-bold">Chi tiết User</h2>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <!-- Main Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <!-- Purple Header Card -->
                    <div class="p-4" style="background: linear-gradient(135deg, #7928CA 0%, #B800FF 100%); color: white;">
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <span class="fs-1" id="userAvatar">👤</span>
                            </div>
                            <div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="h3 mb-1 fw-bold d-inline-block" id="userFullName">Đang tải...</h3>
                                    <span class="opacity-75 fs-6 fw-bold" id="userUsername"></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-white text-purple" id="userRoleBadge" style="color: #7928CA !important;">...</span>
                                    <span class="opacity-75">•</span>
                                    <span class="opacity-75" id="userEmailHeader">...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards Inside -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);">
                                    <div class="small opacity-75 mb-1">Ngày tham gia</div>
                                    <div class="fs-5 fw-bold" id="joinDate">01/01/2024</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);">
                                    <div class="small opacity-75 mb-1">Tổng đơn hàng</div>
                                    <div class="fs-5 fw-bold" id="totalOrders">0</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);">
                                    <div class="small opacity-75 mb-1">Tổng chi tiêu</div>
                                    <div class="fs-5 fw-bold" id="totalSpending">0 đ</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Body -->
                    <div class="card-body p-4 bg-light">
                        <div class="row g-4">
                            <!-- Số điện thoại -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center gap-2 text-muted mb-2">
                                            <i class="fas fa-phone small"></i>
                                            <span class="small fw-bold text-uppercase">Số điện thoại</span>
                                        </div>
                                        <div class="fs-5 fw-bold" id="userPhone">...</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center gap-2 text-muted mb-2">
                                            <i class="fas fa-envelope small"></i>
                                            <span class="small fw-bold text-uppercase">Email</span>
                                        </div>
                                        <div class="fs-5 fw-bold" id="userEmail">...</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Lần đăng nhập cuối -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center gap-2 text-muted mb-2">
                                            <i class="fas fa-calendar-alt small"></i>
                                            <span class="small fw-bold text-uppercase">Lần đăng nhập cuối</span>
                                        </div>
                                        <div class="fs-5 fw-bold" id="lastLogin">29/12/2024 10:30</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Trạng thái -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center gap-2 text-muted mb-2">
                                            <i class="fas fa-shield-alt small"></i>
                                            <span class="small fw-bold text-uppercase">Trạng thái</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill" id="userStatus">Hoạt động</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button id="toggleStatusBtn" class="btn btn-warning d-flex align-items-center gap-1 text-white">
                                <i class="fas fa-user-slash"></i> Vô hiệu hóa
                            </button>
                            <a href="/admin/users/{{ $id }}/edit" class="btn btn-primary d-flex align-items-center gap-1">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <button id="deleteUserBtn" class="btn btn-danger d-flex align-items-center gap-1">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to fetch data -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = "{{ $id }}";
            let currentUserStatus = '';
            
            // Check for success message in localStorage
            const adminSuccessMsg = localStorage.getItem('admin_success');
            if (adminSuccessMsg) {
                window.showAdminToast(adminSuccessMsg, 'success');
                localStorage.removeItem('admin_success');
            }
            
            // Fetch user details
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
                    currentUserStatus = user.TrangThai;
                    
                    // Fill data
                    document.getElementById('userFullName').textContent = user.HoTen || 'N/A';
                    document.getElementById('userUsername').textContent = `@${user.TenDangNhap}`;
                    document.getElementById('userEmailHeader').textContent = user.Email;
                    document.getElementById('userEmail').textContent = user.Email;
                    document.getElementById('userPhone').textContent = user.SoDienThoai || 'N/A';
                    document.getElementById('joinDate').textContent = user.ngay_tham_gia || '01/01/2024';
                    document.getElementById('lastLogin').textContent = user.lan_dang_nhap_cuoi || 'Chưa đăng nhập';
                    
                    // Role mapping
                    const roleBadge = document.getElementById('userRoleBadge');
                    const avatarSpan = document.getElementById('userAvatar');
                    
                    if (user.VaiTro === 'QuanTri') {
                        roleBadge.textContent = 'Admin';
                        avatarSpan.textContent = '👦';
                    } else if (user.VaiTro === 'NguoiBan') {
                        roleBadge.textContent = 'Seller';
                        avatarSpan.textContent = '👨‍🍳';
                    } else {
                        roleBadge.textContent = 'Customer';
                        avatarSpan.textContent = '👤';
                    }
                    
                    // Status mapping function
                    function updateStatusUI(status) {
                        currentUserStatus = status;
                        const statusBadge = document.getElementById('userStatus');
                        const toggleBtn = document.getElementById('toggleStatusBtn');
                        
                        if (status === 'Hoạt động') {
                            statusBadge.textContent = 'Hoạt động';
                            statusBadge.className = 'badge bg-success-subtle text-success px-3 py-2 rounded-pill';
                            toggleBtn.innerHTML = '<i class="fas fa-user-slash"></i> Vô hiệu hóa';
                            toggleBtn.className = 'btn btn-warning d-flex align-items-center gap-1 text-white';
                        } else {
                            statusBadge.textContent = 'Bị khóa';
                            statusBadge.className = 'badge bg-danger-subtle text-danger px-3 py-2 rounded-pill';
                            toggleBtn.innerHTML = '<i class="fas fa-user-check"></i> Kích hoạt';
                            toggleBtn.className = 'btn btn-success d-flex align-items-center gap-1';
                        }
                    }

                    updateStatusUI(user.TrangThai);
                    
                    document.getElementById('totalOrders').textContent = user.tong_don || 0;
                    document.getElementById('totalSpending').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(user.chi_tieu || 0);

                    // Add event listener for toggle status
                    const toggleBtn = document.getElementById('toggleStatusBtn');
                    toggleBtn.addEventListener('click', function() {
                        const actionText = currentUserStatus === 'Hoạt động' ? 'vô hiệu hóa' : 'kích hoạt lại';
                        window.showConfirmModal(
                            'Xác nhận thay đổi',
                            `Bạn có chắc muốn ${actionText} tài khoản này?`,
                            function() {
                                fetch(`/api/v1/admin/users/${userId}/toggle-status`, {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.showAdminToast(data.message || 'Cập nhật trạng thái thành công!', 'success');
                                        updateStatusUI(data.data.TrangThai);
                                    } else {
                                        window.showAdminToast('Có lỗi xảy ra: ' + data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    window.showAdminToast('Đã xảy ra lỗi kết nối.', 'error');
                                });
                            }
                        );
                    });

                    // Add event listener for delete user
                    const deleteBtn = document.getElementById('deleteUserBtn');
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', function() {
                            window.showConfirmModal(
                                'Xác nhận xóa',
                                'Bạn có chắc chắn muốn xóa người dùng này?<br>Hành động này không thể hoàn tác.',
                                function() {
                                    fetch(`/api/v1/admin/users/${userId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            localStorage.setItem('admin_success', data.message || 'Xóa người dùng thành công!');
                                            window.location.href = "{{ route('admin.users.index') }}";
                                        } else {
                                            window.showAdminToast('Có lỗi xảy ra: ' + data.message, 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        window.showAdminToast('Đã xảy ra lỗi kết nối.', 'error');
                                    });
                                }
                            );
                        });
                    }
                } else {
                    window.showAdminToast('Không thể lấy thông tin người dùng.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showAdminToast('Đã xảy ra lỗi kết nối.', 'error');
            });
        });
    </script>
@endsection

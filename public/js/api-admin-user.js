document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let searchTerm = '';
    let roleFilter = '';
    let statusFilter = '';

    // Elements
    const userTableBody = document.getElementById('userTableBody');
    const searchInput = document.getElementById('searchInput');
    const roleFilterSelect = document.getElementById('roleFilter');
    const statusFilterSelect = document.getElementById('statusFilter');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationContainer = document.getElementById('paginationContainer');

    // Stats Elements
    const totalUsersEl = document.getElementById('totalUsers');
    const adminUsersEl = document.getElementById('adminUsers');
    const sellerUsersEl = document.getElementById('sellerUsers');
    const customerUsersEl = document.getElementById('customerUsers');

    // Fetch Stats
    function fetchStats() {
        fetch('/api/v1/admin/users/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    totalUsersEl.textContent = data.data.total;
                    adminUsersEl.textContent = data.data.admin;
                    sellerUsersEl.textContent = data.data.seller;
                    customerUsersEl.textContent = data.data.customer;
                }
            })
            .catch(error => console.error('Error fetching stats:', error));
    }

    // Fetch Users
    function fetchUsers(page = 1, search = '', role = '', status = '') {
        userTableBody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        let url = `/api/v1/admin/users?page=${page}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (role) url += `&role=${encodeURIComponent(role)}`;
        if (status) url += `&status=${encodeURIComponent(status)}`;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTable(data.data);
                    renderPagination(data.pagination);
                }
            })
            .catch(error => {
                console.error('Error fetching users:', error);
                userTableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-circle me-1"></i> Đã xảy ra lỗi khi tải dữ liệu.
                    </td>
                </tr>
            `;
            });
    }

    // Render Table
    function renderTable(users) {
        if (users.length === 0) {
            userTableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        Không tìm thấy người dùng nào.
                    </td>
                </tr>
            `;
            return;
        }

        userTableBody.innerHTML = '';
        users.forEach(user => {
            // Map role
            let roleText = 'Customer';
            let roleClass = 'role-customer';
            let avatarEmoji = '👤';

            if (user.VaiTro === 'QuanTri') {
                roleText = 'Admin';
                roleClass = 'role-admin';
                avatarEmoji = '👦';
            } else if (user.VaiTro === 'NguoiBan') {
                roleText = 'Seller';
                roleClass = 'role-seller';
                avatarEmoji = '👨‍🍳';
            }

            // Map status
            const statusClass = user.TrangThai === 'Hoạt động' ? 'status-active' : 'status-inactive';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-center"><span class="fs-4">${avatarEmoji}</span></td>
                <td>
                    <div class="fw-bold">${user.HoTen || 'N/A'}</div>
                    <small class="text-muted">${user.TenDangNhap}</small>
                </td>
                <td>${user.Email}</td>
                <td>${user.SoDienThoai || 'N/A'}</td>
                <td><span class="role-badge ${roleClass}">${roleText}</span></td>
                <td>${user.tong_don || 0}</td>
                <td>${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(user.chi_tieu || 0)}</td>
                <td><span class="role-badge ${statusClass}">${user.TrangThai || 'Hoạt động'}</span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/admin/users/${user.MaNguoiDung}" class="btn btn-sm btn-outline-warning btn-action" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin/users/${user.MaNguoiDung}/edit" class="btn btn-sm btn-outline-primary btn-action" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger btn-action delete-user-btn" data-id="${user.MaNguoiDung}" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            userTableBody.appendChild(row);
        });
    }

    // Render Pagination
    function renderPagination(pagination) {
        paginationInfo.textContent = `Hiển thị ${((pagination.current_page - 1) * pagination.per_page) + 1} đến ${Math.min(pagination.current_page * pagination.per_page, pagination.total)} của ${pagination.total} kết quả`;

        if (!paginationContainer) return;
        if (pagination.last_page <= 1) { paginationContainer.innerHTML = ''; return; }

        let html = `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}"><i class="fas fa-chevron-left" style="font-size: 11px;"></i></a></li>`;

        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === 1 || i === pagination.last_page || Math.abs(i - pagination.current_page) <= 1) {
                html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            } else if (Math.abs(i - pagination.current_page) === 2) {
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            }
        }

        html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}"><i class="fas fa-chevron-right" style="font-size: 11px;"></i></a></li>`;

        paginationContainer.innerHTML = html;
        paginationContainer.querySelectorAll('.page-link[data-page]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const pg = parseInt(a.getAttribute('data-page'));
                if (pg && pg !== currentPage) {
                    currentPage = pg;
                    fetchUsers(currentPage, searchTerm, roleFilter, statusFilter);
                }
            });
        });
    }

    // Event Listeners
    let debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            searchTerm = this.value;
            currentPage = 1;
            fetchUsers(currentPage, searchTerm, roleFilter, statusFilter);
        }, 500);
    });

    roleFilterSelect.addEventListener('change', function () {
        roleFilter = this.value;
        currentPage = 1;
        fetchUsers(currentPage, searchTerm, roleFilter, statusFilter);
    });

    statusFilterSelect.addEventListener('change', function () {
        statusFilter = this.value;
        currentPage = 1;
        fetchUsers(currentPage, searchTerm, roleFilter, statusFilter);
    });

    // Handle Delete User
    userTableBody.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.delete-user-btn');
        if (deleteBtn) {
            const userId = deleteBtn.getAttribute('data-id');
            window.showConfirmModal(
                'Xác nhận xóa',
                'Bạn có chắc muốn xóa người dùng này?<br>Hành động này không thể hoàn tác.',
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
                                window.showAdminToast(data.message || 'Xóa người dùng thành công!', 'success');
                                fetchUsers(currentPage, searchTerm, roleFilter, statusFilter); // Refresh list
                                fetchStats(); // Refresh stats
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
        }
    });

    // Initial Load
    const adminSuccessMsg = localStorage.getItem('admin_success');
    if (adminSuccessMsg) {
        window.showAdminToast(adminSuccessMsg, 'success');
        localStorage.removeItem('admin_success');
    }

    fetchStats();
    fetchUsers();
});

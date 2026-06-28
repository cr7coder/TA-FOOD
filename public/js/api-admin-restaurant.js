document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;

    // Initialize
    loadRestaurants();

    // Event Listeners for Filters
    document.getElementById('searchInput').addEventListener('input', debounce(function () {
        currentPage = 1;
        loadRestaurants();
    }, 500));

    document.getElementById('statusFilter').addEventListener('change', function () {
        currentPage = 1;
        loadRestaurants();
    });

    function loadRestaurants(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const tbody = document.getElementById('restaurantTableBody');

        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        fetch(`/api/v1/admin/restaurants?page=${page}&search=${search}&status=${status}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                throw new Error(text);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderTable(data.data);
                renderPagination(data.pagination);
                
                const start = (data.pagination.current_page - 1) * data.pagination.per_page + 1;
                const end = Math.min(start + data.pagination.per_page - 1, data.pagination.total);
                document.getElementById('paginationInfo').innerText = 
                    `Hiển thị ${data.pagination.total === 0 ? 0 : start} đến ${end} của ${data.pagination.total} kết quả`;
            } else {
                window.showAdminToast(data.message || 'Có lỗi xảy ra khi tải dữ liệu', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.showAdminToast('Lỗi kết nối server', 'error');
            const tbody = document.getElementById('restaurantTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-circle me-1"></i> Đã xảy ra lỗi khi tải dữ liệu.
                    </td>
                </tr>
            `;
        });
    }

    function renderTable(restaurants) {
        const tbody = document.getElementById('restaurantTableBody');
        tbody.innerHTML = '';

        if (restaurants.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0">Không tìm thấy nhà hàng nào</p>
                    </td>
                </tr>
            `;
            return;
        }

        restaurants.forEach(restaurant => {
            let statusBadge = '';
            let actionButtons = '';
            
            if (restaurant.TrangThai === 'Chờ duyệt') {
                statusBadge = `<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Chờ duyệt</span>`;
                actionButtons = `
                    <button class="btn btn-sm btn-success w-100 mb-1" onclick="updateStatus(${restaurant.MaNhaHang}, 'Hoạt động')">
                        <i class="fas fa-check"></i> Phê duyệt
                    </button>
                    <button class="btn btn-sm btn-danger w-100" onclick="updateStatus(${restaurant.MaNhaHang}, 'Từ chối')">
                        <i class="fas fa-times"></i> Từ chối
                    </button>
                `;
            } else if (restaurant.TrangThai === 'Hoạt động') {
                statusBadge = `<span class="badge status-active role-badge"><i class="fas fa-check-circle"></i> Hoạt động</span>`;
                actionButtons = `
                    <button class="btn btn-sm btn-warning w-100 mb-1" onclick="updateStatus(${restaurant.MaNhaHang}, 'Đóng cửa')">
                        <i class="fas fa-pause"></i> Tạm ngưng
                    </button>
                `;
            } else {
                statusBadge = `<span class="badge status-inactive role-badge"><i class="fas fa-ban"></i> ${restaurant.TrangThai}</span>`;
                actionButtons = `
                    <button class="btn btn-sm btn-success w-100" onclick="updateStatus(${restaurant.MaNhaHang}, 'Hoạt động')">
                        <i class="fas fa-check"></i> Kích hoạt lại
                    </button>
                `;
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${restaurant.HinhAnh || 'https://ui-avatars.com/api/?name='+encodeURI(restaurant.TenNhaHang)+'&background=ffbe33&color=222831&size=128&bold=true'}" class="rounded me-3" style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <div class="fw-bold text-dark">${restaurant.TenNhaHang}</div>
                            <div class="small text-muted">ID: #${restaurant.MaNhaHang}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fw-medium">${restaurant.owner ? restaurant.owner.HoTen : 'N/A'}</div>
                    <div class="small text-muted">${restaurant.owner ? restaurant.owner.Email : ''}</div>
                </td>
                <td>${restaurant.SoDienThoai || 'N/A'}</td>
                <td>
                    <div class="text-truncate" style="max-width: 250px;" title="${restaurant.DiaChi}">
                        ${restaurant.DiaChi || 'Chưa cập nhật'}
                    </div>
                </td>
                <td>${statusBadge}</td>
                <td>
                    ${actionButtons}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        container.innerHTML = '';

        if (pagination.last_page <= 1) return;

        // Prev Button
        container.innerHTML += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Page Numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                container.innerHTML += `
                    <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                container.innerHTML += `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `;
            }
        }

        // Next Button
        container.innerHTML += `
            <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

        // Attach events
        container.querySelectorAll('.page-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page !== pagination.current_page) {
                    loadRestaurants(page);
                }
            });
        });
    }

    window.updateStatus = function(id, status) {
        const statusLabel = {
            'Hoạt động': 'Phê duyệt / Kích hoạt lại',
            'Từ chối': 'Từ chối',
            'Chờ duyệt': 'Chuyển về Chờ duyệt',
            'Đóng cửa': 'Tạm ngưng'
        }[status] || status;

        window.showConfirmModal(
            'Xác nhận thay đổi trạng thái',
            `Bạn có chắc chắn muốn <strong>${statusLabel}</strong> nhà hàng này?`,
            function() {
                fetch(`/api/v1/admin/restaurants/${id}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showAdminToast(data.message, 'success');
                        loadRestaurants(currentPage);
                    } else {
                        window.showAdminToast(data.message || 'Lỗi cập nhật', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showAdminToast('Lỗi kết nối server', 'error');
                });
            }
        );
    };

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});

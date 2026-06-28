document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('doiTacTableBody');
    const searchInput = document.getElementById('searchInput');
    const paginationList = document.getElementById('paginationList');
    const paginationInfo = document.getElementById('paginationInfo');

    let currentPage = 1;
    let searchTerm = '';

    // Initial load
    fetchDoiTacs();

    // Search event
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            searchTerm = e.target.value;
            currentPage = 1; // Reset to page 1 on search
            fetchDoiTacs(currentPage, searchTerm);
        });
    }

    // Fetch data from API
    function fetchDoiTacs(page = 1, search = '') {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        let url = `/api/v1/admin/doi-tac-van-chuyen?page=${page}&per_page=10`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTable(data.data.items);
                renderPagination(data.data.pagination);
            } else {
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Lỗi: ${data.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Đã xảy ra lỗi khi tải dữ liệu.</td></tr>`;
        });
    }

    // Render table rows
    function renderTable(items) {
        if (!items || items.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Không tìm thấy đối tác nào.</td></tr>`;
            return;
        }

        let html = '';
        items.forEach(item => {
            const statusClass = item.trang_thai === 'Hoạt động' ? 'status-hoan-thanh' : 'status-cho-xac-nhan';
            
            // Use real data from API
            const nguoiLienHe = item.nguoi_lien_he || 'Chưa cập nhật';
            const phiKm = item.phi_km ? formatCurrency(item.phi_km) : '0 đ';
            
            html += `
                <tr>
                    <td>
                        <div class="fw-bold text-primary">${item.ten_doi_tac}</div>
                    </td>
                    <td>
                        <div>${nguoiLienHe}</div>
                    </td>
                    <td>
                        <div><i class="fas fa-phone text-muted me-1"></i> ${item.so_dien_thoai || 'N/A'}</div>
                        <div><i class="fas fa-envelope text-muted me-1"></i> ${item.email_lien_he || 'N/A'}</div>
                    </td>
                    <td>
                        <div class="fw-bold text-success">${formatCurrency(item.phi_van_chuyen)}</div>
                    </td>
                    <td>
                        <div>${phiKm}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge-status ${statusClass}">${item.trang_thai || 'N/A'}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="/admin/doi-tac-van-chuyen/${item.id}" class="btn-action btn-action-view" title="Xem chi tiết">
                                <i class="fas fa-eye" style="font-size:12px;"></i>
                            </a>
                            <a href="/admin/doi-tac-van-chuyen/${item.id}/edit" class="btn-action btn-action-edit" title="Chỉnh sửa">
                                <i class="fas fa-edit" style="font-size:12px;"></i>
                            </a>
                            <button class="btn-action btn-action-delete btn-delete" data-id="${item.id}" data-name="${item.ten_doi_tac}" title="Xóa">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Bind delete events
        const deleteButtons = tableBody.querySelectorAll('.btn-delete');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                confirmDelete(id, name);
            });
        });
    }

    // Render pagination
    function renderPagination(pagination) {
        if (!pagination) return;

        currentPage = pagination.current_page;
        
        // Update info
        if (paginationInfo) {
            const from = ((pagination.current_page - 1) * pagination.per_page) + 1;
            const to   = Math.min(pagination.current_page * pagination.per_page, pagination.total);
            paginationInfo.textContent = `Hiển thị ${from}–${to} trong tổng số ${pagination.total} đối tác`;
        }

        if (!paginationList) return;
        if (pagination.last_page <= 1) { paginationList.innerHTML = ''; return; }

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

        paginationList.innerHTML = html;

        // Bind pagination events
        const links = paginationList.querySelectorAll('.page-link');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    fetchDoiTacs(currentPage, searchTerm);
                }
            });
        });
    }

    // Confirm and delete
    function confirmDelete(id, name) {
        window.showConfirmModal(
            'Xác nhận xóa',
            `Bạn có chắc chắn muốn xóa đối tác vận chuyển <strong class="text-danger">${name}</strong>?<br>Hành động này không thể hoàn tác.`,
            function() {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                fetch(`/api/v1/admin/doi-tac-van-chuyen/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showAdminToast('Xóa đối tác vận chuyển thành công!', 'success');
                        fetchDoiTacs(currentPage, searchTerm); // Refresh
                    } else {
                        window.showAdminToast(`Lỗi: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showAdminToast('Đã xảy ra lỗi khi xóa.', 'error');
                });
            }
        );
    }

    // Helper to format currency
    function formatCurrency(amount) {
        if (!amount) return '0 đ';
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
});

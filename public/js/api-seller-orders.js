/**
 * Seller Orders API Loader
 */

document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('orders-table-body');
    const statusFilter = document.getElementById('order-status-filter');
    const emptyState = document.getElementById('empty-state');
    
    // Pagination elements
    const paginationStart = document.getElementById('pagination-start');
    const paginationEnd = document.getElementById('pagination-end');
    const paginationTotal = document.getElementById('pagination-total');
    const paginationButtons = document.getElementById('pagination-buttons');

    let currentPage = 1;
    let currentStatus = 'Tất cả';

    // Initial load
    loadOrders(currentPage, currentStatus);

    // Filter event
    statusFilter.addEventListener('change', function() {
        currentPage = 1;
        currentStatus = this.value;
        loadOrders(currentPage, currentStatus);
    });

    async function loadOrders(page = 1, status = 'Tất cả') {
        showLoading();
        try {
            const response = await fetch(`/api/v1/seller/orders?status=${encodeURIComponent(status)}&page=${page}&per_page=10`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                renderOrders(result.data);
                renderPagination(result.pagination);
            } else {
                showError(result.message);
                clearPagination();
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
            showError('Không thể kết nối đến server.');
            clearPagination();
        }
    }

    function renderOrders(orders) {
        if (!orders || orders.length === 0) {
            tableBody.innerHTML = '';
            emptyState.classList.remove('d-none');
            return;
        }

        emptyState.classList.add('d-none');
        let html = '';

        orders.forEach(order => {
            const statusConfig = getStatusConfig(order.TrangThai);
            
            html += `
                <tr>
                    <td class="order-id">#${order.MaDonHang}</td>
                    <td class="fw-bold">${order.TenKhachHang}</td>
                    <td>
                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${order.MonAn}">
                            ${order.MonAn}
                        </div>
                    </td>
                    <td class="fw-bold text-primary">
                        <div>${order.TongTien}</div>
                        <div class="text-muted fw-normal" style="font-size: 11px; font-weight: normal;">(Đồ ăn: ${order.FoodRevenue})</div>
                    </td>
                    <td>
                        <span class="badge-status ${statusConfig.class}">
                            <i class="fas ${statusConfig.icon}"></i> ${order.TrangThai}
                        </span>
                    </td>
                    <td>
                        <div class="small fw-bold">${order.PhuongThucThanhToan || 'COD'}</div>
                        <div class="text-muted small">${order.ThanhToan}</div>
                    </td>
                    <td>
                        <div class="small fw-500">${order.ThoiGian}</div>
                        <div class="text-muted" style="font-size: 11px;">${order.NgayDat}</div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <a href="/seller/orders/${order.MaDonHang}" class="btn-detail">
                                <i class="far fa-eye"></i> Chi tiết
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;
    }

    function renderPagination(pagination) {
        if (!pagination) {
            clearPagination();
            return;
        }

        const total = pagination.total;
        const perPage = pagination.per_page;
        const current = pagination.current_page;
        const last = pagination.last_page;

        // Update info text
        const start = total === 0 ? 0 : (current - 1) * perPage + 1;
        const end = Math.min(current * perPage, total);
        paginationStart.textContent = start;
        paginationEnd.textContent = end;
        paginationTotal.textContent = total;

        // Render buttons
        let btnHtml = '';

        // Previous button
        const prevDisabled = current === 1 ? 'disabled' : '';
        btnHtml += `
            <li class="page-item ${prevDisabled}">
                <button class="page-link" data-page="${current - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </button>
            </li>
        `;

        // Page buttons
        for (let i = 1; i <= last; i++) {
            const activeClass = i === current ? 'active' : '';
            btnHtml += `
                <li class="page-item ${activeClass}">
                    <button class="page-link" data-page="${i}">${i}</button>
                </li>
            `;
        }

        // Next button
        const nextDisabled = current === last ? 'disabled' : '';
        btnHtml += `
            <li class="page-item ${nextDisabled}">
                <button class="page-link" data-page="${current + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </button>
            </li>
        `;

        paginationButtons.innerHTML = btnHtml;

        // Add event listeners to links
        paginationButtons.querySelectorAll('.page-link').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const targetPage = parseInt(this.getAttribute('data-page'));
                if (targetPage >= 1 && targetPage <= last && targetPage !== current) {
                    currentPage = targetPage;
                    loadOrders(currentPage, currentStatus);
                }
            });
        });
    }

    function clearPagination() {
        paginationStart.textContent = 0;
        paginationEnd.textContent = 0;
        paginationTotal.textContent = 0;
        paginationButtons.innerHTML = '';
    }

    function getStatusConfig(status) {
        switch(status) {
            case 'Hoàn thành': return { class: 'badge-completed', icon: 'fa-check-double' };
            case 'Đang giao': return { class: 'badge-delivering', icon: 'fa-shipping-fast' };
            case 'Đang chuẩn bị': return { class: 'badge-preparing', icon: 'fa-utensils' };
            case 'Chờ xác nhận': return { class: 'badge-pending', icon: 'fa-clock' };
            case 'Đã xác nhận': return { class: 'badge-confirmed', icon: 'fa-check-circle' };
            case 'Đã hủy': return { class: 'badge-cancelled', icon: 'fa-times-circle' };
            default: return { class: 'bg-secondary', icon: 'fa-question-circle' };
        }
    }

    function showLoading() {
        emptyState.classList.add('d-none');
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </td>
            </tr>
        `;
    }

    function showError(msg) {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">${msg}</td></tr>`;
    }
});

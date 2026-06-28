/**
 * API Admin Orders Logic
 */

let currentFilters = {
    search: '',
    status: 'all',
    page: 1
};

function init() {
    loadStats();
    loadOrders();

    // Event listeners for stats cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            
            const status = card.dataset.status;
            document.getElementById('statusFilter').value = status;
            currentFilters.status = status;
            currentFilters.page = 1;
            loadOrders();
        });
    });

    // Auto-filter listeners
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = e.target.value;
            currentFilters.page = 1;
            loadOrders();
        }, 500); // Debounce 500ms
    });

    document.getElementById('statusFilter').addEventListener('change', (e) => {
        currentFilters.status = e.target.value;
        currentFilters.page = 1;
        
        // Update active class on stat cards to match the selected filter
        document.querySelectorAll('.stat-card').forEach(card => {
            if (currentFilters.status !== 'all' && card.dataset.status === currentFilters.status) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });

        loadOrders();
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}

async function loadStats() {
    try {
        const res = await fetch('/api/v1/admin/orders/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();
        
        if (data.success) {
            const stats = data.data;
            document.getElementById('count-cho-xu-ly').textContent = stats.cho_xac_nhan || 0;
            document.getElementById('count-da-thanh-toan').textContent = stats.da_xac_nhan || 0;
            document.getElementById('count-dang-chuan-bi').textContent = stats.dang_chuan_bi || 0;
            document.getElementById('count-dang-giao').textContent = stats.dang_giao || 0;
            document.getElementById('count-hoan-thanh').textContent = stats.hoan_thanh || 0;
            document.getElementById('count-huy').textContent = stats.da_huy || 0;
            document.getElementById('total-orders-count').textContent = stats.total || 0;
        }
    } catch (err) {
        console.error('Error loading stats:', err);
    }
}

async function loadOrders() {
    const tableBody = document.getElementById('ordersTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </td>
        </tr>
    `;

    try {
        const params = new URLSearchParams(currentFilters);
        const res = await fetch(`/api/v1/admin/orders?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (data.success) {
            renderOrders(data.data);
            renderPagination(data.pagination);
        }
    } catch (err) {
        tableBody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>`;
    }
}

function renderOrders(orders) {
    const tableBody = document.getElementById('ordersTableBody');
    if (!orders.length) {
        tableBody.innerHTML = `<tr><td colspan="9" class="text-center py-4">Không tìm thấy đơn hàng nào</td></tr>`;
        return;
    }

    tableBody.innerHTML = orders.map(order => {
        const statusBadge = getStatusBadge(order.status);
        return `
            <tr>
                <td><span class="order-code">${order.order_code}</span></td>
                <td>
                    <div class="fw-bold">${order.customer_name}</div>
                </td>
                <td>${order.store_name}</td>
                <td class="text-center">${order.items_count}</td>
                <td><span class="price-text">${formatVND(order.total_amount)}</span></td>
                <td>${statusBadge}</td>
                <td>
                    <div class="small">${order.payment_method}</div>
                    <div class="extra-small text-muted">${order.payment_status}</div>
                </td>
                <td><div class="small">${order.created_at}</div></td>
                <td class="text-center">
                    <button class="btn btn-action" onclick="viewDetail(${order.id})" title="Xem chi tiết">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function getStatusBadge(status) {
    let config = {
        label: status,
        bg: 'bg-secondary',
        icon: 'fa-circle-question'
    };

    switch (status) {
        case 'Chờ xử lý':
            config = { label: 'Chờ xác nhận', bg: 'status-cho-xac-nhan', icon: 'fa-clock' };
            break;
        case 'Đã xác nhận':
            config = { label: 'Đã xác nhận', bg: 'status-da-xac-nhan', icon: 'fa-check-circle' };
            break;
        case 'Đang chuẩn bị':
            config = { label: 'Đang chuẩn bị', bg: 'status-dang-chuan-bi', icon: 'fa-utensils' };
            break;
        case 'Đang giao':
            config = { label: 'Đang giao', bg: 'status-dang-giao', icon: 'fa-truck' };
            break;
        case 'Hoàn thành':
            config = { label: 'Hoàn thành', bg: 'status-hoan-thanh', icon: 'fa-check-double' };
            break;
        case 'Hủy':
            config = { label: 'Đã hủy', bg: 'status-huy', icon: 'fa-times' };
            break;
    }

    return `<span class="badge badge-status ${config.bg}"><i class="fas ${config.icon}"></i> ${config.label}</span>`;
}

function applyFilters() {
    currentFilters.search = document.getElementById('searchInput').value;
    currentFilters.status = document.getElementById('statusFilter').value;
    currentFilters.page = 1;

    // Update active class on cards
    document.querySelectorAll('.stat-card').forEach(card => {
        if (currentFilters.status !== 'all' && card.dataset.status === currentFilters.status) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    });

    loadOrders();
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = 'all';
    currentFilters = { search: '', status: 'all', page: 1 };
    
    document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active'));

    loadOrders();
}

function renderPagination(paging) {
    const container = document.getElementById('paginationContainer');
    if (!container) return;
    if (paging.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = `<nav><ul class="pagination pagination-sm mb-0">`;
    
    // Prev
    html += `
        <li class="page-item ${paging.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${paging.current_page - 1})"><i class="fas fa-chevron-left" style="font-size: 11px;"></i></a>
        </li>
    `;

    for (let i = 1; i <= paging.last_page; i++) {
        if (i === 1 || i === paging.last_page || Math.abs(i - paging.current_page) <= 1) {
            html += `
                <li class="page-item ${paging.current_page === i ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                </li>
            `;
        } else if (Math.abs(i - paging.current_page) === 2) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
    }

    // Next
    html += `
        <li class="page-item ${paging.current_page === paging.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${paging.current_page + 1})"><i class="fas fa-chevron-right" style="font-size: 11px;"></i></a>
        </li>
    `;

    html += `</ul></nav>`;
    container.innerHTML = html;
}

function changePage(p) {
    currentFilters.page = p;
    loadOrders();
}

function formatVND(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

function viewDetail(id) {
    // Implement detail view if needed
    window.location.href = `/admin/orders/${id}`; // Adjust as per routing
}

function exportOrders() {
    alert('Tính năng Export đang được phát triển');
}

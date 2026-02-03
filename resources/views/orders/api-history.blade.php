@extends('layouts.app')

@section('title', 'Lịch sử đơn hàng')

@section('content')
<style>
    .order-history-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 16px;
        background: white;
        min-height: calc(100vh - 200px);
    }

    .page-title {
        font-family: 'Arimo', sans-serif;
        font-size: 36px;
        line-height: 24px;
        color: #0a0a0a;
        text-align: center;
        margin-bottom: 40px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #4a5565;
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        margin-bottom: 24px;
        transition: all 0.2s;
    }

    .back-button:hover {
        color: #ff6900;
        transform: translateX(-5px);
    }

    .back-button i {
        font-size: 20px;
    }

    .loading-container {
        text-align: center;
        padding: 60px 20px;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e5e7eb;
        border-top-color: #ff6900;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-text {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        color: #6a7282;
    }

    .error-container {
        text-align: center;
        padding: 60px 20px;
    }

    .error-icon {
        font-size: 64px;
        color: #fb2c36;
        margin-bottom: 16px;
    }

    .error-text {
        font-family: 'Arimo', sans-serif;
        font-size: 18px;
        color: #6a7282;
        margin-bottom: 24px;
    }

    .retry-button {
        background: #ff6900;
        border: none;
        padding: 12px 32px;
        border-radius: 10px;
        color: white;
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .retry-button:hover {
        background: #f54900;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 105, 0, 0.3);
    }

    .order-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 16px;
        overflow: hidden;
    }

    .order-header {
        background: #8b5cf6;
        padding: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-info {
        display: flex;
        gap: 24px;
    }

    .order-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .order-field-label {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: white;
        opacity: 0.9;
    }

    .order-field-value {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: white;
        font-weight: 500;
    }

    .order-status-badge {
        padding: 4px 16px;
        border-radius: 26843500px;
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .order-status-badge.completed {
        background: #00b8db;
    }

    .order-status-badge.cancelled {
        background: #fb2c36;
    }

    .order-status-badge.pending {
        background: #fdc700;
        color: #101828;
    }

    .order-status-badge.processing {
        background: #ff6900;
    }

    .order-body {
        padding: 16px 24px;
    }

    .order-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 16px;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .item-info {
        display: flex;
        flex-direction: column;
    }

    .item-name {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #101828;
        font-weight: 500;
    }

    .item-quantity-label {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #6a7282;
    }

    .item-quantity {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #364153;
    }

    .item-price {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #ff6900;
        font-weight: 500;
    }

    .order-divider {
        border-top: 0.8px solid #e5e7eb;
        margin: 16px 0;
    }

    .order-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .order-total-label {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #101828;
    }

    .order-total-amount {
        font-family: 'Arimo', sans-serif;
        font-size: 20px;
        line-height: 28px;
        color: #ff6900;
        font-weight: 500;
    }

    .order-address {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 16px;
    }

    .order-address-label {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #6a7282;
    }

    .order-address-value {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #364153;
    }

    .order-action-divider {
        border-top: 0.8px solid #e5e7eb;
        margin: 16px 0;
    }

    .view-detail-btn {
        display: block;
        width: 100%;
        background: #ff6900;
        border-radius: 10px;
        padding: 8px 16px;
        text-align: center;
        color: white;
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .view-detail-btn:hover {
        background: #f54900;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 105, 0, 0.3);
    }

    .no-orders {
        text-align: center;
        padding: 60px 20px;
    }

    .no-orders i {
        font-size: 64px;
        color: #d1d5dc;
        margin-bottom: 16px;
    }

    .no-orders-text {
        font-family: 'Arimo', sans-serif;
        font-size: 18px;
        color: #6a7282;
        margin-bottom: 24px;
    }

    .btn-primary-custom {
        background: #ff6900;
        border: none;
        padding: 12px 32px;
        border-radius: 10px;
        color: white;
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }

    .btn-primary-custom:hover {
        background: #f54900;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 105, 0, 0.3);
    }

    .pagination {
        margin-top: 32px;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .pagination-btn {
        padding: 8px 16px;
        background: white;
        border: 1px solid #d1d5dc;
        border-radius: 6px;
        color: #364153;
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .pagination-btn:hover:not(:disabled) {
        background: #f8f9fa;
        border-color: #ff6900;
        color: #ff6900;
    }

    .pagination-btn.active {
        background: #ff6900;
        color: white;
        border-color: #ff6900;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<div class="order-history-container">
    <a href="{{ route('foods.index') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Quay lại
    </a>

    <h1 class="page-title">Lịch sử đơn hàng</h1>

    <!-- Loading State -->
    <div id="loadingContainer" class="loading-container" style="display: none;">
        <div class="loading-spinner"></div>
        <div class="loading-text">Đang tải danh sách đơn hàng...</div>
    </div>

    <!-- Error State -->
    <div id="errorContainer" class="error-container" style="display: none;">
        <i class="fas fa-exclamation-triangle error-icon"></i>
        <div class="error-text" id="errorMessage"></div>
        <button class="retry-button" onclick="loadOrders()">
            <i class="fas fa-redo me-2"></i>Thử lại
        </button>
    </div>

    <!-- Orders Container -->
    <div id="ordersContainer"></div>

    <!-- Pagination -->
    <div id="paginationContainer" class="pagination"></div>
</div>

<script>
    let currentPage = 1;
    let totalPages = 1;

    async function loadOrders(page = 1) {
        const loadingContainer = document.getElementById('loadingContainer');
        const errorContainer = document.getElementById('errorContainer');
        const ordersContainer = document.getElementById('ordersContainer');
        const paginationContainer = document.getElementById('paginationContainer');

        // Show loading
        loadingContainer.style.display = 'block';
        errorContainer.style.display = 'none';
        ordersContainer.innerHTML = '';
        paginationContainer.innerHTML = '';

        try {
            const response = await fetch(`/api/v1/orders?per_page=10&page=${page}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (response.ok && result.success) {
                loadingContainer.style.display = 'none';

                if (result.data.length === 0) {
                    ordersContainer.innerHTML = `
                        <div class="no-orders">
                            <i class="fas fa-shopping-bag"></i>
                            <div class="no-orders-text">Bạn chưa có đơn hàng nào</div>
                            <a href="{{ route('foods.index') }}" class="btn-primary-custom">
                                Đặt món ngay
                            </a>
                        </div>
                    `;
                } else {
                    result.data.forEach(order => {
                        ordersContainer.innerHTML += renderOrderCard(order);
                    });

                    // Render pagination
                    currentPage = result.pagination.current_page;
                    totalPages = result.pagination.last_page;
                    renderPagination(result.pagination);
                }
            } else {
                throw new Error(result.message || 'Không thể tải danh sách đơn hàng');
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            loadingContainer.style.display = 'none';
            errorContainer.style.display = 'block';
            document.getElementById('errorMessage').textContent = error.message || 'Đã xảy ra lỗi khi tải danh sách đơn hàng';
        }
    }

    function renderOrderCard(order) {
        let statusClass = 'pending';
        let statusIcon = '';
        
        if (order.status === 'Hoàn thành') {
            statusClass = 'completed';
            statusIcon = '✓ ';
        } else if (order.status === 'Đã hủy') {
            statusClass = 'cancelled';
            statusIcon = '❌ ';
        } else if (order.status === 'Đang xử lý') {
            statusClass = 'processing';
        } else if (order.status === 'Chờ xác nhận') {
            statusClass = 'pending';
        }

        let itemsHtml = order.items.map(item => `
            <div class="order-item">
                <div class="item-info">
                    <div class="item-name">${item.food_name}</div>
                    <div class="item-quantity-label">Số lượng</div>
                    <div class="item-quantity">${item.quantity}</div>
                </div>
                <div class="item-price">${formatCurrency(item.price)} đ</div>
            </div>
        `).join('');

        return `
            <div class="order-card">
                <div class="order-header">
                    <div class="order-info">
                        <div class="order-field">
                            <span class="order-field-label">Mã đơn hàng</span>
                            <span class="order-field-value">${order.order_code}</span>
                        </div>
                        <div class="order-field">
                            <span class="order-field-label">Thời gian</span>
                            <span class="order-field-value">${formatDate(order.created_at)}</span>
                        </div>
                    </div>
                    <div>
                        <span class="order-status-badge ${statusClass}">
                            ${statusIcon}${order.status}
                        </span>
                    </div>
                </div>

                <div class="order-body">
                    <div class="order-items">
                        ${itemsHtml}
                    </div>

                    <div class="order-divider"></div>

                    <div class="order-total">
                        <span class="order-total-label">Tổng cộng</span>
                        <span class="order-total-amount">${formatCurrency(order.total_amount)} đ</span>
                    </div>

                    <div class="order-address">
                        <span class="order-address-label">Địa chỉ giao hàng</span>
                        <span class="order-address-value">${order.delivery_address}</span>
                    </div>

                    <div class="order-action-divider"></div>

                    <a href="/orders/${order.id}" class="view-detail-btn">
                        Xem chi tiết đơn hàng
                    </a>
                </div>
            </div>
        `;
    }

    function renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        
        if (pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';

        // Previous button
        html += `
            <button class="pagination-btn" 
                    onclick="loadOrders(${pagination.current_page - 1})" 
                    ${pagination.current_page === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                html += `
                    <button class="pagination-btn ${i === pagination.current_page ? 'active' : ''}" 
                            onclick="loadOrders(${i})">
                        ${i}
                    </button>
                `;
            } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                html += '<span style="padding: 8px;">...</span>';
            }
        }

        // Next button
        html += `
            <button class="pagination-btn" 
                    onclick="loadOrders(${pagination.current_page + 1})" 
                    ${pagination.current_page === pagination.last_page ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        container.innerHTML = html;
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Load orders when page loads
    document.addEventListener('DOMContentLoaded', () => loadOrders());
</script>
@endsection

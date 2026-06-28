@extends('client.layouts.master')

@section('title', 'Chi tiết đơn hàng')

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
            height: 125px !important;
        }
        .hero_area .bg-box {
            height: 125px !important;
        }
    }

    .order-detail-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 32px 24px;
        background: #f8fafc;
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.02);
    }

    .page-title {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 32px;
        font-weight: 800;
        color: #1e293b;
        text-align: center;
        margin-bottom: 35px;
        letter-spacing: -0.5px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        color: #1e293b !important;
        font-family: 'Inter', sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        margin-bottom: 24px;
        padding: 8px 24px;
        background: #ffbe33 !important;
        border-radius: 25px;
        box-shadow: 0 4px 15px rgba(255, 190, 51, 0.2);
        border: none;
        transition: all 0.2s ease;
    }

    .back-button:hover {
        color: #1e293b !important;
        background: #e69d00 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 190, 51, 0.35);
    }

    .back-button i {
        font-size: 14px !important;
    }

    .loading-container {
        text-align: center;
        padding: 60px 20px;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e5e7eb;
        border-top-color: #ffbe33;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-text {
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        color: #64748b;
    }

    .error-container {
        text-align: center;
        padding: 60px 20px;
    }

    .error-icon {
        font-size: 64px;
        color: #ef4444;
        margin-bottom: 16px;
    }

    .error-text {
        font-family: 'Inter', sans-serif;
        font-size: 18px;
        color: #64748b;
        margin-bottom: 24px;
    }

    .retry-button {
        background: #ffbe33;
        border: none;
        padding: 12px 32px;
        border-radius: 25px;
        color: #1e293b;
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(255, 190, 51, 0.2);
    }

    .retry-button:hover {
        background: #e69d00;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(255, 190, 51, 0.35);
    }

    .detail-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02), 0 1px 3px rgba(0, 0, 0, 0.01);
        margin-bottom: 28px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .detail-section:hover {
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.04);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 24px;
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 16.5px;
        font-weight: 700;
        color: #1e293b;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }

    .header-icon-wrapper {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    
    .header-icon-wrapper.info-icon {
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
    }

    .header-icon-wrapper.items-icon {
        background: rgba(16, 185, 129, 0.08);
        color: #10b981;
    }

    .header-icon-wrapper.summary-icon {
        background: rgba(245, 158, 11, 0.08);
        color: #f59e0b;
    }

    .section-header i {
        font-size: 16px;
    }

    .section-body {
        padding: 24px 30px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px 32px;
    }

    .info-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        border-bottom: 1px dashed rgba(0, 0, 0, 0.04);
        padding-bottom: 12px;
    }

    .info-field:last-child, .info-field:nth-last-child(2) {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-label {
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 24px;
        color: #1e293b;
        font-weight: 500;
    }

    .food-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 16px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .food-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .food-image {
        width: 75px;
        height: 75px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .food-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .food-name {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 16px;
        color: #1e293b;
        font-weight: 600;
    }

    .food-quantity {
        font-family: 'Inter', sans-serif;
        font-size: 13.5px;
        color: #64748b;
        margin-top: 2px;
    }

    .food-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
    }

    .food-price {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 16px;
        color: #ef4444;
        font-weight: 700;
    }

    .review-btn {
        background: linear-gradient(135deg, #ffbe33, #ff9800);
        border: none;
        border-radius: 20px;
        padding: 8px 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #1e293b;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none !important;
        box-shadow: 0 4px 12px rgba(255, 152, 0, 0.15);
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .review-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(255, 152, 0, 0.3);
        color: #1e293b;
    }

    .review-btn i {
        font-size: 13px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }

    .summary-row:last-child {
        margin-bottom: 0;
    }

    .summary-label {
        font-family: 'Inter', sans-serif;
        font-size: 14.5px;
        color: #64748b;
        font-weight: 500;
    }

    .summary-value {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        color: #1e293b;
        font-weight: 600;
    }

    .summary-divider {
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        margin: 18px 0;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
    }

    .summary-total-label {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 16px;
        color: #1e293b;
        font-weight: 700;
    }

    .summary-total-value {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 22px;
        color: #ef4444;
        font-weight: 800;
    }

    /* Premium Shopee/TikTok Shop Stepper Timeline */
    .timeline-container {
        background: #ffffff;
        border-radius: 16px;
        padding: 35px 25px 25px 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0,0,0,0.04);
        margin-bottom: 28px;
    }
    .timeline-container.cancelled {
        border-left: 5px solid #ef4444;
        background: rgba(239, 68, 68, 0.02);
        padding: 25px;
    }
    .timeline-cancelled-header {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ef4444;
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .timeline-cancelled-header i {
        font-size: 22px;
    }
    .timeline-cancelled-body {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }
    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        position: relative;
        width: 100%;
        margin: 0 auto;
    }
    .stepper-line {
        position: absolute;
        top: 22px; /* Center with 44px icons */
        left: 8%;
        right: 8%;
        height: 4px;
        background: #f1f5f9;
        z-index: 1;
        border-radius: 2px;
    }
    .stepper-line-progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background: linear-gradient(90deg, #ffbe33, #ff9800);
        z-index: 2;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 2px;
        box-shadow: 0 0 8px rgba(255, 152, 0, 0.4);
    }
    .step-item {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }
    .step-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 15px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .step-item.active .step-icon {
        background: linear-gradient(135deg, #ffbe33, #ff9800);
        border-color: #ffbe33;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
    }
    .step-item.current .step-icon {
        transform: scale(1.15);
        box-shadow: 0 0 20px rgba(255, 152, 0, 0.4);
    }
    .step-label {
        margin-top: 12px;
        font-family: 'Inter', sans-serif;
        font-size: 13.5px;
        font-weight: 500;
        color: #64748b;
        text-align: center;
        transition: all 0.3s ease;
    }
    .step-item.active .step-label {
        color: #1e293b;
        font-weight: 600;
    }
    .step-item.current .step-label {
        color: #ff9800;
    }

    /* 2-Column Responsive Layout */
    .order-grid {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    @media (min-width: 992px) {
        .order-grid {
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 28px;
            align-items: start;
        }
    }
    .order-main-col, .order-side-col {
        display: flex;
        flex-direction: column;
        gap: 0px;
    }

    /* Payment Status and Carrier styling */
    .badge-payment {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .badge-payment.paid {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-payment.pending-confirm {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-payment.unpaid {
        background: #f3f4f6;
        color: #6b7280;
    }
    .badge-payment.failed {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Detailed Timeline Log styling */
    .timeline-log-card {
        padding: 24px 30px;
    }
    .timeline-log-list {
        margin-top: 15px;
    }
    .timeline-log-item {
        display: flex;
        gap: 15px;
        position: relative;
        padding-bottom: 24px;
    }
    .timeline-log-item::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 20px;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-log-item:last-child {
        padding-bottom: 0;
    }
    .timeline-log-item:last-child::before {
        display: none;
    }
    .timeline-log-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #e2e8f0;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px #cbd5e1;
        margin-top: 4px;
        z-index: 2;
        transition: all 0.3s ease;
    }
    .timeline-log-item.active .timeline-log-dot {
        background: #10b981;
        box-shadow: 0 0 0 1px #10b981;
    }
    .timeline-log-item.cancelled .timeline-log-dot {
        background: #ef4444;
        box-shadow: 0 0 0 1px #ef4444;
    }
    .timeline-log-content {
        flex: 1;
    }
    .timeline-log-title {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s ease;
    }
    .timeline-log-item.active .timeline-log-title {
        color: #0f172a;
    }
    .timeline-log-item.cancelled .timeline-log-title {
        color: #ef4444;
    }
    .timeline-log-time {
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        color: #94a3b8;
        margin-top: 3px;
    }

    /* Adjust info fields to be single-column inside sidebar */
    @media (min-width: 992px) {
        .order-side-col .info-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .order-detail-container {
            padding: 16px 12px;
            border-radius: 16px;
        }
        .page-title {
            font-size: 22px;
            margin-bottom: 20px;
        }
        
        /* Stepper responsive adjustment */
        .timeline-container {
            padding: 20px 10px 15px 10px;
            margin-bottom: 20px;
        }
        .stepper-line {
            top: 18px;
            left: 6%;
            right: 6%;
        }
        .step-icon {
            width: 36px;
            height: 36px;
            font-size: 12px;
        }
        .step-label {
            font-size: 11px;
            margin-top: 8px;
            line-height: 1.3;
        }
        
        /* Info Grid - Single column on mobile */
        .info-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .section-body {
            padding: 16px 20px;
        }
        
        /* Food Item layout adjustment */
        .food-item {
            gap: 12px;
            padding: 12px 0;
        }
        .food-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
        }
        .food-name {
            font-size: 14.5px;
        }
        .food-quantity {
            font-size: 12.5px;
        }
        .food-price {
            font-size: 14.5px;
        }
        .review-btn {
            padding: 6px 14px;
            font-size: 12px;
        }
        
        /* Breadcrumb header */
        .order-detail-container > div:first-child {
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 8px !important;
            padding-bottom: 12px !important;
            margin-bottom: 15px !important;
        }
        .breadcrumb {
            font-size: 11.5px !important;
            margin: 0 !important;
        }
        .breadcrumb-item {
            white-space: nowrap !important;
        }
        #detailBackButton {
            width: auto !important;
            padding: 6px 12px !important;
            font-size: 12px !important;
            flex-shrink: 0 !important;
            margin-left: auto !important;
            gap: 5px !important;
            justify-content: center !important;
        }
    }
</style>

<div class="order-detail-container">
    <!-- Breadcrumb & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 15px; border-bottom: 1px solid rgba(0,0,0,0.08); padding-bottom: 20px; margin-bottom: 25px;">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="{{ route('foods.index') }}" style="color: #6c757d; font-weight: 500; text-decoration: none;">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.history') }}" style="color: #6c757d; font-weight: 500; text-decoration: none;">Lịch sử đơn hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #212529; font-weight: 600;">Chi tiết đơn hàng</li>
            </ol>
        </nav>
        <a href="{{ route('orders.history') }}" id="detailBackButton" class="btn btn-warning px-4 py-2 font-weight-bold shadow-sm d-flex align-items-center" 
           style="border-radius: 25px; background-color: #ffbe33; border: none; color: #1e293b; transition: all 0.2s ease; font-size: 13.5px; gap: 8px; font-weight: 700; text-decoration: none;">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <h1 class="page-title">Chi tiết đơn hàng</h1>

    <!-- Loading State -->
    <div id="loadingContainer" class="loading-container" style="display: none;">
        <div class="loading-spinner"></div>
        <div class="loading-text">Đang tải chi tiết đơn hàng...</div>
    </div>

    <!-- Error State -->
    <div id="errorContainer" class="error-container" style="display: none;">
        <i class="fas fa-exclamation-triangle error-icon"></i>
        <div class="error-text" id="errorMessage"></div>
        <button class="retry-button" onclick="loadOrderDetail()">
            <i class="fas fa-redo me-2"></i>Thử lại
        </button>
    </div>

    <!-- Order Detail Container -->
    <div id="orderDetailContainer"></div>
</div>

<script>
    const orderId = {{ $orderId }};

    async function loadOrderDetail() {
        const loadingContainer = document.getElementById('loadingContainer');
        const errorContainer = document.getElementById('errorContainer');
        const orderDetailContainer = document.getElementById('orderDetailContainer');

        // Show loading
        loadingContainer.style.display = 'block';
        errorContainer.style.display = 'none';
        orderDetailContainer.innerHTML = '';

        try {
            const response = await fetch(`/api/v1/orders/${orderId}`, {
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
                renderOrderDetail(result.data);
            } else {
                throw new Error(result.message || 'Không thể tải chi tiết đơn hàng');
            }
        } catch (error) {
            console.error('Error loading order detail:', error);
            loadingContainer.style.display = 'none';
            errorContainer.style.display = 'block';
            document.getElementById('errorMessage').textContent = error.message || 'Đã xảy ra lỗi khi tải chi tiết đơn hàng';
        }
    }

    function renderOrderDetail(order) {
        const container = document.getElementById('orderDetailContainer');
        
        // 1. Generate Stepper Tracking Flow Timeline (Shopee / TikTok Shop style)
        const statuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đang chuẩn bị', 'Đang giao', 'Hoàn thành'];
        const statusLabels = {
            'Chờ xác nhận': 'Đặt đơn',
            'Đã xác nhận': 'Đã xác nhận',
            'Đang chuẩn bị': 'Chuẩn bị',
            'Đang giao': 'Đang giao',
            'Hoàn thành': 'Hoàn thành'
        };
        const statusIcons = {
            'Chờ xác nhận': 'fa-clipboard-list',
            'Đã xác nhận': 'fa-receipt',
            'Đang chuẩn bị': 'fa-hamburger',
            'Đang giao': 'fa-motorcycle',
            'Hoàn thành': 'fa-box-open'
        };

        let timelineHtml = '';
        if (order.status === 'Đã hủy') {
            let cancelDesc = 'Đơn hàng đã bị hủy.';
            if (order.cancelled_by === 'seller') {
                cancelDesc = `Đơn hàng của bạn đã bị hủy bởi nhà hàng. ${order.cancel_reason ? `Lý do: <i>${order.cancel_reason}</i>` : ''}`;
            } else if (order.cancelled_by === 'admin') {
                cancelDesc = `Đơn hàng của bạn đã bị hủy bởi quản trị viên. ${order.cancel_reason ? `Lý do: <i>${order.cancel_reason}</i>` : ''}`;
            } else if (order.cancelled_by === 'customer') {
                cancelDesc = `Bạn đã chủ động hủy đơn hàng này. ${order.cancel_reason ? `Lý do: <i>${order.cancel_reason}</i>` : ''}`;
            } else if (order.cancelled_by === 'system') {
                cancelDesc = `Đơn hàng đã bị hệ thống tự động hủy do quá thời gian thanh toán.`;
            } else {
                cancelDesc = `Đơn hàng đã bị hủy. ${order.cancel_reason ? `Lý do: <i>${order.cancel_reason}</i>` : ''}`;
            }

            timelineHtml = `
                <div class="timeline-container cancelled">
                    <div class="timeline-cancelled-header">
                        <i class="fas fa-times-circle"></i>
                        <span>ĐƠN HÀNG ĐÃ BỊ HỦY</span>
                    </div>
                    <div class="timeline-cancelled-body">
                        ${cancelDesc}
                    </div>
                </div>
            `;
        } else {
            const currentIndex = statuses.indexOf(order.status);
            const progressPercent = currentIndex >= 0 ? (currentIndex / (statuses.length - 1)) * 100 : 0;

            const stepsHtml = statuses.map((status, index) => {
                const isActive = index <= currentIndex;
                const isCurrent = index === currentIndex;
                const activeClass = isActive ? 'active' : '';
                const currentClass = isCurrent ? 'current' : '';

                return `
                    <div class="step-item ${activeClass} ${currentClass}">
                        <div class="step-icon">
                            <i class="fas ${statusIcons[status]}"></i>
                        </div>
                        <div class="step-label">${statusLabels[status]}</div>
                    </div>
                `;
            }).join('');

            timelineHtml = `
                <div class="timeline-container">
                    <div class="stepper-wrapper">
                        <div class="stepper-line">
                            <div class="stepper-line-progress" style="width: ${progressPercent}%;"></div>
                        </div>
                        ${stepsHtml}
                    </div>
                </div>
            `;
        }
        
        // Render food items
        const itemsHtml = order.items.map(item => `
            <div class="food-item">
                ${item.food_image 
                    ? `<img src="${item.food_image}" alt="${item.food_name}" class="food-image">`
                    : `<div class="food-image" style="background: linear-gradient(45deg, #ffbe33, #ff9800); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-utensils" style="color: white; font-size: 24px;"></i>
                       </div>`
                }
                
                <div class="food-info">
                    <div class="food-name">${item.food_name}</div>
                    <div class="food-quantity">${formatCurrency(item.price)} đ × ${item.quantity}</div>
                </div>
 
                <div class="food-actions">
                    <div class="food-price">${formatCurrency(item.subtotal)} đ</div>
                    ${order.status === 'Hoàn thành' ? (
                        item.has_reviewed ? `
                            <span class="text-success" style="font-size: 13.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; border: 1px solid #28a745; padding: 4px 10px; border-radius: 8px; background: rgba(40,167,69,0.05);">
                                <i class="fas fa-check-circle"></i> Đã đánh giá
                            </span>
                        ` : `
                            <a href="/reviews/create/${order.id}/${item.food_id}" class="review-btn">
                                ⭐ Đánh giá
                            </a>
                        `
                    ) : ''}
                </div>
            </div>
        `).join('');
 
        // Update page header and breadcrumbs with order code
        const breadcrumbActive = document.querySelector('.breadcrumb-item.active');
        if (breadcrumbActive) breadcrumbActive.textContent = 'Đơn hàng #' + order.order_code;

        const pageTitle = document.querySelector('.page-title');
        if (pageTitle) pageTitle.textContent = 'Chi tiết đơn hàng #' + order.order_code;

        // Payment status badge
        let paymentStatusBadge = '';
        if (order.payment_status === 'Đã thanh toán') {
            paymentStatusBadge = '<span class="badge-payment paid"><i class="fas fa-check-circle"></i> Đã thanh toán</span>';
        } else if (order.payment_status === 'Chờ xác nhận') {
            paymentStatusBadge = '<span class="badge-payment pending-confirm"><i class="fas fa-clock"></i> Chờ xác nhận CK</span>';
        } else if (order.payment_status === 'Thất bại') {
            paymentStatusBadge = '<span class="badge-payment failed"><i class="fas fa-times-circle"></i> Thất bại</span>';
        } else {
            paymentStatusBadge = '<span class="badge-payment unpaid"><i class="fas fa-credit-card"></i> Chưa thanh toán</span>';
        }

        // Detailed Status Timeline Logs
        let timelineLogsHtml = '';
        const t = order.timeline;
        const logSteps = [];
        
        logSteps.push({ title: 'Đặt đơn hàng thành công', time: t.created, active: true });
        
        if (order.status === 'Đã hủy') {
            if (t.confirmed) {
                logSteps.push({ title: 'Đơn hàng đã được xác nhận', time: t.confirmed, active: true });
            }
            if (t.delivering) {
                logSteps.push({ title: 'Đơn hàng đang giao', time: t.delivering, active: true });
            }
            
            let cancelMsg = 'Đơn hàng đã bị hủy';
            if (order.cancelled_by === 'seller') {
                cancelMsg = 'Nhà hàng đã hủy đơn';
            } else if (order.cancelled_by === 'customer') {
                cancelMsg = 'Bạn đã chủ động hủy đơn';
            } else if (order.cancelled_by === 'admin') {
                cancelMsg = 'Quản trị viên đã hủy đơn';
            } else if (order.cancelled_by === 'system') {
                cancelMsg = 'Hệ thống tự động hủy đơn';
            }
            if (order.cancel_reason) {
                cancelMsg += ` (Lý do: ${order.cancel_reason})`;
            }
            logSteps.push({ title: cancelMsg, time: t.cancelled, active: true, cancelled: true });
        } else {
            logSteps.push({ title: 'Đơn hàng đã được xác nhận', time: t.confirmed, active: !!t.confirmed });
            logSteps.push({ title: 'Đơn hàng đang giao', time: t.delivering, active: !!t.delivering });
            logSteps.push({ title: 'Giao hàng thành công', time: t.completed, active: !!t.completed });
        }

        logSteps.reverse(); // Newest first

        const logsItemsHtml = logSteps.map(step => `
            <div class="timeline-log-item ${step.active ? 'active' : ''} ${step.cancelled ? 'cancelled' : ''}">
                <div class="timeline-log-dot"></div>
                <div class="timeline-log-content">
                    <div class="timeline-log-title">${step.title}</div>
                    <div class="timeline-log-time">${step.time ? formatDate(step.time) : 'Đang chờ...'}</div>
                </div>
            </div>
        `).join('');

        timelineLogsHtml = `
            <div class="detail-section timeline-log-card">
                <div class="section-header" style="border: none; padding: 0 0 15px 0;">
                    <div class="header-icon-wrapper info-icon" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">
                        <i class="fas fa-history"></i>
                    </div>
                    <span>Lịch sử trạng thái</span>
                </div>
                <div class="timeline-log-list">
                    ${logsItemsHtml}
                </div>
            </div>
        `;

        container.innerHTML = `
            <div class="order-grid">
                <!-- Left Column (Main content) -->
                <div class="order-main-col">
                    ${timelineHtml}
                    
                    <!-- Food Items Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <div class="header-icon-wrapper items-icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <span>Chi tiết món ăn</span>
                        </div>
                        <div class="section-body">
                            ${itemsHtml}
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <div class="header-icon-wrapper summary-icon">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <span>Tổng kết</span>
                        </div>
                        <div class="section-body">
                            <div class="summary-row">
                                <span class="summary-label">Tạm tính:</span>
                                <span class="summary-value">${formatCurrency(order.summary.subtotal)} đ</span>
                            </div>

                            <div class="summary-row">
                                <span class="summary-label"><i class="fas fa-truck" style="margin-right:4px; color:#ff6900; font-size:11px;"></i>Phí vận chuyển:</span>
                                <span class="summary-value" style="${order.summary.shipping_fee > 0 ? '' : 'color:#10b981; font-weight:600;'}">
                                    ${order.summary.shipping_fee > 0 ? '+ ' + formatCurrency(order.summary.shipping_fee) + ' đ' : 'Miễn phí'}
                                </span>
                            </div>

                            ${order.summary.discount > 0 ? `
                                <div class="summary-row">
                                    <span class="summary-label">
                                        <i class="fas fa-tag" style="margin-right:4px; color:#e53e3e; font-size:11px;"></i>Giảm giá voucher:
                                        ${order.voucher ? `<span style="display:inline-block; margin-left:6px; background:#fff3cd; color:#92400e; font-size:10.5px; font-weight:800; padding:1px 7px; border-radius:5px; border:1px dashed #f59e0b; letter-spacing:0.5px;">${order.voucher.code}</span>` : ''}
                                    </span>
                                    <span class="summary-value" style="color: #10b981;">- ${formatCurrency(order.summary.discount)} đ</span>
                                </div>
                            ` : ''}

                            <div class="summary-divider"></div>

                            <div class="summary-total">
                                <span class="summary-total-label">Tổng cộng:</span>
                                <span class="summary-total-value">${formatCurrency(order.summary.total)} đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sidebar details) -->
                <div class="order-side-col">
                    <!-- Order Information Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <div class="header-icon-wrapper info-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <span>Thông tin đơn hàng</span>
                        </div>
                        <div class="section-body">
                            <div class="info-grid">
                                <div class="info-field">
                                    <div class="info-label">Mã đơn hàng:</div>
                                    <div class="info-value" style="font-weight: 700; color: #ff9800;">#${order.order_code}</div>
                                </div>
                                <div class="info-field">
                                    <div class="info-label">Ngày đặt:</div>
                                    <div class="info-value">${formatDate(order.created_at)}</div>
                                </div>
                                <div class="info-field">
                                    <div class="info-label">Khách hàng:</div>
                                    <div class="info-value">${order.customer_name}</div>
                                </div>
                                <div class="info-field">
                                    <div class="info-label">Số điện thoại:</div>
                                    <div class="info-value">${order.phone}</div>
                                </div>
                                <div class="info-field">
                                    <div class="info-label">Địa chỉ giao hàng:</div>
                                    <div class="info-value">${order.delivery_address}</div>
                                </div>
                                <div class="info-field">
                                    <div class="info-label">Phương thức thanh toán:</div>
                                    <div class="info-value d-flex align-items-center flex-wrap" style="gap: 8px;">
                                        <span>${order.payment_method}</span>
                                        ${paymentStatusBadge}
                                    </div>
                                </div>
                                <div class="info-field">
                                    <div class="info-label">Đối tác vận chuyển:</div>
                                    <div class="info-value" style="color: #2563eb; font-weight: 600;">
                                        <i class="fas fa-shipping-fast" style="color: #2563eb; margin-right: 4px;"></i> 
                                        ${order.carrier_name || 'Chưa phân phối'}
                                    </div>
                                </div>
                                <div class="info-field">
                                    <div class="info-label">Ghi chú:</div>
                                    <div class="info-value" style="font-style: italic; color: #64748b;">${order.note || 'Không có ghi chú'}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${timelineLogsHtml}
                </div>
            </div>
        `;
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

    // Load order detail when page loads
    document.addEventListener('DOMContentLoaded', () => loadOrderDetail());
</script>
@endsection

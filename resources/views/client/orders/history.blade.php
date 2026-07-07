@extends('client.layouts.master')

@section('title', 'Lịch sử đơn hàng')

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

    /* Premium Page Background */
    body {
        background: linear-gradient(135deg, #f0f2f5 0%, #e8ecf1 100%) !important;
        min-height: 100vh;
    }

    /* Main Sheet */
    .order-history-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 40px 32px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        min-height: calc(100vh - 240px);
    }

    /* Floating Capsule Back Button */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        color: #1e293b !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        margin-bottom: 30px;
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

    /* Title Centered Perfectly with CSS Underline Indicator */
    .page-title-wrapper {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 32px;
        font-weight: 800;
        color: #222831;
        letter-spacing: -0.5px;
        position: relative;
        display: inline-block;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        right: 0;
        margin: 0 auto; /* Perfect horizontal alignment */
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #ffbe33, #ff6900);
        border-radius: 2px;
    }

    /* Filters & Navigation Tab Panel */
    .filters-panel {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.03);
        margin-bottom: 30px;
    }

    .search-date-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 20px;
        margin-bottom: 20px;
        align-items: end; /* Align search input perfectly with the date picker inputs */
    }

    .search-input-wrapper {
        position: relative;
        width: 100%;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 18px;
        top: 0;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #a0aec0;
        font-size: 16px;
        pointer-events: none;
        margin: 0;
    }

    .search-input-wrapper input {
        width: 100%;
        padding: 12px 16px 12px 48px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14.5px;
        font-weight: 500;
        transition: all 0.25s ease;
        outline: none;
    }

    .search-input-wrapper input:focus {
        border-color: #ff6900;
        box-shadow: 0 0 0 3px rgba(255, 105, 0, 0.1);
    }

    .date-picker-wrapper {
        display: flex;
        gap: 12px;
    }

    .date-input-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .date-input-group label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #718096;
        letter-spacing: 0.5px;
    }

    .date-input-group input {
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        outline: none;
        font-size: 13.5px;
        font-weight: 600;
        font-family: inherit;
        transition: all 0.2s ease;
    }

    .date-input-group input:focus {
        border-color: #ff6900;
    }

    /* Horizontal Category Status Tabs */
    .status-tabs-container {
        display: flex;
        justify-content: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: none; /* Hide scrollbar Firefox */
    }

    .status-tabs-container::-webkit-scrollbar {
        display: none; /* Hide scrollbar Webkit */
    }

    .status-tab {
        padding: 10px 20px;
        background: #f7fafc;
        border: 1px solid #edf2f7;
        border-radius: 30px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.25s ease;
        white-space: nowrap;
    }

    .status-tab:hover {
        background: #edf2f7;
        color: #ff6900;
    }

    .status-tab.active {
        background: linear-gradient(135deg, #ffbe33 0%, #ff6900 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(255, 105, 0, 0.15);
    }

    /* Premium Order Card */
    .order-card {
        background: white;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04), 0 1px 4px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .order-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.09);
    }

    /* Dark gradient Card Header (matched to TAFOOD dark brand) */
    .order-header {
        background: linear-gradient(135deg, #222831 0%, #2d3748 100%);
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: none;
    }

    .order-info {
        display: flex;
        gap: 32px;
    }

    .order-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .order-field-label {
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.45);
        font-weight: 700;
    }

    .order-field-value {
        font-size: 14.5px;
        color: #ffffff;
        font-weight: 700;
    }

    /* Gold highlight for order code in dark header */
    .order-field:first-child .order-field-value {
        color: #ffbe33;
        letter-spacing: 0.5px;
        font-weight: 800;
    }

    /* Modern Translucent Badges - adjusted for dark header */
    .order-status-badge {
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .order-status-badge.completed {
        background: rgba(40, 167, 69, 0.2) !important;
        color: #4cde75 !important;
        border: 1.5px solid rgba(40, 167, 69, 0.35);
    }

    .order-status-badge.cancelled {
        background: rgba(220, 53, 69, 0.2) !important;
        color: #ff7b7b !important;
        border: 1.5px solid rgba(220, 53, 69, 0.35);
    }

    .order-status-badge.pending {
        background: rgba(255, 190, 51, 0.2) !important;
        color: #ffcc5c !important;
        border: 1.5px solid rgba(255, 190, 51, 0.35);
    }

    .order-status-badge.processing {
        background: rgba(255, 105, 0, 0.2) !important;
        color: #ffb07a !important;
        border: 1.5px solid rgba(255, 105, 0, 0.35);
    }

    /* Card Body — subtle tinted background for visual depth */
    .order-body {
        padding: 24px 28px;
        background: #fafbfc;
    }

    .order-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Each item row with subtle hover */
    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #f0f2f5;
        transition: all 0.2s ease;
    }

    .order-item:hover {
        background: #f8f9fc;
        border-color: rgba(255, 190, 51, 0.25);
        transform: translateX(4px);
    }

    .item-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    /* Food Image Thumbnail */
    .item-thumbnail {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        border: 2px solid rgba(255,190,51,0.15);
    }

    .item-name {
        font-size: 15px;
        color: #1e293b;
        font-weight: 700;
    }

    .item-quantity-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
        font-size: 12.5px;
    }

    .item-quantity-badge {
        background: #222831;
        color: #ffbe33;
        padding: 1px 9px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        border: none;
    }

    .item-price {
        font-size: 15px;
        color: #ff6900;
        font-weight: 800;
    }

    /* Toggle items trigger button */
    .toggle-items-btn {
        background: none;
        border: none;
        color: #ff6900;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        padding: 8px 0;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        outline: none !important;
        transition: color 0.2s ease;
    }

    .toggle-items-btn:hover {
        color: #d45700;
    }

    /* Classic receipt dashed divider */
    .order-divider {
        border-top: 1.5px dashed #e2e8f0;
        margin: 22px 0;
    }

    .order-action-divider {
        border-top: 1.5px solid #edf2f7;
        margin: 22px 0;
    }

    /* Detailed price breakdown list */
    .order-total-breakdown {
        background: #f7fafc;
        padding: 16px 20px;
        border-radius: 14px;
        border: 1px solid #edf2f7;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .breakdown-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13.5px;
        color: #718096;
        font-weight: 500;
    }

    .breakdown-row.discount {
        color: #e53e3e;
    }

    .breakdown-row.total {
        margin-top: 4px;
        padding-top: 10px;
        border-top: 1px solid #e2e8f0;
        color: #2d3748;
        font-size: 16px;
        font-weight: 800;
    }

    .breakdown-row.total .breakdown-value {
        color: #ff6900;
        font-size: 18px;
    }

    /* Address card layout */
    .order-address {
        display: flex;
        flex-direction: column;
        gap: 6px;
        background: rgba(247, 250, 252, 0.5);
        padding: 14px 20px;
        border-radius: 14px;
        border: 1px solid #edf2f7;
    }

    .order-address-label {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #a0aec0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .order-address-value {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13.5px;
        color: #4a5568;
        line-height: 1.5;
    }

    /* Action Buttons layout - Left & Right aligned proportionally */
    .order-footer-wrapper {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 12px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: none;
        outline: none;
    }

    .action-btn.secondary-btn {
        background: white;
        border: 1.5px solid #ff6900;
        color: #ff6900 !important;
    }

    .action-btn.secondary-btn:hover {
        background: rgba(255, 105, 0, 0.05);
        transform: translateY(-2px);
    }

    .action-btn.primary-btn {
        background: linear-gradient(135deg, #ffbe33 0%, #ff6900 100%);
        color: white !important;
        box-shadow: 0 4px 15px rgba(255, 105, 0, 0.15);
    }

    .action-btn.primary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 105, 0, 0.3);
        background: linear-gradient(135deg, #ffc74d 0%, #ff7c1a 100%);
    }

    .action-btn.cancel-btn {
        background: rgba(220, 53, 69, 0.05);
        border: 1.5px solid rgba(220, 53, 69, 0.2);
        color: #dc3545 !important;
    }

    .action-btn.cancel-btn:hover {
        background: #dc3545;
        color: white !important;
        border-color: #dc3545;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.25);
        transform: translateY(-2px);
    }

    /* Circular Orange Pagination */
    .pagination {
        margin-top: 40px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .pagination-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 1px solid #edf2f7;
        border-radius: 50% !important;
        color: #4a5568;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.01);
    }

    .pagination-btn:hover:not(:disabled) {
        background: #ff6900;
        border-color: #ff6900;
        color: white;
        transform: scale(1.05);
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, #ffbe33 0%, #ff6900 100%);
        color: white;
        border-color: #ff6900;
        box-shadow: 0 4px 12px rgba(255, 105, 0, 0.3);
    }

    .pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Skeleton Loading card templates */
    .skeleton-card {
        background: white;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 28px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 4px 25px rgba(0,0,0,0.01);
    }

    @keyframes skeleton-glow {
        0% { background-position: -200px 0; }
        100% { background-position: 200px 0; }
    }

    .skeleton-line {
        background: linear-gradient(90deg, #f7fafc 25%, #edf2f7 50%, #f7fafc 75%);
        background-size: 200px 100%;
        animation: skeleton-glow 1.5s infinite linear;
        border-radius: 6px;
    }

    /* Custom Toast CSS */
    .custom-toast {
        animation: toast-in 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes toast-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Empty state visually matching homepage */
    .no-orders {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-state-illustration {
        font-size: 64px;
        color: #cbd5e0;
        margin-bottom: 20px;
    }

    .empty-state-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 20px;
        font-weight: 800;
        color: #2d3748;
        margin-bottom: 12px;
    }

    .no-orders-text {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 15px;
        color: #718096;
        margin-bottom: 28px;
        max-width: 480px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #ffbe33 0%, #ff6900 100%);
        border: none;
        padding: 12px 36px;
        border-radius: 12px;
        color: white !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none !important;
        cursor: pointer;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 105, 0, 0.15);
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 105, 0, 0.3);
    }

    /* Mobile Responsive optimizations */
    @media (max-width: 768px) {
        .order-history-container {
            margin: 15px 10px;
            padding: 24px 16px;
            border-radius: 16px;
        }

        .search-date-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .date-picker-wrapper {
            grid-template-columns: 1fr 1fr;
            display: grid;
        }

        .order-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            padding: 18px 20px;
        }

        .order-info {
            flex-direction: column;
            gap: 12px;
        }

        .order-body {
            padding: 20px;
        }

        .page-title {
            font-size: 26px;
            margin-bottom: 30px;
        }

        .order-footer-wrapper {
            flex-direction: column-reverse;
            align-items: stretch;
            gap: 8px;
        }

        .action-btn {
            width: 100%;
        }

        .status-tabs-container {
            justify-content: flex-start;
        }
    }

    /* Custom Premium Modal styling */
    .custom-modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        opacity: 0;
        transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .custom-modal-backdrop.show {
        opacity: 1;
    }

    .custom-modal-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 40px 32px;
        border-radius: 24px;
        width: 90%;
        max-width: 420px;
        text-align: center;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.7);
        transform: scale(0.92);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
    }

    .custom-modal-backdrop.show .custom-modal-content {
        transform: scale(1);
    }

    .modal-icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%);
        color: #e11d48;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 24px auto;
        animation: pulse-red 2.5s infinite;
        flex-shrink: 0;
    }

    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.35); }
        70% { box-shadow: 0 0 0 12px rgba(225, 29, 72, 0); }
        100% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
    }

    .modal-title {
        font-family: inherit;
        font-size: 21px;
        font-weight: 750;
        color: #0f172a;
        margin-bottom: 8px;
        text-align: center;
        letter-spacing: -0.5px;
    }

    .modal-message {
        font-family: inherit;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 24px;
        text-align: center;
    }

    /* Styled Select & Textarea */
    #cancelReasonSelect, #customCancelReason {
        background-color: #f8fafc !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 12px !important;
        font-size: 14px !important;
        padding: 12px 16px !important;
        outline: none !important;
        width: 100% !important;
        font-family: inherit !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        transition: all 0.25s ease !important;
        box-sizing: border-box !important;
    }

    #cancelReasonSelect:focus, #customCancelReason:focus {
        border-color: #f43f5e !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.12) !important;
    }

    /* Target nice-select generated element */
    .custom-modal-content .nice-select {
        width: 100% !important;
        float: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        background-color: #f8fafc !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 12px !important;
        height: auto !important;
        padding: 12px 16px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        box-sizing: border-box !important;
        line-height: normal !important;
        transition: all 0.25s ease !important;
    }

    .custom-modal-content .nice-select:after {
        right: 18px !important;
        margin-top: -3px !important;
        width: 6px !important;
        height: 6px !important;
    }
    
    .custom-modal-content .nice-select .current {
        font-weight: 500 !important;
        color: #1e293b !important;
        float: none !important;
        display: inline-block !important;
    }
    
    .custom-modal-content .nice-select:focus,
    .custom-modal-content .nice-select.open {
        border-color: #f43f5e !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.12) !important;
    }
    
    .custom-modal-content .nice-select .list {
        width: 100% !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        border: 1px solid #e2e8f0 !important;
        margin-top: 4px !important;
        z-index: 999 !important;
        padding: 6px 0 !important;
    }

    .custom-modal-content .nice-select .option {
        padding: 10px 16px !important;
        font-size: 14.5px !important;
        font-weight: 500 !important;
        color: #4a5568 !important;
        transition: all 0.2s ease !important;
        line-height: normal !important;
        text-align: left !important;
    }

    .custom-modal-content .nice-select .option.selected.focus {
        background-color: #ffe4e6 !important;
        color: #e11d48 !important;
    }

    .custom-modal-content .nice-select .option:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    .modal-actions {
        display: flex !important;
        flex-direction: row !important;
        gap: 12px !important;
        width: 100% !important;
        margin-top: 24px !important;
    }

    .modal-btn {
        flex: 1 1 0% !important;
        padding: 14px !important;
        border-radius: 12px !important;
        font-family: inherit !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: none !important;
        outline: none !important;
        white-space: nowrap !important;
        text-align: center;
        box-sizing: border-box !important;
    }

    .modal-btn.cancel-action-btn {
        background: #f1f5f9 !important;
        color: #475569 !important;
    }

    .modal-btn.cancel-action-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        transform: translateY(-2px);
    }
    
    .modal-btn.cancel-action-btn:active {
        transform: translateY(0);
    }

    .modal-btn.confirm-action-btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2) !important;
    }

    .modal-btn.confirm-action-btn:hover {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%) !important;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.35) !important;
        transform: translateY(-2px);
    }
    
    .modal-btn.confirm-action-btn:active {
        transform: translateY(0);
    }
</style>

<div class="order-history-container">
    <!-- Breadcrumb & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 15px; border-bottom: 1px solid rgba(0,0,0,0.08); padding-bottom: 20px; margin-bottom: 25px;">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="{{ route('foods.index') }}" style="color: #6c757d; font-weight: 500; text-decoration: none;">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #212529; font-weight: 600;">Lịch sử đơn hàng</li>
            </ol>
        </nav>
        <a href="{{ route('foods.index') }}" id="detailBackButton" class="btn btn-warning px-4 py-2 font-weight-bold shadow-sm d-flex align-items-center" 
           style="border-radius: 25px; background-color: #ffbe33; border: none; color: #1e293b; transition: all 0.2s ease; font-size: 13.5px; gap: 8px; font-weight: 700; text-decoration: none;">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="page-title-wrapper">
        <h1 class="page-title">Lịch sử đơn hàng</h1>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning auto-hide-alert" style="border-radius: 12px; font-weight: 600; padding: 15px; margin-bottom: 20px; color: #856404; background-color: #fff3cd; border: 1px solid #ffeeba; display: flex; align-items: center; gap: 10px; transition: opacity 0.5s ease;">
            <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i> {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger auto-hide-alert" style="border-radius: 12px; font-weight: 600; padding: 15px; margin-bottom: 20px; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; display: flex; align-items: center; gap: 10px; transition: opacity 0.5s ease;">
            <i class="fas fa-exclamation-circle" style="font-size: 20px;"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Filters & Navigation Tab Panel -->
    <div class="filters-panel">
        <div class="search-date-grid">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm kiếm theo mã đơn hoặc tên món..." oninput="handleSearchInput()">
            </div>
            <div class="date-picker-wrapper">
                <div class="date-input-group">
                    <label for="startDate">Từ ngày</label>
                    <input type="date" id="startDate" onchange="loadOrders(1)">
                </div>
                <div class="date-input-group">
                    <label for="endDate">Đến ngày</label>
                    <input type="date" id="endDate" onchange="loadOrders(1)">
                </div>
            </div>
        </div>
        
        <div class="status-tabs-container">
            <button class="status-tab active" data-status="all" onclick="switchTab(this)">Tất cả</button>
            <button class="status-tab" data-status="pending" onclick="switchTab(this)">Chờ xác nhận</button>
            <button class="status-tab" data-status="preparing" onclick="switchTab(this)">Đang chuẩn bị</button>
            <button class="status-tab" data-status="shipping" onclick="switchTab(this)">Đang giao</button>
            <button class="status-tab" data-status="completed" onclick="switchTab(this)">Đã hoàn thành</button>
            <button class="status-tab" data-status="cancelled" onclick="switchTab(this)">Đã hủy</button>
        </div>
    </div>

    <!-- Loading Container (Skeleton Loaders) -->
    <div id="loadingContainer" style="display: none;">
        <div class="skeleton-card">
            <div class="skeleton-line" style="height: 24px; width: 40%; margin-bottom: 20px;"></div>
            <div class="skeleton-line" style="height: 60px; width: 100%; margin-bottom: 12px;"></div>
            <div class="skeleton-line" style="height: 60px; width: 100%; margin-bottom: 20px;"></div>
            <div class="skeleton-line" style="height: 48px; width: 100%;"></div>
        </div>
        <div class="skeleton-card">
            <div class="skeleton-line" style="height: 24px; width: 30%; margin-bottom: 20px;"></div>
            <div class="skeleton-line" style="height: 60px; width: 100%; margin-bottom: 12px;"></div>
            <div class="skeleton-line" style="height: 48px; width: 100%;"></div>
        </div>
    </div>

    <!-- Error State -->
    <div id="errorContainer" style="display: none; text-align: center; padding: 60px 20px;">
        <i class="fas fa-exclamation-triangle" style="font-size: 56px; color: #dc3545; margin-bottom: 20px;"></i>
        <div id="errorMessage" style="font-size: 16px; color: #718096; margin-bottom: 24px;">Không thể kết nối đến máy chủ.</div>
        <button class="btn-primary-custom" onclick="loadOrders(1)">
            <i class="fas fa-redo me-2"></i> Thử lại ngay
        </button>
    </div>

    <!-- Orders Container -->
    <div id="ordersContainer"></div>

    <!-- Pagination -->
    <div id="paginationContainer" class="pagination"></div>
</div>

<!-- Custom Elegant Glassmorphic Confirmation Modal -->
<div id="cancelConfirmModal" class="custom-modal-backdrop" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-icon-wrapper">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="modal-title">Xác nhận hủy đơn</h3>
        <p class="modal-message" style="margin-bottom: 12px;">Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.</p>
        
        <div class="form-group text-left" style="margin-top: 15px; margin-bottom: 20px; text-align: left;">
            <label for="cancelReasonSelect" style="font-family: 'Segoe UI', sans-serif; font-size: 13px; font-weight: 700; color: #4a5568; margin-bottom: 6px; display: block;">Lý do hủy đơn:</label>
            <select id="cancelReasonSelect" class="form-control" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 13.5px; padding: 8px; outline: none; width: 100%; font-family: inherit; font-weight: 500; height: auto;" onchange="handleCancelReasonChange()">
                <option value="Thay đổi ý định / Không muốn mua nữa">Thay đổi ý định / Không muốn mua nữa</option>
                <option value="Muốn thay đổi địa chỉ giao hàng">Muốn thay đổi địa chỉ giao hàng</option>
                <option value="Muốn thay đổi món ăn / số lượng">Muốn thay đổi món ăn / số lượng</option>
                <option value="Quên áp dụng mã giảm giá">Quên áp dụng mã giảm giá</option>
                <option value="Khác">Lý do khác...</option>
            </select>
            <textarea id="customCancelReason" class="form-control" placeholder="Nhập lý do khác của bạn..." style="margin-top: 10px; border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 13.5px; padding: 8px; outline: none; width: 100%; font-family: inherit; display: none; min-height: 70px; resize: vertical;"></textarea>
        </div>

        <div class="modal-actions">
            <button class="modal-btn cancel-action-btn" onclick="closeCancelModal()">Quay lại</button>
            <button class="modal-btn confirm-action-btn" id="btnConfirmCancelOrder" onclick="confirmCancelOrderAction()">Hủy đơn</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentPage = 1;
    let activeStatus = 'all';
    let searchTimeout;

    // Switch categories tabs
    function switchTab(button) {
        document.querySelectorAll('.status-tab').forEach(tab => tab.classList.remove('active'));
        button.classList.add('active');
        activeStatus = button.getAttribute('data-status');
        loadOrders(1);
    }

    // Debounced Search Input handler
    function handleSearchInput() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadOrders(1);
        }, 400);
    }

    async function loadOrders(page = 1) {
        const loadingContainer = document.getElementById('loadingContainer');
        const errorContainer = document.getElementById('errorContainer');
        const ordersContainer = document.getElementById('ordersContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        const searchInput = document.getElementById('searchInput').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Show loading state (Skeleton loader)
        loadingContainer.style.display = 'block';
        errorContainer.style.display = 'none';
        ordersContainer.innerHTML = '';
        paginationContainer.innerHTML = '';

        try {
            // Build filter query parameters
            let url = `/api/v1/orders?per_page=10&page=${page}&status=${activeStatus}`;
            if (searchInput.trim()) url += `&search=${encodeURIComponent(searchInput)}`;
            if (startDate) url += `&start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;

            const response = await fetch(url, {
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
                            <div class="empty-state-illustration">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <h3 class="empty-state-title">Chưa có đơn hàng nào</h3>
                            <div class="no-orders-text">Không tìm thấy bất kỳ đơn đặt hàng nào khớp với bộ lọc tìm kiếm hiện tại của bạn.</div>
                            <a href="{{ route('foods.index') }}" class="btn-primary-custom">
                                Khám phá món ngon ngay <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    `;
                } else {
                    result.data.forEach(order => {
                        ordersContainer.innerHTML += renderOrderCard(order);
                    });

                    // Render pagination
                    currentPage = result.pagination.current_page;
                    renderPagination(result.pagination);
                }

                // Smooth scroll back to top of the page on new page load
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            } else {
                throw new Error(result.message || 'Không thể tải danh sách đơn hàng');
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            loadingContainer.style.display = 'none';
            errorContainer.style.display = 'block';
            document.getElementById('errorMessage').textContent = error.message || 'Đã xảy ra lỗi khi kết nối đến máy chủ.';
        }
    }

    function renderSingleItemRow(item, orderId = null, orderStatus = null) {
        const fallbackImg = '{{ asset("images/default-food.png") }}';
        // Check if item has food_image, if not use fallback image
        const imgPath = item.food_image ? item.food_image : fallbackImg;
        
        let reviewBtnHtml = '';
        if (orderId && orderStatus === 'Hoàn thành') {
            if (item.has_reviewed) {
                reviewBtnHtml = `
                    <span class="text-success" style="font-size: 11.5px; font-weight: 700; margin-top: 6px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fas fa-check-circle" style="color: #28a745;"></i> Đã đánh giá
                    </span>
                `;
            } else {
                reviewBtnHtml = `
                    <a href="/reviews/create/${orderId}/${item.food_id}" class="action-btn secondary-btn" style="padding: 4px 10px; font-size: 11.5px; border-radius: 8px; margin-top: 6px; display: inline-flex; width: fit-content; gap: 4px; font-weight: 700; text-decoration: none;">
                        <i class="fas fa-star" style="font-size: 9.5px; color: #ffbe33;"></i> Đánh giá
                    </a>
                `;
            }
        }

        return `
            <div class="order-item">
                <div class="item-info">
                    <img src="${imgPath}" alt="${item.food_name}" class="item-thumbnail" onerror="this.src='${fallbackImg}'">
                    <div style="display: flex; flex-direction: column;">
                        <div class="item-name">${item.food_name}</div>
                        <div class="item-quantity-wrapper">
                            <span class="text-muted">Số lượng:</span>
                            <span class="item-quantity-badge">${item.quantity}</span>
                        </div>
                        ${reviewBtnHtml}
                    </div>
                </div>
                <div class="item-price">${formatCurrency(item.price)} đ</div>
            </div>
        `;
    }

    // Toggle hidden items display
    window.toggleItems = function(orderId, btn, hiddenCount = 1) {
        const e = window.event;
        if (e) {
            if (typeof e.preventDefault === 'function') e.preventDefault();
            if (typeof e.stopPropagation === 'function') e.stopPropagation();
        }
        const wrapper = document.getElementById(`collapse-${orderId}`);
        if (wrapper) {
            if (wrapper.style.display === 'none') {
                wrapper.style.display = 'block';
                btn.innerHTML = `Thu gọn <i class="fas fa-chevron-up"></i>`;
            } else {
                wrapper.style.display = 'none';
                btn.innerHTML = `Xem thêm ${hiddenCount} sản phẩm khác <i class="fas fa-chevron-down"></i>`;
            }
        }
        return false;
    }

    // Re-order: xóa giỏ cũ → thêm lại món → mở popup giỏ hàng (giống Shopee/TikTok Shop)
    window.reorderItems = async function(event, orderId, itemsJson) {
        const btn = event?.currentTarget || event?.target;
        const originalHtml = btn ? btn.innerHTML : '';

        try {
            const items = JSON.parse(decodeURIComponent(itemsJson));

            // Disable button, show loading state
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            }

            // Bước 1: Xóa toàn bộ giỏ hàng cũ
            await fetch('/cart', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            // Bước 2: Thêm lần lượt các món vào giỏ mới
            let successCount = 0;
            let failedItems = [];

            for (const item of items) {
                try {
                    const res = await fetch('/cart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            MaMonAn: item.food_id,
                            SoLuong: item.quantity
                        }),
                        credentials: 'same-origin'
                    });
                    const result = await res.json();
                    if (res.ok && result.success !== false) {
                        successCount++;
                    } else {
                        failedItems.push(item.name || ('Món #' + item.food_id));
                    }
                } catch (e) {
                    failedItems.push(item.name || ('Món #' + item.food_id));
                }
            }

            // Bước 3: Khôi phục nút
            if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }

            if (successCount === 0) {
                showToast('Không thể thêm món nào. Các món có thể đã ngừng bán.', 'error');
                return;
            }

            if (failedItems.length > 0) {
                showToast(`Đã thêm ${successCount} món (bỏ qua: ${failedItems.join(', ')})`, 'warning');
            } else {
                showToast(`Đã thêm ${successCount} món vào giỏ hàng!`, 'success');
            }

            // Bước 4: Reload và mở popup giỏ hàng floating
            if (typeof toggleCartModal === 'function') {
                // Đóng nếu đang mở rồi mở lại để reload data
                const modal = document.getElementById('cartModal');
                if (modal && modal.style.display === 'block') {
                    modal.style.display = 'none';
                }
                setTimeout(() => toggleCartModal(), 300);
            }

        } catch (error) {
            console.error('Reorder error:', error);
            showToast(error.message || 'Có lỗi xảy ra khi đặt lại đơn hàng.', 'error');
            if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
        }
    }

    // Custom Modal Cancellation Logic variables and trigger
    let currentCancelOrderId = null;

    window.cancelOrder = function(orderId) {
        currentCancelOrderId = orderId;
        
        // Reset inputs
        const select = document.getElementById('cancelReasonSelect');
        const customText = document.getElementById('customCancelReason');
        if (select) select.value = select.options[0].value;
        if (customText) {
            customText.value = '';
            customText.style.display = 'none';
        }

        const modal = document.getElementById('cancelConfirmModal');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }

    window.closeCancelModal = function() {
        const modal = document.getElementById('cancelConfirmModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
        currentCancelOrderId = null;
    }

    window.handleCancelReasonChange = function() {
        const select = document.getElementById('cancelReasonSelect');
        const customText = document.getElementById('customCancelReason');
        if (select.value === 'Khác') {
            customText.style.display = 'block';
            customText.focus();
        } else {
            customText.style.display = 'none';
        }
    }

    window.confirmCancelOrderAction = async function() {
        if (!currentCancelOrderId) return;
        
        const select = document.getElementById('cancelReasonSelect');
        const customText = document.getElementById('customCancelReason');
        let reason = select.value;
        if (reason === 'Khác') {
            reason = customText.value.trim();
            if (!reason) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu lý do',
                    text: 'Vui lòng nhập lý do hủy đơn khác của bạn!',
                    confirmButtonColor: '#ff6900'
                });
                customText.focus();
                return;
            }
        }

        const orderId = currentCancelOrderId;
        closeCancelModal();

        try {
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Đang tiến hành hủy đơn hàng của bạn.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const response = await fetch(`/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason }),
                credentials: 'same-origin'
            });

            const result = await response.json();
            Swal.close();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: 'Hủy đơn hàng thành công!',
                    confirmButtonColor: '#ff6900',
                    timer: 2000,
                    timerProgressBar: true
                });
                setTimeout(() => {
                    loadOrders(currentPage);
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Thất bại',
                    text: result.message || 'Hủy đơn hàng thất bại.',
                    confirmButtonColor: '#dc3545'
                });
            }
        } catch (error) {
            Swal.close();
            console.error('Cancel order error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Có lỗi xảy ra khi hủy đơn hàng.',
                confirmButtonColor: '#dc3545'
            });
        }
    }

    // Custom SweetAlert2 Toast configuration
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    function showToast(message, type = 'success') {
        let swalIcon = 'success';
        if (type === 'error') swalIcon = 'error';
        else if (type === 'warning') swalIcon = 'warning';
        else if (type === 'info') swalIcon = 'info';

        Toast.fire({
            icon: swalIcon,
            title: message
        });
    }

    function renderOrderCard(order) {
        let statusClass = 'pending';
        let statusIcon = '<i class="fas fa-clock"></i> ';
        
        if (order.status === 'Hoàn thành') {
            statusClass = 'completed';
            statusIcon = '<i class="fas fa-check-circle"></i> ';
        } else if (order.status === 'Đã hủy') {
            statusClass = 'cancelled';
            statusIcon = '<i class="fas fa-times-circle"></i> ';
        } else if (order.status === 'Đang giao') {
            statusClass = 'processing';
            statusIcon = '<i class="fas fa-shipping-fast"></i> ';
        } else if (order.status === 'Đang chuẩn bị') {
            statusClass = 'processing';
            statusIcon = '<i class="fas fa-fire"></i> ';
        } else if (order.status === 'Chờ xác nhận' || order.status === 'Chờ xử lý') {
            statusClass = 'pending';
            statusIcon = '<i class="fas fa-clock"></i> ';
        }

        // Render first 2 items visible, collapse the rest
        let visibleItems = order.items;
        let hiddenCount = 0;
        let isCollapsed = false;
        
        if (order.items.length > 2) {
            visibleItems = order.items.slice(0, 2);
            hiddenCount = order.items.length - 2;
            isCollapsed = true;
        }
        
        let itemsHtml = visibleItems.map(item => renderSingleItemRow(item, order.id, order.status)).join('');
        
        let collapseHtml = '';
        if (isCollapsed) {
            let hiddenItemsHtml = order.items.slice(2).map(item => renderSingleItemRow(item, order.id, order.status)).join('');
            collapseHtml = `
                <div class="collapse-items-wrapper" id="collapse-${order.id}" style="display: none; border-top: 1px solid #f7fafc; padding-top: 12px; margin-top: 12px;">
                    ${hiddenItemsHtml}
                </div>
                <button type="button" class="toggle-items-btn" onclick="toggleItems(${order.id}, this, ${hiddenCount}); return false;">
                    Xem thêm ${hiddenCount} sản phẩm khác <i class="fas fa-chevron-down"></i>
                </button>
            `;
        }

        return `
            <div class="order-card" data-status="${statusClass}">
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
                    
                    ${collapseHtml}

                    <div class="order-divider"></div>

                    <div class="order-total-breakdown">
                        <div class="breakdown-row">
                            <span class="breakdown-label">Tạm tính (${order.items.length} món)</span>
                            <span class="breakdown-value">${formatCurrency(order.subtotal)} đ</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label"><i class="fas fa-truck" style="margin-right:4px; color:#ff6900; font-size:11px;"></i>Phí giao hàng</span>
                            <span class="breakdown-value" style="${order.shipping_fee > 0 ? '' : 'color:#28a745; font-weight:700;'}">
                                ${order.shipping_fee > 0 ? '+ ' + formatCurrency(order.shipping_fee) + ' đ' : 'Miễn phí'}
                            </span>
                        </div>
                        ${order.discount_amount > 0 ? `
                        <div class="breakdown-row discount">
                            <span class="breakdown-label">
                                <i class="fas fa-tag" style="margin-right:4px; color:#e53e3e; font-size:11px;"></i>Giảm giá voucher
                                ${order.voucher_code ? `<span style="display:inline-block; margin-left:6px; background:#fff3cd; color:#92400e; font-size:10.5px; font-weight:800; padding:1px 7px; border-radius:5px; border:1px dashed #f59e0b; letter-spacing:0.5px;">${order.voucher_code}</span>` : ''}
                            </span>
                            <span class="breakdown-value">- ${formatCurrency(order.discount_amount)} đ</span>
                        </div>
                        ` : ''}
                        <div class="breakdown-row total">
                            <span class="breakdown-label">Tổng thanh toán</span>
                            <span class="breakdown-value">${formatCurrency(order.total_amount)} đ</span>
                        </div>
                    </div>

                    <div class="order-address">
                        <span class="order-address-label">
                            <i class="fas fa-map-marker-alt" style="color: #ff6900;"></i> Địa chỉ giao hàng
                        </span>
                        <span class="order-address-value">${order.delivery_address}</span>
                    </div>

                    <div class="order-action-divider"></div>

                    <div class="order-footer-wrapper">
                        ${(order.status === 'Hoàn thành' || order.status === 'Đã hủy') ? `
                            <button class="action-btn primary-btn" onclick="reorderItems(event, ${order.id}, '${encodeURIComponent(JSON.stringify(order.items))}')">
                                <i class="fas fa-redo"></i> Đặt lại món
                            </button>
                        ` : ''}
                        ${(order.status === 'Chờ xác nhận' || order.status === 'Chờ xử lý' || order.status === 'Đã xác nhận') ? `
                            ${order.payment_method === 'Online' && order.payment_status === 'Chờ thanh toán' ? `
                            <a href="/checkout/payos-resume/${order.id}" class="action-btn primary-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);">
                                <i class="fas fa-qrcode"></i> Thanh toán tiếp
                            </a>
                            ` : ''}
                            <button class="action-btn cancel-btn" onclick="cancelOrder(${order.id})">
                                <i class="fas fa-times"></i> Hủy đơn hàng
                            </button>
                        ` : ''}
                        ${(order.status === 'Đang giao' || order.status === 'Đang chuẩn bị') ? `
                            <a href="tel:0987654321" class="action-btn secondary-btn">
                                <i class="fas fa-phone-alt"></i> Liên hệ quán
                            </a>
                        ` : ''}
                        <a href="/orders/${order.id}" class="action-btn primary-btn">
                            Xem chi tiết <i class="fas fa-chevron-right" style="font-size: 11px; margin-left: 2px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    function renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        
        const cur = parseInt(pagination.current_page, 10);
        const last = parseInt(pagination.last_page, 10);

        if (last <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';

        // Previous button
        html += `
            <button class="pagination-btn" 
                    onclick="loadOrders(${cur - 1})" 
                    ${cur === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;

        let range = [];
        let rangeWithDots = [];
        let l;

        for (let i = 1; i <= last; i++) {
            if (i === 1 || i === last || (i >= cur - 1 && i <= cur + 1)) {
                range.push(i);
            }
        }

        for (let i of range) {
            if (l) {
                if (i - l === 2) {
                    rangeWithDots.push(l + 1);
                } else if (i - l > 2) {
                    rangeWithDots.push('...');
                }
            }
            rangeWithDots.push(i);
            l = i;
        }

        for (let page of rangeWithDots) {
            if (page === '...') {
                html += '<span style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; color: #a0aec0; font-weight: bold; font-size: 14px;">...</span>';
            } else {
                html += `
                    <button class="pagination-btn ${page === cur ? 'active' : ''}" 
                            onclick="loadOrders(${page})">
                        ${page}
                    </button>
                `;
            }
        }

        // Next button
        html += `
            <button class="pagination-btn" 
                    onclick="loadOrders(${cur + 1})" 
                    ${cur === last ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        container.innerHTML = html;
    }

    // Helper functions
    function formatCurrency(amount) {
        const num = parseFloat(amount);
        return isNaN(num) ? '0' : new Intl.NumberFormat('vi-VN').format(num);
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

    // Initial page load
    document.addEventListener('DOMContentLoaded', () => {
        loadOrders(1);
        
        // Tự động ẩn thông báo sau 5 giây
        const alerts = document.querySelectorAll('.auto-hide-alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    });
</script>
@endsection

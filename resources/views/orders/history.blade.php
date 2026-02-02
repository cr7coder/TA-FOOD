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
    }
</style>

<div class="order-history-container">
    <a href="{{ route('foods.index') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Quay lại
    </a>

    <h1 class="page-title">Lịch sử đơn hàng</h1>

    @if($donHangs->count() > 0)
        @foreach($donHangs as $donHang)
            <div class="order-card">
                <div class="order-header">
                    <div class="order-info">
                        <div class="order-field">
                            <span class="order-field-label">Mã đơn hàng</span>
                            <span class="order-field-value">ORD{{ str_pad($donHang->MaDonHang, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="order-field">
                            <span class="order-field-label">Thời gian</span>
                            <span class="order-field-value">{{ $donHang->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                    <div>
                        @php
                            $statusClass = 'pending';
                            $statusText = $donHang->TrangThai;
                            $statusIcon = '';
                            
                            if ($donHang->TrangThai == 'Hoàn thành') {
                                $statusClass = 'completed';
                                $statusIcon = '✓ ';
                            } elseif ($donHang->TrangThai == 'Đã hủy') {
                                $statusClass = 'cancelled';
                                $statusIcon = '❌ ';
                            } elseif ($donHang->TrangThai == 'Đang xử lý') {
                                $statusClass = 'processing';
                            } elseif ($donHang->TrangThai == 'Chờ xác nhận') {
                                $statusClass = 'pending';
                            }
                        @endphp
                        <span class="order-status-badge {{ $statusClass }}">
                            {{ $statusIcon }}{{ $statusText }}
                        </span>
                    </div>
                </div>

                <div class="order-body">
                    <div class="order-items">
                        @foreach($donHang->chiTiet as $chiTiet)
                            <div class="order-item">
                                <div class="item-info">
                                    <div class="item-name">{{ $chiTiet->monAn->TenMonAn }}</div>
                                    <div class="item-quantity-label">Số lượng</div>
                                    <div class="item-quantity">{{ $chiTiet->SoLuong }}</div>
                                </div>
                                <div class="item-price">{{ number_format($chiTiet->Gia) }} đ</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="order-divider"></div>

                    <div class="order-total">
                        <span class="order-total-label">Tổng cộng</span>
                        <span class="order-total-amount">{{ number_format($donHang->TongTien) }} đ</span>
                    </div>

                    <div class="order-address">
                        <span class="order-address-label">Địa chỉ giao hàng</span>
                        <span class="order-address-value">{{ $donHang->DiaChiGiaoHang }}</span>
                    </div>

                    <div class="order-action-divider"></div>

                    <a href="{{ route('orders.show', $donHang->MaDonHang) }}" class="view-detail-btn">
                        Xem chi tiết đơn hàng
                    </a>
                </div>
            </div>
        @endforeach

        <div class="pagination">
            {{ $donHangs->links() }}
        </div>
    @else
        <div class="no-orders">
            <i class="fas fa-shopping-bag"></i>
            <div class="no-orders-text">Bạn chưa có đơn hàng nào</div>
            <a href="{{ route('foods.index') }}" class="btn-primary-custom">
                Đặt món ngay
            </a>
        </div>
    @endif
</div>
@endsection

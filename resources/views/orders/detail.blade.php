@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<style>
    .order-detail-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 16px;
        background: white;
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

    .detail-section {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 16px 24px;
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: white;
    }

    .section-header.info {
        background: #2b7fff;
    }

    .section-header.items {
        background: #00a63e;
    }

    .section-header.summary {
        background: #0092b8;
    }

    .section-header i {
        font-size: 20px;
    }

    .section-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .info-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .info-label {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #364153;
    }

    .info-value {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #364153;
        font-weight: 500;
    }

    .food-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 16px;
        margin-bottom: 16px;
        border-bottom: 0.8px solid #e5e7eb;
    }

    .food-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .food-image {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        object-fit: cover;
    }

    .food-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .food-name {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #101828;
        font-weight: 500;
    }

    .food-quantity {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #6a7282;
    }

    .food-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
    }

    .food-price {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #fb2c36;
        font-weight: 500;
    }

    .review-btn {
        background: #fdc700;
        border: none;
        border-radius: 4px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #101828;
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .review-btn:hover {
        background: #e5b500;
        color: #101828;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(253, 199, 0, 0.3);
    }

    .review-btn i {
        font-size: 14px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .summary-row:last-child {
        margin-bottom: 0;
    }

    .summary-label {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #364153;
    }

    .summary-value {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #101828;
    }

    .summary-divider {
        border-top: 1.6px solid #d1d5dc;
        margin: 16px 0;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
    }

    .summary-total-label {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #101828;
        font-weight: 500;
    }

    .summary-total-value {
        font-family: 'Arimo', sans-serif;
        font-size: 20px;
        line-height: 28px;
        color: #fb2c36;
        font-weight: 600;
    }
</style>

<div class="order-detail-container">
    <a href="{{ route('orders.history') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Quay lại
    </a>

    <h1 class="page-title">Chi tiết đơn hàng</h1>

    <!-- Order Information Section -->
    <div class="detail-section">
        <div class="section-header info">
            <i class="fas fa-info-circle"></i>
            <span>Thông tin đơn hàng</span>
        </div>
        <div class="section-body">
            <div class="info-grid">
                <div class="info-field">
                    <div class="info-label">Ngày đặt:</div>
                    <div class="info-value">{{ $donHang->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Địa chỉ giao hàng:</div>
                    <div class="info-value">{{ $donHang->DiaChiGiaoHang }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Khách hàng:</div>
                    <div class="info-value">{{ $donHang->TenKhachHang }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Phương thức thanh toán:</div>
                    <div class="info-value">{{ $donHang->PhuongThucThanhToan }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Số điện thoại:</div>
                    <div class="info-value">{{ $donHang->SoDienThoai }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Ghi chú:</div>
                    <div class="info-value">{{ $donHang->GhiChu ?: 'Không có ghi chú' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Food Items Section -->
    <div class="detail-section">
        <div class="section-header items">
            <i class="fas fa-utensils"></i>
            <span>Chi tiết món ăn</span>
        </div>
        <div class="section-body">
            @foreach($donHang->chiTiet as $chiTiet)
                <div class="food-item">
                    @if($chiTiet->monAn->HinhAnh)
                        <img src="{{ asset('images/' . $chiTiet->monAn->HinhAnh) }}" 
                             alt="{{ $chiTiet->monAn->TenMonAn }}" 
                             class="food-image">
                    @else
                        <div class="food-image" style="background: linear-gradient(45deg, #ffc107, #ff9800); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-utensils" style="color: white; font-size: 24px;"></i>
                        </div>
                    @endif
                    
                    <div class="food-info">
                        <div class="food-name">{{ $chiTiet->monAn->TenMonAn }}</div>
                        <div class="food-quantity">{{ number_format($chiTiet->Gia) }} đ × {{ $chiTiet->SoLuong }}</div>
                    </div>

                    <div class="food-actions">
                        <div class="food-price">{{ number_format($chiTiet->Gia * $chiTiet->SoLuong) }} đ</div>
                        <a href="{{ route('reviews.create', ['donHang' => $donHang->MaDonHang, 'monAn' => $chiTiet->MaMonAn]) }}" 
                           class="review-btn">
                            ⭐ Đánh giá
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Summary Section -->
    <div class="detail-section">
        <div class="section-header summary">
            <i class="fas fa-calculator"></i>
            <span>Tổng kết</span>
        </div>
        <div class="section-body">
            @php
                $tamTinh = $donHang->chiTiet->sum(function($item) {
                    return $item->Gia * $item->SoLuong;
                });
                $giamGia = 0;
                if ($donHang->MaGiamGia && $donHang->giamGia) {
                    if ($donHang->giamGia->LoaiGiamGia == 'Phần trăm') {
                        $giamGia = $tamTinh * ($donHang->giamGia->GiaTriGiam / 100);
                        if ($donHang->giamGia->GiamToiDa) {
                            $giamGia = min($giamGia, $donHang->giamGia->GiamToiDa);
                        }
                    } else {
                        $giamGia = $donHang->giamGia->GiaTriGiam;
                    }
                }
            @endphp

            <div class="summary-row">
                <span class="summary-label">Tạm tính:</span>
                <span class="summary-value">{{ number_format($tamTinh) }} đ</span>
            </div>

            @if($giamGia > 0)
                <div class="summary-row">
                    <span class="summary-label">Giảm giá:</span>
                    <span class="summary-value" style="color: #00a63e;">-{{ number_format($giamGia) }} đ</span>
                </div>
            @endif

            <div class="summary-row">
                <span class="summary-label">Phí vận chuyển:</span>
                <span class="summary-value">Miễn phí</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-total">
                <span class="summary-total-label">Tổng cộng:</span>
                <span class="summary-total-value">{{ number_format($donHang->TongTien) }} đ</span>
            </div>
        </div>
    </div>
</div>
@endsection

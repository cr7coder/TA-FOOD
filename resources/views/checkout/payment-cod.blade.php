@extends('foods.master')

@section('title', 'Xác nhận thanh toán')

@section('content')
    <section class="payment_section layout_padding">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <!-- Header with icon -->
                            <div class="text-center mb-4">
                                @if($donHang->PhuongThucThanhToan === 'COD')
                                    <div class="mb-3">
                                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                    </div>
                                    <h3 class="mb-2">Đặt hàng thành công!</h3>
                                    <p class="text-muted">Đơn hàng của bạn đã được tiếp nhận</p>
                                @else
                                    <div class="mb-3">
                                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#17a2b8" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                    </div>
                                    <h3 class="mb-2">Xác nhận thanh toán</h3>
                                    <p class="text-muted">Đơn hàng đang chờ xác nhận thanh toán</p>
                                @endif
                            </div>

                            <!-- Payment method badge -->
                            <div class="text-center mb-4">
                                @if($donHang->PhuongThucThanhToan === 'COD')
                                    <span class="badge bg-success px-3 py-2" style="font-size: 14px;">
                                        <i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)
                                    </span>
                                @else
                                    <span class="badge bg-info px-3 py-2" style="font-size: 14px;">
                                        <i class="fas fa-wallet"></i> Thanh toán qua {{ $donHang->PhuongThucThanhToan }}
                                    </span>
                                @endif
                            </div>

                            <!-- Order details -->
                            <div class="border-top border-bottom py-3 mb-4">
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Mã đơn hàng:</div>
                                    <div class="col-6 text-end fw-bold">{{ $donHang->MaDonHang }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Tổng tiền:</div>
                                    <div class="col-6 text-end fw-bold text-primary">{{ number_format($donHang->TongTien, 0, ',', '.') }} đ</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Phương thức:</div>
                                    <div class="col-6 text-end">{{ $donHang->PhuongThucThanhToan }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Trạng thái:</div>
                                    <div class="col-6 text-end">
                                        <span class="badge bg-{{ $donHang->TrangThai === 'Đã thanh toán' ? 'success' : 'warning' }}">
                                            {{ $donHang->TrangThai }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery information -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng</h6>
                                <p class="mb-1"><strong>Người nhận:</strong> {{ $donHang->TenKhachHang }}</p>
                                <p class="mb-1"><strong>Số điện thoại:</strong> {{ $donHang->SoDienThoai }}</p>
                                <p class="mb-1"><strong>Địa chỉ:</strong> {{ $donHang->DiaChiGiaoHang }}</p>
                                @if($donHang->GhiChu)
                                    <p class="mb-1"><strong>Ghi chú:</strong> {{ $donHang->GhiChu }}</p>
                                @endif
                            </div>

                            <!-- Payment instructions -->
                            @if($donHang->PhuongThucThanhToan === 'COD')
                                <div class="alert alert-success mb-4">
                                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Hướng dẫn thanh toán</h6>
                                    <p class="mb-0">Vui lòng chuẩn bị số tiền <strong>{{ number_format($donHang->TongTien, 0, ',', '.') }} đ</strong> để thanh toán khi nhận hàng.</p>
                                </div>
                            @else
                                <div class="alert alert-info mb-4">
                                    <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Thanh toán đã được xác nhận</h6>
                                    <p class="mb-2">Giao dịch qua <strong>{{ $donHang->PhuongThucThanhToan }}</strong> đã được ghi nhận.</p>
                                    <p class="mb-0 small">Chúng tôi sẽ bắt đầu chuẩn bị đơn hàng của bạn ngay.</p>
                                </div>
                            @endif

                            <!-- Next steps -->
                            <div class="bg-light p-3 rounded mb-4">
                                <h6 class="fw-bold mb-2">Bước tiếp theo</h6>
                                <ul class="mb-0 ps-3">
                                    <li>Chúng tôi sẽ xác nhận đơn hàng qua SMS/Email</li>
                                    <li>Đơn hàng sẽ được chuẩn bị và giao trong 30-60 phút</li>
                                    <li>Bạn có thể theo dõi đơn hàng tại mục "Đơn hàng của tôi"</li>
                                </ul>
                            </div>

                            <!-- Action buttons -->
                            <div class="d-grid gap-2">
                                <a href="{{ url('/') }}" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Về trang chủ
                                </a>
                                <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-history me-2"></i>Lịch sử đơn hàng
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Support info -->
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-phone-alt me-2"></i>Cần hỗ trợ? Liên hệ: 
                            <a href="tel:1900xxxx" class="text-decoration-none">1900 xxxx</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .payment_section {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .card {
            border-radius: 15px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge {
            border-radius: 20px;
        }

        .btn {
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
@endsection

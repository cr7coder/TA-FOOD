@extends('foods.master')

@section('title', 'Đặt hàng thành công')

@section('content')
    <div class="hero_area2">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>

        <header class="header_section2">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="{{ route('foods.index') }}">
                        <span>CabaFood</span>
                    </a>
                </nav>
            </div>
        </header>
    </div>

    <section class="success_section layout_padding">
        <div class="container">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Success Message -->
                    <div class="success-header text-center mb-5">
                        <div class="success-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <h1 class="success-title">Đặt hàng thành công!</h1>
                        <p class="success-subtitle">
                            Cảm ơn bạn đã đặt hàng. Đơn hàng #{{ $donHang->MaDonHang }} đã được tiếp nhận và đang được xử
                            lý.
                        </p>
                    </div>

                    <!-- Order Details Card -->
                    <div class="card order-details-card mb-4">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fa fa-receipt"></i> Chi tiết đơn hàng #{{ $donHang->MaDonHang }}
                                </h5>
                                <span class="badge badge-{{ $donHang->TrangThaiColor }}">
                                    {{ $donHang->TrangThai }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Ngày đặt:</strong>
                                    <p class="mb-0">
                                        {{ optional($donHang->NgayDat ?? $donHang->created_at)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Phương thức thanh toán:</strong>
                                    <p class="mb-0">
                                        @if($donHang->PhuongThucThanhToan === 'COD')
                                            <i class="fa fa-money-bill text-success"></i> Thanh toán khi nhận hàng
                                        @else
                                            <i class="fa fa-credit-card text-primary"></i> Thanh toán online
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <h6 class="border-bottom pb-2 mb-3">Món ăn đã đặt</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Món ăn</th>
                                            <th>Giá</th>
                                            <th>Số lượng</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($donHang->chiTiet as $item)
                                            @php $mon = optional($item->monAn); @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($mon->HinhAnh)
                                                            <img src="{{ asset('images/' . $mon->HinhAnh) }}"
                                                                alt="{{ $mon->TenMonAn ?? 'Món đã xoá' }}"
                                                                class="order-item-image me-2">
                                                        @endif
                                                        <span>{{ $mon->TenMonAn ?? 'Món đã xoá' }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($item->Gia, 0, ',', '.') }} đ</td>
                                                <td>{{ $item->SoLuong }}</td>
                                                <td class="fw-bold">{{ $item->ThanhTienFormat }} đ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Order Summary -->
                            <div class="order-summary mt-4">
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="summary-details">
                                            <div class="d-flex justify-content-between">
                                                <span>Tạm tính:</span>
                                                <span>
                                                    {{ number_format($donHang->chiTiet->sum('ThanhTien'), 0, ',', '.') }} đ
                                                </span>
                                            </div>
                                            @if($donHang->giamGia)
                                                <div class="d-flex justify-content-between text-success">
                                                    <span>Giảm giá ({{ $donHang->giamGia->PhanTram }}%):</span>
                                                    <span>
                                                        -{{ number_format($donHang->chiTiet->sum('ThanhTien') * $donHang->giamGia->PhanTram / 100, 0, ',', '.') }}
                                                        đ
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between">
                                                <span>Phí vận chuyển:</span>
                                                <span class="text-success">Miễn phí</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between total-row">
                                                <strong>Tổng cộng:</strong>
                                                <strong class="text-danger">{{ $donHang->TongTienFormat }} đ</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    @if($donHang->thanhToan)
                        <div class="card mb-4">
                            <div class="card-header bg-{{ $donHang->thanhToan->TrangThaiColor }} text-white">
                                <h6 class="mb-0">
                                    <i class="fa fa-credit-card"></i> Trạng thái thanh toán
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Phương thức:</strong>
                                        <p class="mb-0">
                                            {{ $donHang->thanhToan->PhuongThuc === 'COD' ? 'Thanh toán khi nhận hàng' : 'Thanh toán online' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Trạng thái:</strong>
                                        <span class="badge badge-{{ $donHang->thanhToan->TrangThaiColor }}">
                                            {{ $donHang->thanhToan->TrangThai }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Next Steps -->
                    <div class="card next-steps-card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fa fa-info-circle"></i> Bước tiếp theo
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div
                                    class="timeline-item {{ $donHang->TrangThai === 'Chờ xử lý' ? 'active' : 'completed' }}">
                                    <div class="timeline-marker">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Đơn hàng đang được xử lý</h6>
                                        <p>Chúng tôi đang chuẩn bị món ăn cho bạn</p>
                                    </div>
                                </div>
                                <div class="timeline-item {{ $donHang->TrangThai === 'Đang giao' ? 'active' : '' }}">
                                    <div class="timeline-marker">
                                        <i class="fa fa-shipping-fast"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Đang giao hàng</h6>
                                        <p>Shipper sẽ liên hệ với bạn sớm nhất</p>
                                    </div>
                                </div>
                                <div class="timeline-item {{ $donHang->TrangThai === 'Hoàn thành' ? 'active' : '' }}">
                                    <div class="timeline-marker">
                                        <i class="fa fa-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Giao hàng thành công</h6>
                                        <p>Cảm ơn bạn đã tin tưởng CabaFood</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="{{ route('foods.index') }}" class="btn btn-primary me-3">
                            <i class="fa fa-home"></i> Về trang chủ
                        </a>
                        @if(Route::has('orders.track'))
                            <a href="{{ route('orders.track', $donHang->MaDonHang) }}" class="btn btn-info">
                                <i class="fa fa-search"></i> Theo dõi đơn hàng
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .success_section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 80vh;
        }

        .success-header {
            padding: 2rem 0;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: successPulse 2s infinite;
        }

        .success-icon i {
            font-size: 3rem;
            color: white;
        }

        @keyframes successPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .success-title {
            color: #28a745;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .success-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .order-details-card,
        .next-steps-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .1);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }

        .order-item-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
        }

        .summary-details>div {
            margin-bottom: .5rem;
            padding: .25rem 0;
        }

        .total-row {
            font-size: 1.2rem;
            padding: .75rem 0 !important;
            border-top: 2px solid #dee2e6;
        }

        .timeline {
            position: relative;
            padding-left: 0;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            width: 2px;
            height: calc(100% + 1rem);
            background: #e9ecef;
        }

        .timeline-item.active::after,
        .timeline-item.completed::after {
            background: #28a745;
        }

        .timeline-marker {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            position: relative;
            z-index: 1;
        }

        .timeline-item.active .timeline-marker,
        .timeline-item.completed .timeline-marker {
            background: #28a745;
            color: white;
        }

        .timeline-content h6 {
            margin-bottom: .5rem;
            color: #495057;
        }

        .timeline-content p {
            margin-bottom: 0;
            color: #6c757d;
            font-size: .9rem;
        }

        @media (max-width: 768px) {
            .success-title {
                font-size: 2rem;
            }

            .success-icon {
                width: 80px;
                height: 80px;
            }

            .success-icon i {
                font-size: 2rem;
            }

            .timeline-item {
                margin-bottom: 1.5rem;
            }

            .timeline-marker {
                width: 35px;
                height: 35px;
            }
        }
    </style>
@endsection
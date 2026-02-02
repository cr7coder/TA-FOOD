@extends('foods.master')

@section('title', 'Thanh toán')

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

                    <div class="user_option">
                        <a href="{{ route('foods.index') }}" class="order_online">
                            <i class="fa fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </nav>
            </div>
        </header>
    </div>

    <section class="checkout_section layout_padding">
        <div class="container">
            <div class="heading_container heading_center">
                <h1>Thanh toán đơn hàng</h1>
            </div>

            <!-- Display Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Display Success Message -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row">
                    <!-- Left Column - Order Summary -->
                    <div class="col-lg-8">
                        <!-- Customer Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fa fa-user"></i> Thông tin khách hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_name">Họ và tên khách hàng *</label>
                                            <input type="text"
                                                class="form-control @error('customer_info.name') is-invalid @enderror"
                                                id="customer_name" name="customer_info[name]"
                                                value="{{ old('customer_info.name', Auth::user()->HoTen ?? '') }}">
                                            @error('customer_info.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_phone">Số điện thoại *</label>
                                            <input type="tel"
                                                class="form-control @error('customer_info.phone') is-invalid @enderror"
                                                id="customer_phone" name="customer_info[phone]"
                                                value="{{ old('customer_info.phone', Auth::user()->SoDienThoai ?? '') }}">
                                            @error('customer_info.phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="customer_address" class="mb-0">Địa chỉ giao hàng *</label>
                                        <button type="button" id="btnGeoAddress" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-crosshair"></i> Lấy vị trí hiện tại
                                        </button>
                                    </div>

                                    <textarea class="form-control @error('customer_info.address') is-invalid @enderror"
                                        id="customer_address" name="customer_info[address]"
                                        rows="3">{{ old('customer_info.address') }}</textarea>
                                    @error('customer_info.address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <small id="geoStatus" class="text-muted d-block mt-1"></small>

                                    {{-- Lưu tọa độ kèm địa chỉ (tùy chọn) --}}
                                    <input type="hidden" name="customer_info[lat]" id="customer_lat"
                                        value="{{ old('customer_info.lat') }}">
                                    <input type="hidden" name="customer_info[lng]" id="customer_lng"
                                        value="{{ old('customer_info.lng') }}">
                                </div>
                                <div class="form-group">
                                    <label for="customer_note">Ghi chú đơn hàng</label>
                                    <textarea class="form-control" id="customer_note" name="customer_info[note]" rows="2"
                                        placeholder="Yêu cầu đặc biệt về đơn hàng...">{{ old('customer_info.note') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fa fa-shopping-cart"></i> Chi tiết đơn hàng
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Món ăn</th>
                                                <th>Giá</th>
                                                <th>Số lượng</th>
                                                <th>Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cartItems as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                                                class="item-image me-2">
                                                            <div>
                                                                <div class="fw-bold">{{ $item['name'] }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $item['quantity'] }}</span>
                                                    </td>
                                                    <td class="fw-bold">{{ number_format($item['total'], 0, ',', '.') }} đ</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fa fa-credit-card"></i> Phương thức thanh toán
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check payment-method">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cod"
                                                value="COD" {{ old('payment_method', 'COD') == 'COD' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cod">
                                                <div class="payment-option">
                                                    <i class="fa fa-money-bill text-success"></i>
                                                    <div>
                                                        <strong>Thanh toán khi nhận hàng (COD)</strong>
                                                        <small class="d-block text-muted">Thanh toán bằng tiền mặt khi nhận
                                                            hàng</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check payment-method">
                                            <input class="form-check-input" type="radio" name="payment_method" id="online"
                                                value="Online" {{ old('payment_method') == 'Online' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="online">
                                                <div class="payment-option">
                                                    <i class="fa fa-credit-card text-primary"></i>
                                                    <div>
                                                        <strong>Thanh toán online</strong>
                                                        <small class="d-block text-muted">Thanh toán qua thẻ ngân hàng/ví
                                                            điện tử</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('payment_method')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="col-lg-4">
                        <div class="card checkout-summary sticky-top">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fa fa-calculator"></i> Tổng kết đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Auto Applied Voucher Display -->
                                @if(isset($bestVoucherApplied) && $bestVoucherApplied)
                                    <div id="autoAppliedVoucherSection" class="applied-voucher mb-4">
                                        <div class="alert alert-success d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-check-circle"></i>
                                                <span id="autoAppliedVoucherText">Đã tự động áp dụng mã
                                                    {{ $bestVoucherApplied['code'] }} (giảm
                                                    {{ $bestVoucherApplied['discount'] }})</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                id="removeAutoVoucherBtn">
                                                <i class="fa fa-times"></i> Bỏ
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Available Vouchers Section (Collapsible) -->
                                <div class="available-vouchers-section mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">
                                            <i class="fa fa-gift text-warning"></i> Mã giảm giá khả dụng
                                        </label>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse" data-bs-target="#vouchersCollapse"
                                            aria-expanded="{{ isset($bestVoucherApplied) && $bestVoucherApplied ? 'false' : 'true' }}"
                                            id="toggleVouchersBtn">
                                            <i class="fa fa-chevron-down" id="toggleIcon"></i>
                                            <span
                                                id="toggleText">{{ isset($bestVoucherApplied) && $bestVoucherApplied ? 'Hiển thị' : 'Thu gọn' }}</span>
                                        </button>
                                    </div>

                                    <div class="collapse {{ isset($bestVoucherApplied) && $bestVoucherApplied ? '' : 'show' }}"
                                        id="vouchersCollapse">

                                        @if($vouchers && count($vouchers) > 0)
                                            <div class="vouchers-grid" id="vouchersGrid">
                                                @foreach($vouchers as $voucher)
                                                    <div class="voucher-card" data-code="{{ $voucher->MaCode }}"
                                                        data-percent="{{ $voucher->PhanTram }}"
                                                        data-min-order="{{ $voucher->DonHangToiThieu ?? 0 }}">
                                                        <div class="voucher-header">
                                                            <div class="voucher-icon">
                                                                <i class="fa fa-percent"></i>
                                                            </div>
                                                            <div class="voucher-info">
                                                                <div class="voucher-code">{{ $voucher->MaCode }}</div>
                                                                <div class="voucher-desc">Giảm {{ $voucher->PhanTram }}%</div>
                                                            </div>
                                                            <div class="voucher-action">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-primary apply-voucher-btn">
                                                                    Áp dụng
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @if($voucher->DonHangToiThieu > 0)
                                                            <div class="voucher-condition">
                                                                <small class="text-muted">
                                                                    <i class="fa fa-info-circle"></i>
                                                                    Đơn tối thiểu:
                                                                    {{ number_format($voucher->DonHangToiThieu, 0, ',', '.') }}đ
                                                                </small>
                                                            </div>
                                                        @endif
                                                        @if($voucher->NgayKetThuc)
                                                            <div class="voucher-expiry">
                                                                <small class="text-danger">
                                                                    <i class="fa fa-clock"></i>
                                                                    HSD:
                                                                    {{ \Carbon\Carbon::parse($voucher->NgayKetThuc)->format('d/m/Y') }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-muted py-3">
                                                <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
                                                <p>Hiện tại không có mã giảm giá khả dụng</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Manual Voucher Input -->
                                    {{-- <div class="manual-voucher-section">
                                        <label class="form-label">
                                            <i class="fa fa-keyboard text-info"></i> Hoặc nhập mã giảm giá
                                        </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="voucherInput" name="voucher_code"
                                                placeholder="Nhập mã giảm giá" value="{{ old('voucher_code') }}">
                                            <button type="button" class="btn btn-outline-primary" id="applyVoucherBtn">
                                                Áp dụng
                                            </button>
                                        </div>
                                        <div id="voucherMessage" class="mt-2"></div>
                                    </div> --}}

                                    <!-- Manual Applied Voucher Display -->
                                    <div id="manualAppliedVoucherSection" class="applied-voucher mt-3"
                                        style="display: none;">
                                        <div class="alert alert-success d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-check-circle"></i>
                                                <span id="manualAppliedVoucherText">Đã áp dụng mã giảm giá</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                id="removeManualVoucherBtn">
                                                <i class="fa fa-times"></i> Bỏ
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price Breakdown -->
                                <div class="price-breakdown">
                                    <div class="d-flex justify-content-between">
                                        <span>Tạm tính:</span>
                                        <span id="subtotal">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between" id="discountRow"
                                        style="display: {{ isset($bestVoucherApplied) && $bestVoucherApplied ? 'flex' : 'none' }} !important;">
                                        <span class="text-success">Giảm giá:</span>
                                        <span class="text-success"
                                            id="discountAmount">{{ isset($bestVoucherApplied) && $bestVoucherApplied ? '-' . $bestVoucherApplied['discount'] : '0 đ' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Phí vận chuyển:</span>
                                        <span class="text-success">Miễn phí</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between total-amount">
                                        <strong>Tổng cộng:</strong>
                                        <strong class="text-danger"
                                            id="totalAmount">{{ isset($bestVoucherApplied) && $bestVoucherApplied ? $bestVoucherApplied['total'] : number_format($subtotal, 0, ',', '.') . ' đ' }}</strong>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100 mt-3" id="submitBtn">
                                    <i class="fa fa-check-circle"></i> Đặt hàng ngay
                                </button>

                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Bằng việc đặt hàng, bạn đồng ý với
                                        <a href="#" class="text-primary">Điều khoản sử dụng</a> của chúng tôi
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <script src="{{ asset('js/checkout-validator.js') }}"></script>
    <style>
        .checkout_section {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem;
        }

        .form-control:focus {
            border-color: #ffbe33;
            box-shadow: 0 0 0 0.2rem rgba(255, 190, 51, 0.25);
        }

        .payment-method {
            margin-bottom: 1rem;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method input:checked+label .payment-option {
            border-color: #ffbe33;
            background: rgba(255, 190, 51, 0.1);
        }

        .checkout-summary {
            top: 100px;
        }

        .price-breakdown>div {
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
        }

        .total-amount {
            font-size: 1.2rem;
            padding: 1rem 0 !important;
            border-top: 2px solid #ffbe33;
        }

        .voucher-section .input-group {
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background: #2c3e50;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .table td {
            vertical-align: middle;
            font-size: 14px;
        }

        .item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .checkout-summary {
                position: relative !important;
                top: auto !important;
            }

            .price-breakdown {
                font-size: 14px;
            }

            .table-responsive {
                font-size: 12px;
            }
        }

        /* Voucher Styles */
        .vouchers-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .vouchers-grid::-webkit-scrollbar {
            width: 4px;
        }

        .vouchers-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .vouchers-grid::-webkit-scrollbar-thumb {
            background: #ffbe33;
            border-radius: 4px;
        }

        .voucher-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
        }

        .voucher-card:hover {
            border-color: #ffbe33;
            box-shadow: 0 4px 15px rgba(255, 190, 51, 0.2);
            transform: translateY(-2px);
        }

        .voucher-card.selected {
            border-color: #ffbe33;
            background: rgba(255, 190, 51, 0.05);
        }

        .voucher-card.disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .voucher-card.disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .voucher-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .voucher-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ffbe33, #ff9800);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .voucher-info {
            flex: 1;
        }

        .voucher-code {
            font-weight: 700;
            font-size: 16px;
            color: #333;
        }

        .voucher-desc {
            color: #666;
            font-size: 14px;
        }

        .voucher-action {
            margin-left: auto;
        }

        .voucher-condition,
        .voucher-expiry {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #f0f0f0;
        }

        .apply-voucher-btn {
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 600;
        }

        .applied-voucher .alert {
            border-radius: 10px;
            border: none;
            margin: 0;
        }

        .manual-voucher-section {
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }

        @media (max-width: 576px) {
            .voucher-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .voucher-action {
                margin-left: 0;
                width: 100%;
            }

            .apply-voucher-btn {
                width: 100%;
            }
        }
    </style>

    {{--
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setupVoucherHandler();
            setupFormValidation();
        });

        function setupVoucherHandler() {
            const applyBtn = document.getElementById('applyVoucherBtn');
            const voucherInput = document.getElementById('voucherInput');
            const messageDiv = document.getElementById('voucherMessage');

            if (!applyBtn || !voucherInput) return;

            applyBtn.addEventListener('click', function () {
                const code = voucherInput.value.trim();

                if (!code) {
                    showVoucherMessage('Vui lòng nhập mã giảm giá', 'warning');
                    return;
                }

                applyBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
                applyBtn.disabled = true;

                fetch('{{ route("checkout.apply-voucher") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        voucher_code: code
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showVoucherMessage(data.message, 'success');
                            updatePricing(data.voucher);
                        } else {
                            showVoucherMessage(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showVoucherMessage('Có lỗi xảy ra. Vui lòng thử lại.', 'danger');
                    })
                    .finally(() => {
                        applyBtn.innerHTML = 'Áp dụng';
                        applyBtn.disabled = false;
                    });
            });

            // Allow Enter key to apply voucher
            voucherInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyBtn.click();
                }
            });
        }

        function showVoucherMessage(message, type) {
            const messageDiv = document.getElementById('voucherMessage');
            if (messageDiv) {
                messageDiv.innerHTML = `<div class="alert alert-${type} alert-sm py-2 px-3 mb-0">${message}</div>`;

                if (type === 'success') {
                    setTimeout(() => {
                        messageDiv.innerHTML = '';
                    }, 5000);
                }
            }
        }

        function updatePricing(voucher) {
            const discountRow = document.getElementById('discountRow');
            const discountAmount = document.getElementById('discountAmount');
            const totalAmount = document.getElementById('totalAmount');

            if (discountRow && discountAmount && totalAmount) {
                discountRow.style.display = 'flex';
                discountAmount.textContent = `-${voucher.discount} đ`;
                totalAmount.textContent = `${voucher.total} đ`;
            }
        }

        function setupFormValidation() {
            const form = document.getElementById('checkoutForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function (e) {
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
                    submitBtn.disabled = true;
                });
            }
        }
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setupVoucherHandler();
            setupFormValidation();
            setupVoucherCards();
            setupVoucherToggle();
            setupRemoveVoucher();

            // Auto-apply pricing if voucher is already applied
            @if(isset($bestVoucherApplied) && $bestVoucherApplied)
                const autoVoucherData = {
                    code: '{{ $bestVoucherApplied["code"] }}',
                    discount: '{{ $bestVoucherApplied["discount"] }}',
                    total: '{{ $bestVoucherApplied["total"] }}',
                    discount_value: {{ $bestVoucherApplied["discount_value"] }},
                    total_value: {{ $bestVoucherApplied["total_value"] }}
                };
                updatePricing(autoVoucherData);
                appliedVoucher = autoVoucherData;
            @endif
        });

        // Global variables
        let subtotal = {{ $subtotal }};
        let appliedVoucher = null;

        function setupVoucherCards() {
            const voucherCards = document.querySelectorAll('.voucher-card');

            voucherCards.forEach(card => {
                const minOrder = parseFloat(card.dataset.minOrder) || 0;
                const applyBtn = card.querySelector('.apply-voucher-btn');

                // Check if voucher can be applied
                if (minOrder > 0 && subtotal < minOrder) {
                    card.classList.add('disabled');
                    applyBtn.disabled = true;
                    applyBtn.textContent = 'Không đủ điều kiện';
                } else {
                    // Add click handler
                    applyBtn.addEventListener('click', () => {
                        const code = card.dataset.code;
                        applyVoucherByCode(code, card);
                    });
                }
            });
        }

        function setupVoucherToggle() {
            const toggleBtn = document.getElementById('toggleVouchersBtn');
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleText = document.getElementById('toggleText');
            const vouchersCollapse = document.getElementById('vouchersCollapse');

            if (toggleBtn && vouchersCollapse) {
                vouchersCollapse.addEventListener('shown.bs.collapse', function () {
                    toggleIcon.className = 'fa fa-chevron-up';
                    toggleText.textContent = 'Thu gọn';
                });

                vouchersCollapse.addEventListener('hidden.bs.collapse', function () {
                    toggleIcon.className = 'fa fa-chevron-down';
                    toggleText.textContent = 'Hiển thị';
                });
            }
        }

        function setupRemoveVoucher() {
            const removeAutoBtn = document.getElementById('removeAutoVoucherBtn');
            const removeManualBtn = document.getElementById('removeManualVoucherBtn');

            if (removeAutoBtn) {
                removeAutoBtn.addEventListener('click', function () {
                    clearVoucherSelection();
                    hideAutoAppliedVoucher();
                    resetPricing();
                    showVoucherMessage('Đã bỏ mã giảm giá tự động', 'info');

                    // Show voucher list if hidden
                    const vouchersCollapse = document.getElementById('vouchersCollapse');
                    if (vouchersCollapse && !vouchersCollapse.classList.contains('show')) {
                        new bootstrap.Collapse(vouchersCollapse, { show: true });
                    }
                });
            }

            if (removeManualBtn) {
                removeManualBtn.addEventListener('click', function () {
                    clearVoucherSelection();
                    hideManualAppliedVoucher();
                    resetPricing();
                    showVoucherMessage('Đã bỏ mã giảm giá', 'info');
                });
            }
        }

        function hideAutoAppliedVoucher() {
            const appliedSection = document.getElementById('autoAppliedVoucherSection');
            if (appliedSection) {
                appliedSection.style.display = 'none';
            }
        }

        function hideManualAppliedVoucher() {
            const appliedSection = document.getElementById('manualAppliedVoucherSection');
            if (appliedSection) {
                appliedSection.style.display = 'none';
            }
        }

        function resetPricing() {
            updatePricing(null);

            // Clear voucher input
            const voucherInput = document.getElementById('voucherInput');
            if (voucherInput) voucherInput.value = '';

            appliedVoucher = null;
        }

        function applyVoucherByCode(code, cardElement = null) {
            const voucherInput = document.getElementById('voucherInput');
            if (voucherInput) {
                voucherInput.value = code;
            }

            // Highlight selected card
            if (cardElement) {
                document.querySelectorAll('.voucher-card').forEach(c => c.classList.remove('selected'));
                cardElement.classList.add('selected');
            }

            // Call existing apply voucher function
            applyVoucher(code);
        }

        function applyVoucher(code) {
            const messageDiv = document.getElementById('voucherMessage');

            showVoucherMessage('Đang áp dụng mã giảm giá...', 'info');

            fetch('{{ route("checkout.apply-voucher") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    voucher_code: code
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showVoucherMessage(data.message, 'success');
                        updatePricing(data.voucher);
                        showAppliedVoucher(data.voucher);
                        appliedVoucher = data.voucher;
                    } else {
                        showVoucherMessage(data.message, 'danger');
                        clearVoucherSelection();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showVoucherMessage('Có lỗi xảy ra. Vui lòng thử lại.', 'danger');
                    clearVoucherSelection();
                });
        }

        function setupVoucherHandler() {
            const applyBtn = document.getElementById('applyVoucherBtn');
            const voucherInput = document.getElementById('voucherInput');

            if (applyBtn && voucherInput) {
                applyBtn.addEventListener('click', function () {
                    const code = voucherInput.value.trim();

                    if (!code) {
                        showVoucherMessage('Vui lòng nhập mã giảm giá', 'warning');
                        return;
                    }

                    applyVoucher(code);
                });

                // Allow Enter key to apply voucher
                voucherInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyBtn.click();
                    }
                });
            }
        }

        function showAppliedVoucher(voucher) {
            const appliedSection = document.getElementById('manualAppliedVoucherSection');
            const appliedText = document.getElementById('manualAppliedVoucherText');

            if (appliedSection && appliedText) {
                appliedText.textContent = `Đã áp dụng mã ${voucher.code} (giảm ${voucher.discount})`;
                appliedSection.style.display = 'block';
            }
        }

        function clearVoucherSelection() {
            document.querySelectorAll('.voucher-card').forEach(c => c.classList.remove('selected'));
        }

        function clearVoucherMessage() {
            const messageDiv = document.getElementById('voucherMessage');
            if (messageDiv) messageDiv.innerHTML = '';
        }

        function showVoucherMessage(message, type) {
            const messageDiv = document.getElementById('voucherMessage');
            if (messageDiv) {
                const alertClass = {
                    'success': 'alert-success',
                    'danger': 'alert-danger',
                    'warning': 'alert-warning',
                    'info': 'alert-info'
                }[type] || 'alert-info';

                messageDiv.innerHTML = `<div class="alert ${alertClass} alert-sm py-2 px-3 mb-0">${message}</div>`;

                if (type === 'success') {
                    setTimeout(() => {
                        messageDiv.innerHTML = '';
                    }, 5000);
                }
            }
        }

        function updatePricing(voucher) {
            const discountRow = document.getElementById('discountRow');
            const discountAmount = document.getElementById('discountAmount');
            const totalAmount = document.getElementById('totalAmount');

            if (voucher && discountRow && discountAmount && totalAmount) {
                discountRow.style.display = 'flex';
                discountAmount.textContent = `-${voucher.discount}`;
                totalAmount.textContent = `${voucher.total}`;
            } else if (!voucher && discountRow && totalAmount) {
                discountRow.style.display = 'none';
                totalAmount.textContent = `${subtotal.toLocaleString('vi-VN')} đ`;
            }
        }

        function setupFormValidation() {
            const form = document.getElementById('checkoutForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function (e) {
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
                    submitBtn.disabled = true;
                });
            }
        }
    </script>
    <script>
        (function() {
            const btnGeo = document.getElementById('btnGeoAddress');
            const address = document.getElementById('customer_address');
            const statusEl = document.getElementById('geoStatus');
            const latEl = document.getElementById('customer_lat');
            const lngEl = document.getElementById('customer_lng');

            if (!btnGeo) return;

            btnGeo.addEventListener('click', async function () {
            if (!navigator.geolocation) {
                showGeoStatus('Trình duyệt của bạn không hỗ trợ định vị.', 'danger');
                return;
            }

            setBusy(true, 'Đang lấy vị trí...');

            const options = { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 };

            navigator.geolocation.getCurrentPosition(async (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                // Lưu lat/lng ẩn
                if (latEl) latEl.value = lat;
                if (lngEl) lngEl.value = lng;

                try {
                    // Reverse geocoding tiếng Việt
                    const url =
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=vi&addressdetails=1`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json().catch(() => ({}));

                    const display = data?.display_name;
                    if (display) {
                        address.value = display;
                        showGeoStatus('Đã điền địa chỉ hiện tại của bạn.', 'success');
                        address.focus();
                    } else {
                        address.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        showGeoStatus('Không lấy được tên địa chỉ. Đã điền tọa độ (lat, lng).', 'warning');
                    }
                } catch (e) {
                    address.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    showGeoStatus('Không thể gọi dịch vụ bản đồ. Đã điền tọa độ (lat, lng).', 'warning');
                } finally {
                    setBusy(false);
                }
            }, (err) => {
                const msg = {
                    1: 'Bạn đã từ chối quyền truy cập vị trí.',
                    2: 'Không lấy được vị trí hiện tại.',
                    3: 'Quá thời gian chờ khi lấy vị trí.'
                }[err.code] || 'Không thể lấy vị trí.';
                showGeoStatus(msg, 'danger');
                setBusy(false);
            }, options);
        });

        function setBusy(busy, text) {
            if (busy) {
                btnGeo.disabled = true;
                btnGeo.dataset.oldText = btnGeo.innerHTML;
                btnGeo.innerHTML = `<i class="fa fa-spinner fa-spin"></i> ${text || 'Đang xử lý...'}`;
            } else {
                btnGeo.disabled = false;
                btnGeo.innerHTML = btnGeo.dataset.oldText || 'Lấy vị trí hiện tại';
            }
        }

        function showGeoStatus(message, type = 'muted') {
            if (!statusEl) return;
            const map = {
                success: 'text-success',
                danger: 'text-danger',
                warning: 'text-warning',
                muted: 'text-muted'
            };
            statusEl.className = `d-block mt-1 ${map[type] || 'text-muted'}`;
            statusEl.textContent = message || '';
        }
        })();
    </script>
@endsection
@extends('client.layouts.master')

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
                        <span>TAFOOD</span>
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

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif



            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                @csrf
                <input type="hidden" name="selected_items" value="{{ request('items') }}">
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
                                        <label for="customer_address" class="mb-0">
                                            Địa chỉ giao hàng *
                                        </label>
                                        <button type="button" id="btnGeoAddress" class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 3px 10px;">
                                            <i class="bi bi-crosshair"></i> Lấy vị trí hiện tại
                                        </button>
                                    </div>

                                    {{-- Địa chỉ đã lưu từ DB - tabs chọn nhanh --}}
                                    @php
                                        $user = Auth::user();
                                        $savedAddresses = [
                                            'nha_rieng'  => ['label' => 'Nhà riêng',  'icon' => '🏠', 'value' => $user->DiaChiNhaRieng],
                                            'van_phong'  => ['label' => 'Văn phòng',  'icon' => '🏢', 'value' => $user->DiaChiVanPhong],
                                            'truong_hoc' => ['label' => 'Trường học', 'icon' => '🏫', 'value' => $user->DiaChiTruongHoc],
                                        ];
                                        $hasSavedAddress = collect($savedAddresses)->filter(fn($a) => !empty($a['value']))->isNotEmpty();
                                        // Default address: DiaChi (profile default)
                                        $defaultAddress = old('customer_info.address', $user->DiaChi ?? '');
                                    @endphp

                                    @if($hasSavedAddress)
                                    <div class="d-flex align-items-center mb-2 flex-wrap" style="gap: 8px;">
                                        <span class="text-muted" style="font-size: 11px; font-weight: 600;">Địa chỉ đã lưu:</span>
                                        @if($user->DiaChi)
                                        <button type="button" class="btn btn-xs rounded-pill checkout-tag-btn" data-tag="mac_dinh" data-address="{{ $user->DiaChi }}"
                                            style="padding: 3px 12px; font-size: 11px; border: 1px solid rgba(0,0,0,0.15); background: #f8f9fa; color: #495057; transition: all 0.2s; font-weight: 600; outline: none;">
                                            ⭐ Mặc định
                                        </button>
                                        @endif
                                        @foreach($savedAddresses as $key => $addr)
                                            @if(!empty($addr['value']))
                                            <button type="button" class="btn btn-xs rounded-pill checkout-tag-btn" data-tag="{{ $key }}" data-address="{{ $addr['value'] }}"
                                                style="padding: 3px 12px; font-size: 11px; border: 1px solid rgba(0,0,0,0.15); background: #f8f9fa; color: #495057; transition: all 0.2s; font-weight: 600; outline: none;">
                                                {{ $addr['icon'] }} {{ $addr['label'] }}
                                            </button>
                                            @endif
                                        @endforeach
                                    </div>
                                    @endif

                                    <textarea class="form-control @error('customer_info.address') is-invalid @enderror"
                                        id="customer_address" name="customer_info[address]"
                                        rows="3">{{ $defaultAddress }}</textarea>
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
                                                <th class="col-price">Giá</th>
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
                                                    <td class="col-price">{{ number_format($item['price'], 0, ',', '.') }} đ</td>
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
                                @php
                                    $isCodActive = App\Services\SettingService::check('payment_cod', true);
                                    $hasOnline = App\Services\SettingService::check('payment_momo', true) || 
                                                 App\Services\SettingService::check('payment_zalopay', true) || 
                                                 App\Services\SettingService::check('payment_vnpay', true) || 
                                                 App\Services\SettingService::check('payment_transfer', true);
                                    $defaultMethod = $isCodActive ? 'COD' : ($hasOnline ? 'Online' : '');
                                @endphp
                                <div class="row align-items-stretch">
                                    @if($isCodActive)
                                    <div class="col-md-6 d-flex align-items-stretch mb-3 mb-md-0">
                                        <div class="form-check payment-method w-100">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cod"
                                                value="COD" {{ old('payment_method', $defaultMethod) == 'COD' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cod">
                                                <div class="payment-option">
                                                    <i class="fa fa-money-bill text-success"></i>
                                                    <div style="min-width: 0;">
                                                        <strong style="white-space: nowrap; display: block; overflow: hidden; text-overflow: ellipsis;">Thanh toán khi nhận hàng (COD)</strong>
                                                        <small class="d-block text-muted">Thanh toán bằng tiền mặt khi nhận hàng</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                    @if($hasOnline)
                                    <div class="col-md-6 d-flex align-items-stretch">
                                        <div class="form-check payment-method w-100">
                                            <input class="form-check-input" type="radio" name="payment_method" id="online"
                                                value="Online" {{ old('payment_method', $defaultMethod) == 'Online' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="online">
                                                <div class="payment-option">
                                                    <i class="fa fa-credit-card text-primary"></i>
                                                    <div style="min-width: 0;">
                                                        <strong style="white-space: nowrap; display: block; overflow: hidden; text-overflow: ellipsis;">Thanh toán online</strong>
                                                        <small class="d-block text-muted">Thanh toán qua thẻ ngân hàng/ví điện tử</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                    @if(!$isCodActive && !$hasOnline)
                                    <div class="col-12 text-center text-danger py-2">
                                        <i class="fa fa-exclamation-triangle mr-2"></i> Hiện tại hệ thống tạm thời chưa hỗ trợ phương thức thanh toán nào. Vui lòng quay lại sau!
                                    </div>
                                    @endif
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
                                                                                            {{ $bestVoucherApplied['display_discount'] ?? $bestVoucherApplied['discount'] }})</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center"
                                                id="removeAutoVoucherBtn" style="white-space: nowrap; flex-shrink: 0;">
                                                <i class="fa fa-times mr-1"></i> Bỏ
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Pre-applied Voucher but Not Eligible (Upsell Alert) -->
                                @if(isset($preAppliedError) && $preAppliedError)
                                    <div class="alert alert-warning mb-4 shadow-sm" style="border-left: 4px solid #ffbe33; background: rgba(255, 190, 51, 0.08); color: #856404; border-radius: 8px;">
                                        <div class="d-flex align-items-start">
                                            <i class="fa fa-exclamation-triangle mt-1 mr-2" style="color: #ffbe33; font-size: 16px;"></i>
                                            <div>
                                                <strong style="color: #664d03; font-size: 14px;">Mã {{ $preAppliedError['code'] }} chưa đủ điều kiện!</strong>
                                                <div style="font-size: 12.5px; margin-top: 4px; line-height: 1.4;">
                                                    Bạn đã lưu mã giảm <strong>{{ $preAppliedError['discount_desc'] }}</strong> cho đơn tối thiểu <strong>{{ number_format($preAppliedError['min_order'], 0, ',', '.') }}đ</strong>.
                                                    <br>
                                                    Đơn hàng hiện tại là <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>, cần thêm <strong class="text-danger">{{ number_format($preAppliedError['diff'], 0, ',', '.') }}đ</strong> để được áp dụng giảm giá.
                                                </div>
                                                <div style="margin-top: 8px;">
                                                    <a href="{{ route('foods.index') }}#food-menu-section" class="btn btn-sm btn-warning text-dark font-weight-bold" style="background: #ffbe33; border-color: #ffbe33; border-radius: 20px; font-size: 11.5px; padding: 4px 12px;">
                                                        <i class="fa fa-plus-circle"></i> Tiếp tục chọn món
                                                    </a>
                                                </div>
                                            </div>
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
                                            id="toggleVouchersBtn">
                                            <i class="fa {{ isset($bestVoucherApplied) && $bestVoucherApplied ? 'fa-chevron-down' : 'fa-chevron-up' }}" id="toggleIcon"></i>
                                            <span
                                                id="toggleText">{{ isset($bestVoucherApplied) && $bestVoucherApplied ? 'Hiển thị' : 'Thu gọn' }}</span>
                                        </button>
                                    </div>

                                    <div class="collapse {{ isset($bestVoucherApplied) && $bestVoucherApplied ? '' : 'show' }}"
                                        id="vouchersCollapse">

                                        @if($vouchers && count($vouchers) > 0)
                                            <div class="vouchers-grid" id="vouchersGrid">
                                                @foreach($vouchers as $voucher)
                                                    @php
                                                        $isCurrentlyApplied = isset($bestVoucherApplied) && $bestVoucherApplied && $bestVoucherApplied['code'] === $voucher->MaCode;
                                                        $isSuggested = isset($suggestedBestVoucher) && $suggestedBestVoucher && $suggestedBestVoucher->MaCode === $voucher->MaCode;
                                                    @endphp
                                                    <div class="voucher-card {{ $isCurrentlyApplied ? 'selected' : '' }}" data-code="{{ $voucher->MaCode }}"
                                                        data-percent="{{ $voucher->PhanTram }}"
                                                        data-min-order="{{ $voucher->DonHangToiThieu ?? 0 }}">
                                                        <div class="voucher-header">
                                                            <div class="voucher-icon">
                                                                <i class="fa {{ $voucher->LoaiGiamGia === 'TienMat' ? 'fa-money-bill' : 'fa-percent' }}"></i>
                                                            </div>
                                                            <div class="voucher-info">
                                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                                    <div class="voucher-code fw-bold">{{ $voucher->MaCode }}</div>
                                                                    @if($isSuggested)
                                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 10px; font-weight: 700; background: #e8f5e9; color: #2e7d32 !important; border: 1px solid #c8e6c9 !important;">
                                                                            <i class="fa fa-thumbs-up me-1"></i> Tốt nhất
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="voucher-desc">
                                                                    Giảm {{ $voucher->LoaiGiamGia === 'TienMat' ? number_format($voucher->GiamToiDa, 0, ',', '.') . 'đ' : ((float)$voucher->PhanTram) . '%' }}
                                                                    @if($voucher->LoaiGiamGia === 'PhanTram' && $voucher->GiamToiDa > 0)
                                                                        (Tối đa {{ number_format($voucher->GiamToiDa, 0, ',', '.') }}đ)
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="voucher-action">
                                                                @if($isCurrentlyApplied)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-success apply-voucher-btn fw-bold d-flex align-items-center justify-content-center" disabled>
                                                                        <i class="fa fa-check" style="margin-right: 6px;"></i> Đang áp dụng
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary apply-voucher-btn d-flex align-items-center justify-content-center">
                                                                        <span style="margin: auto;">Áp dụng</span>
                                                                    </button>
                                                                @endif
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
                                            <!-- bỏ voucher -->
                                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center"
                                                id="removeManualVoucherBtn" style="white-space: nowrap; flex-shrink: 0;">
                                                <i class="fa fa-times mr-1"></i> Bỏ
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
                                        <span class="text-success font-weight-bold" id="shippingAmount">Miễn phí</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between total-amount">
                                        <strong>Tổng cộng:</strong>
                                        <strong class="text-danger"
                                            id="totalAmount">{{ isset($bestVoucherApplied) && $bestVoucherApplied ? $bestVoucherApplied['total'] : number_format($subtotal, 0, ',', '.') . ' đ' }}</strong>
                                    </div>
                                </div>
                                        <!-- nút đặt hàng -->   
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/checkout-validator.js') }}?v={{ time() }}"></script>
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
            margin-bottom: 0;
            padding-left: 0 !important;
            height: 100%;
        }

        .payment-method input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .payment-method label {
            width: 100%;
            display: block;
            margin-bottom: 0;
            cursor: pointer;
            height: 100%;
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
            height: 100%;
        }

        .payment-option::before {
            content: "";
            width: 18px;
            height: 18px;
            border: 2px solid #adb5bd;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .payment-method input:checked+label .payment-option {
            border-color: #ffbe33;
            background: rgba(255, 190, 51, 0.08);
        }

        .payment-method input:checked+label .payment-option::before {
            border-color: #ffbe33;
            background: radial-gradient(circle, #ffbe33 45%, transparent 55%);
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
                font-size: 13px;
                overflow-x: hidden; /* Không cuộn ngang */
            }
            
            /* Tối ưu bảng trên Mobile: Ẩn cột Giá, dồn không gian cho Món ăn và Thành tiền */
            .table th.col-price, .table td.col-price {
                display: none;
            }
            
            .table th:nth-child(1), .table td:nth-child(1) { width: 55%; white-space: normal; } /* Cột món ăn */
            .table th:nth-child(3), .table td:nth-child(3) { width: 15%; text-align: center; }  /* Cột số lượng */
            .table th:nth-child(4), .table td:nth-child(4) { width: 30%; text-align: right; }   /* Cột thành tiền */
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
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15);
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
                flex-wrap: wrap;
            }

            .voucher-action {
                margin-left: 0;
                width: 100%;
                margin-top: 5px;
            }

            .apply-voucher-btn {
                width: 100%;
            }
        }

        /* Suggestions list dropdown styling */
        .address-suggestions-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 5px;
            display: none;
            padding: 5px 0;
        }
        
        .address-suggestion-item {
            padding: 10px 15px;
            color: #333333;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .address-suggestion-item:last-child {
            border-bottom: none;
        }

        .address-suggestion-item:hover {
            background: #f8f9fa;
            color: #ffbe33;
        }

        .address-suggestion-item i {
            color: #ffbe33;
            font-size: 14px;
            flex-shrink: 0;
        }
    </style>

    {{--
    <script>
        const subtotal = {{ $subtotal }};
        let currentShippingFee = 0;
        let appliedVoucher = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Read coordinates from localStorage on load and recalculate
            const savedLat = localStorage.getItem("ta_food_delivery_lat");
            const savedLng = localStorage.getItem("ta_food_delivery_lng");
            const latField = document.getElementById('customer_lat');
            const lngField = document.getElementById('customer_lng');
            if (latField && savedLat && !latField.value) latField.value = savedLat;
            if (lngField && savedLng && !lngField.value) lngField.value = savedLng;
            
            recalculateShipping();

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
        const subtotal = {{ $subtotal }};
        let currentShippingFee = 0;
        let appliedVoucher = null;

        document.addEventListener('DOMContentLoaded', async function () {
            const addressInput = document.getElementById("customer_address");
            const latField = document.getElementById('customer_lat');
            const lngField = document.getElementById('customer_lng');
            const GOONG_API_KEY = @json(env('GOONG_API_KEY'));

            // Ensure relative positioning on parent for absolute suggestions container
            if (addressInput && addressInput.parentNode) {
                addressInput.parentNode.style.position = "relative";
            }

            async function geocodeAddressQuery(q) {
                if (GOONG_API_KEY && GOONG_API_KEY.trim() !== '' && !GOONG_API_KEY.includes('GOONG_API_KEY')) {
                    try {
                        const response = await fetch(`https://rsapi.goong.io/geocode?address=${encodeURIComponent(q)}&api_key=${GOONG_API_KEY}`);
                        const data = await response.json();
                        if (data && data.results && data.results.length > 0) {
                            const location = data.results[0].geometry.location;
                            return {
                                lat: parseFloat(location.lat),
                                lng: parseFloat(location.lng),
                                success: true
                            };
                        }
                    } catch (e) {
                        console.warn("Goong Geocoding failed, falling back to Nominatim:", e);
                    }
                }

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1&countrycodes=vn`);
                    const data = await response.json();
                    if (data && data.length > 0) {
                        return {
                            lat: parseFloat(data[0].lat),
                            lng: parseFloat(data[0].lon),
                            success: true
                        };
                    }
                } catch (e) {
                    console.error("Nominatim Geocoding failed:", e);
                }

                return { success: false };
            }

            // 1. Address tabs quick select (DB-driven, server-side rendered)
            const tagButtons = document.querySelectorAll(".checkout-tag-btn");

            // Highlight the active button whose address matches current textarea
            function highlightActiveTag(activeTag = null) {
                if (tagButtons) {
                    tagButtons.forEach(btn => {
                        const tag = btn.getAttribute("data-tag");
                        if (tag === activeTag) {
                            btn.style.background = "#ffbe33";
                            btn.style.color = "#1e293b";
                            btn.style.borderColor = "#ffbe33";
                            btn.style.boxShadow = "0 2px 6px rgba(255,190,51,0.3)";
                            btn.style.fontWeight = "700";
                        } else {
                            btn.style.background = "#f8f9fa";
                            btn.style.color = "#495057";
                            btn.style.borderColor = "rgba(0,0,0,0.15)";
                            btn.style.boxShadow = "none";
                        }
                    });
                }
            }

            // Auto-highlight the tab matching the current address in textarea
            function autoHighlightFromValue(val) {
                let matchedTag = null;
                if (tagButtons && val.trim()) {
                    tagButtons.forEach(btn => {
                        const addr = btn.getAttribute("data-address") || "";
                        if (addr.trim() === val.trim()) {
                            matchedTag = btn.getAttribute("data-tag");
                        }
                    });
                }
                highlightActiveTag(matchedTag);
            }

            // Click handler for each tab button
            if (tagButtons) {
                tagButtons.forEach(btn => {
                    btn.addEventListener("click", function(e) {
                        e.preventDefault();
                        const tag = this.getAttribute("data-tag");
                        const addr = this.getAttribute("data-address") || "";

                        if (addr && addressInput) {
                            addressInput.value = addr;
                            if (latField) latField.value = "";
                            if (lngField) lngField.value = "";

                            highlightActiveTag(tag);

                            // Geocode the selected address and recalculate shipping
                            const statusEl = document.getElementById('geoStatus');
                            if (statusEl) {
                                statusEl.className = 'd-block mt-1 text-info';
                                statusEl.textContent = 'Đang xác định tọa độ...';
                            }
                            geocodeAddressQuery(addr)
                                .then(res => {
                                    if (res.success) {
                                        if (latField) latField.value = res.lat;
                                        if (lngField) lngField.value = res.lng;
                                        if (statusEl) {
                                            statusEl.className = 'd-block mt-1 text-success font-weight-bold';
                                            statusEl.textContent = 'Định vị địa chỉ thành công!';
                                        }
                                        if (window.recalculateShipping) window.recalculateShipping(res.lat, res.lng);
                                    } else {
                                        if (statusEl) {
                                            statusEl.className = 'd-block mt-1 text-warning';
                                            statusEl.textContent = 'Không tìm thấy tọa độ. Phí ship mặc định được áp dụng.';
                                        }
                                        if (window.recalculateShipping) window.recalculateShipping('', '');
                                    }
                                })
                                .catch(() => {
                                    if (window.recalculateShipping) window.recalculateShipping('', '');
                                });
                        }
                    });
                });
            }

            // Auto-highlight matching tab on page load
            if (addressInput && addressInput.value.trim()) {
                autoHighlightFromValue(addressInput.value);
            }

            // Re-check highlight as user types
            if (addressInput) {
                addressInput.addEventListener("input", function() {
                    autoHighlightFromValue(this.value);
                });
            }

            // 2. Resolve coordinates and calculate shipping fee
            if (addressInput && addressInput.value.trim()) {
                const currentAddress = addressInput.value.trim();
                const gpsAuthorized = localStorage.getItem("ta_food_gps_authorized") === "true";

                // If coordinates fields are empty, find them
                if (latField && lngField && (!latField.value || !lngField.value)) {
                    const savedAddress = localStorage.getItem("ta_food_delivery_address");
                    const savedLat = localStorage.getItem("ta_food_delivery_lat");
                    const savedLng = localStorage.getItem("ta_food_delivery_lng");

                    // Fall back to saved coordinates ONLY if the address matches the saved homepage address
                    if (gpsAuthorized && savedAddress === currentAddress && savedLat && savedLng) {
                        latField.value = savedLat;
                        lngField.value = savedLng;
                        recalculateShipping(savedLat, savedLng);
                    } else {
                        // Otherwise, geocode the address dynamically (e.g. user profile default address)
                        const statusEl = document.getElementById('geoStatus');
                        if (statusEl) {
                            statusEl.className = 'd-block mt-1 text-info';
                            statusEl.textContent = 'Đang xác định tọa độ địa chỉ giao hàng...';
                        }
                        geocodeAddressQuery(currentAddress)
                            .then(res => {
                                if (res.success) {
                                    latField.value = res.lat;
                                    lngField.value = res.lng;
                                    if (statusEl) {
                                        statusEl.className = 'd-block mt-1 text-success';
                                        statusEl.textContent = 'Định vị địa chỉ thành công!';
                                    }
                                    recalculateShipping(res.lat, res.lng);
                                } else {
                                    if (statusEl) {
                                        statusEl.className = 'd-block mt-1 text-warning';
                                        statusEl.textContent = 'Không tìm thấy tọa độ địa chỉ. Phí ship mặc định được áp dụng.';
                                    }
                                    recalculateShipping('', '');
                                }
                            })
                            .catch(err => {
                                console.warn("Dynamic geocoding failed: " + err.message);
                                if (statusEl) {
                                    statusEl.className = 'd-block mt-1 text-warning';
                                    statusEl.textContent = 'Lỗi định vị. Phí ship mặc định được áp dụng.';
                                }
                                recalculateShipping('', '');
                            });
                    }
                } else {
                    recalculateShipping();
                }
            } else {
                recalculateShipping();
            }

            // Setup Autocomplete suggestions dropdown for checkout address input
            function setupAutocomplete(inputElement) {
                if (!inputElement) return;

                let suggestionsBox = document.createElement("div");
                suggestionsBox.className = "address-suggestions-list";
                inputElement.parentNode.appendChild(suggestionsBox);

                let autocompleteTimeout = null;

                document.addEventListener("click", function(e) {
                    if (e.target !== inputElement && e.target !== suggestionsBox && !suggestionsBox.contains(e.target)) {
                        suggestionsBox.style.display = "none";
                    }
                });

                inputElement.addEventListener("input", function() {
                    const query = this.value.trim();
                    clearTimeout(autocompleteTimeout);

                    if (query.length < 3) {
                        suggestionsBox.style.display = "none";
                        suggestionsBox.innerHTML = "";
                        return;
                    }

                    autocompleteTimeout = setTimeout(async () => {
                        try {
                            if (GOONG_API_KEY && GOONG_API_KEY.trim() !== '' && !GOONG_API_KEY.includes('GOONG_API_KEY')) {
                                const res = await fetch(`https://rsapi.goong.io/Place/AutoComplete?api_key=${GOONG_API_KEY}&input=${encodeURIComponent(query)}`);
                                const data = await res.json();
                                // Check if input value matches query to avoid race conditions
                                if (inputElement.value.trim() !== query) return;
                                if (data && data.predictions) {
                                    renderGoongSuggestions(data.predictions, suggestionsBox, inputElement);
                                }
                            } else {
                                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=vn&accept-language=vi`);
                                const data = await res.json();
                                // Check if input value matches query to avoid race conditions
                                if (inputElement.value.trim() !== query) return;
                                if (data) {
                                    renderNominatimSuggestions(data, suggestionsBox, inputElement);
                                }
                            }
                        } catch (err) {
                            console.warn("Autocomplete error:", err);
                        }
                    }, 350); // Lowered to 350ms for snappier suggestions
                });

                inputElement.addEventListener("focus", function() {
                    if (this.value.trim().length >= 3 && suggestionsBox.children.length > 0) {
                        suggestionsBox.style.display = "block";
                    }
                });
            }

            function renderNominatimSuggestions(results, box, input) {
                box.innerHTML = "";
                if (results.length === 0) {
                    box.style.display = "none";
                    return;
                }

                results.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "address-suggestion-item";
                    div.innerHTML = `<i class="fa fa-map-marker-alt"></i> <span>${item.display_name}</span>`;
                    
                    div.addEventListener("click", function() {
                        const address = item.display_name;
                        input.value = address;
                        box.style.display = "none";

                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lon);

                        if (latField) latField.value = lat;
                        if (lngField) lngField.value = lng;

                        const statusEl = document.getElementById('geoStatus');
                        if (statusEl) {
                            statusEl.className = 'd-block mt-1 text-success font-weight-bold';
                            statusEl.textContent = 'Định vị địa chỉ thành công!';
                        }

                        recalculateShipping(lat, lng);
                        autoHighlightFromValue(address);
                    });
                    box.appendChild(div);
                });
                box.style.display = "block";
            }

            async function renderGoongSuggestions(predictions, box, input) {
                box.innerHTML = "";
                if (predictions.length === 0) {
                    box.style.display = "none";
                    return;
                }

                predictions.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "address-suggestion-item";
                    div.innerHTML = `<i class="fa fa-map-marker-alt"></i> <span>${item.description}</span>`;
                    
                    div.addEventListener("click", async function() {
                        const address = item.description;
                        input.value = address;
                        box.style.display = "none";

                        try {
                            const detailRes = await fetch(`https://rsapi.goong.io/Place/Detail?api_key=${GOONG_API_KEY}&place_id=${item.place_id}`);
                            const detailData = await detailRes.json();
                            if (detailData && detailData.result && detailData.result.geometry) {
                                const lat = detailData.result.geometry.location.lat;
                                const lng = detailData.result.geometry.location.lng;
                                if (latField) latField.value = lat;
                                if (lngField) lngField.value = lng;

                                const statusEl = document.getElementById('geoStatus');
                                if (statusEl) {
                                    statusEl.className = 'd-block mt-1 text-success font-weight-bold';
                                    statusEl.textContent = 'Định vị địa chỉ thành công!';
                                }

                                recalculateShipping(lat, lng);
                                autoHighlightFromValue(address);
                            }
                        } catch (err) {
                            console.error("Error fetching Goong place details:", err);
                        }
                    });
                    box.appendChild(div);
                });
                box.style.display = "block";
            }

            setupAutocomplete(addressInput);

            setupVoucherHandler();
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
                
                // Mark the auto-applied voucher card as selected and update buttons
                updateVoucherCardsUI('{{ $bestVoucherApplied["code"] }}');
            @else
                updateVoucherCardsUI(null);
            @endif
        });

        // Global variables - already declared above
        // subtotal and appliedVoucher are declared at the top of the script tag to avoid redeclaration syntax error
        // <! tu dong goi ham -->
        function setupVoucherCards() {
            const voucherCards = document.querySelectorAll('.voucher-card');

            voucherCards.forEach(card => {
                const applyBtn = card.querySelector('.apply-voucher-btn');
                if (applyBtn) {
                    applyBtn.addEventListener('click', () => {
                        const code = card.dataset.code;
                        applyVoucherByCode(code, card);
                    });
                }
            });
        }

        function updateVoucherCardsUI(appliedCode) {
            const voucherCards = document.querySelectorAll('.voucher-card');
            voucherCards.forEach(card => {
                const code = card.dataset.code;
                const applyBtn = card.querySelector('.apply-voucher-btn');
                const minOrder = parseFloat(card.dataset.minOrder) || 0;

                if (minOrder > 0 && subtotal < minOrder) {
                    card.classList.add('disabled');
                    card.classList.remove('selected');
                    if (applyBtn) {
                        applyBtn.disabled = true;
                        applyBtn.className = 'btn btn-sm btn-secondary apply-voucher-btn';
                        applyBtn.innerHTML = 'Không đủ điều kiện';
                    }
                } else if (code === appliedCode) {
                    card.classList.add('selected');
                    card.classList.remove('disabled');
                    if (applyBtn) {
                        applyBtn.disabled = true;
                        applyBtn.className = 'btn btn-sm btn-success apply-voucher-btn fw-bold d-flex align-items-center gap-1';
                        applyBtn.innerHTML = '<i class="fa fa-check"></i> Đang áp dụng';
                    }
                } else {
                    card.classList.remove('selected');
                    card.classList.remove('disabled');
                    if (applyBtn) {
                        applyBtn.disabled = false;
                        applyBtn.className = 'btn btn-sm btn-primary apply-voucher-btn';
                        applyBtn.innerHTML = 'Áp dụng';
                    }
                }
            });
        }

        function setupVoucherToggle() {
            const toggleBtn = document.getElementById('toggleVouchersBtn');
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleText = document.getElementById('toggleText');
            const vouchersCollapse = document.getElementById('vouchersCollapse');

            if (!toggleBtn || !vouchersCollapse) return;

            // Manual toggle on button click
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const isShown = vouchersCollapse.classList.contains('show');
                
                if (isShown) {
                    // Hide vouchers
                    vouchersCollapse.classList.remove('show');
                    toggleIcon.className = 'fa fa-chevron-down';
                    toggleText.textContent = 'Hiển thị';
                } else {
                    // Show vouchers
                    vouchersCollapse.classList.add('show');
                    toggleIcon.className = 'fa fa-chevron-up';
                    toggleText.textContent = 'Thu gọn';
                }
            });
        }

        function setupRemoveVoucher() {
            const removeAutoBtn = document.getElementById('removeAutoVoucherBtn');
            const removeManualBtn = document.getElementById('removeManualVoucherBtn');

            if (removeAutoBtn) {
                removeAutoBtn.addEventListener('click', function () {
                    clearVoucherSelection();
                    hideAutoAppliedVoucher();
                    resetPricing();
                    updateVoucherCardsUI(null);
                    showVoucherMessage('Đã bỏ mã giảm giá tự động', 'info');

                    // Show voucher list if hidden
                    const vouchersCollapse = document.getElementById('vouchersCollapse');
                    const toggleIcon = document.getElementById('toggleIcon');
                    const toggleText = document.getElementById('toggleText');
                    
                    if (vouchersCollapse && !vouchersCollapse.classList.contains('show')) {
                        vouchersCollapse.classList.add('show');
                        if (toggleIcon) toggleIcon.className = 'fa fa-chevron-up';
                        if (toggleText) toggleText.textContent = 'Thu gọn';
                    }
                });
            }

            if (removeManualBtn) {
                removeManualBtn.addEventListener('click', function () {
                    clearVoucherSelection();
                    hideManualAppliedVoucher();
                    
                    // Check if there's an auto-applied voucher to restore
                    @if(isset($bestVoucherApplied) && $bestVoucherApplied)
                        const autoSection = document.getElementById('autoAppliedVoucherSection');
                        if (autoSection) {
                            autoSection.style.display = 'block';
                            
                            // Restore auto voucher data
                            const autoVoucherData = {
                                code: '{{ $bestVoucherApplied["code"] }}',
                                discount: '{{ $bestVoucherApplied["discount"] }}',
                                total: '{{ $bestVoucherApplied["total"] }}',
                                discount_value: {{ $bestVoucherApplied["discount_value"] }},
                                total_value: {{ $bestVoucherApplied["total_value"] }}
                            };
                            updatePricing(autoVoucherData);
                            appliedVoucher = autoVoucherData;
                            
                            // Update cards UI
                            updateVoucherCardsUI('{{ $bestVoucherApplied["code"] }}');
                            
                            showVoucherMessage('Đã khôi phục mã giảm giá tự động', 'info');
                        }
                    @else
                        resetPricing();
                        updateVoucherCardsUI(null);
                        showVoucherMessage('Đã bỏ mã giảm giá', 'info');
                    @endif
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
        //bo giam gia
        function resetPricing() {
            updatePricing(null);

            // Clear voucher input
            const voucherInput = document.getElementById('voucherInput');
            if (voucherInput) voucherInput.value = '';

            appliedVoucher = null;

            // Xóa mã giảm giá khỏi session trên máy chủ
            fetch('{{ route("checkout.remove-voucher") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                console.log('Voucher cleared from session:', data.message);
            })
            .catch(e => console.error('Failed to clear voucher session:', e));
        }
        // ham ap dung ma giam gia
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
        // ham ap dung ma giam gia
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
                        
                        // Hide auto-applied section when applying new voucher manually
                        hideAutoAppliedVoucher();
                        
                        // Show manual applied section
                        showAppliedVoucher(data.voucher);
                        appliedVoucher = data.voucher;

                        // Update voucher cards buttons and selection styles
                        updateVoucherCardsUI(data.voucher.code);
                        
                        // Auto collapse voucher list after applying
                        const vouchersCollapse = document.getElementById('vouchersCollapse');
                        const toggleIcon = document.getElementById('toggleIcon');
                        const toggleText = document.getElementById('toggleText');
                        
                        if (vouchersCollapse && vouchersCollapse.classList.contains('show')) {
                            vouchersCollapse.classList.remove('show');
                            if (toggleIcon) toggleIcon.className = 'fa fa-chevron-down';
                            if (toggleText) toggleText.textContent = 'Hiển thị';
                        }
                    } else {
                        showVoucherMessage(data.message, 'danger');
                        clearVoucherSelection();
                        updateVoucherCardsUI(null);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showVoucherMessage('Có lỗi xảy ra. Vui lòng thử lại.', 'danger');
                    clearVoucherSelection();
                    updateVoucherCardsUI(null);
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
                const display = voucher.display_discount || voucher.discount;
                appliedText.textContent = `Đã áp dụng mã ${voucher.code} (giảm ${display})`;
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
            const customerAddress = document.getElementById('customer_address')?.value.trim();
            const submitBtn = document.getElementById('submitBtn');
            const isAddressEmpty = (!customerAddress || customerAddress.length < 5);

            let discountVal = 0;
            if (voucher) {
                appliedVoucher = voucher;
                
                if (voucher.discount_value !== undefined) {
                    discountVal = parseFloat(voucher.discount_value);
                } else {
                    let discountClean = voucher.discount.toString().replace(/[^0-9.-]/g, '').trim();
                    discountVal = parseFloat(discountClean) || 0;
                }
                
                if (discountRow && discountAmount) {
                    discountRow.style.cssText = 'display: flex !important;';
                    discountAmount.textContent = `-${discountVal.toLocaleString('vi-VN')} đ`;
                }
            } else {
                appliedVoucher = null;
                if (discountRow) {
                    discountRow.style.cssText = 'display: none !important;';
                }
            }

            // Cập nhật text Phí vận chuyển hiển thị
            const shippingAmount = document.getElementById('shippingAmount');
            if (shippingAmount) {
                if (isAddressEmpty) {
                    shippingAmount.textContent = 'Vui lòng nhập địa chỉ';
                    shippingAmount.className = 'text-muted font-italic';
                } else {
                    if (currentShippingFee > 0) {
                        shippingAmount.textContent = `${currentShippingFee.toLocaleString('vi-VN')} đ`;
                        shippingAmount.className = 'text-danger font-weight-bold';
                    } else {
                        shippingAmount.textContent = 'Miễn phí';
                        shippingAmount.className = 'text-success font-weight-bold';
                    }
                }
            }

            // Tính tổng cộng = subtotal - discountVal + currentShippingFee
            const finalShippingFee = isAddressEmpty ? 0 : currentShippingFee;
            const finalTotal = Math.max(0, subtotal - discountVal + finalShippingFee);
            if (totalAmount) {
                totalAmount.textContent = `${finalTotal.toLocaleString('vi-VN')} đ`;
            }
        }

        function recalculateShipping(lat = null, lng = null) {
            const customerAddress = document.getElementById('customer_address')?.value.trim();
            if (!customerAddress || customerAddress.length < 5) {
                currentShippingFee = 0;
                updatePricing(appliedVoucher);
                return;
            }

            const customerLat = lat || document.getElementById('customer_lat')?.value;
            const customerLng = lng || document.getElementById('customer_lng')?.value;

            if (!customerLat || !customerLng) {
                const savedLat = localStorage.getItem("ta_food_delivery_lat");
                const savedLng = localStorage.getItem("ta_food_delivery_lng");
                if (savedLat && savedLng) {
                    const latField = document.getElementById('customer_lat');
                    const lngField = document.getElementById('customer_lng');
                    if (latField) latField.value = savedLat;
                    if (lngField) lngField.value = savedLng;
                    recalculateShipping(savedLat, savedLng);
                    return;
                }
            }

            fetch('{{ route("checkout.calculate-shipping") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    lat: customerLat || '',
                    lng: customerLng || ''
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentShippingFee = parseFloat(data.shipping_fee) || 0;
                    updatePricing(appliedVoucher);
                }
            })
            .catch(e => console.error('Failed to calculate shipping fee:', e));
        }
        window.recalculateShipping = recalculateShipping;
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
                
                localStorage.setItem("ta_food_delivery_lat", lat);
                localStorage.setItem("ta_food_delivery_lng", lng);
                localStorage.setItem("ta_food_gps_authorized", "true");

                // Destroy tag badge when geolocating a new address on checkout
                const badge = document.querySelector(".address-tag-badge");
                if (badge) badge.remove();
                localStorage.removeItem("ta_food_delivery_tag");

                try {
                    // Reverse geocoding tiếng Việt
                    const url =
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=vi&addressdetails=1`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json().catch(() => ({}));

                    const display = data?.display_name;
                    if (display) {
                        address.value = display;
                        localStorage.setItem("ta_food_delivery_address", display);
                        showGeoStatus('Đã điền địa chỉ hiện tại của bạn.', 'success');
                        address.focus();
                    } else {
                        address.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        localStorage.setItem("ta_food_delivery_address", address.value);
                        showGeoStatus('Không lấy được tên địa chỉ. Đã điền tọa độ (lat, lng).', 'warning');
                    }
                    
                    if (window.recalculateShipping) {
                        window.recalculateShipping(lat, lng);
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

        // Live manual address typing geocoder to update shipping dynamically
        let geocodeTimeout = null;
        if (address) {
            address.addEventListener('input', function() {
                // Immediate active badge destruction on manual edits
                const badge = document.querySelector(".address-tag-badge");
                if (badge) badge.remove();
                localStorage.removeItem("ta_food_delivery_tag");

                clearTimeout(geocodeTimeout);
                geocodeTimeout = setTimeout(async () => {
                    const query = address.value.trim();
                    if (query.length < 5) {
                        // Clear coordinate inputs if address is cleared/too short
                        if (latEl) latEl.value = '';
                        if (lngEl) lngEl.value = '';
                        localStorage.removeItem("ta_food_delivery_lat");
                        localStorage.removeItem("ta_food_delivery_lng");
                        localStorage.removeItem("ta_food_delivery_address");
                        showGeoStatus('Vui lòng nhập địa chỉ giao hàng cụ thể để tính phí ship.', 'warning');
                        if (window.recalculateShipping) {
                            window.recalculateShipping('', '');
                        }
                        return;
                    }
                    
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`);
                        const data = await res.json();
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            
                            if (latEl) latEl.value = lat;
                            if (lngEl) lngEl.value = lng;
                            
                            localStorage.setItem("ta_food_delivery_lat", lat);
                            localStorage.setItem("ta_food_delivery_lng", lng);
                            localStorage.setItem("ta_food_delivery_address", query);
                            localStorage.setItem("ta_food_gps_authorized", "true");
                            
                            showGeoStatus('Đã định vị thành công địa chỉ mới của bạn!', 'success');
                            if (window.recalculateShipping) {
                                window.recalculateShipping(lat, lng);
                            }
                        } else {
                            // Geocoding failed
                            if (latEl) latEl.value = '';
                            if (lngEl) lngEl.value = '';
                            localStorage.removeItem("ta_food_delivery_lat");
                            localStorage.removeItem("ta_food_delivery_lng");
                            showGeoStatus('Không tìm thấy tọa độ cho địa chỉ này. Phí ship mặc định sẽ được áp dụng.', 'warning');
                            if (window.recalculateShipping) {
                                window.recalculateShipping('', '');
                            }
                        }
                    } catch (e) {
                        console.warn("Manual address geocoding failed: " + e.message);
                        if (latEl) latEl.value = '';
                        if (lngEl) lngEl.value = '';
                        showGeoStatus('Không thể kết nối bản đồ. Phí ship mặc định được áp dụng.', 'warning');
                    }
                }, 1500);
            });
        }
        })();
    </script>
@endsection
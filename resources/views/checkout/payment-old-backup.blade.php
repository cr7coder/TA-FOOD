@extends('foods.master')

@section('title', 'Thanh toán online')

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
                </nav>
            </div>
        </header>
    </div>

    <section class="payment_section layout_padding">
        <div class="container">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="payment-header text-center mb-4">
                        <h2>Thanh toán đơn hàng #{{ $donHang->MaDonHang }}</h2>
                        <p class="text-muted">Vui lòng chọn phương thức thanh toán</p>
                    </div>

                    <!-- Order Summary -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fa fa-shopping-cart"></i> Thông tin đơn hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Mã đơn hàng:</strong> #{{ $donHang->MaDonHang }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Ngày đặt:</strong>
                                    {{ optional($donHang->NgayDat ?? $donHang->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Tổng tiền cần thanh toán:</strong>
                                <h4 class="text-danger mb-0">{{ $donHang->TongTienFormat }} đ</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fa fa-credit-card"></i> Chọn phương thức thanh toán
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('checkout.process-payment', $donHang->MaDonHang) }}" method="POST"
                                id="paymentForm">
                                @csrf

                                <div class="payment-methods">
                                    <!-- ATM Card -->
                                    <div class="payment-method-item">
                                        <input type="radio" name="payment_method" id="atm" value="atm" checked>
                                        <label for="atm" class="payment-method-label">
                                            <div class="payment-method-content">
                                                <div class="payment-icon">
                                                    <i class="fa fa-credit-card text-primary"></i>
                                                </div>
                                                <div class="payment-info">
                                                    <h6>Thẻ ATM/Debit</h6>
                                                    <small class="text-muted">Thanh toán qua thẻ ATM nội địa</small>
                                                    <div class="bank-logos mt-2">
                                                        <img src="{{ asset('images/banks/vietcombank.png') }}" alt="VCB">
                                                        <img src="{{ asset('images/banks/techcombank.png') }}" alt="TCB">
                                                        <img src="{{ asset('images/banks/bidv.png') }}" alt="BIDV">
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Credit Card -->
                                    <div class="payment-method-item">
                                        <input type="radio" name="payment_method" id="visa" value="visa">
                                        <label for="visa" class="payment-method-label">
                                            <div class="payment-method-content">
                                                <div class="payment-icon">
                                                    <i class="fa fa-credit-card text-warning"></i>
                                                </div>
                                                <div class="payment-info">
                                                    <h6>Thẻ Visa/Master</h6>
                                                    <small class="text-muted">Thanh toán qua thẻ tín dụng quốc tế</small>
                                                    <div class="bank-logos mt-2">
                                                        <img src="{{ asset('images/cards/visa.png') }}" alt="Visa">
                                                        <img src="{{ asset('images/cards/mastercard.png') }}"
                                                            alt="Mastercard">
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- E-Wallet -->
                                    <div class="payment-method-item">
                                        <input type="radio" name="payment_method" id="ewallet" value="ewallet">
                                        <label for="ewallet" class="payment-method-label">
                                            <div class="payment-method-content">
                                                <div class="payment-icon">
                                                    <i class="fa fa-mobile-alt text-success"></i>
                                                </div>
                                                <div class="payment-info">
                                                    <h6>Ví điện tử</h6>
                                                    <small class="text-muted">Thanh toán qua ví điện tử</small>
                                                    <div class="bank-logos mt-2">
                                                        <img src="{{ asset('images/wallets/momo.png') }}" alt="MoMo">
                                                        <img src="{{ asset('images/wallets/zalopay.png') }}" alt="ZaloPay">
                                                        <img src="{{ asset('images/wallets/vnpay.png') }}" alt="VNPay">
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Security Info -->
                                <div class="security-info mt-4">
                                    <div class="alert alert-info">
                                        <h6><i class="fa fa-shield-alt"></i> Bảo mật thanh toán</h6>
                                        <ul class="mb-0">
                                            <li>Thông tin thanh toán được mã hóa SSL 256-bit</li>
                                            <li>Chúng tôi không lưu trữ thông tin thẻ của bạn</li>
                                            <li>Giao dịch được xử lý qua cổng thanh toán an toàn</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-secondary me-3" onclick="history.back()">
                                        <i class="fa fa-arrow-left"></i> Quay lại
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg" id="payBtn">
                                        <i class="fa fa-lock"></i> Thanh toán {{ $donHang->TongTienFormat }} đ
                                    </button>
                                </div>

                                <!-- Demo Note -->
                                <div class="demo-note mt-3">
                                    <small class="text-muted text-center d-block">
                                        <em>* Đây là demo, thanh toán sẽ được mô phỏng</em>
                                    </small>
                                    <div class="text-center mt-2">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="simulate_success" value="1" checked>
                                            Mô phỏng thanh toán thành công
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .payment_section {
            background: #f8f9fa;
            min-height: 80vh;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .payment-method-item {
            position: relative;
        }

        .payment-method-label {
            display: block;
            cursor: pointer;
            margin-bottom: 0;
        }

        .payment-method-content {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: white;
        }

        .payment-method-item input:checked+.payment-method-label .payment-method-content {
            border-color: #007bff;
            background: rgba(0, 123, 255, .05);
        }

        .payment-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f8f9fa;
            margin-right: 1rem;
        }

        .payment-icon i {
            font-size: 1.5rem;
        }

        .payment-info h6 {
            margin-bottom: .25rem;
            font-weight: 600;
        }

        .bank-logos {
            display: flex;
            gap: .5rem;
        }

        .bank-logos img {
            width: 30px;
            height: 20px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 2px;
        }

        .security-info .alert h6 {
            color: #0c5460;
            margin-bottom: .5rem;
        }

        .security-info ul {
            font-size: .9rem;
            padding-left: 1.2rem;
        }

        .demo-note {
            background: #fff3cd;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #ffeaa7;
        }

        @media (max-width: 768px) {
            .payment-method-content {
                padding: 1rem;
            }

            .payment-icon {
                width: 50px;
                height: 50px;
            }

            .bank-logos img {
                width: 25px;
                height: 18px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('paymentForm');
            const payBtn = document.getElementById('payBtn');

            form.addEventListener('submit', function () {
                payBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý thanh toán...';
                payBtn.disabled = true;
            });

            const paymentInputs = document.querySelectorAll('input[name="payment_method"]');
            paymentInputs.forEach(input => {
                input.addEventListener('change', function () {
                    let methodText = this.value === 'atm' ? 'ATM/Debit'
                        : this.value === 'visa' ? 'Visa/Master'
                            : 'Ví điện tử';
                    payBtn.innerHTML = `<i class="fa fa-lock"></i> Thanh toán qua ${methodText} - {{ $donHang->TongTienFormat }} đ`;
                });
            });
        });
    </script>
@endsection
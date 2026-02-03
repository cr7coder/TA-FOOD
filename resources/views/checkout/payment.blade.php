@extends('foods.master')

@section('title', 'Thanh toán online')

@section('content')
    <!-- Payment Modal Overlay -->
    <div class="payment-modal-overlay" id="paymentModal">
        <div class="payment-modal-container">
            <!-- Header -->
            <div class="payment-modal-header">
                <div class="header-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h2>Thanh toán online</h2>
                </div>
                <button class="close-btn" onclick="window.history.back()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="payment-modal-content">
                <!-- Amount Section -->
                <div class="amount-section">
                    <span>Số tiền cần thanh toán:</span>
                    <span class="amount" id="totalAmount">{{ number_format($donHang->TongTien, 0, ',', '.') }} đ</span>
                </div>

                <!-- Payment Method Tabs -->
                <div class="payment-method-tabs">
                    <button class="tab-btn active" data-method="qr">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 11h8V3H3v8zm2-6h4v4H5V5zM3 21h8v-8H3v8zm2-6h4v4H5v-4zM13 3v8h8V3h-8zm6 6h-4V5h4v4zM13 13h2v2h-2v-2zm2 2h2v2h-2v-2zm-2 2h2v2h-2v-2zm4 0h2v2h-2v-2zm2-2h2v2h-2v-2zm0-2h2v2h-2v-2zm-2-2h2v2h-2v-2z"/>
                        </svg>
                        <span>QR Code</span>
                    </button>
                    <button class="tab-btn" data-method="card">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                            <path d="M2 10h20" stroke-width="2"/>
                        </svg>
                        <span>Thẻ ngân hàng</span>
                    </button>
                    <button class="tab-btn" data-method="wallet">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 12V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2v-5z" stroke-width="2"/>
                            <path d="M16 12h.01" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>Ví điện tử</span>
                    </button>
                </div>

                <!-- QR Payment Content -->
                <div class="payment-content" id="qrContent">
                    <!-- Wallet Selection -->
                    <div class="section-wrapper">
                        <label class="section-label">Chọn ví điện tử để quét mã QR<span class="required">*</span></label>
                        <div class="wallet-grid">
                            <button class="wallet-btn" data-wallet="MoMo">
                                <div class="wallet-icon" style="background: #F6339A;">M</div>
                                <span>MoMo</span>
                            </button>
                            <button class="wallet-btn" data-wallet="ZaloPay">
                                <div class="wallet-icon" style="background: #2B7FFF;">Z</div>
                                <span>ZaloPay</span>
                            </button>
                            <button class="wallet-btn active" data-wallet="VNPay">
                                <div class="wallet-icon" style="background: #FB2C36;">V</div>
                                <span>VNPay</span>
                            </button>
                            <button class="wallet-btn" data-wallet="ShopeePay">
                                <div class="wallet-icon" style="background: #FF6900;">S</div>
                                <span>ShopeePay</span>
                            </button>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="qr-section">
                        <div class="qr-header">
                            <h3>Quét mã QR để thanh toán</h3>
                            <p>Mở ứng dụng <span id="selectedWallet">VNPay</span> và quét mã bên dưới</p>
                        </div>
                        
                        <div class="qr-code-container">
                            <div id="qrCodeImage"></div>
                        </div>

                        <div class="payment-details">
                            <div class="detail-row">
                                <span class="label">Số tiền:</span>
                                <span class="value">{{ number_format($donHang->TongTien, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Ví điện tử:</span>
                                <span class="value" id="walletName">VNPay</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Mã đơn hàng:</span>
                                <span class="value">ORDER_{{ str_pad($donHang->MaDonHang, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>

                        <p class="qr-timer">Mã QR có hiệu lực trong <span id="countdown">05:00</span></p>
                    </div>

                    <!-- Instructions -->
                    <div class="instructions-box">
                        <h4>📱 Hướng dẫn thanh toán</h4>
                        <ol>
                            <li>Mở ứng dụng <span id="instructionWallet">VNPay</span> trên điện thoại</li>
                            <li>Chọn tính năng Quét mã QR</li>
                            <li>Quét mã QR phía trên</li>
                            <li>Xác nhận thông tin và hoàn tất thanh toán</li>
                        </ol>
                    </div>
                </div>

                <!-- Card Payment Content -->
                <div class="payment-content" id="cardContent" style="display: none;">
                    <!-- Card Type Selection -->
                    <div class="section-wrapper">
                        <label class="section-label">Loại thẻ<span class="required">*</span></label>
                        <div class="card-type-grid">
                            <button class="card-type-btn active" data-cardtype="VISA">VISA</button>
                            <button class="card-type-btn" data-cardtype="Mastercard">Mastercard</button>
                            <button class="card-type-btn" data-cardtype="ATM">ATM nội địa</button>
                        </div>
                    </div>

                    <!-- Card Number -->
                    <div class="section-wrapper">
                        <label class="section-label">Số thẻ<span class="required">*</span></label>
                        <input type="text" class="payment-input" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>

                    <!-- Card Holder Name -->
                    <div class="section-wrapper">
                        <label class="section-label">Tên chủ thẻ<span class="required">*</span></label>
                        <input type="text" class="payment-input" id="cardHolder" placeholder="NGUYEN VAN A" style="text-transform: uppercase;">
                    </div>

                    <!-- Expiry and CVV -->
                    <div class="card-details-row">
                        <div class="section-wrapper" style="flex: 1;">
                            <label class="section-label">Ngày hết hạn<span class="required">*</span></label>
                            <input type="text" class="payment-input" id="cardExpiry" placeholder="MM/YY" maxlength="5">
                        </div>
                        <div class="section-wrapper" style="flex: 1;">
                            <label class="section-label">CVV<span class="required">*</span></label>
                            <input type="text" class="payment-input" id="cardCVV" placeholder="123" maxlength="3">
                        </div>
                    </div>
                </div>

                <!-- Wallet Payment Content -->
                <div class="payment-content" id="walletContent" style="display: none;">
                    <!-- Wallet Selection -->
                    <div class="section-wrapper">
                        <label class="section-label">Chọn ví điện tử<span class="required">*</span></label>
                        <div class="wallet-selection-grid">
                            <!-- MoMo -->
                            <div class="wallet-item active" data-wallet="MoMo">
                                <div class="wallet-icon" style="background: #F6339A;">M</div>
                                <div class="wallet-info">
                                    <div class="wallet-name">MoMo</div>
                                    <div class="wallet-desc">Ví điện tử MoMo</div>
                                </div>
                            </div>
                            
                            <!-- ZaloPay -->
                            <div class="wallet-item" data-wallet="ZaloPay">
                                <div class="wallet-icon" style="background: #2B7FFF;">Z</div>
                                <div class="wallet-info">
                                    <div class="wallet-name">ZaloPay</div>
                                    <div class="wallet-desc">Ví điện tử ZaloPay</div>
                                </div>
                            </div>
                            
                            <!-- VNPay -->
                            <div class="wallet-item" data-wallet="VNPay">
                                <div class="wallet-icon" style="background: #FB2C36;">V</div>
                                <div class="wallet-info">
                                    <div class="wallet-name">VNPay</div>
                                    <div class="wallet-desc">Ví điện tử VNPay</div>
                                </div>
                            </div>
                            
                            <!-- ShopeePay -->
                            <div class="wallet-item" data-wallet="ShopeePay">
                                <div class="wallet-icon" style="background: #FF6900;">S</div>
                                <div class="wallet-info">
                                    <div class="wallet-name">ShopeePay</div>
                                    <div class="wallet-desc">Ví điện tử ShopeePay</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="security-notice">
                    <span class="icon">🔒</span>
                    <p>Thông tin thanh toán của bạn được mã hóa an toàn. Chúng tôi không lưu trữ thông tin thẻ của bạn.</p>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn-cancel" onclick="window.history.back()">Hủy</button>
                    <button class="btn-confirm" id="confirmPaymentBtn">Đã thanh toán</button>
                </div>
            </div>
        </div>
    </div>

    <section class="payment_section layout_padding d-none">
        <div class="container">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif>
        </div>
    </section>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .payment-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .payment-modal-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 660px;
            width: 100%;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .payment-modal-header {
            background: #2B7FFF;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 16px 16px 0 0;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
        }

        .header-title svg {
            width: 24px;
            height: 24px;
        }

        .header-title h2 {
            font-size: 16px;
            font-weight: 400;
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .payment-modal-content {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .amount-section {
            background: linear-gradient(to right, #EFF6FF, #FAF5FF);
            padding: 16px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .amount-section span:first-child {
            color: #364153;
            font-size: 16px;
        }

        .amount-section .amount {
            color: #155DFC;
            font-size: 24px;
            font-weight: 400;
        }

        .payment-method-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .tab-btn {
            background: white;
            border: 1.6px solid #E5E7EB;
            border-radius: 10px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-btn:hover {
            border-color: #2B7FFF;
        }

        .tab-btn.active {
            background: #EFF6FF;
            border-color: #2B7FFF;
        }

        .tab-btn svg {
            width: 24px;
            height: 24px;
            color: #0A0A0A;
        }

        .tab-btn.active svg {
            color: #1447E6;
        }

        .tab-btn span {
            font-size: 14px;
            color: #0A0A0A;
        }

        .tab-btn.active span {
            color: #1447E6;
        }

        .payment-content {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .section-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .section-label {
            font-size: 16px;
            color: #364153;
        }

        .required {
            color: #FB2C36;
            margin-left: 4px;
        }

        .wallet-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .wallet-btn {
            background: white;
            border: 1.6px solid #E5E7EB;
            border-radius: 10px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .wallet-btn:hover {
            border-color: #2B7FFF;
        }

        .wallet-btn.active {
            background: #FEF2F2;
            border-color: #FB2C36;
        }

        .wallet-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: 400;
        }

        .wallet-btn span {
            font-size: 16px;
            color: #101828;
        }

        .qr-section {
            background: linear-gradient(140.63deg, #EFF6FF 0%, #FAF5FF 100%);
            border-radius: 14px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .qr-header {
            text-align: center;
        }

        .qr-header h3 {
            font-size: 16px;
            color: #101828;
            margin-bottom: 4px;
            font-weight: 400;
        }

        .qr-header p {
            font-size: 14px;
            color: #4A5565;
        }

        .qr-code-container {
            background: white;
            border-radius: 10px;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 240px;
        }

        #qrCodeImage {
            width: 192px;
            height: 192px;
            background: #f0f0f0;
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #666;
        }

        .payment-details {
            background: white;
            border-radius: 10px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-row .label {
            font-size: 14px;
            color: #4A5565;
        }

        .detail-row .value {
            font-size: 14px;
            color: #101828;
        }

        .qr-timer {
            text-align: center;
            font-size: 14px;
            color: #4A5565;
        }

        .qr-timer span {
            color: #FB2C36;
        }

        .instructions-box {
            background: #EFF6FF;
            border: 0.8px solid #BEDBFF;
            border-radius: 10px;
            padding: 17px;
        }

        .instructions-box h4 {
            font-size: 16px;
            color: #101828;
            margin-bottom: 8px;
            font-weight: 400;
        }

        .instructions-box ol {
            margin: 0;
            padding-left: 20px;
        }

        .instructions-box li {
            font-size: 14px;
            color: #364153;
            margin-bottom: 4px;
        }

        .security-notice {
            background: #FEFCE8;
            border: 0.8px solid #FFF085;
            border-radius: 10px;
            padding: 17px;
            display: flex;
            gap: 8px;
        }

        .security-notice .icon {
            font-size: 16px;
        }

        .security-notice p {
            font-size: 14px;
            color: #894B00;
            margin: 0;
            line-height: 1.4;
        }

        /* Card Payment Styles */
        .card-type-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .card-type-btn {
            background: white;
            border: 1.6px solid #E5E7EB;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            color: #101828;
        }

        .card-type-btn:hover {
            border-color: #2B7FFF;
        }

        .card-type-btn.active {
            background: #EFF6FF;
            border-color: #2B7FFF;
            color: #155DFC;
        }

        .card-type-btn[data-card-type="mastercard"].active {
            color: #FB2C36;
        }

        .card-type-btn[data-card-type="atm"].active {
            color: #00A63E;
        }

        .payment-input {
            width: 100%;
            padding: 12px 16px;
            border: 0.8px solid #D1D5DC;
            border-radius: 10px;
            font-size: 16px;
            color: #0A0A0A;
            transition: all 0.2s;
        }

        .payment-input:focus {
            outline: none;
            border-color: #2B7FFF;
        }

        .payment-input::placeholder {
            color: rgba(10, 10, 10, 0.5);
        }

        .card-details-row {
            display: flex;
            gap: 16px;
        }

        .wallet-selection-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .wallet-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px;
            border: 1.6px solid #E5E7EB;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .wallet-item:hover {
            border-color: #2B7FFF;
        }

        .wallet-item.active {
            background: #FDF2F8;
            border-color: #F6339A;
        }

        .wallet-item[data-wallet="MoMo"].active {
            background: #FDF2F8;
            border-color: #F6339A;
        }

        .wallet-item[data-wallet="ZaloPay"].active {
            background: #EFF6FF;
            border-color: #2B7FFF;
        }

        .wallet-item[data-wallet="VNPay"].active {
            background: #FEF2F2;
            border-color: #FB2C36;
        }

        .wallet-item[data-wallet="ShopeePay"].active {
            background: #FFF7ED;
            border-color: #FF6900;
        }

        .wallet-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .wallet-info {
            flex: 1;
        }

        .wallet-name {
            font-size: 16px;
            color: #101828;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .wallet-desc {
            font-size: 14px;
            color: #6A7282;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 8px;
        }

        .btn-cancel {
            background: white;
            border: 0.8px solid #D1D5DC;
            border-radius: 20px;
            padding: 13px 24px;
            font-size: 16px;
            color: #364153;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #F9FAFB;
        }

        .btn-confirm {
            background: #2B7FFF;
            border: none;
            border-radius: 20px;
            padding: 13px 24px;
            font-size: 16px;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-confirm:hover {
            background: #1D6FE8;
        }

        .btn-confirm:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .payment-modal-container {
                max-width: 100%;
                margin: 0;
                border-radius: 0;
                max-height: 100vh;
            }

            .payment-modal-header {
                border-radius: 0;
            }

            .payment-method-tabs {
                grid-template-columns: 1fr;
            }

            .wallet-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        let countdownTimer;
        let timeLeft = 300; // 5 minutes
        let selectedWallet = 'VNPay';
        let selectedCardType = 'VISA';
        let currentPaymentMethod = 'qr'; // qr, card, wallet
        const orderId = parseInt('{{ $donHang->MaDonHang ?? 0 }}');
        const totalAmount = parseFloat('{{ $donHang->TongTien ?? 0 }}');

        // Generate QR Code
        function generateQRCode() {
            const qrContainer = document.getElementById('qrCodeImage');
            qrContainer.innerHTML = '';
            
            const qrData = `ORDER_${String(orderId).padStart(6, '0')}|${totalAmount}|${selectedWallet}`;
            
            new QRCode(qrContainer, {
                text: qrData,
                width: 192,
                height: 192,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        // Countdown timer
        function startCountdown() {
            if (countdownTimer) clearInterval(countdownTimer);
            
            timeLeft = 300;
            updateCountdownDisplay();
            
            countdownTimer = setInterval(() => {
                timeLeft--;
                updateCountdownDisplay();
                
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    alert('Mã QR đã hết hạn. Vui lòng thử lại.');
                    window.location.reload();
                }
            }, 1000);
        }

        function updateCountdownDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('countdown').textContent = 
                `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        // Wallet selection
        document.querySelectorAll('.wallet-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.wallet-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                selectedWallet = this.dataset.wallet;
                document.getElementById('selectedWallet').textContent = selectedWallet;
                document.getElementById('walletName').textContent = selectedWallet;
                document.getElementById('instructionWallet').textContent = selectedWallet;
                
                generateQRCode();
                startCountdown();
            });
        });

        // Payment method tabs - Show/Hide content
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const method = this.dataset.method;
                
                // Update active tab
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Show/hide content
                document.getElementById('qrContent').style.display = method === 'qr' ? 'block' : 'none';
                document.getElementById('cardContent').style.display = method === 'card' ? 'block' : 'none';
                document.getElementById('walletContent').style.display = method === 'wallet' ? 'block' : 'none';
                
                currentPaymentMethod = method;
                
                // Start timer for QR if selected
                if (method === 'qr') {
                    generateQRCode();
                    startCountdown();
                } else {
                    if (countdownTimer) clearInterval(countdownTimer);
                }
            });
        });

        // Confirm payment - Call API
        document.getElementById('confirmPaymentBtn').addEventListener('click', async function() {
            const btn = this;
            
            let paymentData = {
                simulate_success: true
            };
            
            // Validate based on current payment method
            if (currentPaymentMethod === 'qr') {
                // QR payment
                if (!confirm(`Xác nhận bạn đã thanh toán ${totalAmount.toLocaleString('vi-VN')} đ qua ${selectedWallet}?`)) {
                    return;
                }
                paymentData.payment_method = selectedWallet;
                
            } else if (currentPaymentMethod === 'card') {
                // Card payment
                const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
                const cardHolder = document.getElementById('cardHolder').value;
                const cardExpiry = document.getElementById('cardExpiry').value;
                const cardCVV = document.getElementById('cardCVV').value;
                
                // Validate card inputs
                if (!cardNumber || cardNumber.length < 13) {
                    alert('Vui lòng nhập số thẻ hợp lệ');
                    return;
                }
                if (!cardHolder || cardHolder.trim().length < 3) {
                    alert('Vui lòng nhập tên chủ thẻ');
                    return;
                }
                if (!cardExpiry || !/^\d{2}\/\d{2}$/.test(cardExpiry)) {
                    alert('Vui lòng nhập ngày hết hạn (MM/YY)');
                    return;
                }
                if (!cardCVV || cardCVV.length < 3) {
                    alert('Vui lòng nhập mã CVV');
                    return;
                }
                
                if (!confirm(`Xác nhận thanh toán ${totalAmount.toLocaleString('vi-VN')} đ bằng thẻ ${selectedCardType}?`)) {
                    return;
                }
                
                paymentData.payment_method = selectedCardType; // VISA, Mastercard, or ATM
                paymentData.card_data = {
                    number: cardNumber,
                    holder: cardHolder,
                    expiry: cardExpiry,
                    cvv: cardCVV,
                    type: selectedCardType
                };
                
            } else if (currentPaymentMethod === 'wallet') {
                // Wallet payment
                if (!confirm(`Xác nhận thanh toán ${totalAmount.toLocaleString('vi-VN')} đ qua ví ${selectedWallet}?`)) {
                    return;
                }
                paymentData.payment_method = selectedWallet;
            }
            
            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';
            
            try {
                const response = await fetch(`/api/checkout/process-payment/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(paymentData)
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to confirmation page
                    window.location.href = data.data.redirect_url;
                } else {
                    alert(data.message || 'Thanh toán thất bại. Vui lòng thử lại.');
                    btn.disabled = false;
                    btn.textContent = currentPaymentMethod === 'qr' ? 'Đã thanh toán' : 'Xác nhận thanh toán';
                }
            } catch (error) {
                console.error('Payment error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
                btn.disabled = false;
                btn.textContent = currentPaymentMethod === 'qr' ? 'Đã thanh toán' : 'Xác nhận thanh toán';
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Hide card and wallet content initially
            document.getElementById('cardContent').style.display = 'none';
            document.getElementById('walletContent').style.display = 'none';
            
            // QR payment initialization
            generateQRCode();
            startCountdown();
            
            // Card type selection
            document.querySelectorAll('.card-type-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.card-type-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    selectedCardType = this.dataset.cardtype;
                });
            });
            
            // Card number formatting (XXXX XXXX XXXX XXXX)
            const cardNumberInput = document.getElementById('cardNumber');
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '');
                value = value.replace(/\D/g, ''); // Only digits
                
                // Limit to 16 digits
                if (value.length > 16) {
                    value = value.substring(0, 16);
                }
                
                // Add space every 4 digits
                let formatted = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formatted += ' ';
                    }
                    formatted += value[i];
                }
                
                e.target.value = formatted;
            });
            
            // Card holder - only letters and spaces
            const cardHolderInput = document.getElementById('cardHolder');
            cardHolderInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
            
            // Card expiry formatting (MM/YY)
            const cardExpiryInput = document.getElementById('cardExpiry');
            cardExpiryInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Only digits
                
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                
                e.target.value = value;
            });
            
            // CVV - only 3-4 digits
            const cardCVVInput = document.getElementById('cardCVV');
            cardCVVInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Only digits
                
                // Limit to 4 digits (American Express can have 4)
                if (value.length > 4) {
                    value = value.substring(0, 4);
                }
                
                e.target.value = value;
            });
            
            // Wallet item selection
            document.querySelectorAll('.wallet-item').forEach(item => {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.wallet-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    selectedWallet = this.dataset.wallet;
                });
            });
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (countdownTimer) clearInterval(countdownTimer);
        });
    </script>
@endsection

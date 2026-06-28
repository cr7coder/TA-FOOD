@extends('client.layouts.master')
@section('title', 'Thanh toán QR Code - TA Food')
@section('content')

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

.qr-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.6);
    display: flex; align-items: center; justify-content: center;
    z-index: 9999; padding: 16px;
    backdrop-filter: blur(4px);
}

.swal2-container {
    z-index: 100000 !important;
}
.swal2-popup {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    border-radius: 18px !important;
    padding: 20px !important;
    width: 360px !important;
}
.swal2-title {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    font-size: 18px !important;
    font-weight: 700 !important;
    color: #111111 !important;
    margin: 10px 0 !important;
}
.swal2-html-container {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    font-size: 13.5px !important;
    line-height: 1.5 !important;
    color: #555555 !important;
    margin: 0 !important;
}
.swal2-icon {
    transform: scale(0.7) !important;
    margin: 0 auto !important;
}
.swal2-actions {
    margin-top: 16px !important;
    gap: 8px !important;
}
.swal2-confirm, .swal2-cancel {
    font-size: 12.5px !important;
    font-weight: 600 !important;
    padding: 8px 16px !important;
    border-radius: 8px !important;
    margin: 0 !important;
}

.qr-modal {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 32px 64px rgba(0,0,0,0.25);
    width: 100%; max-width: 480px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: slideUp 0.4s cubic-bezier(.16,1,.3,1);
    overflow: hidden;
}

@keyframes slideUp {
    from { transform: translateY(40px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

/* ── Header ── */
.modal-header {
    background: linear-gradient(135deg, #e31837 0%, #c41230 100%);
    padding: 20px 24px;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.modal-header-left { display: flex; align-items: center; gap: 10px; color: #fff; }
.modal-header-left .bank-logo {
    background: #fff; border-radius: 8px;
    padding: 4px 10px;
    font-weight: 800; font-size: 15px; color: #e31837;
    letter-spacing: 0.5px;
}
.modal-header-left h2 { font-size: 17px; font-weight: 600; color: #fff; }
.modal-header .timer-badge {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.4);
    color: #fff; border-radius: 20px;
    padding: 6px 14px; font-size: 14px; font-weight: 700;
    display: flex; align-items: center; gap: 5px;
}
.timer-badge.expired { background: rgba(255,100,100,0.3); }

/* ── Amount banner ── */
.amount-banner {
    background: linear-gradient(135deg, #fff5f6 0%, #fef9f9 100%);
    border-bottom: 1px solid #fce4e7;
    padding: 16px 24px;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.amount-label { font-size: 13px; color: #666; }
.amount-value { font-size: 26px; font-weight: 800; color: #e31837; }

/* ── Body ── */
.modal-body {
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    flex: 1;
    overflow-y: auto;
    min-height: 0;
}
.modal-body > * {
    flex-shrink: 0;
}

/* Scrollbar styling */
.modal-body::-webkit-scrollbar {
    width: 6px;
}
.modal-body::-webkit-scrollbar-track {
    background: transparent;
}
.modal-body::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
.modal-body::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}

/* ── QR block ── */
.qr-block {
    background: linear-gradient(135deg, #fdf8f8 0%, #fff5f5 100%);
    border: 1.5px solid #fce4e7;
    border-radius: 16px;
    padding: 20px;
    display: flex; flex-direction: column; align-items: center; gap: 12px;
}
.qr-block p { font-size: 13px; color: #777; text-align: center; }
.qr-img-wrap {
    background: #fff;
    border-radius: 12px;
    padding: 8px;
    box-shadow: 0 4px 20px rgba(227,24,55,0.15);
    position: relative;
}
.qr-img-wrap img {
    width: 220px; height: 220px;
    border-radius: 8px; display: block;
}
.qr-loading {
    width: 220px; height: 220px;
    display: flex; align-items: center; justify-content: center;
    flex-direction: column; gap: 10px;
    background: #f9f9f9; border-radius: 8px;
    font-size: 13px; color: #999;
}
.spinner {
    width: 36px; height: 36px;
    border: 3px solid #f0f0f0;
    border-top-color: #e31837;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── File Uploader ── */
.proof-upload-box {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    width: 100%;
}
.proof-upload-box:hover {
    border-color: #e31837;
    background: #fff5f5;
}
.proof-upload-box.dragover {
    border-color: #e31837;
    background: #ffe3e6;
}
.proof-upload-icon {
    font-size: 28px;
    color: #64748b;
}
.proof-upload-text {
    font-size: 13px;
    color: #475569;
    font-weight: 600;
}
.proof-upload-hint {
    font-size: 11px;
    color: #94a3b8;
}
.proof-preview-container {
    display: none;
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    max-height: 200px;
    border: 1.5px solid #cbd5e1;
    width: 100%;
    background: #f8fafc;
    padding: 8px;
}
.proof-preview-img {
    width: 100%;
    max-height: 180px;
    object-fit: contain;
    border-radius: 8px;
}
.proof-remove-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0,0,0,0.6);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    z-index: 10;
}
.proof-remove-btn:hover {
    background: rgba(220,38,38,0.9);
}

/* ── Bank info ── */
.bank-info-table {
    width: 100%;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
}
.bank-info-row {
    display: flex; align-items: center;
    padding: 12px 14px;
    border-bottom: 1px solid #f0f0f0;
    gap: 10px;
}
.bank-info-row:last-child { border-bottom: none; }
.bank-info-row .bi-label {
    font-size: 12.5px; color: #777;
    min-width: 110px; flex-shrink: 0;
    font-weight: 500;
}
.bank-info-row .bi-value {
    font-size: 14px; font-weight: 600; color: #111;
    flex: 1;
}
.bank-info-row .bi-value.highlight { color: #e31837; font-size: 15px; }

.copy-btn {
    background: rgba(227, 24, 55, 0.06); border: none;
    border-radius: 6px; padding: 4px 10px;
    font-size: 11px; font-weight: 600; color: #e31837; cursor: pointer;
    transition: all 0.2s;
}
.copy-btn:hover { background: #e31837; color: #fff; }

/* ── Transfer notice ── */
.notice-box {
    background: #fffbeb; border: 1px solid #fde68a;
    border-radius: 12px; padding: 12px 16px;
    font-size: 12.5px; color: #92400e;
    display: flex; gap: 10px; align-items: flex-start;
    line-height: 1.5;
}
.notice-box .notice-icon { font-size: 16px; flex-shrink: 0; }

/* ── Buttons ── */
.action-row {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 12px; padding: 16px 24px;
    border-top: 1px solid #f3f4f6;
    background: #ffffff;
    flex-shrink: 0;
}
.btn-back {
    background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px;
    padding: 14px; font-size: 15px; font-weight: 600;
    color: #ef4444; cursor: pointer; transition: all 0.2s;
}
.btn-back:hover { background: #fee2e2; }

.btn-confirm {
    background: linear-gradient(135deg, #e31837 0%, #c41230 100%);
    border: none; border-radius: 12px;
    padding: 14px; font-size: 15px; font-weight: 700;
    color: #fff; cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    box-shadow: 0 4px 12px rgba(227,24,55,0.35);
}
.btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(227,24,55,0.45); }
.btn-confirm:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

/* Responsive mobile view */
@media (max-width: 576px) {
    .qr-overlay {
        padding: 0;
        align-items: flex-end;
    }
    .qr-modal {
        max-width: 100%;
        max-height: 96vh;
        border-radius: 24px 24px 0 0;
    }
    .action-row {
        padding-bottom: 24px;
    }
}
</style>

<div class="qr-overlay">
    <div class="qr-modal">
        <div class="modal-header">
            <div class="modal-header-left">
                <span class="bank-logo">TCB</span>
                <h2>Thanh toán QR VietQR</h2>
            </div>
            <div class="timer-badge" id="timerBadge">
                ⏱ <span id="countdown">15:00</span>
            </div>
        </div>

        <div class="amount-banner">
            <span class="amount-label">Số tiền cần chuyển</span>
            <span class="amount-value">{{ number_format($totalAmount, 0, ',', '.') }} đ</span>
        </div>

        {{-- ── MAIN BODY (QR view) ── --}}
        <div class="modal-body" id="qrView">

            {{-- QR Image --}}
            <div class="qr-block">
                <p>Mở <strong>App ngân hàng bất kỳ</strong> → Quét mã QR bên dưới</p>
                <div class="qr-img-wrap">
                    <div class="qr-loading" id="qrLoading">
                        <div class="spinner"></div>
                        <span>Đang tải mã QR…</span>
                    </div>
                    <img id="qrImage"
                         src="{{ $qrUrl }}"
                         alt="QR VietQR Techcombank"
                         style="display:none;"
                         onload="document.getElementById('qrLoading').style.display='none'; this.style.display='block';"
                         onerror="document.getElementById('qrLoading').innerHTML='<span style=color:#e31837>Không tải được QR. Vui lòng nhập thông tin thủ công.</span>';">
                </div>
                <p style="font-size:12px; color:#aaa;">Hỗ trợ tất cả App ngân hàng Việt Nam (Napas 247)</p>
            </div>

            {{-- File Uploader (Moved up for immediate visibility) --}}
            <div class="proof-upload-box" id="proofUploadBox">
                <div class="proof-upload-icon">📸</div>
                <div class="proof-upload-text">Tải lên ảnh hóa đơn chuyển khoản thành công</div>
                <div class="proof-upload-hint">Nhấp vào đây hoặc kéo thả ảnh vào đây (Hỗ trợ: JPG, PNG, tối đa 5MB)</div>
                <input type="file" id="proofInput" accept="image/*" style="display: none;">
            </div>
            <div class="proof-preview-container" id="proofPreviewContainer">
                <button class="proof-remove-btn" id="proofRemoveBtn" onclick="removeProof(event)">&times;</button>
                <img class="proof-preview-img" id="proofPreviewImg" src="" alt="Proof Preview">
            </div>

            {{-- Bank details --}}
            <div class="bank-info-table">
                <div class="bank-info-row">
                    <span class="bi-label">🏦 Ngân hàng</span>
                    <span class="bi-value">Techcombank (TCB)</span>
                </div>
                <div class="bank-info-row">
                    <span class="bi-label">💳 Số tài khoản</span>
                    <span class="bi-value highlight" id="stk">{{ $accountNo }}</span>
                    <button class="copy-btn" onclick="copyText('{{ $accountNo }}', this)">Sao chép</button>
                </div>
                <div class="bank-info-row">
                    <span class="bi-label">👤 Chủ tài khoản</span>
                    <span class="bi-value">{{ $accountName }}</span>
                </div>
                <div class="bank-info-row">
                    <span class="bi-label">💰 Số tiền</span>
                    <span class="bi-value highlight">{{ number_format($totalAmount, 0, ',', '.') }} đ</span>
                    <button class="copy-btn" onclick="copyText('{{ (int)$totalAmount }}', this)">Sao chép</button>
                </div>
                <div class="bank-info-row">
                    <span class="bi-label">📝 Nội dung CK</span>
                    <span class="bi-value highlight" id="orderCodeDisplay">{{ $orderCode }}</span>
                    <button class="copy-btn" onclick="copyText('{{ $orderCode }}', this)">Sao chép</button>
                </div>
            </div>

            {{-- Notice --}}
            <div class="notice-box">
                <span class="notice-icon">⚠️</span>
                <span>Vui lòng chuyển khoản <strong>đúng số tiền</strong> và <strong>đúng nội dung</strong> <code>{{ $orderCode }}</code> để đơn hàng được xác nhận nhanh nhất.</span>
            </div>
        </div>

        {{-- ── ACTION BUTTONS ── --}}
        <div class="action-row" id="actionRow">
            <button class="btn-back" onclick="handleCancelOrder()">❌ Hủy đơn hàng</button>
            <button class="btn-confirm" id="confirmBtn" onclick="handleConfirm()">
                ✅ Tôi đã chuyển khoản
            </button>
        </div>

    </div>{{-- .qr-modal --}}
</div>{{-- .qr-overlay --}}

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ── Cancel Order Action ──
function handleCancelOrder() {
    Swal.fire({
        title: 'Hủy đơn hàng?',
        html: `
            <p style="margin-bottom: 15px; font-size: 14px;">Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.</p>
            <div style="text-align: left; margin-top: 10px;">
                <label style="font-weight: 600; font-size: 13.5px; margin-bottom: 6px; display: block; color: #333;">Lý do hủy đơn:</label>
                <select id="cancelReasonSelect" style="width: 100%; margin: 0; padding: 10px; font-size: 14px; border-radius: 8px; border: 1px solid #d9d9d9; display: block; outline: none; box-sizing: border-box; font-family: inherit;" onchange="if(this.value==='Khác'){document.getElementById('customCancelReason').style.display='block';}else{document.getElementById('customCancelReason').style.display='none';}">
                    <option value="Thay đổi quyết định mua">Thay đổi quyết định mua</option>
                    <option value="Không muốn thanh toán online nữa">Không muốn thanh toán online nữa</option>
                    <option value="Muốn thay đổi/Thêm bớt món ăn">Muốn thay đổi/Thêm bớt món ăn</option>
                    <option value="Khác">Khác...</option>
                </select>
                <textarea id="customCancelReason" style="width: 100%; margin: 10px 0 0 0; padding: 10px; font-size: 14px; border-radius: 8px; border: 1px solid #d9d9d9; display: none; min-height: 80px; box-sizing: border-box; outline: none; font-family: inherit;" placeholder="Nhập lý do hủy của bạn..."></textarea>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đồng ý hủy',
        cancelButtonText: 'Không hủy',
        preConfirm: () => {
            const selectVal = document.getElementById('cancelReasonSelect').value;
            const customVal = document.getElementById('customCancelReason').value.trim();
            if (selectVal === 'Khác' && !customVal) {
                Swal.showValidationMessage('Vui lòng nhập lý do hủy đơn khác của bạn!');
                return false;
            }
            return selectVal === 'Khác' ? customVal : selectVal;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const cancelReason = result.value;
            Swal.fire({
                title: 'Đang hủy đơn...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Call cancel API for all order IDs
            const ids = maDonHang.split(',');
            const cancelPromises = ids.map(id => {
                return fetch(`/orders/${id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        reason: cancelReason
                    })
                }).then(res => {
                    if (!res.ok) {
                        return res.json().then(err => Promise.reject(err));
                    }
                    return res.json();
                });
            });

            Promise.all(cancelPromises)
                .then(results => {
                    Swal.fire({
                        title: 'Đã hủy thành công!',
                        text: 'Đơn hàng của bạn đã được hủy thành công.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('orders.history') }}";
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        title: 'Hủy đơn thất bại',
                        text: err.message || 'Có lỗi xảy ra khi hủy đơn hàng. Vui lòng kiểm tra lại.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
        }
    });
}

// ── Variables ──
const maDonHang  = "{{ $maDonHang }}";
const successUrl = "{{ route('checkout.success', $maDonHang) }}";
const confirmUrl = "{{ route('checkout.confirm-transfer', $maDonHang) }}";
const csrfToken  = "{{ csrf_token() }}";

let countdownTimer  = null;
let secondsLeft     = 15 * 60; // 15 minutes
let alreadyConfirmed = false;

// ── Countdown timer ──
function startCountdown() {
    countdownTimer = setInterval(() => {
        if (secondsLeft <= 0) {
            clearInterval(countdownTimer);
            document.getElementById('timerBadge').classList.add('expired');
            document.getElementById('timerBadge').innerHTML = '⏱ Hết giờ';
            document.getElementById('confirmBtn').disabled = true;
            document.getElementById('confirmBtn').textContent = 'QR đã hết hạn';
            return;
        }
        secondsLeft--;
        const m = String(Math.floor(secondsLeft / 60)).padStart(2,'0');
        const s = String(secondsLeft % 60).padStart(2,'0');
        document.getElementById('countdown').textContent = m + ':' + s;
    }, 1000);
}

// ── Copy to clipboard ──
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const old = btn.textContent;
        btn.textContent = '✓ Đã copy';
        btn.style.background = '#16a34a';
        btn.style.color = '#fff';
        btn.style.borderColor = '#16a34a';
        setTimeout(() => {
            btn.textContent = old;
            btn.style.background = '';
            btn.style.color = '';
            btn.style.borderColor = '';
        }, 2000);
    });
}

// ── File Upload Handlers ──
const proofUploadBox = document.getElementById('proofUploadBox');
const proofInput = document.getElementById('proofInput');
const proofPreviewContainer = document.getElementById('proofPreviewContainer');
const proofPreviewImg = document.getElementById('proofPreviewImg');

if (proofUploadBox && proofInput) {
    // Click box to trigger file select
    proofUploadBox.addEventListener('click', () => {
        proofInput.click();
    });

    // Drag & Drop events
    ['dragenter', 'dragover'].forEach(eventName => {
        proofUploadBox.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            proofUploadBox.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        proofUploadBox.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            proofUploadBox.classList.remove('dragover');
        }, false);
    });

    proofUploadBox.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            proofInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    proofInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });
}

function handleFileSelect(file) {
    if (!file) return;

    // Validate size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({
            title: 'File quá lớn',
            text: 'Dung lượng ảnh tối đa là 5MB.',
            icon: 'error',
            confirmButtonColor: '#e31837'
        });
        proofInput.value = '';
        return;
    }

    // Validate type
    if (!file.type.match('image.*')) {
        Swal.fire({
            title: 'Định dạng không hợp lệ',
            text: 'Vui lòng chọn file hình ảnh (JPG, PNG).',
            icon: 'error',
            confirmButtonColor: '#e31837'
        });
        proofInput.value = '';
        return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        proofPreviewImg.src = e.target.result;
        proofPreviewContainer.style.display = 'block';
        proofUploadBox.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function removeProof(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    proofInput.value = '';
    proofPreviewImg.src = '';
    proofPreviewContainer.style.display = 'none';
    proofUploadBox.style.display = 'flex';
}

// ── Handle confirm button ──
function handleConfirm() {
    const fileInput = document.getElementById('proofInput');
    if (!fileInput || fileInput.files.length === 0) {
        Swal.fire({
            title: 'Chưa tải ảnh minh chứng',
            text: 'Vui lòng tải lên ảnh chụp hóa đơn (chuyển khoản thành công) để xác nhận.',
            icon: 'warning',
            confirmButtonColor: '#e31837'
        });
        return;
    }

    if (alreadyConfirmed) return;
    alreadyConfirmed = true;

    const btn = document.getElementById('confirmBtn');
    btn.disabled = true;
    btn.textContent = 'Đang xử lý…';

    const formData = new FormData();
    formData.append('payment_proof', fileInput.files[0]);

    fetch(confirmUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Redirect immediately to success page as user requested
            window.location.href = successUrl;
        } else {
            btn.disabled = false;
            btn.textContent = '✅ Tôi đã chuyển khoản';
            alreadyConfirmed = false;
            Swal.fire({ title: 'Có lỗi', text: data.message || 'Vui lòng thử lại.', icon: 'error' });
        }
    })
    .catch((err) => {
        console.error(err);
        btn.disabled = false;
        btn.textContent = '✅ Tôi đã chuyển khoản';
        alreadyConfirmed = false;
        Swal.fire({ title: 'Mất kết nối', text: 'Vui lòng kiểm tra mạng và thử lại.', icon: 'warning' });
    });
}

// ── Init ──
startCountdown();
</script>
@endsection

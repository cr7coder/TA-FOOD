@extends('admin.layouts.app')

@section('title', 'Cài đặt hệ thống')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .settings-container {
        font-family: 'Outfit', sans-serif;
    }
    
    /* Header Card */
    .settings-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        color: #fff;
        padding: 1.5rem 1.8rem;
        border-radius: var(--radius);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.18);
        display: flex;
        align-items: center;
        gap: 1.2rem;
        position: relative;
        overflow: hidden;
    }
    
    .settings-header::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        top: -150px;
        right: -100px;
        pointer-events: none;
    }

    .settings-header .icon-wrap {
        width: 52px;
        height: 52px;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);
    }

    .settings-header h2 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: -0.3px;
    }

    .settings-header p {
        margin: 0;
        opacity: 0.85;
        font-size: 0.82rem;
        font-weight: 500;
    }

    /* Cards */
    .settings-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1.6rem;
        height: 100%;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .settings-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(139, 92, 246, 0.08);
    }

    .settings-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1.4rem;
        display: flex;
        align-items: center;
        gap: 0.7rem;
        border-bottom: 1px dashed var(--border);
        padding-bottom: 0.8rem;
    }

    .settings-card-title i {
        color: #8b5cf6;
        font-size: 1.2rem;
    }

    /* Form Controls */
    .form-group {
        margin-bottom: 1.2rem;
    }

    .form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 0.45rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        opacity: 0.85;
    }

    .form-control, .form-select {
        background-color: var(--bg);
        border: 1px solid var(--border);
        color: var(--text);
        font-size: 0.88rem;
        font-weight: 500;
        padding: 0.65rem 0.9rem;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        background-color: var(--card);
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        color: var(--text);
    }

    textarea.form-control {
        resize: none;
    }

    /* Switch Style (iOS premium toggle) */
    .switch-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.7rem 0.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }

    body.theme-dark .switch-container {
        border-bottom-color: rgba(255,255,255,0.02);
    }

    .switch-label-wrap {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .switch-title {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text);
    }

    .switch-desc {
        font-size: 0.72rem;
        color: var(--muted);
        font-weight: 400;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(139, 92, 246, 0.1);
        border: 1px solid var(--border);
        transition: .3s ease;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 2px;
        bottom: 2px;
        background-color: #fff;
        transition: .3s ease;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }

    input:checked + .slider {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
    }

    body.theme-dark input:checked + .slider {
        background-color: #a78bfa;
        border-color: #a78bfa;
    }

    input:checked + .slider:before {
        transform: translateX(20px);
    }

    /* Save Button */
    .btn-save {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.65rem 1.4rem;
        border-radius: 10px;
        box-shadow: 0 6px 16px rgba(139, 92, 246, 0.22);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        width: 100%;
        margin-top: 0.6rem;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
    }

    .btn-save:active {
        transform: translateY(0);
    }

    .btn-save:disabled {
        background: var(--muted);
        box-shadow: none;
        cursor: not-allowed;
    }

    /* Feedback Toasts */
    .toast-container-custom {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 2000;
    }

    .toast-custom {
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--text);
        box-shadow: var(--shadow);
        border-radius: 12px;
        padding: 1rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        min-width: 300px;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .toast-custom.show {
        transform: translateY(0);
        opacity: 1;
    }

    .toast-custom.success .toast-icon {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }

    .toast-custom.error .toast-icon {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
    }

    .toast-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
    }

    .toast-content {
        flex-grow: 1;
    }

    .toast-title {
        font-weight: 700;
        font-size: 0.88rem;
        margin: 0;
    }

    .toast-message {
        font-size: 0.78rem;
        color: var(--muted);
        margin: 0;
    }
</style>
@endsection

@section('content')
<div class="settings-container container-fluid p-0">
    <!-- Header Page -->
    <div class="settings-header mb-4">
        <div class="icon-wrap">
            <i class="fas fa-gears text-white"></i>
        </div>
        <div>
            <h2>Cài đặt hệ thống</h2>
            <p>admin.settings.system</p>
        </div>
    </div>

    <!-- Main Grid Form -->
    <form id="systemSettingsForm" onsubmit="event.preventDefault();">
        <div class="row g-4">
            
            <!-- 1. Cài đặt chung -->
            <div class="col-xl-6 col-md-6">
                <div class="settings-card">
                    <div class="settings-card-title">
                        <i class="fas fa-globe"></i> Cài đặt chung
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="site_name">Tên website</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="site_desc">Mô tả</label>
                        <textarea class="form-control" id="site_desc" name="site_desc" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label" for="support_email">Email hỗ trợ</label>
                            <input type="email" class="form-control" id="support_email" name="support_email" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label" for="hotline">Hotline</label>
                            <input type="text" class="form-control" id="hotline" name="hotline" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="timezone">Múi giờ</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="Asia/Ho_Chi_Minh">Việt Nam (GMT+7)</option>
                                <option value="UTC">UTC (GMT+0)</option>
                                <option value="America/New_York">New York (GMT-5)</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="language">Ngôn ngữ</label>
                            <select class="form-select" id="language" name="language">
                                <option value="vi">Tiếng Việt</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="currency">Tiền tệ</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="VND">VND</option>
                                <option value="USD">USD</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn-save btn-submit-section" data-section="general">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>
            </div>

            <!-- 2. Cài đặt thông báo -->
            <div class="col-xl-6 col-md-6">
                <div class="settings-card">
                    <div class="settings-card-title">
                        <i class="fas fa-bell"></i> Cài đặt thông báo
                    </div>

                    <div class="switch-container">
                        <div class="switch-label-wrap">
                            <span class="switch-title">Email thông báo</span>
                            <span class="switch-desc">Gửi thông báo qua email</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="notify_email" name="notify_email">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="switch-container">
                        <div class="switch-label-wrap">
                            <span class="switch-title">SMS thông báo</span>
                            <span class="switch-desc">Gửi thông báo qua SMS</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="notify_sms" name="notify_sms">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="switch-container">
                        <div class="switch-label-wrap">
                            <span class="switch-title">Push notification</span>
                            <span class="switch-desc">Thông báo đẩy trên app</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="notify_push" name="notify_push">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="switch-container">
                        <div class="switch-label-wrap">
                            <span class="switch-title">Thông báo đơn hàng</span>
                            <span class="switch-desc">Nhận thông báo đơn hàng mới</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="notify_order" name="notify_order">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="switch-container">
                        <div class="switch-label-wrap">
                            <span class="switch-title">Thông báo khuyến mãi</span>
                            <span class="switch-desc">Nhận thông báo khuyến mãi</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="notify_promo" name="notify_promo">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="switch-container mb-3">
                        <div class="switch-label-wrap">
                            <span class="switch-title">Thông báo hệ thống</span>
                            <span class="switch-desc">Nhận cảnh báo hệ thống</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="notify_system" name="notify_system">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <button type="button" class="btn-save btn-submit-section" data-section="notifications">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>
            </div>

            <!-- 3. Cài đặt thanh toán -->
            <div class="col-xl-6 col-md-6">
                <div class="settings-card">
                    <div class="settings-card-title">
                        <i class="fas fa-credit-card"></i> Cài đặt thanh toán
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label d-block mb-2">Phương thức thanh toán</label>
                        
                        <div class="switch-container py-2">
                            <div class="switch-label-wrap">
                                <span class="switch-title" style="font-size: 0.82rem;">COD (Tiền mặt)</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="payment_cod" name="payment_cod">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="switch-container py-2">
                            <div class="switch-label-wrap">
                                <span class="switch-title" style="font-size: 0.82rem;">Momo</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="payment_momo" name="payment_momo">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="switch-container py-2">
                            <div class="switch-label-wrap">
                                <span class="switch-title" style="font-size: 0.82rem;">VNPay</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="payment_vnpay" name="payment_vnpay">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="switch-container py-2">
                            <div class="switch-label-wrap">
                                <span class="switch-title" style="font-size: 0.82rem;">ZaloPay</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="payment_zalopay" name="payment_zalopay">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="switch-container py-2 border-0">
                            <div class="switch-label-wrap">
                                <span class="switch-title" style="font-size: 0.82rem;">Chuyển khoản ngân hàng</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="payment_transfer" name="payment_transfer">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="order_min">Đơn tối thiểu (đ)</label>
                            <input type="number" class="form-control" id="order_min" name="order_min" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="order_max">Đơn tối đa (đ)</label>
                            <input type="number" class="form-control" id="order_max" name="order_max" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="shipping_fee">Phí giao hàng (đ)</label>
                            <input type="number" class="form-control" id="shipping_fee" name="shipping_fee" required>
                        </div>
                    </div>

                    <button type="button" class="btn-save btn-submit-section" data-section="payments">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>
            </div>

            <!-- 4. Cài đặt bảo mật -->
            <div class="col-xl-6 col-md-6">
                <div class="settings-card">
                    <div class="settings-card-title">
                        <i class="fas fa-shield-halved"></i> Cài đặt bảo mật
                    </div>

                    <div class="switch-container mb-3">
                        <div class="switch-label-wrap">
                            <span class="switch-title">Xác thực 2 lớp</span>
                            <span class="switch-desc">Bật xác thực 2 yếu tố cho Admin</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="security_2fa" name="security_2fa">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label" for="session_timeout">Timeout phiên (phút)</label>
                            <input type="number" class="form-control" id="session_timeout" name="session_timeout" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label" for="max_login_fail">Số lần đăng nhập sai tối đa</label>
                            <input type="number" class="form-control" id="max_login_fail" name="max_login_fail" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_expire">Hết hạn mật khẩu (ngày)</label>
                        <input type="number" class="form-control" id="password_expire" name="password_expire" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="ip_whitelist">IP Whitelist</label>
                        <textarea class="form-control" id="ip_whitelist" name="ip_whitelist" rows="2" placeholder="Nhập danh sách IP, mỗi IP một dòng..."></textarea>
                    </div>

                    <button type="button" class="btn-save btn-submit-section" data-section="security">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>
            </div>

            <!-- 5. Cài đặt ứng dụng (Dòng dưới cùng - Full Width) -->
            <div class="col-12">
                <div class="settings-card">
                    <div class="settings-card-title">
                        <i class="fas fa-mobile-screen-button"></i> Cài đặt ứng dụng
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="switch-container py-3">
                                <div class="switch-label-wrap">
                                    <span class="switch-title">Chế độ bảo trì</span>
                                    <span class="switch-desc">Tạm ngưng dịch vụ toàn hệ thống</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="app_maintenance" name="app_maintenance">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="switch-container py-3 border-0">
                                <div class="switch-label-wrap">
                                    <span class="switch-title">Đặt hàng khách (Guest)</span>
                                    <span class="switch-desc">Cho phép đặt hàng không cần tài khoản</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="app_guest_checkout" name="app_guest_checkout">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="switch-container py-3">
                                <div class="switch-label-wrap">
                                    <span class="switch-title">Cho phép đăng ký</span>
                                    <span class="switch-desc">Khách hàng có thể đăng ký tài khoản mới</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="app_allow_register" name="app_allow_register">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="switch-container py-3 border-0">
                                <div class="switch-label-wrap">
                                    <span class="switch-title">Hiển thị đánh giá</span>
                                    <span class="switch-desc">Hiển thị đánh giá món ăn công khai</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="app_show_reviews" name="app_show_reviews">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="switch-container py-3">
                                <div class="switch-label-wrap">
                                    <span class="switch-title">Bắt buộc xác thực Email</span>
                                    <span class="switch-desc">Bắt buộc xác thực tài khoản qua email</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="app_email_verify" name="app_email_verify">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="switch-container py-3 border-0">
                                <div class="switch-label-wrap">
                                    <span class="switch-title">Tự động nhận đơn</span>
                                    <span class="switch-desc">Tự động duyệt đơn hàng của khách hàng</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="app_auto_accept" name="app_auto_accept">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn-save btn-submit-section" data-section="app">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- Custom Toasts -->
<div class="toast-container-custom" id="toastContainer"></div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadSettings();

        // Đăng ký sự kiện click cho tất cả các nút Lưu cài đặt
        document.querySelectorAll('.btn-submit-section').forEach(btn => {
            btn.addEventListener('click', function() {
                saveSettings(this);
            });
        });
    });

    // Custom Toast function
    function showToast(title, message, type = 'success') {
        const container = document.getElementById('toastContainer');
        
        const toast = document.createElement('div');
        toast.className = `toast-custom ${type}`;
        
        const icon = type === 'success' ? 'fa-check' : 'fa-exclamation-triangle';
        
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${icon}"></i>
            </div>
            <div class="toast-content">
                <p class="toast-title">${title}</p>
                <p class="toast-message">${message}</p>
            </div>
        `;
        
        container.appendChild(toast);
        
        // Trigger reflow for slide animation
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // Load Settings from API
    function loadSettings() {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch('/api/v1/admin/settings', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                const data = res.data;
                
                // Điền dữ liệu vào form
                Object.keys(data).forEach(key => {
                    const el = document.getElementById(key);
                    if (el) {
                        if (el.type === 'checkbox') {
                            el.checked = !!data[key];
                        } else {
                            el.value = data[key];
                        }
                    }
                });
            } else {
                showToast('Lỗi tải cấu hình', 'Không thể kết nối với API cấu hình.', 'error');
            }
        })
        .catch(err => {
            console.error('Error loading settings:', err);
            showToast('Lỗi kết nối', 'Không thể tải cấu hình từ máy chủ.', 'error');
        });
    }

    // Save Settings via API
    function saveSettings(buttonEl) {
        const originalHtml = buttonEl.innerHTML;
        buttonEl.disabled = true;
        buttonEl.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Đang lưu...';

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const form = document.getElementById('systemSettingsForm');
        const formData = new FormData(form);

        // Đọc tất cả các switch checkboxes và gán giá trị boolean thủ công
        // vì các switch checkbox unchecked không tự động được gửi qua FormData của trình duyệt
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            formData.set(cb.name, cb.checked ? 'true' : 'false');
        });

        // Đổi FormData thành object thông thường để gửi JSON
        const payload = {};
        formData.forEach((value, key) => {
            payload[key] = value;
        });

        fetch('/api/v1/admin/settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            buttonEl.disabled = false;
            buttonEl.innerHTML = originalHtml;

            if (res.success) {
                showToast('Lưu thành công', 'Cấu hình hệ thống đã được cập nhật thành công.', 'success');
                // Tải lại dữ liệu để đảm bảo khớp trạng thái
                loadSettings();
            } else {
                showToast('Thất bại', res.message || 'Lỗi cập nhật cấu hình.', 'error');
            }
        })
        .catch(err => {
            buttonEl.disabled = false;
            buttonEl.innerHTML = originalHtml;
            console.error('Error saving settings:', err);
            showToast('Lỗi kết nối', 'Không thể gửi yêu cầu lưu cấu hình.', 'error');
        });
    }
</script>
@endsection

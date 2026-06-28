// API Configuration for Checkout
// NOTE: Cart info uses the API route (with web session), voucher uses web route (to store in session)
const CHECKOUT_API = '/api/v1/checkout';
const CHECKOUT_WEB = '/checkout';

// State management
let checkoutSubtotal = 0;
let appliedVoucherData = null;

// Get CSRF token from meta tag
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// Helper: API call with web session (credentials: same-origin)
async function checkoutApi(url, options = {}) {
    const opts = {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers
        },
        ...options
    };
    if (opts.body && typeof opts.body === 'object' && !(opts.body instanceof FormData)) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(opts.body);
    }
    return fetch(url, opts);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('checkoutForm')) {
        loadCheckoutData();
        setupVoucherToggle();
    }
});

// Load checkout data (cart items + available vouchers)
async function loadCheckoutData() {
    try {
        const response = await checkoutApi(CHECKOUT_API);

        if (response.status === 401 || response.status === 403) {
            window.location.href = '/login';
            return;
        }

        const result = await response.json();

        if (result.success) {
            const data = result.data;
            checkoutSubtotal = data.subtotal || 0;

            renderCartItems(data.cart_items || []);
            renderVouchers(data.vouchers || []);
            updatePricingDisplay(checkoutSubtotal, 0, checkoutSubtotal);
        } else if (result.message === 'Giỏ hàng trống') {
            window.location.href = '/';
        }
    } catch (error) {
        console.error('Error loading checkout info:', error);
    }
}

// Render cart items table
function renderCartItems(items) {
    const container = document.getElementById('checkoutItemsContainer');
    if (!container) return;

    if (!items || items.length === 0) {
        container.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Giỏ hàng trống</td></tr>';
        return;
    }

    container.innerHTML = items.map(item => `
        <tr>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <img src="${item.image || '/images/no-image.png'}" alt="${item.name}"
                        style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                    <div class="fw-bold">${item.name}</div>
                </div>
            </td>
            <td>${formatVND(item.price)} đ</td>
            <td><span class="badge bg-primary">${item.quantity}</span></td>
            <td class="fw-bold text-danger">${formatVND(item.total)} đ</td>
        </tr>
    `).join('');
}

// Render vouchers grid
function renderVouchers(vouchers) {
    const container = document.getElementById('vouchersGrid');
    if (!container) return;

    if (!vouchers || vouchers.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fa fa-tag fa-2x mb-2"></i>
                <p>Hiện tại không có mã giảm giá khả dụng</p>
            </div>`;
        return;
    }

    container.innerHTML = vouchers.map(v => `
        <div class="voucher-card" data-code="${v.code}">
            <div class="voucher-header d-flex align-items-center gap-2">
                <div class="voucher-icon">
                    <i class="fa ${v.loai_giam_gia === 'TienMat' ? 'fa-money-bill-wave' : 'fa-percent'}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">${v.code}</div>
                    <div class="text-muted small">Giảm ${v.loai_giam_gia === 'TienMat' ? formatVND(v.giam_toi_da) + ' đ' : v.percent + '%'}</div>
                </div>
                <button type="button" class="btn btn-sm btn-warning fw-bold"
                    onclick="applyVoucher('${v.code}', this)">
                    Áp dụng
                </button>
            </div>
            ${v.min_order > 0 ? `
                <div class="mt-2 small text-muted">
                    <i class="fa fa-info-circle"></i> Đơn tối thiểu: ${formatVND(v.min_order)} đ
                </div>` : ''}
            ${v.loai_giam_gia === 'PhanTram' && v.giam_toi_da > 0 ? `
                <div class="mt-1 small text-muted">
                    <i class="fa fa-info-circle"></i> Giảm tối đa: ${formatVND(v.giam_toi_da)} đ
                </div>` : ''}
            ${v.expires_at ? `
                <div class="mt-1 small text-danger">
                    <i class="fa fa-clock"></i> HSD: ${new Date(v.expires_at).toLocaleDateString('vi-VN')}
                </div>` : ''}
        </div>
    `).join('');
}

// Apply voucher via WEB route (to store in session for checkout.store)
async function applyVoucher(code, btn) {
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    }

    try {
        const response = await checkoutApi(`${CHECKOUT_WEB}/apply-voucher`, {
            method: 'POST',
            body: JSON.stringify({ voucher_code: code }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        const result = await response.json();

        if (result.success) {
            const v = result.voucher;
            appliedVoucherData = v;

            // Highlight selected card
            document.querySelectorAll('.voucher-card').forEach(c => c.classList.remove('selected'));
            const card = document.querySelector(`.voucher-card[data-code="${code}"]`);
            if (card) card.classList.add('selected');

            // Show applied voucher info
            showAppliedVoucherBanner(result.message, code);

            // Update pricing with discount values
            updatePricingDisplay(
                v.discount_value !== undefined ? (checkoutSubtotal) : checkoutSubtotal,
                v.discount_value || 0,
                v.total_value || checkoutSubtotal
            );
            showToast('success', result.message);
        } else {
            showToast('danger', result.message || 'Mã giảm giá không hợp lệ');
        }
    } catch (err) {
        console.error('Apply voucher error:', err);
        showToast('danger', 'Không thể áp dụng mã giảm giá lúc này');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Áp dụng';
        }
    }
}

// Show applied voucher banner
function showAppliedVoucherBanner(message, code) {
    const section = document.getElementById('manualAppliedVoucherSection');
    const text = document.getElementById('manualAppliedVoucherText');
    if (section && text) {
        text.textContent = message;
        section.style.display = 'block';
    }
}

// Remove applied voucher
function removeAppliedVoucher() {
    appliedVoucherData = null;
    document.querySelectorAll('.voucher-card').forEach(c => c.classList.remove('selected'));

    const section = document.getElementById('manualAppliedVoucherSection');
    if (section) section.style.display = 'none';

    updatePricingDisplay(checkoutSubtotal, 0, checkoutSubtotal);
    showToast('info', 'Đã bỏ mã giảm giá');
}

// Update pricing display
function updatePricingDisplay(subtotalVal, discountVal, totalVal) {
    const subtotalEl = document.getElementById('subtotal');
    const discountEl = document.getElementById('discountAmount');
    const totalEl = document.getElementById('totalAmount');
    const discountRow = document.getElementById('discountRow');

    if (subtotalEl) subtotalEl.textContent = formatVND(subtotalVal) + ' đ';

    if (discountRow) {
        if (discountVal > 0) {
            discountRow.style.cssText = 'display:flex !important';
            if (discountEl) discountEl.textContent = '-' + formatVND(discountVal) + ' đ';
        } else {
            discountRow.style.cssText = 'display:none !important';
        }
    }

    if (totalEl) totalEl.textContent = formatVND(totalVal) + ' đ';
}

// Voucher panel toggle
function setupVoucherToggle() {
    const btn = document.getElementById('toggleVouchersBtn');
    const icon = document.getElementById('toggleIcon');
    const text = document.getElementById('toggleText');
    const panel = document.getElementById('vouchersCollapse');

    if (!btn || !panel) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const shown = panel.classList.contains('show');
        panel.classList.toggle('show', !shown);
        if (icon) icon.className = shown ? 'fa fa-chevron-up' : 'fa fa-chevron-down';
        if (text) text.textContent = shown ? 'Thu gọn' : 'Hiển thị';
    });
}

// Wire up remove voucher button
document.addEventListener('DOMContentLoaded', function () {
    const removeBtn = document.getElementById('removeManualVoucherBtn');
    if (removeBtn) removeBtn.addEventListener('click', removeAppliedVoucher);
});

// Format currency VND
function formatVND(value) {
    return new Intl.NumberFormat('vi-VN').format(value || 0);
}

// Toast notification
function showToast(type, message) {
    let container = document.getElementById('checkoutToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'checkoutToastContainer';
        container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} shadow`;
    toast.style.cssText = 'min-width:280px;margin-bottom:8px;';
    const icons = { success: 'check-circle', danger: 'exclamation-circle', info: 'info-circle', warning: 'exclamation-triangle' };
    toast.innerHTML = `<i class="fa fa-${icons[type] || 'info-circle'} me-2"></i> ${message}`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

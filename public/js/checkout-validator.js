// Validate form + voucher, update pricing, and show center popup for payment-method error

// ====================== Helpers: Inline input errors ======================
function showInputError(inputId, message) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.classList.remove("is-invalid");
    let feedback = input.parentNode.querySelector(".invalid-feedback");
    if (!feedback) {
        feedback = document.createElement("div");
        feedback.className = "invalid-feedback";
        input.parentNode.appendChild(feedback);
    }
    input.classList.add("is-invalid");
    feedback.textContent = message;
}

function clearInputError(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.classList.remove("is-invalid");
    const feedback = input.parentNode.querySelector(".invalid-feedback");
    if (feedback) feedback.textContent = "";
}

// ====================== Helpers: Payment method error (inline) ======================
function showPaymentErrorInline(message) {
    const paymentBox = document.querySelector(".card .card-body");
    if (paymentBox) {
        let feedback = paymentBox.querySelector(
            ".invalid-feedback.payment-method"
        );
        if (!feedback) {
            feedback = document.createElement("div");
            feedback.className = "invalid-feedback payment-method d-block";
            paymentBox.appendChild(feedback);
        }
        feedback.textContent = message;
    }
}

function clearPaymentError() {
    const paymentBox = document.querySelector(".card .card-body");
    if (!paymentBox) return;
    const feedback = paymentBox.querySelector(
        ".invalid-feedback.payment-method"
    );
    if (feedback) feedback.textContent = "";
}

// ====================== Helper: Show Voucher Error ======================
function showVoucherErrorMessage(message) {
    const voucherInput = document.getElementById("voucherInput");
    if (!voucherInput) return;

    // Clear previous errors
    voucherInput.classList.remove("is-invalid");
    voucherInput.parentNode.querySelector(".invalid-feedback")?.remove();

    // Create and show error
    let feedback = document.createElement("div");
    feedback.className = "invalid-feedback";
    voucherInput.parentNode.appendChild(feedback);
    voucherInput.classList.add("is-invalid");
    feedback.textContent = message;

    // Clear success message
    const voucherMessageDiv = document.getElementById("voucherMessage");
    if (voucherMessageDiv) voucherMessageDiv.innerHTML = "";
}

// ====================== Unified Voucher Validation ======================

function validateVoucherEmpty(value) {
    if (!value || !value.trim()) {
        return true; // Empty voucher is valid (optional field)
    }
    return false; // Has value, need further validation
}

function validateVoucher(value, checkNotFound = false) {
    if (!/^[a-zA-Z0-9]+$/.test(value)) {
        showVoucherErrorMessage("Mã voucher chỉ được chứa chữ cái và số.");
        return false;
    }
    if (value.length > 12) {
        showVoucherErrorMessage("Mã voucher không được vượt quá 12 ký tự.");
        return false;
    }
    if (checkNotFound) {
        showVoucherErrorMessage("Mã voucher không tồn tại.");
        return false;
    }
    return true;
}

// ====================== Clear Voucher Error ======================
function clearVoucherError() {
    const voucherInput = document.getElementById("voucherInput");
    if (!voucherInput) return;
    voucherInput.classList.remove("is-invalid");
    const feedback = voucherInput.parentNode.querySelector(".invalid-feedback");
    if (feedback) feedback.textContent = "";
}

// ====================== Validators ======================

function validateName(name) {
    if (!name.trim())
        return "Họ tên người nhận hàng không được để trống.";
    if (name.length < 2)
        return "Vui lòng điền họ tên hợp lệ (tối thiểu 2 ký tự).";
    if (name.length > 100)
        return "Vui lòng điền họ tên nhỏ hơn 100 ký tự.";
    if (!/^[a-zA-ZÀ-ỹ\s]+$/.test(name))
        return "Họ tên chỉ có chữ cái.";
    return "";
}

function validatePhone(phoneNumber) {
    if (!phoneNumber.trim())
        return "Vui lòng điền số điện thoại người nhận hàng.";
    if (!/^\d+$/.test(phoneNumber) || parseInt(phoneNumber) <= 0)
        return "Vui lòng điền số điện thoại là số nguyên.";
    if (phoneNumber.length < 10 || phoneNumber.length > 11)
        return "Vui lòng điền số điện thoại có 10-11 chữ số.";
    return "";
}

function validateAddress(address) {
    if (!address.trim())
        return "Vui lòng điền địa chỉ giao hàng.";
    if (address.length < 10)
        return "Vui lòng điền địa chỉ giao hàng nhiều hơn 10 ký tự.";
    if (address.length > 200)
        return "Vui lòng điền địa chỉ giao hàng ít hơn 200 ký tự.";
    return "";
}

// ====================== Center Popup (no dependency) ======================
function ensurePopupStyles() {
    if (document.getElementById("cp-styles")) return;
    const style = document.createElement("style");
    style.id = "cp-styles";
    style.textContent = `
  .cp-overlay{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:10550}
  .cp-overlay.show{display:flex;animation:cp-fade .15s ease-out}
  @keyframes cp-fade{from{opacity:0}to{opacity:1}}
  .cp-modal{width:min(92vw,520px);background:#fff;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.2);padding:20px 20px 16px;transform:scale(.96);animation:cp-pop .18s ease-out forwards}
  @keyframes cp-pop{to{transform:scale(1)}}
  .cp-head{display:flex;align-items:center;gap:12px;margin-bottom:8px}
  .cp-icon{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#fee2e2;color:#dc2626;font-weight:700;font-size:18px}
  .cp-title{font-weight:700;margin:0;font-size:16px}
  .cp-msg{color:#333;margin:8px 0 4px;font-size:15px;line-height:1.4}
  .cp-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:12px}
  .cp-btn{border:0;border-radius:8px;padding:8px 14px;cursor:pointer;font-weight:600}
  .cp-btn.primary{background:#dc2626;color:#fff}
  .cp-btn.ghost{background:#f3f4f6;color:#111827}
  `;
    document.head.appendChild(style);
}

function createPopupContainer() {
    if (document.getElementById("cp-overlay")) return;
    ensurePopupStyles();
    const overlay = document.createElement("div");
    overlay.id = "cp-overlay";
    overlay.className = "cp-overlay";
    overlay.innerHTML = `
    <div class="cp-modal" role="alertdialog" aria-modal="true" aria-labelledby="cp-title" aria-describedby="cp-msg">
      <div class="cp-head">
        <div class="cp-icon">!</div>
        <h3 id="cp-title" class="cp-title">Thông báo</h3>
      </div>
      <div id="cp-msg" class="cp-msg"></div>
      <div class="cp-actions">
        <button type="button" class="cp-btn ghost" data-cp-close>Đóng</button>
        <button type="button" class="cp-btn primary" data-cp-close>OK</button>
      </div>
    </div>
  `;
    document.body.appendChild(overlay);
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay || e.target.hasAttribute("data-cp-close"))
            hideCenterPopup();
    });
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") hideCenterPopup();
    });
}

function showCenterPopup(message, title) {
    createPopupContainer();
    const overlay = document.getElementById("cp-overlay");
    if (!overlay) return;
    overlay.querySelector("#cp-title").textContent = title || "Thông báo";
    overlay.querySelector("#cp-msg").textContent = message || "";
    overlay.classList.add("show");
}

function hideCenterPopup() {
    const overlay = document.getElementById("cp-overlay");
    if (overlay) overlay.classList.remove("show");
}

// ====================== Pricing updater after voucher ======================
function updatePricing(voucher) {
    const discountRow = document.getElementById("discountRow");
    const discountAmount = document.getElementById("discountAmount");
    const totalAmount = document.getElementById("totalAmount");
    if (voucher && discountRow && discountAmount && totalAmount) {
        discountRow.style.cssText = 'display: flex !important;';
        
        let discountClean = voucher.discount.toString().replace(/[^0-9.-]/g, '').trim();
        let totalClean = voucher.total.toString().replace(/[^0-9.-]/g, '').trim();
        
        if (!isNaN(parseFloat(discountClean))) {
            discountClean = parseFloat(discountClean).toLocaleString('vi-VN');
        }
        if (!isNaN(parseFloat(totalClean))) {
            totalClean = parseFloat(totalClean).toLocaleString('vi-VN');
        }

        discountAmount.textContent = `-${discountClean} đ`;
        totalAmount.textContent = `${totalClean} đ`;
    } else if (!voucher && discountRow && totalAmount) {
        discountRow.style.cssText = 'display: none !important;';
        if (typeof subtotal !== 'undefined') {
            totalAmount.textContent = `${subtotal.toLocaleString('vi-VN')} đ`;
        }
    }
}

// ====================== Bootstrapping ======================
document.addEventListener("DOMContentLoaded", function () {
    // Refs
    const nameInput = document.getElementById("customer_name");
    const phoneInput = document.getElementById("customer_phone");
    const addressInput = document.getElementById("customer_address");
    const voucherInput = document.getElementById("voucherInput");
    const applyBtn = document.getElementById("applyVoucherBtn");
    const voucherMessageDiv = document.getElementById("voucherMessage");
    const form = document.getElementById("checkoutForm");
    const submitBtn = document.getElementById("submitBtn");

    // Realtime validation
    if (nameInput) {
        const h = () => {
            const msg = validateName(nameInput.value);
            msg
                ? showInputError("customer_name", msg)
                : clearInputError("customer_name");
        };
        nameInput.addEventListener("input", h);
        nameInput.addEventListener("blur", h);
    }

    if (phoneInput) {
        const h = () => {
            const msg = validatePhone(phoneInput.value);
            msg
                ? showInputError("customer_phone", msg)
                : clearInputError("customer_phone");
        };
        phoneInput.addEventListener("input", h);
        phoneInput.addEventListener("blur", h);
    }

    if (addressInput) {
        const h = () => {
            const msg = validateAddress(addressInput.value);
            msg
                ? showInputError("customer_address", msg)
                : clearInputError("customer_address");
        };
        addressInput.addEventListener("input", h);
        addressInput.addEventListener("blur", h);
    }

    // Voucher realtime validation - Chỉ check format và length
    if (voucherInput) {
        const h = () => {
            const value = voucherInput.value.trim();
            // Chỉ validate khi có giá trị, bỏ qua khi trống
            if (value) {
                validateVoucher(value, false);
            } else {
                // Xóa lỗi khi ô trống
                clearVoucherError();
            }
            // validateVoucher(voucherInput.value, false);
        };
        voucherInput.addEventListener("input", h);
        voucherInput.addEventListener("blur", h);
    }

    // Apply voucher
    if (applyBtn && voucherInput) {
        applyBtn.addEventListener("click", function () {
            const code = voucherInput.value.trim();

            // Validate format và length trước
            if (!validateVoucher(code, false)) {
                return;
            }

            clearVoucherError();
            if (voucherMessageDiv) voucherMessageDiv.innerHTML = "";

            applyBtn.innerHTML =
                '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
            applyBtn.disabled = true;

            fetch("/checkout/apply-voucher", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                },
                body: JSON.stringify({ voucher_code: code }),
            })
                .then((r) => r.json())
                .then((data) => {
                    if (data.success) {
                        if (voucherMessageDiv) {
                            voucherMessageDiv.innerHTML = `<div class="alert alert-success alert-sm py-2 px-3 mb-0">${data.message || "Áp dụng mã thành công!"
                                }</div>`;
                            setTimeout(
                                () => (voucherMessageDiv.innerHTML = ""),
                                5000
                            );
                        }
                        if (data.voucher) updatePricing(data.voucher);
                        clearVoucherError();
                    } else {
                        // Sử dụng unified function với checkNotFound = true
                        validateVoucher(code, true);
                    }
                })
                .catch(() => {
                    if (voucherMessageDiv)
                        voucherMessageDiv.innerHTML = `<div class="alert alert-danger alert-sm py-2 px-3 mb-0">Có lỗi xảy ra. Vui lòng thử lại.</div>`;
                })
                .finally(() => {
                    applyBtn.innerHTML = "Áp dụng";
                    applyBtn.disabled = false;
                });
        });

        voucherInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                applyBtn.click();
            }
        });
    }

    // Clear payment inline error when user changes selection
    document
        .querySelectorAll('input[name="payment_method"]')
        .forEach((radio) => {
            radio.addEventListener("change", function () {
                clearPaymentError();
            });
        });

    // Submit validation
    if (form && submitBtn) {
        form.addEventListener("submit", function (e) {
            let hasError = false;

            const nameMsg = validateName(nameInput?.value || "");
            if (nameMsg) {
                showInputError("customer_name", nameMsg);
                hasError = true;
            }

            const phoneMsg = validatePhone(phoneInput?.value || "");
            if (phoneMsg) {
                showInputError("customer_phone", phoneMsg);
                hasError = true;
            }

            const addressMsg = validateAddress(addressInput?.value || "");
            if (addressMsg) {
                showInputError("customer_address", addressMsg);
                hasError = true;
            }

            // Voucher validation - chỉ check format và length
            // if (!validateVoucher(voucherInput?.value || '', false)) {
            //   hasError = true;
            // }
            const voucherValue = voucherInput?.value?.trim() || "";
            if (voucherValue && !validateVoucher(voucherValue, false)) {
                hasError = true;
            }

            const paymentMethod = document.querySelector(
                'input[name="payment_method"]:checked'
            );
            if (!paymentMethod) {
                showCenterPopup(
                    "Vui lòng chọn phương thức thanh toán. (7E.14)",
                    "Thiếu phương thức thanh toán"
                );
                hasError = true;
            } else {
                clearPaymentError();
            }

            if (hasError) {
                e.preventDefault();
                submitBtn.innerHTML =
                    '<i class="fa fa-check-circle"></i> Đặt hàng ngay';
                submitBtn.disabled = false;
                return false;
            }

            e.preventDefault();
            
            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'COD';
            let warningText = '';
            
            if (selectedPaymentMethod === 'Online') {
                warningText = 'Đơn hàng sẽ không thể thay đổi sau khi chuyển sang bước Thanh toán trực tuyến.';
            } else {
                warningText = 'Đơn hàng sẽ được gửi ngay đến nhà hàng để chuẩn bị và không thể thay đổi.';
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Xác nhận đặt hàng',
                    html: `Vui lòng kiểm tra kỹ danh sách món ăn, số lượng và địa chỉ giao hàng.<br><br><span style="color: #e53e3e; font-weight: 600;">Lưu ý: ${warningText}</span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check"></i> Xác nhận',
                    cancelButtonText: 'Quay lại',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
                        submitBtn.disabled = true;
                        form.submit();
                    } else {
                        submitBtn.innerHTML = '<i class="fa fa-check-circle"></i> Đặt hàng ngay';
                        submitBtn.disabled = false;
                    }
                });
            } else {
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
                submitBtn.disabled = true;
                form.submit();
            }
        });
    }

    // Xử lý bfcache (trường hợp người dùng ấn nút Back từ PayOS hoặc trang khác)
    window.addEventListener('pageshow', function (event) {
        // event.persisted = true nghĩa là trang được load lại từ bộ nhớ đệm của trình duyệt (bfcache) mà không cần gọi Server
        if (event.persisted) {
            // Ép trình duyệt tải lại trang (reload) để Server PHP chạy logic kiểm tra giỏ hàng
            // Từ đó CheckoutController sẽ thấy giỏ trống và redirect về Lịch sử đơn hàng
            window.location.reload();
        }
    });
});

// Export functions for testing
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateAddress,
        validateName,
        validatePhone,
        validateVoucher
    };
}

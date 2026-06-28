/**
 * Validator cho form Đối tác vận chuyển
 * Hỗ trợ validation theo yêu cầu 6E.1 - 6E.11
 */

class DoiTacVanChuyenValidator {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    // Hiển thị lỗi
    showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        let feedback;
        const isInputGroup = field.parentNode.classList.contains('input-group');
        
        if (isInputGroup) {
            feedback = field.parentNode.parentNode.querySelector(".invalid-feedback") || 
                       field.parentNode.parentNode.querySelector(".error-message");
        } else {
            feedback = field.parentNode.querySelector(".invalid-feedback") || 
                       field.parentNode.querySelector(".error-message");
        }

        field.classList.add("is-invalid");

        if (feedback) {
            feedback.textContent = message;
            feedback.style.display = "block";
        } else {
            const errorDiv = document.createElement("div");
            errorDiv.className = "invalid-feedback error-message";
            errorDiv.textContent = message;
            errorDiv.style.display = "block";
            
            if (isInputGroup) {
                field.parentNode.parentNode.appendChild(errorDiv);
            } else {
                field.parentNode.appendChild(errorDiv);
            }
        }
    }

    // Xóa lỗi
    clearError(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        let feedback;
        const isInputGroup = field.parentNode.classList.contains('input-group');
        
        if (isInputGroup) {
            feedback = field.parentNode.parentNode.querySelector(".invalid-feedback") || 
                       field.parentNode.parentNode.querySelector(".error-message");
        } else {
            feedback = field.parentNode.querySelector(".invalid-feedback") || 
                       field.parentNode.querySelector(".error-message");
        }

        field.classList.remove("is-invalid");

        if (feedback) {
            feedback.style.display = "none";
        }
    }

    // Validate tên đối tác
    validateTenDoiTac() {
        const field = document.getElementById("ten_doi_tac");
        if (!field) return true;

        const value = field.value.trim();

        // 6E.1: Bỏ trống
        if (!value) {
            this.showError(
                "ten_doi_tac",
                "Tên đối tác vận chuyển không được bỏ trống"
            );
            return false;
        }

        // 6E.2: Quá 100 ký tự
        if (value.length > 100) {
            this.showError(
                "ten_doi_tac",
                "Tên đối tác vận chuyển không quá 100 ký tự"
            );
            return false;
        }

        // 6E.3: Chứa ký tự đặc biệt (chỉ cho phép chữ cái, số, khoảng trắng, dấu ngoặc đơn, dấu gạch ngang, dấu chấm, &)
        const specialCharRegex = /^[a-zA-ZÀ-ỹ0-9\s\(\)\-\.&]+$/;
        if (!specialCharRegex.test(value)) {
            this.showError(
                "ten_doi_tac",
                "Tên đối tác vận chuyển không chứa ký tự đặc biệt"
            );
            return false;
        }

        this.clearError("ten_doi_tac");
        return true;
    }

    // Validate số điện thoại
    validateSoDienThoai() {
        const field = document.getElementById("so_dien_thoai");
        if (!field) return true;

        const value = field.value.trim();

        // 6E.4: Bỏ trống
        if (!value) {
            this.showError(
                "so_dien_thoai",
                "Số điện thoại liên hệ không được bỏ trống"
            );
            return false;
        }

        // 6E.5: Không phải số
        if (!/^\d+$/.test(value)) {
            this.showError(
                "so_dien_thoai",
                "Số điện thoại liên hệ chỉ chấp nhận chữ số"
            );
            return false;
        }

        // 6E.6: Độ dài < 10 hoặc > 11
        if (value.length < 10 || value.length > 11) {
            this.showError(
                "so_dien_thoai",
                "Số điện thoại liên hệ phải có 10–11 chữ số"
            );
            return false;
        }

        // 6E.7: Không bắt đầu bằng 0
        if (!value.startsWith("0")) {
            this.showError(
                "so_dien_thoai",
                "Số điện thoại liên hệ phải bắt đầu bằng số 0"
            );
            return false;
        }

        this.clearError("so_dien_thoai");
        return true;
    }

    // Validate địa chỉ trụ sở
    validateDiaChiTruSo() {
        const field = document.getElementById("dia_chi_tru_so");
        if (!field) return true;

        const value = field.value.trim();

        // 6E.8: Bỏ trống
        if (!value) {
            this.showError(
                "dia_chi_tru_so",
                "Địa chỉ trụ sở không được bỏ trống"
            );
            return false;
        }

        // 6E.9: Quá 200 ký tự
        if (value.length > 200) {
            this.showError(
                "dia_chi_tru_so",
                "Địa chỉ trụ sở không quá 200 ký tự"
            );
            return false;
        }

        this.clearError("dia_chi_tru_so");
        return true;
    }

    // Validate email
    validateEmailLienHe() {
        const field = document.getElementById("email_lien_he");
        if (!field) return true;

        const value = field.value.trim();

        // 6E.10: Bỏ trống
        if (!value) {
            this.showError("email_lien_he", "Email không được bỏ trống");
            return false;
        }

        // 6E.11: Sai định dạng
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            this.showError("email_lien_he", "Email không hợp lệ");
            return false;
        }

        this.clearError("email_lien_he");
        return true;
    }

    // Validate phí vận chuyển
    validatePhiVanChuyen() {
        const field = document.getElementById("phi_van_chuyen");
        if (!field) return true;

        const value = field.value.trim();
        if (!value) {
            this.showError("phi_van_chuyen", "Phí vận chuyển không được bỏ trống");
            return false;
        }

        if (isNaN(value) || parseFloat(value) < 0) {
            this.showError("phi_van_chuyen", "Phí vận chuyển phải là số không âm");
            return false;
        }

        this.clearError("phi_van_chuyen");
        return true;
    }

    // Validate phí/km
    validatePhiKm() {
        const field = document.getElementById("phi_km");
        if (!field) return true;

        const value = field.value.trim();
        if (!value) {
            this.showError("phi_km", "Phí/km không được bỏ trống");
            return false;
        }

        if (isNaN(value) || parseFloat(value) < 0) {
            this.showError("phi_km", "Phí/km phải là số không âm");
            return false;
        }

        this.clearError("phi_km");
        return true;
    }

    // Validate người liên hệ
    validateNguoiLienHe() {
        const field = document.getElementById("nguoi_lien_he");
        if (!field) return true;

        const value = field.value.trim();

        if (!value) {
            this.showError("nguoi_lien_he", "Tên người liên hệ không được bỏ trống");
            return false;
        }

        if (value.length > 255) {
            this.showError("nguoi_lien_he", "Tên người liên hệ không quá 255 ký tự");
            return false;
        }

        const nameRegex = /^[a-zA-ZÀ-ỹ\s]+$/;
        if (!nameRegex.test(value)) {
            this.showError("nguoi_lien_he", "Tên người liên hệ chỉ được chứa chữ cái và khoảng trắng");
            return false;
        }

        this.clearError("nguoi_lien_he");
        return true;
    }

    // Validate toàn bộ form
    validateAll() {
        const isValidTen = this.validateTenDoiTac();
        const isValidPhone = this.validateSoDienThoai();
        const isValidAddress = this.validateDiaChiTruSo();
        const isValidEmail = this.validateEmailLienHe();
        const isValidPhi = this.validatePhiVanChuyen();
        const isValidPhiKm = this.validatePhiKm();
        const isValidNguoiLienHe = this.validateNguoiLienHe();

        return isValidTen && isValidPhone && isValidAddress && isValidEmail && isValidPhi && isValidPhiKm && isValidNguoiLienHe;
    }

    // Bind events
    bindEvents() {
        // Real-time validation
        const tenDoiTacField = document.getElementById("ten_doi_tac");
        const soDienThoaiField = document.getElementById("so_dien_thoai");
        const diaChiTruSoField = document.getElementById("dia_chi_tru_so");
        const emailLienHeField = document.getElementById("email_lien_he");
        const phiVanChuyenField = document.getElementById("phi_van_chuyen");
        const phiKmField = document.getElementById("phi_km");
        const nguoiLienHeField = document.getElementById("nguoi_lien_he");

        if (tenDoiTacField) {
            tenDoiTacField.addEventListener("blur", () => this.validateTenDoiTac());
            tenDoiTacField.addEventListener("input", () => this.clearError("ten_doi_tac"));
        }

        if (soDienThoaiField) {
            soDienThoaiField.addEventListener("blur", () => this.validateSoDienThoai());
            soDienThoaiField.addEventListener("input", () => this.clearError("so_dien_thoai"));
        }

        if (diaChiTruSoField) {
            diaChiTruSoField.addEventListener("blur", () => this.validateDiaChiTruSo());
            diaChiTruSoField.addEventListener("input", () => this.clearError("dia_chi_tru_so"));
        }

        if (emailLienHeField) {
            emailLienHeField.addEventListener("blur", () => this.validateEmailLienHe());
            emailLienHeField.addEventListener("input", () => this.clearError("email_lien_he"));
        }

        if (phiVanChuyenField) {
            phiVanChuyenField.addEventListener("blur", () => this.validatePhiVanChuyen());
            phiVanChuyenField.addEventListener("input", () => this.clearError("phi_van_chuyen"));
        }

        if (phiKmField) {
            phiKmField.addEventListener("blur", () => this.validatePhiKm());
            phiKmField.addEventListener("input", () => this.clearError("phi_km"));
        }

        if (nguoiLienHeField) {
            nguoiLienHeField.addEventListener("blur", () => this.validateNguoiLienHe());
            nguoiLienHeField.addEventListener("input", () => this.clearError("nguoi_lien_he"));
        }
    }

    // Setup form submission
    setupFormSubmission(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener("submit", (e) => {
            e.preventDefault();

            if (!this.validateAll()) {
                return false;
            }

            // If all validations pass, submit the form via API
            const submitBtn = form.querySelector('button[type="submit"]');
            let originalText = '';
            if (submitBtn) {
                originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-1"></i>Đang lưu...';
            }

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const isEdit = form.id === 'editForm';
            const id = form.getAttribute('data-id');
            const url = isEdit ? `/api/v1/admin/doi-tac-van-chuyen/${id}` : '/api/v1/admin/doi-tac-van-chuyen';

            fetch(url, {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    localStorage.setItem('admin_success', isEdit ? 'Cập nhật đối tác vận chuyển thành công!' : 'Thêm đối tác vận chuyển thành công!');
                    window.location.href = '/admin/doi-tac-van-chuyen';
                } else {
                    window.showAdminToast(`Lỗi: ${res.message || 'Không thể lưu.'}`, 'error');
                    if (res.errors) {
                        for (const [key, value] of Object.entries(res.errors)) {
                            this.showError(key, value[0]);
                        }
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showAdminToast('Đã xảy ra lỗi khi lưu.', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        });
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    window.doiTacValidator = new DoiTacVanChuyenValidator();

    // Setup form submissions for common form IDs
    if (document.getElementById("createForm")) {
        window.doiTacValidator.setupFormSubmission("createForm");
    }

    if (document.getElementById("editForm")) {
        window.doiTacValidator.setupFormSubmission("editForm");
    }
});

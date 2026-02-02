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

        const feedback =
            field.parentNode.querySelector(".invalid-feedback") ||
            field.parentNode.querySelector(".error-message");

        field.classList.add("is-invalid");

        if (feedback) {
            feedback.textContent = message;
            feedback.style.display = "block";
        } else {
            const errorDiv = document.createElement("div");
            errorDiv.className = "invalid-feedback error-message";
            errorDiv.textContent = message;
            errorDiv.style.display = "block";
            field.parentNode.appendChild(errorDiv);
        }
    }

    // Xóa lỗi
    clearError(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        const feedback =
            field.parentNode.querySelector(".invalid-feedback") ||
            field.parentNode.querySelector(".error-message");

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

    // Validate toàn bộ form
    validateAll() {
        const isValidTen = this.validateTenDoiTac();
        const isValidPhone = this.validateSoDienThoai();
        const isValidAddress = this.validateDiaChiTruSo();
        const isValidEmail = this.validateEmailLienHe();

        return isValidTen && isValidPhone && isValidAddress && isValidEmail;
    }

    // Bind events
    bindEvents() {
        // Real-time validation
        const tenDoiTacField = document.getElementById("ten_doi_tac");
        const soDienThoaiField = document.getElementById("so_dien_thoai");
        const diaChiTruSoField = document.getElementById("dia_chi_tru_so");
        const emailLienHeField = document.getElementById("email_lien_he");

        if (tenDoiTacField) {
            tenDoiTacField.addEventListener("blur", () =>
                this.validateTenDoiTac()
            );
        }

        if (soDienThoaiField) {
            soDienThoaiField.addEventListener("blur", () =>
                this.validateSoDienThoai()
            );

            // Note: Removed auto-format to allow testing validation with invalid characters
            // Format phone number (chỉ cho phép nhập số)
            // soDienThoaiField.addEventListener("input", function (e) {
            //     let value = e.target.value.replace(/\D/g, "");
            //     if (value.length > 11) {
            //         value = value.substring(0, 11);
            //     }
            //     e.target.value = value;
            // });
        }

        if (diaChiTruSoField) {
            diaChiTruSoField.addEventListener("blur", () =>
                this.validateDiaChiTruSo()
            );
        }

        if (emailLienHeField) {
            emailLienHeField.addEventListener("blur", () =>
                this.validateEmailLienHe()
            );
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

            // If all validations pass, submit the form
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-1"></i>Đang lưu...';
            }

            // Actually submit the form
            form.submit();
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

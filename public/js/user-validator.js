class UserValidator {
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

    // Validate Họ tên
    validateHoTen() {
        const field = document.getElementById("HoTen");
        if (!field) return true;
        const value = field.value.trim();

        if (!value) {
            this.showError("HoTen", "Họ và tên không được bỏ trống");
            return false;
        }
        if (value.length < 2) {
            this.showError("HoTen", "Họ tên phải chứa ít nhất 2 ký tự");
            return false;
        }
        this.clearError("HoTen");
        return true;
    }

    // Validate Tên đăng nhập
    validateTenDangNhap() {
        const field = document.getElementById("TenDangNhap");
        if (!field) return true;
        const value = field.value.trim();

        if (!value) {
            this.showError("TenDangNhap", "Tên đăng nhập không được bỏ trống");
            return false;
        }
        if (value.length < 3) {
            this.showError("TenDangNhap", "Tên đăng nhập phải có ít nhất 3 ký tự");
            return false;
        }
        this.clearError("TenDangNhap");
        return true;
    }

    // Validate Email
    validateEmail() {
        const field = document.getElementById("Email");
        if (!field) return true;
        const value = field.value.trim();

        if (!value) {
            this.showError("Email", "Email không được bỏ trống");
            return false;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            this.showError("Email", "Email không đúng định dạng");
            return false;
        }
        this.clearError("Email");
        return true;
    }

    // Validate Số điện thoại
    validateSoDienThoai() {
        const field = document.getElementById("SoDienThoai");
        if (!field) return true;
        const value = field.value.trim();

        if (!value) {
            this.showError("SoDienThoai", "Số điện thoại không được bỏ trống");
            return false;
        }
        if (value.length > 15) {
            this.showError("SoDienThoai", "Số điện thoại không được vượt quá 15 ký tự");
            return false;
        }
        if (value.length < 10 || value.length > 11) {
            this.showError("SoDienThoai", "Số điện thoại phải là số từ 10-11 chữ số");
            return false;
        }
        if (!/^\d+$/.test(value)) {
            this.showError("SoDienThoai", "Số điện thoại chỉ được chứa chữ số");
            return false;
        }
        const phoneRegex = /^(02|03|05|07|08|09)\d{8,9}$/;
        if (!phoneRegex.test(value)) {
            this.showError("SoDienThoai", "Số điện thoại không đúng định dạng");
            return false;
        }
        this.clearError("SoDienThoai");
        return true;
    }

    // Validate Mật khẩu
    validateMatKhau() {
        const field = document.getElementById("MatKhau");
        if (!field) return true;
        const value = field.value;

        if (!value) {
            this.showError("MatKhau", "Mật khẩu không được bỏ trống");
            return false;
        }
        if (value.length < 6) {
            this.showError("MatKhau", "Mật khẩu phải có ít nhất 6 ký tự");
            return false;
        }
        const passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/;
        if (!passwordRegex.test(value)) {
            this.showError("MatKhau", "Mật khẩu từ 6 ký tự, chứa ít nhất 1 chữ cái viết hoa và 1 ký tự đặc biệt");
            return false;
        }
        this.clearError("MatKhau");
        return true;
    }

    // Bind Events
    bindEvents() {
        const fields = ["HoTen", "TenDangNhap", "Email", "SoDienThoai", "MatKhau"];
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', () => {
                    this.clearError(fieldId);
                });
            }
        });

        const form = document.getElementById('createUserForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                const isHoTenValid = this.validateHoTen();
                const isTenDangNhapValid = this.validateTenDangNhap();
                const isEmailValid = this.validateEmail();
                const isSoDienThoaiValid = this.validateSoDienThoai();
                const isMatKhauValid = this.validateMatKhau();

                if (!isHoTenValid || !isTenDangNhapValid || !isEmailValid || !isSoDienThoaiValid || !isMatKhauValid) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    e.preventDefault();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';
                    
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData.entries());
                    
                    fetch('/api/v1/admin/users', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.setItem('admin_success', 'Tạo người dùng thành công!');
                            window.location.href = '/admin/users';
                        } else {
                            if (data.errors) {
                                for (const key in data.errors) {
                                    this.showError(key, data.errors[key][0]);
                                }
                            } else {
                                window.showAdminToast('Lỗi: ' + (data.message || 'Đã xảy ra lỗi.'), 'error');
                            }
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.showAdminToast('Đã xảy ra lỗi kết nối.', 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
                }
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new UserValidator();
});

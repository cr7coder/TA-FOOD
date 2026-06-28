// ====================== Validation Helper Functions ======================
function setInvalid(el, msg) {
    if (!el) return;
    el.classList.add("is-invalid");
    let feedback = el.parentElement.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback text-start mt-1';
        feedback.style.fontSize = '0.85rem';
        el.parentElement.appendChild(feedback);
    }
    feedback.innerText = msg;
}

function clearInvalid(el) {
    if (!el) return;
    el.classList.remove("is-invalid");
    let feedback = el.parentElement.querySelector('.invalid-feedback');
    if (feedback) feedback.innerText = '';
}

function clearAllInvalid() {
    document.querySelectorAll('.is-invalid').forEach(el => clearInvalid(el));
}

function showAlert(type, message) {
    if (typeof window.showAlert === 'function') {
        window.showAlert(type, message);
        return;
    }
    const alertContainer = document.getElementById("alertContainer");
    if (!alertContainer) return;
    alertContainer.innerHTML = `
        <div class="alert alert-${type}" role="alert">
            <i class="fas fa-${type === "success" ? "check-circle" : "exclamation-circle"}"></i>
            ${message}
        </div>
    `;
    setTimeout(() => {
        alertContainer.innerHTML = "";
    }, 6000);
}

async function checkExists(type, value) {
    let url = "";
    if (type === "username") url = "/api/check-username?username=" + encodeURIComponent(value);
    if (type === "email") url = "/api/check-email?email=" + encodeURIComponent(value);
    if (type === "phone") url = "/api/check-phone?phone=" + encodeURIComponent(value);
    if (!url) return false;
    try {
        const res = await fetch(url);
        const data = await res.json();
        return data.exists;
    } catch {
        return false;
    }
}

async function validateUsername(usernameInput) {
    const username = usernameInput.value.trim();

    if (!username) {
        setInvalid(usernameInput, "Tên đăng nhập không được để trống");
        return false;
    }
    if (username.length > 50) {
        setInvalid(usernameInput, "Tên đăng nhập không quá 50 ký tự");
        return false;
    }
    if (await checkExists("username", username)) {
        setInvalid(usernameInput, "Tên đăng nhập đã tồn tại");
        return false;
    }
    return true;
}

function validateFullname(fullnameInput) {
    const fullname = fullnameInput.value.trim();

    if (!fullname) {
        setInvalid(fullnameInput, "Họ tên không được để trống");
        return false;
    }
    if (fullname.length < 2) {
        setInvalid(fullnameInput, "Họ tên phải chứa ít nhất 2 ký tự");
        return false;
    }
    if (fullname.length > 50) {
        setInvalid(fullnameInput, "Họ tên không quá 50 ký tự");
        return false;
    }
    return true;
}

function validatePassword(passwordInput, confirmPasswordInput) {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    let isValid = true;

    if (!password) {
        setInvalid(passwordInput, "Mật khẩu không được để trống");
        isValid = false;
    } else if (password.length < 6 || !/[A-Z]/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        setInvalid(passwordInput, "Mật khẩu từ 6 ký tự, chứa ít nhất 1 chữ hoa và 1 ký tự đặc biệt");
        isValid = false;
    }

    if (!confirmPassword) {
        setInvalid(confirmPasswordInput, "Xác nhận mật khẩu không được để trống");
        isValid = false;
    } else if (confirmPassword !== password) {
        setInvalid(confirmPasswordInput, "Xác nhận mật khẩu không trùng khớp");
        isValid = false;
    }

    return isValid;
}

async function validateEmail(emailInput) {
    const email = emailInput.value.trim();

    if (!email) {
        setInvalid(emailInput, "Email không được để trống");
        return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || email.length > 100) {
        setInvalid(emailInput, "Email không hợp lệ");
        return false;
    }
    if (await checkExists("email", email)) {
        setInvalid(emailInput, "Email đã được sử dụng");
        return false;
    }
    return true;
}

async function validatePhone(phoneInput) {
    const phone = phoneInput.value.trim();
    if (!phone) {
        setInvalid(phoneInput, "Số điện thoại không được để trống");
        return false;
    }
    if (phone.length > 15) {
        setInvalid(phoneInput, "Số điện thoại không được vượt quá 15 ký tự");
        return false;
    }
    if (phone.length < 10 || phone.length > 11) {
        setInvalid(phoneInput, "Số điện thoại phải là số từ 10-11 chữ số");
        return false;
    }
    if (!/^\d+$/.test(phone)) {
        setInvalid(phoneInput, "Số điện thoại chỉ được chứa chữ số");
        return false;
    }
    if (!/^(02|03|05|07|08|09)\d{8,9}$/.test(phone)) {
        setInvalid(phoneInput, "Số điện thoại không đúng định dạng");
        return false;
    }
    if (await checkExists("phone", phone)) {
        setInvalid(phoneInput, "Số điện thoại đã được đăng ký");
        return false;
    }
    return true;
}

// ====================== Main Validation Function ======================
window.validateRegisterForm = async function() {
    const usernameInput = document.getElementById("username");
    const fullnameInput = document.getElementById("fullname");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("password_confirmation");
    const emailInput = document.getElementById("email");
    const phoneInput = document.getElementById("phone");
    const alertContainer = document.getElementById("alertContainer");

    if (alertContainer) alertContainer.innerHTML = "";
    clearAllInvalid();

    let isValid = true;

    // Validate tất cả các trường
    const isUsernameValid = await validateUsername(usernameInput);
    if (!isUsernameValid) isValid = false;

    if (!validateFullname(fullnameInput)) {
        isValid = false;
    }

    const isEmailValid = await validateEmail(emailInput);
    if (!isEmailValid) isValid = false;

    if (!validatePassword(passwordInput, confirmPasswordInput)) {
        isValid = false;
    }

    const isPhoneValid = await validatePhone(phoneInput);
    if (!isPhoneValid) isValid = false;

    return isValid;
}

document.addEventListener("DOMContentLoaded", function () {
    // Focus effects (clear invalid when typing)
    document.querySelectorAll(".form-control").forEach((input) => {
        input.addEventListener("focus", function () {
            this.parentElement.classList.add("focused");
            clearInvalid(this);
        });
        input.addEventListener("blur", function () {
            this.parentElement.classList.remove("focused");
        });
    });
});

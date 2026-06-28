function getErrorMessage(code) {
    if (code === "LOGIN_EMPTY") return "Tên đăng nhập hoặc email không được để trống";
    if (code === "USER_NOT_FOUND") return "Tên đăng nhập hoặc email không tồn tại.";
    if (code === "EMAIL_INVALID") return "Email chưa hợp lệ";
    if (code === "EMAIL_EMPTY") return "Tên đăng nhập hoặc email không được để trống";
    return "";
}

function getErrorPassword(code) {
    if (code === "PASSWORD_EMPTY") return "Mật khẩu không được để trống";
    if (code === "PASSWORD_INCORRECT") return "Mật khẩu không chính xác";
    return "";
}

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    const loginInput = document.getElementById("login");
    const alertContainer = document.getElementById("alertContainer");
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

    const loginUrl = loginForm?.getAttribute("action") || loginForm?.getAttribute("data-login-url") || "/login";
    const homeUrlFallback = "/";

    function showAlert(type, message) {
        if (typeof window.showAlert === 'function') {
            window.showAlert(type, message);
            return;
        }
        alertContainer.innerHTML = `
      <div class="alert alert-${type}" role="alert">
        <i class="fas fa-${type === "success" ? "check-circle" : "exclamation-circle"} me-1"></i>
        ${message}
      </div>`;
        setTimeout(() => { alertContainer.innerHTML = ""; }, 5000);
    }

    function setInvalid(el, msg) {
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
        el.classList.remove("is-invalid");
        let feedback = el.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.innerText = '';
    }

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    }

    function validateLoginForm() {
        let ok = true;
        const loginVal = (loginInput?.value || "").trim();
        const passVal = (passwordInput?.value || "").trim();

        if (loginInput) clearInvalid(loginInput);
        if (passwordInput) clearInvalid(passwordInput);

        if (!loginVal) {
            if (loginInput) setInvalid(loginInput, getErrorMessage("LOGIN_EMPTY"));
            ok = false;
        } else if (loginVal.includes("@") && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(loginVal)) {
            if (loginInput) setInvalid(loginInput, getErrorMessage("EMAIL_INVALID"));
            ok = false;
        }

        if (!passVal) {
            if (passwordInput) setInvalid(passwordInput, getErrorPassword("PASSWORD_EMPTY"));
            ok = false;
        }

        return ok;
    }

    function normalizeServerMessage(msg, code) {
        const text = (msg || "").toString().toLowerCase();
        
        if (text.includes("bị khóa") || text.includes("locked")) {
            return "Tài khoản của bạn đã bị khóa.Vui lòng liên hệ Admin.";
        }
        if (code === "USER_NOT_FOUND" || /không tồn tại|not found/.test(text)) {
            return getErrorMessage("USER_NOT_FOUND");
        }
        if (code === "PASSWORD_INCORRECT" || /mật khẩu.*(sai|không đúng|chính xác)|password.*incorrect/.test(text)) {
            return getErrorPassword("PASSWORD_INCORRECT");
        }
        if (/email.*required/.test(text)) {
            return getErrorMessage("EMAIL_EMPTY");
        }
        if (/email.*invalid|email.*không hợp lệ/.test(text)) {
            return getErrorMessage("EMAIL_INVALID");
        }
        return msg || "Có lỗi xảy ra. Vui lòng thử lại!";
    }

    if (loginForm) {
        loginForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            if (!validateLoginForm()) return;

            const submitBtn = this.querySelector(".btn-login");
            const btnText = submitBtn?.querySelector(".btn-text");
            const loadingSpinner = submitBtn?.querySelector(".loading-spinner");

            if (submitBtn) submitBtn.disabled = true;
            if (btnText) btnText.style.opacity = "0";
            if (loadingSpinner) loadingSpinner.style.display = "block";

            try {
                const formData = new FormData(this);
                const response = await fetch(loginUrl, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrf,
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                const ct = response.headers.get("content-type") || "";
                const data = ct.includes("application/json") ? await response.json() : { success: false, message: await response.text() };

                if (response.ok && data.success) {
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message || "Đăng nhập thành công");
                    } else {
                        showAlert("success", data.message || "Đăng nhập thành công");
                    }
                    if (data.user) {
                        localStorage.setItem("user", JSON.stringify(data.user));
                    }
                    setTimeout(() => {
                        window.location.href = data.redirect || homeUrlFallback;
                    }, 800);
                } else {
                    const normalized = normalizeServerMessage(data.message, data.code);
                    let fieldError = false;
                    if (/không tồn tại/i.test(normalized) && loginInput) {
                        setInvalid(loginInput, normalized);
                        fieldError = true;
                    }
                    if (/mật khẩu/i.test(normalized) && passwordInput) {
                        setInvalid(passwordInput, normalized);
                        fieldError = true;
                    }
                    if (!fieldError) showAlert("danger", normalized);
                    resetSubmitButton();
                }
            } catch (error) {
                showAlert("danger", "Có lỗi xảy ra. Vui lòng thử lại!");
                resetSubmitButton();
            }

            function resetSubmitButton() {
                if (submitBtn) submitBtn.disabled = false;
                if (btnText) btnText.style.opacity = "1";
                if (loadingSpinner) loadingSpinner.style.display = "none";
            }
        });
    }

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

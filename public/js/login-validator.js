// ====================== Error Messages Function ======================
function getErrorMessage(code) {
    if (code === "LOGIN_EMPTY")
        return "Tên đăng nhập hoặc email không được bỏ trống";
    if (code === "USER_NOT_FOUND")
        return "Tên đăng nhập hoặc email không tồn tại";
    if (code === "EMAIL_INVALID") return "Email chưa hợp lệ";
    if (code === "EMAIL_EMPTY") return "Email không được bỏ trống";
    return "";
}

function getErrorPassword(code) {
    if (code === "PASSWORD_EMPTY") return "Mật khẩu không được bỏ trống";
    if (code === "PASSWORD_INCORRECT") return "Mật khẩu không đúng";
    return "";
}

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    const loginInput = document.getElementById("login");
    const alertContainer = document.getElementById("alertContainer");
    const csrf =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

    // Lấy URL đúng từ form (tránh dùng Blade trong file .js)
    const loginUrl =
        loginForm?.getAttribute("action") ||
        loginForm?.getAttribute("data-login-url") ||
        "/login";
    const homeUrlFallback = "/"; // fallback nếu server không trả redirect

    function showAlert(type, message) {
        alertContainer.innerHTML = `
      <div class="alert alert-${type}" role="alert">
        <i class="fas fa-${
            type === "success" ? "check-circle" : "exclamation-circle"
        } me-1"></i>
        ${message}
      </div>`;
        setTimeout(() => {
            alertContainer.innerHTML = "";
        }, 5000);
    }

    function setInvalid(el) {
        el.classList.add("is-invalid");
    }
    function clearInvalid(el) {
        el.classList.remove("is-invalid");
    }

    // Toggle password visibility (có kiểm tra tồn tại)
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
            const type =
                passwordInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordInput.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    }

    // Realtime simple validation - sử dụng hàm mới
    if (loginInput) {
        loginInput.addEventListener("blur", () => {
            const v = loginInput.value.trim();
            clearInvalid(loginInput);
            if (!v) {
                setInvalid(loginInput);
                showAlert("danger", getErrorMessage("LOGIN_EMPTY"));
            } else if (
                v.includes("@") &&
                !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)
            ) {
                setInvalid(loginInput);
                showAlert("danger", getErrorMessage("EMAIL_INVALID"));
            }
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener("blur", () => {
            const v = passwordInput.value.trim();
            clearInvalid(passwordInput);
            if (!v) {
                setInvalid(passwordInput);
                showAlert("danger", getErrorPassword("PASSWORD_EMPTY"));
            }
        });
    }

    function validateLoginForm() {
        let ok = true;
        const loginVal = (loginInput?.value || "").trim();
        const passVal = (passwordInput?.value || "").trim();

        if (loginInput) clearInvalid(loginInput);
        if (passwordInput) clearInvalid(passwordInput);
        alertContainer.innerHTML = "";

        if (!loginVal) {
            if (loginInput) setInvalid(loginInput);
            showAlert("danger", getErrorMessage("LOGIN_EMPTY"));
            ok = false;
        } else if (
            loginVal.includes("@") &&
            !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(loginVal)
        ) {
            if (loginInput) setInvalid(loginInput);
            showAlert("danger", getErrorMessage("EMAIL_INVALID"));
            ok = false;
        }

        if (!passVal) {
            if (passwordInput) setInvalid(passwordInput);
            showAlert("danger", getErrorPassword("PASSWORD_EMPTY"));
            ok = false;
        }

        return ok;
    }

    function normalizeServerMessage(msg, code) {
        const text = (msg || "").toString().toLowerCase();
        if (code === "USER_NOT_FOUND" || /không tồn tại|not found/.test(text)) {
            return getErrorMessage("USER_NOT_FOUND");
        }
        if (
            code === "PASSWORD_INCORRECT" ||
            /mật khẩu.*(sai|không đúng)|password.*incorrect/.test(text)
        ) {
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

            // Loading state
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
                const data = ct.includes("application/json")
                    ? await response.json()
                    : { success: false, message: await response.text() };

                if (response.ok && data.success) {
                    // Giữ nguyên thông báo thành công
                    showAlert(
                        "success",
                        data.message || "Đăng nhập thành công (1S.1)"
                    );
                    if (data.user) {
                        localStorage.setItem("user", JSON.stringify(data.user));
                    }
                    setTimeout(() => {
                        window.location.href = data.redirect || homeUrlFallback;
                    }, 800);
                } else {
                    const normalized = normalizeServerMessage(
                        data.message,
                        data.code
                    );
                    showAlert("danger", normalized);

                    if (/1e\.2/i.test(normalized) && loginInput)
                        setInvalid(loginInput);
                    if (/1e\.4/i.test(normalized) && passwordInput)
                        setInvalid(passwordInput);
                    resetSubmitButton();
                }
            } catch (error) {
                // Giữ nguyên thông báo lỗi network
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

    // Focus effects (optional)
    document.querySelectorAll(".form-control").forEach((input) => {
        input.addEventListener("focus", function () {
            this.parentElement.classList.add("focused");
        });
        input.addEventListener("blur", function () {
            this.parentElement.classList.remove("focused");
        });
    });
});

// document.addEventListener("DOMContentLoaded", function () {
//     const loginForm = document.getElementById("loginForm");
//     const togglePassword = document.getElementById("togglePassword");
//     const passwordInput = document.getElementById("password");
//     const loginInput = document.getElementById("login");
//     const alertContainer = document.getElementById("alertContainer");
//     const csrf =
//         document
//             .querySelector('meta[name="csrf-token"]')
//             ?.getAttribute("content") || "";

//     // Lấy URL đúng từ form (tránh dùng Blade trong file .js)
//     const loginUrl =
//         loginForm?.getAttribute("action") ||
//         loginForm?.getAttribute("data-login-url") ||
//         "/login";
//     const homeUrlFallback = "/"; // fallback nếu server không trả redirect

//     function showAlert(type, message) {
//         alertContainer.innerHTML = `
//       <div class="alert alert-${type}" role="alert">
//         <i class="fas fa-${
//             type === "success" ? "check-circle" : "exclamation-circle"
//         } me-1"></i>
//         ${message}
//       </div>`;
//         setTimeout(() => {
//             alertContainer.innerHTML = "";
//         }, 5000);
//     }

//     function setInvalid(el) {
//         el.classList.add("is-invalid");
//     }
//     function clearInvalid(el) {
//         el.classList.remove("is-invalid");
//     }

//     // Toggle password visibility (có kiểm tra tồn tại)
//     if (togglePassword && passwordInput) {
//         togglePassword.addEventListener("click", function () {
//             const type =
//                 passwordInput.getAttribute("type") === "password"
//                     ? "text"
//                     : "password";
//             passwordInput.setAttribute("type", type);
//             this.classList.toggle("fa-eye");
//             this.classList.toggle("fa-eye-slash");
//         });
//     }

//     // Realtime simple validation
//     if (loginInput) {
//         loginInput.addEventListener("blur", () => {
//             const v = loginInput.value.trim();
//             clearInvalid(loginInput);
//             if (!v) {
//                 setInvalid(loginInput);
//                 showAlert(
//                     "danger",
//                     "Tên đăng nhập hoặc email không được bỏ trống (1E.1)"
//                 );
//             } else if (
//                 v.includes("@") &&
//                 !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)
//             ) {
//                 setInvalid(loginInput);
//                 showAlert("danger", "Email chưa hợp lệ (1E.5)");
//             }
//         });
//     }

//     if (passwordInput) {
//         passwordInput.addEventListener("blur", () => {
//             const v = passwordInput.value.trim();
//             clearInvalid(passwordInput);
//             if (!v) {
//                 setInvalid(passwordInput);
//                 showAlert("danger", "Mật khẩu không được bỏ trống (1E.3)");
//             }
//         });
//     }

//     function validateLoginForm() {
//         let ok = true;
//         const loginVal = (loginInput?.value || "").trim();
//         const passVal = (passwordInput?.value || "").trim();

//         if (loginInput) clearInvalid(loginInput);
//         if (passwordInput) clearInvalid(passwordInput);
//         alertContainer.innerHTML = "";

//         if (!loginVal) {
//             if (loginInput) setInvalid(loginInput);
//             showAlert(
//                 "danger",
//                 "Tên đăng nhập hoặc email không được bỏ trống (1E.1)"
//             );
//             ok = false;
//         } else if (
//             loginVal.includes("@") &&
//             !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(loginVal)
//         ) {
//             if (loginInput) setInvalid(loginInput);
//             showAlert("danger", "Email chưa hợp lệ (1E.5)");
//             ok = false;
//         }

//         if (!passVal) {
//             if (passwordInput) setInvalid(passwordInput);
//             showAlert("danger", "Mật khẩu không được bỏ trống (1E.3)");
//             ok = false;
//         }

//         return ok;
//     }

//     function normalizeServerMessage(msg, code) {
//         const text = (msg || "").toString().toLowerCase();
//         if (code === "USER_NOT_FOUND" || /không tồn tại|not found/.test(text)) {
//             return "Tên đăng nhập không tồn tại (1E.2)";
//         }
//         if (
//             code === "PASSWORD_INCORRECT" ||
//             /mật khẩu.*(sai|không đúng)|password.*incorrect/.test(text)
//         ) {
//             return "Mật khẩu không đúng (1E.4)";
//         }
//         if (/email.*required/.test(text)) {
//             return "Email không được bỏ trống (1E.6)";
//         }
//         if (/email.*invalid|email.*không hợp lệ/.test(text)) {
//             return "Email chưa hợp lệ (1E.5)";
//         }
//         return msg || "Có lỗi xảy ra. Vui lòng thử lại!";
//     }

//     if (loginForm) {
//         loginForm.addEventListener("submit", async function (e) {
//             e.preventDefault();

//             if (!validateLoginForm()) return;

//             const submitBtn = this.querySelector(".btn-login");
//             const btnText = submitBtn?.querySelector(".btn-text");
//             const loadingSpinner = submitBtn?.querySelector(".loading-spinner");

//             // Loading state
//             if (submitBtn) submitBtn.disabled = true;
//             if (btnText) btnText.style.opacity = "0";
//             if (loadingSpinner) loadingSpinner.style.display = "block";

//             try {
//                 const formData = new FormData(this);
//                 const response = await fetch(loginUrl, {
//                     method: "POST",
//                     body: formData,
//                     headers: {
//                         "X-CSRF-TOKEN": csrf,
//                         Accept: "application/json",
//                         "X-Requested-With": "XMLHttpRequest",
//                     },
//                 });

//                 const ct = response.headers.get("content-type") || "";
//                 const data = ct.includes("application/json")
//                     ? await response.json()
//                     : { success: false, message: await response.text() };

//                 if (response.ok && data.success) {
//                     // 1S.1
//                     showAlert(
//                         "success",
//                         data.message || "Đăng nhập thành công (1S.1)"
//                     );
//                     if (data.user) {
//                         localStorage.setItem("user", JSON.stringify(data.user));
//                     }
//                     setTimeout(() => {
//                         window.location.href = data.redirect || homeUrlFallback;
//                     }, 800);
//                 } else {
//                     const normalized = normalizeServerMessage(
//                         data.message,
//                         data.code
//                     );
//                     showAlert("danger", normalized);

//                     if (/1e\.2/i.test(normalized) && loginInput)
//                         setInvalid(loginInput);
//                     if (/1e\.4/i.test(normalized) && passwordInput)
//                         setInvalid(passwordInput);
//                     resetSubmitButton();
//                 }
//             } catch (error) {
//                 showAlert("danger", "Có lỗi xảy ra. Vui lòng thử lại!");
//                 resetSubmitButton();
//             }

//             function resetSubmitButton() {
//                 if (submitBtn) submitBtn.disabled = false;
//                 if (btnText) btnText.style.opacity = "1";
//                 if (loadingSpinner) loadingSpinner.style.display = "none";
//             }
//         });
//     }

//     // Focus effects (optional)
//     document.querySelectorAll(".form-control").forEach((input) => {
//         input.addEventListener("focus", function () {
//             this.parentElement.classList.add("focused");
//         });
//         input.addEventListener("blur", function () {
//             this.parentElement.classList.remove("focused");
//         });
//     });
// });

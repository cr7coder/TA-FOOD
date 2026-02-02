// ====================== Validation Helper Functions ======================
async function validateUsername(usernameInput) {
    const username = usernameInput.value.trim();

    if (!username) {
        showAlert("danger", "Tên đăng nhập không được bỏ trống");
        usernameInput.focus();
        return false;
    }
    if (username.length > 50) {
        showAlert("danger", "Tên đăng nhập không quá 50 ký tự");
        usernameInput.focus();
        return false;
    }
    if (!/^[A-Za-z0-9_]+$/.test(username)) {
        showAlert(
            "danger",
            "Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới"
        );
        usernameInput.focus();
        return false;
    }
    if (await checkExists("username", username)) {
        showAlert("danger", "Tên đăng nhập đã tồn tại");
        usernameInput.focus();
        return false;
    }
    return true;
}

function validatePassword(passwordInput, confirmPasswordInput) {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (!password) {
        showAlert("danger", "Mật khẩu không được bỏ trống");
        passwordInput.focus();
        return false;
    }
    if (password.length < 6 || password.length > 50) {
        showAlert("danger", "Mật khẩu phải từ 6 đến 50 ký tự");
        passwordInput.focus();
        return false;
    }
    if (confirmPassword !== password) {
        showAlert("danger", "Xác nhận mật khẩu không khớp");
        confirmPasswordInput.focus();
        return false;
    }
    return true;
}

async function validateEmail(emailInput) {
    const email = emailInput.value.trim();

    if (!email) {
        showAlert("danger", "Email không được bỏ trống");
        emailInput.focus();
        return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || email.length > 100) {
        showAlert("danger", "Email không hợp lệ");
        emailInput.focus();
        return false;
    }
    if (await checkExists("email", email)) {
        showAlert("danger", "Email đã được sử dụng");
        emailInput.focus();
        return false;
    }
    return true;
}

function validatePhone(phoneInput) {
    const phone = phoneInput.value.trim();
    if (!phone) {
        return true;
    }
    if (!/^0\d{9,10}$/.test(phone)) {
        showAlert("danger", "Số điện thoại không hợp lệ");
        phoneInput.focus();
        return false;
    }
    return true;
}

// ====================== Main Validation Function ======================
async function validateRegisterForm() {
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById(
        "password_confirmation"
    );
    const emailInput = document.getElementById("email");
    const phoneInput = document.getElementById("phone");
    const alertContainer = document.getElementById("alertContainer");

    alertContainer.innerHTML = "";

    // Validate từng phần theo thứ tự
    if (!(await validateUsername(usernameInput))) {
        return false;
    }

    if (!validatePassword(passwordInput, confirmPasswordInput)) {
        return false;
    }

    if (!(await validateEmail(emailInput))) {
        return false;
    }

    if (!validatePhone(phoneInput)) {
        return false;
    }

    return true;
}

// ====================== Existing Functions ======================
function showAlert(type, message) {
    const alertContainer = document.getElementById("alertContainer");
    alertContainer.innerHTML = `
        <div class="alert alert-${type}" role="alert">
            <i class="fas fa-${
                type === "success" ? "check-circle" : "exclamation-circle"
            }"></i>
            ${message}
        </div>
    `;
    setTimeout(() => {
        alertContainer.innerHTML = "";
    }, 6000);
}

async function checkExists(type, value) {
    let url = "";
    if (type === "username")
        url = "/api/check-username?username=" + encodeURIComponent(value);
    if (type === "email")
        url = "/api/check-email?email=" + encodeURIComponent(value);
    try {
        const res = await fetch(url);
        const data = await res.json();
        return data.exists;
    } catch {
        return false;
    }
}
// function showAlert(type, message) {
//     const alertContainer = document.getElementById("alertContainer");
//     alertContainer.innerHTML = `
//         <div class="alert alert-${type}" role="alert">
//             <i class="fas fa-${
//                 type === "success" ? "check-circle" : "exclamation-circle"
//             }"></i>
//             ${message}
//         </div>
//     `;
//     setTimeout(() => {
//         alertContainer.innerHTML = "";
//     }, 6000);
// }
// async function checkExists(type, value) {
//     let url = "";
//     if (type === "username")
//         url = "/api/check-username?username=" + encodeURIComponent(value);
//     if (type === "email")
//         url = "/api/check-email?email=" + encodeURIComponent(value);
//     try {
//         const res = await fetch(url);
//         const data = await res.json();
//         return data.exists;
//     } catch {
//         return false;
//     }
// }
// async function validateRegisterForm() {
//     const usernameInput = document.getElementById("username");
//     const passwordInput = document.getElementById("password");
//     const confirmPasswordInput = document.getElementById(
//         "password_confirmation"
//     );
//     const emailInput = document.getElementById("email");
//     const phoneInput = document.getElementById("phone");
//     const alertContainer = document.getElementById("alertContainer");

//     alertContainer.innerHTML = "";

//     // Tên đăng nhập
//     const username = usernameInput.value.trim();
//     if (!username) {
//         showAlert("danger", "Tên đăng nhập không được bỏ trống");
//         usernameInput.focus();
//         return false;
//     }
//     if (username.length > 50) {
//         showAlert("danger", "Tên đăng nhập không quá 50 ký tự");
//         usernameInput.focus();
//         return false;
//     }
//     if (!/^[A-Za-z0-9_]+$/.test(username)) {
//         showAlert(
//             "danger",
//             "Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới"
//         );
//         usernameInput.focus();
//         return false;
//     }
//     if (await checkExists("username", username)) {
//         showAlert("danger", "Tên đăng nhập đã tồn tại");
//         usernameInput.focus();
//         return false;
//     }

//     // Mật khẩu
//     const password = passwordInput.value;
//     const confirmPassword = confirmPasswordInput.value;
//     if (!password) {
//         showAlert("danger", "Mật khẩu không được bỏ trống");
//         passwordInput.focus();
//         return false;
//     }
//     if (password.length < 6) {
//         showAlert("danger", "Mật khẩu phải từ 6 ký tự trở lên");
//         passwordInput.focus();
//         return false;
//     }
//     if (confirmPassword !== password) {
//         showAlert("danger", "Xác nhận mật khẩu không khớp");
//         confirmPasswordInput.focus();
//         return false;
//     }

//     // Email
//     const email = emailInput.value.trim();
//     if (!email) {
//         showAlert("danger", "Email không được bỏ trống");
//         emailInput.focus();
//         return false;
//     }
//     if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
//         showAlert("danger", "Email không hợp lệ");
//         emailInput.focus();
//         return false;
//     }
//     if (await checkExists("email", email)) {
//         showAlert("danger", "Email đã được sử dụng");
//         emailInput.focus();
//         return false;
//     }

//     // Số điện thoại (nếu có nhập)
//     const phone = phoneInput.value.trim();
//     if (phone) {
//         if (!/^0\d{9,10}$/.test(phone)) {
//             showAlert("danger", "Số điện thoại không hợp lệ");
//             phoneInput.focus();
//             return false;
//         }
//     }

//     return true;
// }

(function () {
    'use strict';

    // Get form and elements
    const form = document.getElementById('loginForm');
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');
    const loginError = document.getElementById('loginError');
    const passwordError = document.getElementById('passwordError');

    // Error message mapping
    function getErrorMessage(code) {
        const messages = {
            '1E.1': 'Vui lòng nhập email hoặc tên đăng nhập',
            '1E.2': 'Tài khoản không tồn tại trong hệ thống',
            '1E.5': 'Email không hợp lệ',
            '1E.6': 'Tên đăng nhập không hợp lệ'
        };
        return messages[code] || 'Có lỗi xảy ra, vui lòng thử lại';
    }

    function getErrorPassword(code) {
        const messages = {
            '1E.3': 'Vui lòng nhập mật khẩu',
            '1E.4': 'Mật khẩu không chính xác'
        };
        return messages[code] || 'Có lỗi xảy ra với mật khẩu';
    }

    // Show error
    function showError(element, message) {
        const errorDiv = element.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('error-message')) {
            errorDiv.textContent = message;
            errorDiv.classList.add('active');
            element.classList.add('error');
        }
    }

    // Clear error
    function clearError(element) {
        const errorDiv = element.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('error-message')) {
            errorDiv.textContent = '';
            errorDiv.classList.remove('active');
            element.classList.remove('error');
        }
    }

    // Validate email format
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Validate login field
    function validateLogin() {
        const loginValue = loginInput.value.trim();

        if (!loginValue) {
            showError(loginInput, getErrorMessage('1E.1'));
            return false;
        }

        // Check if it's email format
        if (loginValue.includes('@')) {
            if (!isValidEmail(loginValue)) {
                showError(loginInput, getErrorMessage('1E.5'));
                return false;
            }
        }

        clearError(loginInput);
        return true;
    }

    // Validate password field
    function validatePassword() {
        const passwordValue = passwordInput.value.trim();

        if (!passwordValue) {
            showError(passwordInput, getErrorPassword('1E.3'));
            return false;
        }

        clearError(passwordInput);
        return true;
    }

    // Real-time validation
    loginInput.addEventListener('blur', validateLogin);
    passwordInput.addEventListener('blur', validatePassword);

    // Clear error on input
    loginInput.addEventListener('input', function () {
        if (this.value.trim()) {
            clearError(this);
        }
    });

    passwordInput.addEventListener('input', function () {
        if (this.value.trim()) {
            clearError(this);
        }
    });

    // Normalize server message to error code
    function normalizeServerMessage(message) {
        message = message.toLowerCase();

        if (message.includes('không tồn tại') || message.includes('not found')) {
            return '1E.2';
        }
        if (message.includes('mật khẩu') && message.includes('không chính xác')) {
            return '1E.4';
        }
        if (message.includes('email') && message.includes('không hợp lệ')) {
            return '1E.5';
        }

        return null;
    }

    // Form submission
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validate both fields
        const isLoginValid = validateLogin();
        const isPasswordValid = validatePassword();

        if (!isLoginValid || !isPasswordValid) {
            return;
        }

        // Show loading state
        loginBtn.disabled = true;
        loginBtn.classList.add('loading');

        try {
            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Store user data if provided
                if (data.user) {
                    localStorage.setItem('user', JSON.stringify(data.user));
                }

                // Redirect to intended page or home
                window.location.href = data.redirect || '/';
            } else {
                // Handle error response
                if (data.errors) {
                    if (data.errors.login) {
                        const errorCode = normalizeServerMessage(data.errors.login[0]);
                        showError(loginInput, getErrorMessage(errorCode || '1E.2'));
                    }
                    if (data.errors.password) {
                        const errorCode = normalizeServerMessage(data.errors.password[0]);
                        showError(passwordInput, getErrorPassword(errorCode || '1E.4'));
                    }
                } else if (data.message) {
                    const errorCode = normalizeServerMessage(data.message);
                    if (errorCode) {
                        if (errorCode === '1E.2' || errorCode === '1E.5') {
                            showError(loginInput, getErrorMessage(errorCode));
                        } else if (errorCode === '1E.4') {
                            showError(passwordInput, getErrorPassword(errorCode));
                        }
                    } else {
                        showError(loginInput, data.message);
                    }
                }
            }
        } catch (error) {
            console.error('Login error:', error);
            showError(loginInput, 'Có lỗi xảy ra, vui lòng thử lại sau');
        } finally {
            // Remove loading state
            loginBtn.disabled = false;
            loginBtn.classList.remove('loading');
        }
    });
})();
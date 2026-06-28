<style>
    /* Full-Screen Centered Glassmorphic Modal Overlay */
    .global-alert-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(10, 15, 23, 0.75);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 9999999;
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: auto;
    }

    .global-alert-card {
        background: rgba(30, 36, 45, 0.95);
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        padding: 35px 40px;
        max-width: 440px;
        width: 90%;
        text-align: center;
        position: relative;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6), 
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
        transform: scale(0.8) translateY(30px);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        color: #ffffff;
        font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .global-alert-overlay.show {
        opacity: 1;
    }

    .global-alert-overlay.show .global-alert-card {
        transform: scale(1) translateY(0);
    }

    .global-alert-close {
        position: absolute;
        top: 15px;
        right: 22px;
        font-size: 28px;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.2s ease;
        line-height: 1;
        background: none;
        border: none;
        outline: none !important;
    }

    .global-alert-close:hover {
        color: #ffffff;
        transform: scale(1.1);
    }

    .global-alert-icon-container {
        font-size: 65px;
        margin-bottom: 20px;
        display: inline-block;
        animation: globalAlertBounce 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes globalAlertBounce {
        0% { transform: scale(0.3); opacity: 0; }
        50% { transform: scale(1.1); }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); opacity: 1; }
    }

    .global-alert-title {
        font-weight: 700;
        font-size: 21px;
        margin-bottom: 12px;
        color: #ffffff;
        letter-spacing: -0.5px;
    }

    .global-alert-desc {
        color: #cbd5e1;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 26px;
    }

    .global-alert-desc strong {
        color: #ffbe33;
        font-weight: 600;
    }

    .global-alert-btn-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .global-alert-primary-btn {
        width: 100%;
        padding: 13px 20px;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15.5px;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        color: #ffffff;
        outline: none !important;
    }

    .global-alert-primary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
    }

    .global-alert-primary-btn:active {
        transform: translateY(0);
    }

    .global-alert-secondary-link {
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.15);
        color: #cbd5e1;
        width: 100%;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        text-decoration: none;
        display: block;
        transition: all 0.2s ease;
        outline: none !important;
    }

    .global-alert-secondary-link:hover {
        border-color: #ffbe33;
        color: #ffbe33;
        background: rgba(255, 190, 51, 0.05);
        transform: translateY(-2px);
    }

    .global-alert-text-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 14.5px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: underline;
        outline: none !important;
        margin-top: 6px;
        transition: color 0.2s;
    }

    .global-alert-text-btn:hover {
        color: #ffffff;
    }

    /* Auto-dismiss countdown progress bar */
    .global-alert-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        border-radius: 0 0 22px 22px;
        width: 100%;
        transform-origin: left center;
        animation: alertCountdown linear forwards;
    }

    @keyframes alertCountdown {
        from { transform: scaleX(1); }
        to   { transform: scaleX(0); }
    }

    /* Toast Notification System */
    .global-toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000000;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }
    .global-toast {
        pointer-events: auto;
        background: rgba(30, 36, 45, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 14px;
        padding: 14px 22px;
        display: flex;
        align-items: center;
        gap: 14px;
        color: #ffffff;
        box-shadow: 0 15px 30px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        font-family: 'Outfit', 'Inter', sans-serif;
        min-width: 300px;
        max-width: 440px;
        transform: translateX(130%);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
        opacity: 0;
    }
    .global-toast.show {
        transform: translateX(0);
        opacity: 1;
    }
    .global-toast-icon {
        font-size: 22px;
        color: #22c55e;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .global-toast-message {
        font-size: 14.5px;
        flex-grow: 1;
        line-height: 1.4;
    }
    .global-toast-message strong {
        color: #ffbe33;
        font-weight: 600;
    }
    .global-toast-close {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
        padding: 0;
        outline: none !important;
        transition: color 0.2s;
    }
    .global-toast-close:hover {
        color: #ffffff;
    }
</style>

<script>
    (function() {
        /**
         * Global Premium Screen-Centered Glassmorphic Alert System
         * @param {string} type - 'success', 'danger', 'warning', 'info', 'guest_checkout', 'voucher_saved'
         * @param {string} message - Content of the message (HTML supported)
         */
        function showAlert(type, message) {
            // Remove existing alert overlay to prevent stack-blocking
            const existingOverlay = document.getElementById('globalAlertOverlay');
            if (existingOverlay) {
                existingOverlay.remove();
            }

            // Create Screen-Centered Overlay Container
            const overlay = document.createElement('div');
            overlay.id = 'globalAlertOverlay';
            overlay.className = 'global-alert-overlay';

            // Create Card
            const card = document.createElement('div');
            card.className = 'global-alert-card';

            // Close function with beautiful scaling exit
            function closeAlert() {
                overlay.classList.remove('show');
                setTimeout(() => overlay.remove(), 250);
            }

            // Close button (x)
            const closeBtn = document.createElement('button');
            closeBtn.className = 'global-alert-close';
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = closeAlert;
            card.appendChild(closeBtn);

            // Icon & Color Logic Setup
            const iconContainer = document.createElement('div');
            iconContainer.className = 'global-alert-icon-container';
            
            let btnText = 'Đóng';
            let primaryBtnBg = '';
            let alertTitle = 'Thông báo';

            // Custom setup based on alert type
            const lowerType = String(type).toLowerCase();
            if (lowerType === 'success') {
                iconContainer.innerHTML = '<i class="fa fa-check-circle" style="color: #22c55e; text-shadow: 0 0 20px rgba(34, 197, 94, 0.4);"></i>';
                primaryBtnBg = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
                card.style.borderColor = 'rgba(34, 197, 94, 0.3)';
                alertTitle = 'Thành công!';
                btnText = 'Tuyệt vời!';
            } else if (lowerType === 'danger' || lowerType === 'error' || lowerType === 'fail') {
                iconContainer.innerHTML = '<i class="fa fa-times-circle" style="color: #f43f5e; text-shadow: 0 0 20px rgba(244, 63, 94, 0.4);"></i>';
                primaryBtnBg = 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)';
                card.style.borderColor = 'rgba(244, 63, 94, 0.3)';
                alertTitle = 'Thông báo lỗi';
                btnText = 'Đã hiểu';
            } else if (lowerType === 'guest_checkout') {
                iconContainer.innerHTML = '<i class="fa fa-user-circle" style="color: #ffbe33; text-shadow: 0 0 20px rgba(255, 190, 51, 0.4);"></i>';
                primaryBtnBg = 'linear-gradient(135deg, #ff6b35 0%, #f7931e 100%)';
                card.style.borderColor = 'rgba(255, 190, 51, 0.3)';
                alertTitle = 'Bạn chưa đăng nhập';
                btnText = 'Đăng nhập ngay';
            } else if (lowerType === 'voucher_saved') {
                iconContainer.innerHTML = '<i class="fa fa-tag" style="color: #22c55e; text-shadow: 0 0 20px rgba(34, 197, 94, 0.4);"></i>';
                primaryBtnBg = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
                card.style.borderColor = 'rgba(34, 197, 94, 0.3)';
                alertTitle = 'Đã lưu mã giảm giá!';
                btnText = 'Thanh toán ngay';
            } else {
                iconContainer.innerHTML = '<i class="fa fa-exclamation-circle" style="color: #ffbe33; text-shadow: 0 0 20px rgba(255, 190, 51, 0.4);"></i>';
                primaryBtnBg = 'linear-gradient(135deg, #ffbe33 0%, #f59e0b 100%)';
                card.style.borderColor = 'rgba(255, 190, 51, 0.3)';
                alertTitle = 'Thông báo';
                btnText = 'Đóng';
            }

            card.appendChild(iconContainer);

            // Title
            const titleEl = document.createElement('h4');
            titleEl.className = 'global-alert-title';
            titleEl.textContent = alertTitle;
            card.appendChild(titleEl);

            // Message Body
            const descEl = document.createElement('p');
            descEl.className = 'global-alert-desc';
            
            if (lowerType === 'voucher_saved') {
                const cartCountEl = document.getElementById('cartCount');
                const cartCount = cartCountEl ? parseInt(cartCountEl.textContent.trim() || '0') : 0;
                const hasItems = cartCount > 0;
                const subtext = hasItems
                    ? 'Mã sẽ được <strong style="color:#ffbe33;">tự động áp dụng</strong> khi bạn vào trang thanh toán.'
                    : 'Mã sẽ được <strong style="color:#ffbe33;">tự động áp dụng</strong> khi bạn thanh toán. Hãy chọn món trước nhé!';
                
                descEl.innerHTML = `
                    <div style="background:rgba(34,197,94,0.12);border:2.5px dashed #22c55e;border-radius:12px;padding:10px 24px;margin:14px auto;display:inline-block;">
                        <span style="font-size:22px;font-weight:800;color:#22c55e;letter-spacing:3px;">${message}</span>
                    </div>
                    <div style="color:#e0e0e0;font-size:14px;line-height:1.5;margin-top:10px;">${subtext}</div>
                `;
            } else {
                descEl.innerHTML = message;
            }
            
            card.appendChild(descEl);

            // Button Action Handler Group
            const btnContainer = document.createElement('div');
            btnContainer.className = 'global-alert-btn-container';

            const primaryBtn = document.createElement('button');
            primaryBtn.className = 'global-alert-primary-btn';
            primaryBtn.style.background = primaryBtnBg;
            primaryBtn.innerHTML = btnText;

            if (lowerType === 'guest_checkout') {
                primaryBtn.onclick = () => {
                    window.location.href = '/checkout';
                };

                const registerLink = document.createElement('a');
                registerLink.href = '/register';
                registerLink.className = 'global-alert-secondary-link';
                registerLink.textContent = 'Đăng ký nhanh tài khoản';

                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'global-alert-text-btn';
                cancelBtn.textContent = 'Quay lại mua sắm';
                cancelBtn.onclick = closeAlert;

                btnContainer.appendChild(primaryBtn);
                btnContainer.appendChild(registerLink);
                btnContainer.appendChild(cancelBtn);
            } else if (lowerType === 'voucher_saved') {
                // Read cart item count from DOM badge to direct user flow
                const cartCountEl = document.getElementById('cartCount');
                const cartCount = cartCountEl ? parseInt(cartCountEl.textContent.trim() || '0') : 0;

                if (cartCount > 0) {
                    primaryBtn.onclick = () => {
                        window.location.href = '/checkout';
                    };
                } else {
                    primaryBtn.innerHTML = 'Chọn món ngay';
                    primaryBtn.onclick = () => {
                        closeAlert();
                        // Scroll to foods smoothly
                        const foodSection = document.querySelector('.food_section');
                        if (foodSection) {
                            foodSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    };
                }

                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'global-alert-text-btn';
                cancelBtn.textContent = cartCount > 0 ? 'Tiếp tục mua sắm' : 'Để sau';
                cancelBtn.onclick = closeAlert;

                btnContainer.appendChild(primaryBtn);
                btnContainer.appendChild(cancelBtn);
            } else {
                primaryBtn.onclick = closeAlert;
                btnContainer.appendChild(primaryBtn);
            }

            card.appendChild(btnContainer);
            overlay.appendChild(card);
            document.body.appendChild(overlay);

            // Close alert when clicking overlay boundary
            overlay.onclick = (e) => {
                if (e.target === overlay) closeAlert();
            };

            // Animate transition entry
            requestAnimationFrame(() => {
                overlay.classList.add('show');
            });

            // Auto-dismiss after a few seconds depending on type
            // guest_checkout and voucher_saved (with cart) need deliberate user action — skip auto-dismiss
            let autoDismissMs = 0;
            if (lowerType === 'success') {
                autoDismissMs = 3000;
            } else if (lowerType === 'danger' || lowerType === 'error' || lowerType === 'fail') {
                autoDismissMs = 4000;
            } else if (lowerType === 'voucher_saved') {
                autoDismissMs = 4000;
            } else if (lowerType !== 'guest_checkout') {
                autoDismissMs = 3500;
            }

            if (autoDismissMs > 0) {
                const autoDismissTimer = setTimeout(closeAlert, autoDismissMs);

                // Cancel auto-dismiss if user hovers over the card
                card.addEventListener('mouseenter', () => {
                    clearTimeout(autoDismissTimer);
                }, { once: true });
            }
        }

        // Intercept browser window.alert calls
        window.alert = function(message) {
            showAlert('danger', message);
        };

        /**
         * Global Premium Screen-Centered Glassmorphic Confirm Dialog
         * @param {string} title - Title of the confirmation dialog
         * @param {string} subtitle - Subtitle or description (HTML supported)
         * @returns {Promise<boolean>}
         */
        function showConfirm(title, subtitle = '') {
            return new Promise((resolve) => {
                const existingOverlay = document.getElementById('globalAlertOverlay');
                if (existingOverlay) {
                    existingOverlay.remove();
                }

                // Create Overlay Container
                const overlay = document.createElement('div');
                overlay.id = 'globalAlertOverlay';
                overlay.className = 'global-alert-overlay';

                // Create Card
                const card = document.createElement('div');
                card.className = 'global-alert-card';
                card.style.borderColor = 'rgba(255, 190, 51, 0.3)';

                // Icon Container
                const iconContainer = document.createElement('div');
                iconContainer.className = 'global-alert-icon-container';
                iconContainer.innerHTML = '<i class="fa fa-question-circle" style="color: #ffbe33; text-shadow: 0 0 20px rgba(255, 190, 51, 0.4);"></i>';
                card.appendChild(iconContainer);

                // Title
                const titleEl = document.createElement('h4');
                titleEl.className = 'global-alert-title';
                titleEl.textContent = title;
                card.appendChild(titleEl);

                // Description
                const descEl = document.createElement('p');
                descEl.className = 'global-alert-desc';
                descEl.innerHTML = subtitle;
                card.appendChild(descEl);

                // Button Action Handler Group
                const btnContainer = document.createElement('div');
                btnContainer.className = 'global-alert-btn-container';

                const confirmBtn = document.createElement('button');
                confirmBtn.className = 'global-alert-primary-btn';
                confirmBtn.style.background = 'linear-gradient(135deg, #ffbe33 0%, #f59e0b 100%)';
                confirmBtn.innerHTML = 'Đồng ý';
                
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'global-alert-secondary-link';
                cancelBtn.style.textAlign = 'center';
                cancelBtn.style.background = 'transparent';
                cancelBtn.style.marginTop = '4px';
                cancelBtn.innerHTML = 'Hủy bỏ';

                function close(result) {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.remove();
                        resolve(result);
                    }, 250);
                }

                confirmBtn.onclick = () => close(true);
                cancelBtn.onclick = () => close(false);

                btnContainer.appendChild(confirmBtn);
                btnContainer.appendChild(cancelBtn);
                card.appendChild(btnContainer);
                overlay.appendChild(card);
                document.body.appendChild(overlay);

                // Trigger animations
                requestAnimationFrame(() => {
                    overlay.classList.add('show');
                });
            });
        }

        function showToast(message) {
            let container = document.querySelector('.global-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'global-toast-container';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            toast.className = 'global-toast';
            toast.innerHTML = `
                <div class="global-toast-icon"><i class="fa fa-check-circle"></i></div>
                <div class="global-toast-message">${message}</div>
                <button class="global-toast-close">&times;</button>
            `;
            
            container.appendChild(toast);
            
            // trigger animation
            requestAnimationFrame(() => {
                toast.classList.add('show');
            });
            
            const closeBtn = toast.querySelector('.global-toast-close');
            const dismiss = () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            };
            closeBtn.onclick = dismiss;
            
            setTimeout(dismiss, 3000);
        }

        // Inject globally on window object
        window.showAlert = showAlert;
        window.showConfirm = showConfirm;
        window.showToast = showToast;
    })();
</script>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof window.showAlert === 'function') {
                    window.showAlert('success', "{!! addslashes(session('success')) !!}");
                }
            }, 300);
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof window.showAlert === 'function') {
                    window.showAlert('danger', "{!! addslashes(session('error')) !!}");
                }
            }, 300);
        });
    </script>
@endif

@if(session('toast_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof window.showToast === 'function') {
                    window.showToast("{!! addslashes(session('toast_success')) !!}");
                }
            }, 300);
        });
    </script>
@endif

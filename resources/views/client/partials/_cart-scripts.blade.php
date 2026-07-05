<script>
    const CSRF_TOKEN = "{{ csrf_token() }}";
    const CART_BASE = "/cart";
    const IS_AUTHENTICATED = {{ Auth::check() ? 'true' : 'false' }};

    const floatingCart = document.getElementById('floatingCart');
    const cartModal = document.getElementById('cartModal');
    const closeCart = document.getElementById('closeCart');
    const cartCount = document.getElementById('cartCount');
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const cartFooter = document.getElementById('cartFooter');
    const cartTotal = document.getElementById('cartTotal');

    const money = v => new Intl.NumberFormat('vi-VN').format(v ?? 0);

    // Premium Fly-to-Cart Parabolic Animation
    function flyToCart(buttonEl, imageSrc) {
        const floatingCart = document.getElementById('floatingCart');
        if (!floatingCart) return;

        // 1) Get the coordinates of button and floating cart
        const btnRect = buttonEl.getBoundingClientRect();
        const cartRect = floatingCart.getBoundingClientRect();

        // 2) Create the fly thumbnail element
        const flyEl = document.createElement('div');
        flyEl.className = 'fly-to-cart-item';
        flyEl.style.position = 'fixed';
        flyEl.style.top = `${btnRect.top + btnRect.height / 2 - 20}px`;
        flyEl.style.left = `${btnRect.left + btnRect.width / 2 - 20}px`;
        flyEl.style.width = '40px';
        flyEl.style.height = '40px';
        flyEl.style.borderRadius = '50%';
        flyEl.style.backgroundImage = `url('${imageSrc || "/images/no-image.png"}')`;
        flyEl.style.backgroundSize = 'cover';
        flyEl.style.backgroundPosition = 'center';
        flyEl.style.boxShadow = '0 8px 20px rgba(255, 190, 51, 0.6)';
        flyEl.style.border = '2px solid #ffbe33';
        flyEl.style.zIndex = '99999';
        flyEl.style.pointerEvents = 'none';
        
        // Horizontal moves linearly, vertical moves with easing to create a perfect parabolic arc
        flyEl.style.transition = 'left 0.75s linear, top 0.75s cubic-bezier(0.55, 0, 0.85, 0.36), transform 0.75s ease, opacity 0.75s ease';

        document.body.appendChild(flyEl);

        // 3) Trigger transition in the next animation frame
        requestAnimationFrame(() => {
            const targetX = cartRect.left + cartRect.width / 2 - 20;
            const targetY = cartRect.top + cartRect.height / 2 - 20;

            flyEl.style.left = `${targetX}px`;
            flyEl.style.top = `${targetY}px`;
            flyEl.style.transform = 'scale(0.15) rotate(360deg)';
            flyEl.style.opacity = '0.4';
        });

        // 4) Clean up after animation ends and trigger cart bounce
        setTimeout(() => {
            flyEl.remove();
            
            // Bouncy Cart Micro-animation
            floatingCart.classList.add('cart-bounce');
            setTimeout(() => floatingCart.classList.remove('cart-bounce'), 600);
        }, 750);
    }

    // API helper: đọc cả body lỗi để lấy message
    async function api(url, options = {}) {
        const opts = {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            ...options
        };
        if (opts.body && !(opts.body instanceof FormData)) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(opts.body);
        }

        const res = await fetch(url, opts);

        let data, text = '';
        try { data = await res.json(); } catch {
            try { text = await res.text(); } catch { }
        }

        if (!res.ok) {
            const msg = (data && (data.message || data.error)) || text || ('Cart API ' + res.status);
            console.error('Cart API error', res.status, msg);
            const err = new Error(msg);
            err.status = res.status;
            err.data = data;
            throw err;
        }
        return data;
    }

    function renderCart(data) {
        const { data: items = [], count = 0, total = 0 } = data || {};

        if (cartCount) {
            cartCount.textContent = count;
            cartCount.style.display = count > 0 ? 'flex' : 'none';
        }

        const headerCartCount = document.getElementById('headerCartCount');
        if (headerCartCount) {
            headerCartCount.textContent = count;
            headerCartCount.style.display = count > 0 ? 'inline-block' : 'none';
        }

        // Đồng bộ dữ liệu sang trang giỏ hàng lớn (nếu đang ở trang /cart)
        if (typeof window.renderFullCartPage === 'function') {
            window.renderFullCartPage(data);
        }

        if (!items.length) {
            if (emptyCart) emptyCart.style.display = 'block';
            if (cartItems) cartItems.style.display = 'none';
            if (cartFooter) cartFooter.style.display = 'block';
            if (cartTotal) cartTotal.textContent = '0';
            return;
        }

        if (emptyCart) emptyCart.style.display = 'none';
        if (cartItems) {
            cartItems.style.display = 'block';
            cartItems.innerHTML = items.map(i => `
                <div class="cart-item" data-id="${i.id}">
                    <img src="${i.image}" alt="${i.name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${i.name}</div>
                        <div class="cart-item-price">${money(i.price)} đ</div>
                    </div>
                    <div class="cart-item-controls">
                        <div class="quantity-btn" onclick="cartDecrease(${i.id})"><i class="fa fa-minus"></i></div>
                        <input type="text" class="quantity-input" id="qty-${i.id}" value="${i.quantity}"
                            style="width:45px;text-align:center;border-radius:5px;border:1px solid #ccc;"
                            onchange="cartInputChange(${i.id}, this); validateQuantity(${i.id}, this); Quantity(${i.id}, this);">
                        <div class="quantity-btn" onclick="cartIncrease(${i.id})"><i class="fa fa-plus"></i></div>
                        <div class="remove-item" onclick="cartRemove(${i.id})"><i class="fa fa-trash"></i></div>
                    </div>
                </div>
            `).join('');
        }

        if (cartFooter) cartFooter.style.display = 'block';
        if (cartTotal) cartTotal.textContent = money(total);
    }

    async function Quantity(id, input) {
        let value = input.value.trim();
        if (value === '') {
            showAlert('danger', 'Vui lòng nhập số lượng');
            return null;
        }
        if (!/^-?\d+$/.test(value)) {
            showAlert('danger', 'Vui lòng nhập số nguyên dương');
            return null;
        }
        let numValue = parseInt(value, 10);
        if (numValue < 0) {
            showAlert('danger', 'Số lượng món ăn phải lớn hơn hoặc bằng 0');
            return null;
        }
        if (numValue === 0) {
            const data = await api(`${CART_BASE}/${id}`, { method: 'DELETE' });
            renderCart(data);
            showToast('Đã xóa món khỏi giỏ hàng');
            return null;
        }
        return numValue;
    }

    async function validateQuantity(id, input) {
        let numberValue = input.value.trim();
        if (isNaN(numberValue)) {
            showAlert('danger', 'Số lượng món ăn phải là số');
            return null;
        }
        if (!Number.isInteger(parseFloat(numberValue))) {
            showAlert('danger', 'Số lượng món ăn phải là số nguyên');
            return null;
        }
        if (numberValue <= 0) {
            showAlert('danger', 'Số lượng món ăn phải lớn hơn 0');
            return null;
        }
        return numberValue;
    }


    // Nhập số lượng trực tiếp
    async function cartInputChange(id, input) {
        let newQty = input.value.trim();

        // Kiểm tra nếu ô trống
        // if (newQty === '') {
        //     showAlert('danger', 'Vui lòng nhập số lượng');
        //     return;
        // }

        // // Kiểm tra có phải số không
        // if (!/^-?\d+$/.test(newQty)) {
        //     showAlert('danger', 'Vui lòng nhập số nguyên dương');
        //     input.value = "";
        //     return;
        // }

        // newQty = parseInt(newQty);

        // // Kiểm tra số âm
        // if (newQty < 0) {
        //     showAlert('danger', 'Số lượng món ăn phải lớn hơn hoặc bằng 0');
        //     input.value = "";
        //     return;
        // }
        // newQty = Quantity(input);

        // newQty = validateQuantity(input);
        // if (newQty === null) return;
        newQty = parseInt(newQty);

        // Kiểm tra số 0
        if (newQty === 0) {
            const data = await api(`${CART_BASE}/${id}`, { method: 'DELETE' });
            renderCart(data);
            showToast('Đã xóa món khỏi giỏ hàng');
            return;
        }
        showToast('Cập nhật giỏ hàng thành công');
        const data = await api(`${CART_BASE}/${id}`, { method: 'PUT', body: { SoLuong: newQty } });
        renderCart(data);
    }


    async function toggleCartModal() {
        if (!cartModal) return;
        if (cartModal.style.display === 'block') {
            cartModal.style.display = 'none';
            return;
        }
        try {
            const data = await api(`${CART_BASE}`);
            renderCart(data);
            cartModal.style.display = 'block';
        } catch (e) {
            showAlert('danger', 'Không tải được giỏ hàng. Vui lòng thử lại.');
        }
    }

    if (floatingCart) floatingCart.addEventListener('click', toggleCartModal);
    if (closeCart) closeCart.addEventListener('click', toggleCartModal);
    if (cartModal) cartModal.addEventListener('click', (e) => { if (e.target === cartModal) toggleCartModal(); });

    // nut them gio hang
    document.addEventListener('click', async function (ev) {
        const btn = ev.target.closest('.add-to-cart-btn');
        if (!btn) return;

        ev.preventDefault();

        // 1) Chặn phía client nếu "Ngừng bán"
        const inStoppedCard = !!btn.closest('.ngung-ban');
        const flaggedStopped = (btn.dataset.status || '').toLowerCase() === 'ngung-ban'
            || btn.dataset.available === '0'
            || btn.getAttribute('aria-disabled') === 'true';

        // Phòng hờ: tìm text Ngừng bán cạnh nút (nếu có)
        const siblingStatus = btn.parentElement?.querySelector('.text-danger')?.textContent?.trim() || '';
        const textSaysStopped = /ngừng\s*bán/i.test(siblingStatus);

        const isStopped = inStoppedCard || flaggedStopped || textSaysStopped;

        if (isStopped) {
            showAlert('danger', 'Món này hiện đã ngừng bán');
            return;
        }

        try {
            btn.classList.add('adding');
            const MaMonAn = Number(btn.dataset.id);
            const imageSrc = btn.dataset.image;

            // Trigger fly-to-cart animation immediately
            flyToCart(btn, imageSrc);

            const data = await api(`${CART_BASE}`, {
                method: 'POST',
                body: { MaMonAn, SoLuong: 1 }
            });
            renderCart(data);

            // Show dynamic success toast notification
            const foodName = btn.dataset.name || 'món ăn';
            showToast(`Đã thêm <strong>${foodName}</strong> vào giỏ hàng thành công!`);
        } catch (e) {
            // 2) Nếu server trả lỗi do ngừng bán, hiển thị thông điệp đúng
            const serverMsg = (e && e.message) ? String(e.message) : '';
            if (/ngừng\s*bán/i.test(serverMsg) || e?.status === 409 || e?.status === 422) {
                showAlert('danger', 'Món này hiện đã ngừng bán');
            } else {
                showAlert('danger', 'Thêm vào giỏ thất bại. Xem Console để biết chi tiết.');
            }
        } finally {
            setTimeout(() => btn.classList.remove('adding'), 500);
        }
    });

    // Kept as a no-op function for backwards compatibility with any remaining legacy scripts
    window.attachAddToCartListeners = function() {};

    window.refreshFloatingCart = function() {
        api(`${CART_BASE}`).then(renderCart).catch(() => { });
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Cập nhật badge ban đầu
        api(`${CART_BASE}`).then(renderCart).catch(() => { });
    });

    // Tăng/giảm/xóa
    async function cartIncrease(id) {
        const input = document.getElementById('qty-' + id);
        let cur = parseInt(input.value.trim());
        if (isNaN(cur) || cur < 1) cur = 1;
        const newQty = cur + 1;
        input.value = newQty;
        const data = await api(`${CART_BASE}/${id}`, { method: 'PUT', body: { SoLuong: newQty } });
        renderCart(data);
    }
    async function cartDecrease(id) {
        const input = document.getElementById('qty-' + id);
        let cur = parseInt(input.value.trim());
        if (isNaN(cur) || cur < 1) cur = 1;
        const newQty = cur - 1;

        if (newQty === 0) {
            // Xóa món khỏi giỏ hàng khi số lượng = 0
            const data = await api(`${CART_BASE}/${id}`, { method: 'DELETE' });
            renderCart(data);
            showAlert('success', 'Đã xóa món khỏi giỏ hàng');
            return;
        }

        input.value = newQty;
        const data = await api(`${CART_BASE}/${id}`, { method: 'PUT', body: { SoLuong: newQty } });
        renderCart(data);
    }
    async function cartRemove(id) {
        const data = await api(`${CART_BASE}/${id}`, { method: 'DELETE' });
        renderCart(data);
    }
    async function clearCart() {
        const confirmed = await showConfirm('Xóa tất cả món trong giỏ?', 'Hành động này sẽ làm trống toàn bộ giỏ hàng hiện tại của bạn nhé.');
        if (!confirmed) return;
        const data = await api(`${CART_BASE}`, { method: 'DELETE' });
        renderCart(data);
    }



    function goToCheckout() {
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            const count = Number(cartCount.textContent || 0);
            if (count <= 0) {
                showAlert('danger', 'Vui lòng chọn ít nhất 1 món ăn để đặt hàng.');
                return;
            }
        }

        // Kiểm tra khách vãng lai
        if (!IS_AUTHENTICATED) {
            showAlert('guest_checkout', 'Vui lòng Đăng nhập hoặc Đăng ký nhanh tài khoản để tiến hành thanh toán giỏ hàng của bạn.');
            return;
        }

        // Kiểm tra các ô số lượng có trống không
        const quantityInputs = document.querySelectorAll('.quantity-input');
        let hasEmptyQuantity = false;

        quantityInputs.forEach(input => {
            const value = input.value.trim();
            if (value === '' || value === '0' || isNaN(parseInt(value)) || parseInt(value) < 1) {
                hasEmptyQuantity = true;
            }
        });

        if (hasEmptyQuantity) {
            showAlert('danger', 'Vui lòng nhập số lượng');
            return;
        }

        window.location.href = '/checkout';
    }

    // Global AJAC Toggle Favorite logic
    $(document).on('click', '.toggle-fav-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = $(this);
        const foodId = btn.data('id');
        
        btn.prop('disabled', true);

        $.ajax({
            url: `/favorites/${foodId}/toggle`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    const isAdded = response.status === 'added';
                    
                    // Update all heart buttons on the page with the same food ID to keep state synchronized
                    $(`.heart-fav-btn[data-id="${foodId}"]`).each(function() {
                        const targetBtn = $(this);
                        const icon = targetBtn.find('i');
                        
                        if (isAdded) {
                            icon.removeClass('far text-muted').addClass('fa fa-heart text-danger');
                            targetBtn.attr('title', 'Xóa khỏi yêu thích');
                            icon.css('transform', 'scale(1.4)');
                            setTimeout(() => icon.css('transform', ''), 300);
                        } else {
                            icon.removeClass('fa text-danger').addClass('far fa-heart text-muted');
                            targetBtn.attr('title', 'Thêm vào yêu thích');
                            icon.css('transform', 'scale(0.8)');
                            setTimeout(() => icon.css('transform', ''), 300);
                        }
                    });

                    // Toast message alert
                    const msg = response.message || (isAdded ? 'Đã thêm vào danh sách yêu thích!' : 'Đã xóa khỏi danh sách yêu thích!');
                    if (window.showToast) {
                        showToast(msg);
                    } else if (window.showAlert) {
                        showAlert('success', msg);
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false);
                if (window.showAlert) {
                    showAlert('danger', 'Đã có lỗi xảy ra. Vui lòng thử lại!');
                }
            }
        });
    });

    $(document).on('click', '.require-login-fav-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (window.showAlert) {
            showAlert('danger', 'Vui lòng đăng nhập để lưu món ăn yêu thích!');
        } else {
            alert('Vui lòng đăng nhập để lưu món ăn yêu thích!');
        }
    });
</script>

<style>
    /* Global Favorite Heart Button styling */
    .heart-fav-btn {
        transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .heart-fav-btn:hover {
        transform: scale(1.15) !important;
        background: #ffffff !important;
        box-shadow: 0 6px 15px rgba(239, 68, 68, 0.35) !important;
    }
    .heart-fav-btn i {
        transition: transform 0.2s ease;
    }
    .heart-fav-btn:active i {
        transform: scale(0.8) !important;
    }
</style>
<script>
    const CSRF_TOKEN = "{{ csrf_token() }}";
    const CART_BASE = "/cart";

    const floatingCart = document.getElementById('floatingCart');
    const cartModal = document.getElementById('cartModal');
    const closeCart = document.getElementById('closeCart');
    const cartCount = document.getElementById('cartCount');
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const cartFooter = document.getElementById('cartFooter');
    const cartTotal = document.getElementById('cartTotal');

    const money = v => new Intl.NumberFormat('vi-VN').format(v ?? 0);

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

        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'flex' : 'none';

        if (!items.length) {
            emptyCart.style.display = 'block';
            cartItems.style.display = 'none';
            cartFooter.style.display = 'block';
            cartTotal.textContent = '0';
            return;
        }

        emptyCart.style.display = 'none';
        cartItems.style.display = 'block';
        cartFooter.style.display = 'block';

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

        cartTotal.textContent = money(total);
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
            showAlert('success', 'Đã xóa món khỏi giỏ hàng');
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
            showAlert('success', 'Đã xóa món khỏi giỏ hàng');
            return;
        }
        showAlert('success', 'Cập nhật giỏ hàng thành công');
        const data = await api(`${CART_BASE}/${id}`, { method: 'PUT', body: { SoLuong: newQty } });
        renderCart(data);
    }


    async function toggleCartModal() {
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

    floatingCart.addEventListener('click', toggleCartModal);
    closeCart.addEventListener('click', toggleCartModal);
    cartModal.addEventListener('click', (e) => { if (e.target === cartModal) toggleCartModal(); });

    // Function to attach cart button listeners
    function attachAddToCartListeners() {
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            // Remove old listener if exists
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.addEventListener('click', async function (ev) {
                ev.preventDefault();

                // 1) Chặn phía client nếu "Ngừng bán"
                const inStoppedCard = !!this.closest('.ngung-ban');
                const flaggedStopped = (this.dataset.status || '').toLowerCase() === 'ngung-ban'
                    || this.dataset.available === '0'
                    || this.getAttribute('aria-disabled') === 'true';

                // Phòng hờ: tìm text Ngừng bán cạnh nút (nếu có)
                const siblingStatus = this.parentElement?.querySelector('.text-danger')?.textContent?.trim() || '';
                const textSaysStopped = /ngừng\s*bán/i.test(siblingStatus);

                const isStopped = inStoppedCard || flaggedStopped || textSaysStopped;

                if (isStopped) {
                    showAlert('danger', 'Món này hiện đã ngừng bán');
                    return;
                }

                try {
                    this.classList.add('adding');
                    const MaMonAn = Number(this.dataset.id);
                    const data = await api(`${CART_BASE}`, {
                        method: 'POST',
                        body: { MaMonAn, SoLuong: 1 }
                    });
                    renderCart(data);

                    if (data.added_new === true) {
                        showAlert('success', 'Món đã được thêm vào giỏ hàng');
                    } else if (data.added_new === false) {
                        showAlert('success', 'Số lượng món đã được cập nhật trong giỏ hàng');
                    }
                } catch (e) {
                    // 2) Nếu server trả lỗi do ngừng bán, hiển thị thông điệp đúng
                    const serverMsg = (e && e.message) ? String(e.message) : '';
                    if (/ngừng\s*bán/i.test(serverMsg) || e?.status === 409 || e?.status === 422) {
                        showAlert('danger', 'Món này hiện đã ngừng bán');
                    } else {
                        showAlert('danger', 'Thêm vào giỏ thất bại. Xem Console để biết chi tiết.');
                    }
                } finally {
                    setTimeout(() => this.classList.remove('adding'), 500);
                }
            });
        });
    }

    // Make function globally accessible for search results
    window.attachAddToCartListeners = attachAddToCartListeners;

    document.addEventListener('DOMContentLoaded', () => {
        // Initial attachment
        attachAddToCartListeners();

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
        if (!confirm('Xoá tất cả món trong giỏ?')) return;
        const data = await api(`${CART_BASE}`, { method: 'DELETE' });
        renderCart(data);
    }

    // Toast nhỏ góc phải
    function showAlert(type, message) {
        let alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            alertContainer = document.createElement('div');
            alertContainer.id = 'alertContainer';
            document.body.appendChild(alertContainer);
        }
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.style.position = 'fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = 9999;
        alert.innerHTML = `<i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i> ${message}`;
        alertContainer.appendChild(alert);
        setTimeout(() => alert.remove(), 4000);
    }

    function goToCheckout() {
        const cartCount = document.getElementById('cartCount');
        const count = Number(cartCount.textContent || 0);
        if (count <= 0) {
            showAlert('danger', 'Vui lòng chọn ít nhất 1 món ăn để đặt hàng.');
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
</script>
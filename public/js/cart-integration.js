// Cart integration with RESTful API
class CartManager {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('cart')) || [];
        this.isLoggedIn = this.checkLoginStatus();
        this.apiBaseUrl = '/api/v1';
        this.init();
    }

    checkLoginStatus() {
        return document.querySelector('meta[name="user-id"]') !== null;
    }

    async init() {
        if (this.isLoggedIn) {
            await this.syncWithBackend();
        }
        this.updateCartDisplay();
        this.bindAddToCartButtons();
    }

    async syncWithBackend() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/cart`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data.items.length > 0) {
                    this.cart = data.data.items.map(item => ({
                        id: String(item.ma_mon_an || item.MaMonAn),
                        name: item.mon_an?.TenMonAn || item.monAn?.TenMonAn,
                        price: item.mon_an?.Gia || item.monAn?.Gia,
                        quantity: item.so_luong || item.SoLuong,
                        image: item.mon_an?.hinh_anh_url || item.mon_an?.hinhAnhUrl || (item.mon_an?.HinhAnh ? (item.mon_an.HinhAnh.startsWith('http') ? item.mon_an.HinhAnh : `/images/${item.mon_an.HinhAnh}`) : '/images/no-image.png')
                    }));
                    this.saveCart();
                } else if (this.cart.length > 0) {
                    await this.syncLocalToBackend();
                }
            }
        } catch (error) {
            console.log('Sync failed, using localStorage:', error);
        }
    }

    async syncLocalToBackend() {
        if (!this.isLoggedIn || this.cart.length === 0) return;

        for (const item of this.cart) {
            await this.addToBackend(item.id, item.quantity);
        }
    }

    async addToBackend(foodId, quantity = 1) {
        if (!this.isLoggedIn) return false;

        try {
            const response = await fetch(`${this.apiBaseUrl}/cart`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ 
                    MaMonAn: foodId, 
                    SoLuong: quantity 
                })
            });

            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('Failed to add to backend:', error);
            return false;
        }
    }

    async updateBackend(cartItemId, quantity) {
        if (!this.isLoggedIn) return false;

        try {
            const response = await fetch(`${this.apiBaseUrl}/cart/${cartItemId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ SoLuong: quantity })
            });

            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('Failed to update backend:', error);
            return false;
        }
    }

    async removeFromBackend(cartItemId) {
        if (!this.isLoggedIn) return false;

        try {
            const response = await fetch(`${this.apiBaseUrl}/cart/${cartItemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('Failed to remove from backend:', error);
            return false;
        }
    }

    async clearBackend() {
        if (!this.isLoggedIn) return false;

        try {
            const response = await fetch(`${this.apiBaseUrl}/cart`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('Failed to clear backend:', error);
            return false;
        }
    }

    bindAddToCartButtons() {
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', async () => {
                const foodData = {
                    id: button.dataset.id,
                    name: button.dataset.name,
                    price: parseFloat(button.dataset.price),
                    image: button.dataset.image
                };

                button.classList.add('adding');
                button.disabled = true;

                await this.addToCart(foodData);

                setTimeout(() => {
                    button.classList.remove('adding');
                    button.disabled = false;
                }, 600);
            });
        });
    }

    async addToCart(food) {
        const existingItem = this.cart.find(item => item.id === food.id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.cart.push({ ...food, quantity: 1 });
        }

        this.saveCart();

        if (this.isLoggedIn) {
            const success = await this.addToBackend(food.id, 1);
            if (!success) console.warn('Failed to sync with server');
        }

        this.updateCartDisplay();
        this.showAddedToCartNotification(food.name);
    }

    async updateQuantity(itemId, change) {
        const item = this.cart.find(item => item.id === itemId);
        if (!item) return;

        const newQuantity = item.quantity + change;

        if (newQuantity <= 0) {
            await this.removeFromCart(itemId);
        } else {
            item.quantity = newQuantity;
            this.saveCart();

            if (this.isLoggedIn) {
                await this.updateBackend(itemId, newQuantity);
            }

            this.updateCartDisplay();
        }
    }

    async removeFromCart(itemId) {
        this.cart = this.cart.filter(item => item.id !== itemId);
        this.saveCart();

        if (this.isLoggedIn) {
            await this.removeFromBackend(itemId);
        }

        this.updateCartDisplay();
    }

    async clearCart() {
        if (confirm('Bạn có chắc chắn muốn xóa tất cả món ăn khỏi giỏ hàng?')) {
            this.cart = [];
            this.saveCart();

            if (this.isLoggedIn) {
                await this.clearBackend();
            }

            this.updateCartDisplay();
        }
    }

    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
    }

    updateCartDisplay() {
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
        }

        const emptyCart = document.getElementById('emptyCart');
        const cartItems = document.getElementById('cartItems');
        const cartFooter = document.getElementById('cartFooter');
        const cartTotal = document.getElementById('cartTotal');

        if (this.cart.length === 0) {
            if (emptyCart) emptyCart.style.display = 'block';
            if (cartItems) cartItems.style.display = 'none';
            if (cartFooter) cartFooter.style.display = 'none';
        } else {
            if (emptyCart) emptyCart.style.display = 'none';
            if (cartItems) cartItems.style.display = 'block';
            if (cartFooter) cartFooter.style.display = 'block';

            this.renderCartItems();
            if (cartTotal) cartTotal.textContent = this.formatPrice(totalPrice);
        }
    }

    renderCartItems() {
        const cartItems = document.getElementById('cartItems');
        if (!cartItems) return;

        cartItems.innerHTML = this.cart.map(item => `
            <div class="cart-item" data-id="${item.id}">
                <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${this.formatPrice(item.price)} đ</div>
                </div>
                <div class="cart-item-controls">
                    <div class="quantity-btn" onclick="cartManager.updateQuantity('${item.id}', -1)">
                        <i class="fa fa-minus"></i>
                    </div>
                    <div class="quantity">${item.quantity}</div>
                    <div class="quantity-btn" onclick="cartManager.updateQuantity('${item.id}', 1)">
                        <i class="fa fa-plus"></i>
                    </div>
                    <div class="remove-item" onclick="cartManager.removeFromCart('${item.id}')">
                        <i class="fa fa-trash"></i>
                    </div>
                </div>
            </div>
        `).join('');
    }

    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price);
    }

    showAddedToCartNotification(itemName) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 1002;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        `;
        notification.innerHTML = `
            <i class="fa fa-check-circle"></i>
            <strong>${itemName}</strong> đã được thêm vào giỏ hàng!
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    async goToCheckout() {
        if (this.cart.length === 0) {
            alert('Giỏ hàng trống!');
            return;
        }

        if (!this.isLoggedIn) {
            alert('Vui lòng đăng nhập để thanh toán!');
            window.location.href = '/login';
            return;
        }

        // Sync with backend before checkout
        await this.syncWithBackend();
        window.location.href = '/checkout';
    }
}

// Initialize cart manager
let cartManager;
document.addEventListener('DOMContentLoaded', function () {
    cartManager = new CartManager();

    const floatingCart = document.getElementById('floatingCart');
    const cartModal = document.getElementById('cartModal');
    const closeCart = document.getElementById('closeCart');

    if (floatingCart) {
        floatingCart.addEventListener('click', () => {
            cartModal.style.display = 'block';
        });
    }

    if (closeCart) {
        closeCart.addEventListener('click', () => {
            cartModal.style.display = 'none';
        });
    }

    if (cartModal) {
        cartModal.addEventListener('click', (e) => {
            if (e.target === cartModal) {
                cartModal.style.display = 'none';
            }
        });
    }
});

// Global helpers
function toggleCartModal() {
    const cartModal = document.getElementById('cartModal');
    if (cartModal.style.display === 'block') {
        cartModal.style.display = 'none';
    } else {
        cartModal.style.display = 'block';
    }
}
function clearCart() {
    if (cartManager) cartManager.clearCart();
}
function goToCheckout() {
    if (cartManager) cartManager.goToCheckout();
}

// Validate quantity function for testing
async function validateQuantity(elementId, input) {
    const value = input?.value?.trim();

    // Use global showAlert if available (for tests), otherwise check if function exists
    const alertFunction = (typeof global !== 'undefined' && global.showAlert) ||
        (typeof showAlert === 'function' ? showAlert : null);

    if (!value) {
        if (alertFunction) {
            alertFunction('danger', 'Số lượng món ăn không được để trống');
        }
        return null;
    }

    // Check if it contains decimal point first
    if (value.includes('.')) {
        if (alertFunction) {
            alertFunction('danger', 'Số lượng món ăn phải là số nguyên');
        }
        return null;
    }

    // Check if it's numeric (integers only)
    if (!/^\d+$/.test(value)) {
        if (alertFunction) {
            alertFunction('danger', 'Số lượng món ăn phải là số');
        }
        return null;
    }

    const numValue = parseInt(value, 10);

    // Check if positive
    if (numValue <= 0) {
        if (alertFunction) {
            alertFunction('danger', 'Số lượng món ăn phải lớn hơn 0');
        }
        return null;
    }

    return value;
}

// Export functions for testing
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateQuantity
    };
}
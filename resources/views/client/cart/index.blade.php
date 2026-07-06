@extends('client.layouts.master')

@section('title', 'Giỏ hàng - TAFOOD')

@section('content')
    <div class="hero_area">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>
        @include('client.partials._header')
    </div>

<style>
    @media (min-width: 992px) {
        .hero_area { min-height: 125px !important; height: 125px !important; }
        .hero_area .bg-box { height: 125px !important; }
    }

    body {
        background: linear-gradient(135deg, #f0f2f5 0%, #e8ecf1 100%) !important;
        min-height: 100vh;
    }

    .cart-page-wrapper {
        max-width: 960px;
        margin: 40px auto;
        padding: 0 16px 60px;
    }

    /* Back button */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        color: #1e293b !important;
        font-size: 13.5px;
        font-weight: 700;
        margin-bottom: 28px;
        padding: 8px 24px;
        background: #ffbe33;
        border-radius: 25px;
        box-shadow: 0 4px 15px rgba(255,190,51,0.2);
        transition: all 0.2s ease;
    }
    .back-button:hover {
        background: #e69d00;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255,190,51,0.35);
    }

    /* Page title */
    .cart-title {
        font-size: 28px;
        font-weight: 800;
        color: #222831;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cart-title i { color: #ffbe33; font-size: 26px; }

    /* Two-column layout */
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        align-items: start;
    }

    /* Cart items panel */
    .cart-items-panel {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .cart-items-header {
        background: linear-gradient(135deg, #222831 0%, #2d3748 100%);
        padding: 18px 24px;
        color: white;
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cart-items-header i { color: #ffbe33; }
    .cart-count-badge {
        background: #ffbe33;
        color: #222831;
        font-size: 12px;
        font-weight: 800;
        padding: 2px 9px;
        border-radius: 20px;
        margin-left: auto;
    }

    .cart-items-body { padding: 16px; }

    /* Individual cart item */
    .cart-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 12px;
        border-radius: 14px;
        border: 1px solid #f0f2f5;
        margin-bottom: 12px;
        background: #fafbfc;
        transition: all 0.2s ease;
    }
    .cart-item:last-child { margin-bottom: 0; }
    .cart-item:hover {
        border-color: rgba(255,190,51,0.3);
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .cart-item-img {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid rgba(255,190,51,0.2);
        flex-shrink: 0;
    }

    .cart-item-details { flex: 1; min-width: 0; }
    .cart-item-name {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cart-item-price {
        font-size: 14px;
        color: #ff6900;
        font-weight: 700;
        margin-top: 3px;
    }

    /* Quantity controls */
    .qty-controls {
        display: flex;
        align-items: center;
        gap: 0;
        background: #f0f2f5;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .qty-btn {
        width: 34px;
        height: 34px;
        border: none;
        background: transparent;
        color: #475569;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .qty-btn:hover { background: #e2e8f0; color: #222831; }
    .qty-btn.delete-btn:hover { background: rgba(220,53,69,0.1); color: #dc3545; }
    .qty-value {
        min-width: 32px;
        text-align: center;
        font-size: 15px;
        font-weight: 800;
        color: #222831;
    }

    .cart-item-subtotal {
        font-size: 15px;
        font-weight: 800;
        color: #222831;
        min-width: 80px;
        text-align: right;
        flex-shrink: 0;
    }

    /* Empty state */
    .cart-empty {
        text-align: center;
        padding: 60px 20px;
    }
    .cart-empty > i {
        font-size: 60px;
        color: #cbd5e0;
        margin-bottom: 16px;
        display: block;
    }
    .cart-empty p {
        color: #718096;
        font-size: 16px;
        margin-bottom: 24px;
    }
    .btn-shop {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #ffbe33, #ff6900);
        color: white !important;
        text-decoration: none !important;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        transition: all 0.25s;
        box-shadow: 0 4px 15px rgba(255,105,0,0.2);
    }
    .btn-shop i {
        font-size: 16px;
        color: white;
        margin-bottom: 0;
        display: inline-block;
    }
    .btn-shop:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,105,0,0.3); }

    /* Summary panel */
    .cart-summary-panel {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }

    .summary-header {
        background: linear-gradient(135deg, #222831 0%, #2d3748 100%);
        padding: 18px 24px;
        color: white;
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .summary-header i { color: #ffbe33; }

    .summary-body { padding: 20px; }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        color: #64748b;
        margin-bottom: 12px;
    }
    .summary-row.total {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1.5px dashed #e2e8f0;
        font-size: 17px;
        font-weight: 800;
        color: #222831;
    }
    .summary-row.total .summary-value { color: #ff6900; font-size: 20px; }

    .btn-checkout {
        display: block;
        width: 100%;
        background: linear-gradient(135deg, #ffbe33 0%, #ff6900 100%);
        color: white !important;
        text-decoration: none !important;
        text-align: center;
        padding: 14px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 800;
        border: none;
        cursor: pointer;
        transition: all 0.25s;
        box-shadow: 0 4px 15px rgba(255,105,0,0.2);
        margin-top: 20px;
        letter-spacing: 0.3px;
    }
    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255,105,0,0.35);
    }
    .btn-checkout:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .btn-clear-cart {
        display: block;
        width: 100%;
        background: transparent;
        border: 1.5px solid #e2e8f0;
        color: #94a3b8 !important;
        text-align: center;
        padding: 10px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 10px;
    }
    .btn-clear-cart:hover {
        border-color: #dc3545;
        color: #dc3545 !important;
        background: rgba(220,53,69,0.05);
    }

    /* Skeleton */
    @keyframes skeleton-glow {
        0%   { background-position: -200px 0; }
        100% { background-position:  200px 0; }
    }
    .skeleton {
        background: linear-gradient(90deg, #f0f2f5 25%, #e2e8f0 50%, #f0f2f5 75%);
        background-size: 200px 100%;
        animation: skeleton-glow 1.4s infinite linear;
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .cart-layout { grid-template-columns: 1fr; }
        .cart-summary-panel { position: static; }
        .cart-page-wrapper { margin: 16px auto; }
    }

    /* Shopee-style elements */
    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #ffbe33;
        cursor: pointer;
        border-radius: 4px;
    }
    .cart-select-all-bar {
        display: flex;
        align-items: center;
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom: 1.5px solid #edf2f7;
        margin-bottom: 0px;
    }
    .cart-shop-group {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        margin: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .cart-shop-header {
        background: #f8fafc;
        padding: 12px 20px;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .shop-name-label {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0;
        cursor: pointer;
        font-size: 14.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .shop-name-label i {
        color: #ffbe33;
    }
    .cart-shop-items {
        padding: 16px;
    }
    .cart-item-checkbox-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 4px;
    }
</style>

<div class="cart-page-wrapper">
    <a href="{{ route('foods.index') }}" class="back-button">
        <i class="fa fa-arrow-left"></i> Tiếp tục mua sắm
    </a>

    <h1 class="cart-title">
        <i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn
    </h1>

    <div class="cart-layout">
        <!-- Items panel -->
        <div class="cart-items-panel">
            <div class="cart-items-header">
                <i class="fas fa-utensils"></i> Các món đã chọn
                <span class="cart-count-badge" id="cartCountBadge">0</span>
            </div>
            <div class="cart-items-body" id="cartItemsBody">
                <!-- Skeleton loading -->
                <div id="cartSkeleton">
                    @for ($i = 0; $i < 3; $i++)
                    <div class="cart-item">
                        <div class="skeleton" style="width:64px;height:64px;border-radius:12px;flex-shrink:0;"></div>
                        <div style="flex:1;">
                            <div class="skeleton" style="height:16px;width:60%;margin-bottom:8px;"></div>
                            <div class="skeleton" style="height:13px;width:35%;"></div>
                        </div>
                        <div class="skeleton" style="width:100px;height:34px;border-radius:10px;"></div>
                        <div class="skeleton" style="width:70px;height:16px;border-radius:6px;"></div>
                    </div>
                    @endfor
                </div>
                <div id="cartItemsList" style="display:none;"></div>
                <div id="cartEmptyState" class="cart-empty" style="display:none;">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Giỏ hàng của bạn đang trống</p>
                    <a href="{{ route('foods.index') }}" class="btn-shop">
                        <i class="fas fa-store"></i> Khám phá món ngon
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary panel -->
        <div class="cart-summary-panel">
            <div class="summary-header">
                <i class="fas fa-receipt"></i> Tóm tắt đơn hàng
            </div>
            <div class="summary-body">
                <div class="summary-row">
                    <span>Tạm tính (<span id="summaryCount">0</span> món)</span>
                    <span id="summarySubtotal">0 đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí giao hàng</span>
                    <span style="color:#28a745;font-weight:600;">Tính khi thanh toán</span>
                </div>
                <div class="summary-row total">
                    <span>Tổng cộng</span>
                    <span class="summary-value" id="summaryTotal">0 đ</span>
                </div>
                <!-- button thanh toán ngay -->
                <a href="{{ route('checkout.index') }}" id="btnCheckout" class="btn-checkout" style="pointer-events:none;opacity:0.5;">
                    <i class="fas fa-credit-card"></i> Thanh toán ngay
                </a>
                <button class="btn-clear-cart" id="btnClearCart" onclick="clearCart()" style="display:none;">
                    <i class="fas fa-trash-alt"></i> Xóa toàn bộ giỏ hàng
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    console.log("TA-FOOD: Cart index script loaded!");
    const CSRF = '{{ csrf_token() }}';

    let currentCartData = null;
    let selectedIds = [];
    let initializedSelected = false;

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }

    async function loadCart() {
        console.log("TA-FOOD: loadCart function started!");
        try {
            const res = await fetch('/cart', {
                headers: { 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            console.log("TA-FOOD: fetch completed with status:", res.status);
            if (!res.ok) {
                const text = await res.text();
                alert('HTTP Error: ' + res.status + '\nResponse: ' + text.substring(0, 300));
                return;
            }
            const data = await res.json();
            console.log("TA-FOOD: JSON parsed successfully:", data);
            renderFullCartPage(data);
        } catch (e) {
            alert('JavaScript Error: ' + e.message);
            console.error(e);
        }
    }

    function renderFullCartPage(data) {
        currentCartData = data;
        document.getElementById('cartSkeleton').style.display = 'none';
        const list = document.getElementById('cartItemsList');
        const empty = document.getElementById('cartEmptyState');
        
        const items = data.data?.items ?? data.data ?? [];
        const total = data.data?.total ?? data.total ?? 0;
        const count = data.data?.count ?? data.count ?? 0;

        // Lọc các item ID đã bị xóa khỏi giỏ hàng
        const activeIds = items.map(i => i.id);
        selectedIds = selectedIds.filter(id => activeIds.includes(id));

        // Tự động chọn tất cả sản phẩm ở lần tải đầu tiên
        if (!initializedSelected && items.length > 0) {
            selectedIds = items.map(i => i.id);
            initializedSelected = true;
        }

        // Tính toán tổng số lượng và tổng tiền của các món ĐÃ CHỌN
        const selectedItems = items.filter(i => selectedIds.includes(i.id));
        const selectedSubtotal = selectedItems.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        const selectedCount = selectedItems.reduce((sum, i) => sum + i.quantity, 0);

        // Cập nhật các badge số lượng và thông tin tổng tiền
        document.getElementById('cartCountBadge').textContent = count;
        document.getElementById('summaryCount').textContent = selectedCount;
        document.getElementById('summarySubtotal').textContent = formatCurrency(selectedSubtotal);
        document.getElementById('summaryTotal').textContent = formatCurrency(selectedSubtotal);

        if (!items || items.length === 0) {
            list.style.display = 'none';
            empty.style.display = 'block';
            document.getElementById('btnCheckout').style.pointerEvents = 'none';
            document.getElementById('btnCheckout').style.opacity = '0.5';
            document.getElementById('btnClearCart').style.display = 'none';
            return;
        }

        list.style.display = 'block';
        empty.style.display = 'none';
        document.getElementById('btnClearCart').style.display = 'block';

        // Cập nhật nút Thanh toán: Chỉ cho phép click nếu chọn ít nhất 1 món
        const btnCheckout = document.getElementById('btnCheckout');
        if (selectedIds.length === 0) {
            btnCheckout.style.pointerEvents = 'none';
            btnCheckout.style.opacity = '0.5';
            btnCheckout.href = '#';
        } else {
            btnCheckout.style.pointerEvents = '';
            btnCheckout.style.opacity = '1';
            btnCheckout.href = `/checkout?items=${selectedIds.join(',')}`;
        }

        let html = '';

        // Thanh Chọn Tất Cả (Shopee style)
        const allChecked = items.length > 0 && items.every(item => selectedIds.includes(item.id));
        html += `
            <div class="cart-select-all-bar">
                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" ${allChecked ? 'checked' : ''}>
                <label for="selectAllCheckbox" style="margin-left: 8px; font-weight: 700; cursor: pointer; margin-bottom: 0; user-select: none;">
                    Chọn tất cả (${selectedItems.length} món đã chọn)
                </label>
            </div>
        `;

        // Nhóm các sản phẩm theo Nhà hàng
        const groups = {};
        items.forEach(item => {
            const shopId = item.restaurant_id || 0;
            const shopName = item.restaurant_name || 'Hệ thống TAFOOD';
            if (!groups[shopId]) {
                groups[shopId] = {
                    id: shopId,
                    name: shopName,
                    items: []
                };
            }
            groups[shopId].items.push(item);
        });

        // Vẽ danh sách nhóm theo từng nhà hàng
        Object.values(groups).forEach(group => {
            const shopId = group.id;
            const shopName = group.name;
            const shopItems = group.items;
            
            const allShopItemsChecked = shopItems.every(item => selectedIds.includes(item.id));
            
            html += `
                <div class="cart-shop-group">
                    <div class="cart-shop-header">
                        <input type="checkbox" class="shop-checkbox" id="shop-chk-${shopId}" onchange="toggleSelectShop(${shopId}, this)" ${allShopItemsChecked ? 'checked' : ''}>
                        <label for="shop-chk-${shopId}" class="shop-name-label" style="user-select: none;">
                            <i class="fas fa-store"></i> ${shopName}
                        </label>
                    </div>
                    <div class="cart-shop-items">
            `;
            
            shopItems.forEach(item => {
                const isChecked = selectedIds.includes(item.id);
                html += `
                    <div class="cart-item" id="item-${item.id}">
                        <div class="cart-item-checkbox-wrapper">
                            <input type="checkbox" class="item-checkbox" data-id="${item.id}" data-shop-id="${shopId}" ${isChecked ? 'checked' : ''} onchange="toggleSelectItem(${item.id}, ${shopId}, this)">
                        </div>
                        <img class="cart-item-img" src="${item.image}" alt="${item.name}" onerror="this.src='{{ asset('images/no-image.png') }}'">
                        <div class="cart-item-details">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${formatCurrency(item.price)}</div>
                        </div>
                        <div class="qty-controls">
                            <button class="qty-btn delete-btn" title="Xóa" onclick="removeItem(${item.id})">
                                <i class="fas fa-trash-alt" style="font-size:13px;"></i>
                            </button>
                            <button class="qty-btn" onclick="changeQty(${item.id}, ${item.quantity - 1})">−</button>
                            <span class="qty-value" id="qty-${item.id}">${item.quantity}</span>
                            <button class="qty-btn" onclick="changeQty(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                        <div class="cart-item-subtotal">${formatCurrency(item.price * item.quantity)}</div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        list.innerHTML = html;
    }

    function toggleSelectAll(el) {
        const checked = el.checked;
        const items = currentCartData?.data?.items ?? currentCartData?.data ?? [];
        if (checked) {
            selectedIds = items.map(i => i.id);
        } else {
            selectedIds = [];
        }
        renderFullCartPage(currentCartData);
    }

    function toggleSelectShop(shopId, el) {
        const checked = el.checked;
        const items = currentCartData?.data?.items ?? currentCartData?.data ?? [];
        const shopItemIds = items.filter(i => (i.restaurant_id || 0) === shopId).map(i => i.id);
        
        if (checked) {
            shopItemIds.forEach(id => {
                if (!selectedIds.includes(id)) selectedIds.push(id);
            });
        } else {
            selectedIds = selectedIds.filter(id => !shopItemIds.includes(id));
        }
        renderFullCartPage(currentCartData);
    }

    function toggleSelectItem(itemId, shopId, el) {
        const checked = el.checked;
        if (checked) {
            if (!selectedIds.includes(itemId)) selectedIds.push(itemId);
        } else {
            selectedIds = selectedIds.filter(id => id !== itemId);
        }
        renderFullCartPage(currentCartData);
    }


    async function changeQty(foodId, newQty) {
        if (newQty < 1) { removeItem(foodId); return; }
        try {
            const res = await fetch(`/cart/${foodId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ SoLuong: newQty }),
                credentials: 'same-origin'
            });
            const data = await res.json();
            renderFullCartPage(data);
            // Trigger floating cart update in master layout
            if (window.refreshFloatingCart) window.refreshFloatingCart();
        } catch (e) { console.error(e); }
    }

    async function removeItem(foodId) {
        try {
            const row = document.getElementById('item-' + foodId);
            if (row) { row.style.opacity = '0.4'; row.style.pointerEvents = 'none'; }
            const res = await fetch(`/cart/${foodId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const data = await res.json();
            renderFullCartPage(data);
            if (window.refreshFloatingCart) window.refreshFloatingCart();
        } catch (e) { console.error(e); }
    }

    async function clearCart() {
        if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng không?')) return;
        try {
            const res = await fetch('/cart', {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const data = await res.json();
            renderFullCartPage(data);
            if (window.refreshFloatingCart) window.refreshFloatingCart();
        } catch (e) { console.error(e); }
    }

    loadCart();
</script>
@endsection

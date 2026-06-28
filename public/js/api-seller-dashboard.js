document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

const formatCurrency = (value) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

async function loadDashboardData() {
    try {
        const response = await fetch('/api/v1/seller/dashboard', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.status === 401 || response.status === 403) {
            window.location.href = '/login';
            return;
        }

        const result = await response.json();

        if (result.success) {
            renderDashboard(result.data);
        } else {
            console.error('Error loading dashboard:', result.message);
            // Handle error (e.g., show a toast or alert)
        }
    } catch (error) {
        console.error('Network error loading dashboard:', error);
    }
}

function renderDashboard(data) {
    // Restaurant Name
    const restaurantNameBadge = document.getElementById('restaurantNameBadge');
    if (restaurantNameBadge && data.restaurant) {
        restaurantNameBadge.textContent = data.restaurant.name;
        restaurantNameBadge.style.display = 'inline-block';
    }

    // Helper for growth badges
    const setGrowth = (elId, growthValue) => {
        const el = document.getElementById(elId);
        if (!el) return;
        if (growthValue > 0) {
            el.className = 'text-success small fw-bold';
            el.innerHTML = `<i class="fas fa-arrow-up"></i> +${growthValue}%`;
        } else if (growthValue < 0) {
            el.className = 'text-danger small fw-bold';
            el.innerHTML = `<i class="fas fa-arrow-down"></i> ${growthValue}%`;
        } else {
            el.className = 'text-muted small fw-bold';
            el.innerHTML = `0%`;
        }
    };

    // Stats
    document.getElementById('statTotalRevenue').textContent = formatCurrency(data.stats.revenue.value);
    setGrowth('revenueGrowth', data.stats.revenue.growth);

    document.getElementById('statTotalOrders').textContent = data.stats.orders.value;
    setGrowth('ordersGrowth', data.stats.orders.growth);

    document.getElementById('statTotalCustomers').textContent = data.stats.customers.value;
    setGrowth('customersGrowth', data.stats.customers.growth);

    // This was previously Total Foods, now Pending Orders
    const pendingStatEl = document.getElementById('statTotalFoods');
    if (pendingStatEl) pendingStatEl.textContent = data.stats.pending_orders;

    // Recent Orders
    const recentOrdersContainer = document.getElementById('recentOrdersContainer');
    if (data.recent_orders && data.recent_orders.length > 0) {
        recentOrdersContainer.innerHTML = data.recent_orders.map(order => `
            <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold text-dark">#${order.id}</div>
                    <div class="text-muted small">${order.customer_name}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-dark mb-1">${formatCurrency(order.amount)}</div>
                    <span class="badge bg-opacity-10 rounded-pill ${
                        order.status === 'Hoàn thành' ? 'bg-success text-success' : 
                        (order.status === 'Đang giao' ? 'bg-primary text-primary' : 
                        (order.status === 'Đang chuẩn bị' ? 'bg-info text-info' : 'bg-warning text-warning'))
                    }" style="font-size: 0.7rem; letter-spacing: 0.3px;">
                        ${order.status}
                    </span>
                    <div class="text-muted mt-1" style="font-size: 0.7rem;"><i class="far fa-clock me-1"></i>${order.created_at}</div>
                </div>
            </div>
        `).join('');
    } else {
        recentOrdersContainer.innerHTML = '<p class="text-muted text-center py-4">Chưa có đơn hàng nào.</p>';
    }

    // Top Selling Foods
    const topFoodsContainer = document.getElementById('topSellingFoodsContainer');
    if (data.top_selling_foods && data.top_selling_foods.length > 0) {
        topFoodsContainer.innerHTML = data.top_selling_foods.map(food => `
            <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold text-dark">${food.food_name}</div>
                    <div class="text-muted small">${food.sold_count} đã bán</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-dark">${formatCurrency(food.revenue)}</div>
                </div>
            </div>
        `).join('');
    } else {
        topFoodsContainer.innerHTML = '<p class="text-muted text-center py-4">Chưa có dữ liệu bán chạy.</p>';
    }
}

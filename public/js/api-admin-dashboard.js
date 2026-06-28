/**
 * Admin Dashboard – api-admin-dashboard.js
 * Connects to GET /api/v1/admin/dashboard (RESTful)
 */
document.addEventListener('DOMContentLoaded', function () {

    let revenueChart = null;
    let categoryChart = null;

    // ── Boot ───────────────────────────────────────────────────────────────────
    loadDashboard();

    // ── Fetch ──────────────────────────────────────────────────────────────────
    async function loadDashboard() {
        try {
            const res = await fetch('/api/v1/admin/dashboard', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (!json.success) throw new Error(json.message || 'Lỗi API');

            const d = json.data;
            renderKPI(d.kpi);
            renderMonthlyChart(d.monthly);
            renderCategoryChart(d.category_dist);
            renderTopRestaurants(d.top_restaurants);
            renderRecentOrders(d.recent_orders);
        } catch (err) {
            console.error('Dashboard error:', err);
        }
    }

    // ── KPI Cards ──────────────────────────────────────────────────────────────
    function renderKPI(kpi) {
        setText('kpi-revenue', '₫' + kpi.revenue.fmt);
        renderChange('kpi-revenue-change', kpi.revenue.change);

        setText('kpi-orders', fmtNum(kpi.orders.total));
        renderChange('kpi-orders-change', kpi.orders.change);

        setText('kpi-users', fmtNum(kpi.users.total));
        renderChange('kpi-users-change', kpi.users.change);

        setText('kpi-restaurants', fmtNum(kpi.restaurants.total));
        renderChange('kpi-restaurants-change', kpi.restaurants.change);
    }

    function renderChange(id, val) {
        const el = document.getElementById(id);
        if (!el) return;
        const up = val >= 0;
        el.innerHTML = `<i class="fas fa-arrow-trend-${up ? 'up' : 'down'} me-1"></i>${up ? '+' : ''}${val}%`;
        el.className = `kpi-change ${up ? 'up' : 'down'}`;
    }

    // ── Monthly Line Chart ─────────────────────────────────────────────────────
    function renderMonthlyChart(monthly) {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const labels  = monthly.map(m => m.month);
        const revenue = monthly.map(m => m.revenue);
        const orders  = monthly.map(m => m.orders);

        if (revenueChart) revenueChart.destroy();

        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Doanh thu (đ)',
                        data: revenue,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#8b5cf6',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'yRevenue',
                    },
                    {
                        label: 'Đơn hàng',
                        data: orders,
                        borderColor: '#ec4899',
                        backgroundColor: 'rgba(236, 72, 153, 0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#ec4899',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'yOrders',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                if (ctx.dataset.yAxisID === 'yRevenue') {
                                    return ' Doanh thu: ' + fmtCurrency(ctx.parsed.y);
                                }
                                return ' Đơn hàng: ' + ctx.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: '#6b7280', font: { size: 11 } }
                    },
                    yRevenue: {
                        type: 'linear',
                        position: 'left',
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            color: '#8b5cf6',
                            font: { size: 11 },
                            callback: v => fmtShort(v)
                        }
                    },
                    yOrders: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: '#ec4899', font: { size: 11 } }
                    }
                }
            }
        });
    }

    // ── Category Pie Chart with Outside Labels ────────────────────────────────
    function renderCategoryChart(dist) {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;

        const palette = ['#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444','#14b8a6','#f97316'];
        const labels  = dist.map(d => d.name);
        const values  = dist.map(d => d.value);

        if (categoryChart) categoryChart.destroy();

        categoryChart = new Chart(ctx, {
            type: 'pie',
            plugins: [ChartDataLabels],
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: palette.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 24,
                        bottom: 24,
                        left: 45,
                        right: 45
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed} món`
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        offset: 8,
                        color: (context) => context.dataset.backgroundColor[context.dataIndex],
                        font: {
                            family: 'Outfit',
                            weight: 'bold',
                            size: 11
                        },
                        formatter: (value, context) => {
                            const label = context.chart.data.labels[context.dataIndex];
                            const dataset = context.chart.data.datasets[0];
                            const total = dataset.data.reduce((sum, val) => sum + val, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            
                            // Ẩn các nhãn nhỏ hơn 3% để tránh chập chữ đè lên nhau
                            if (percentage < 3) {
                                return null;
                            }
                            return `${label} ${percentage}%`;
                        }
                    }
                }
            }
        });
    }

    // ── Top Restaurants ────────────────────────────────────────────────────────
    function renderTopRestaurants(list) {
        const tbody = document.getElementById('topRestaurantsBody');
        if (!tbody) return;

        if (!list || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>`;
            return;
        }

        const maxRevenue = list[0].revenue || 1;
        tbody.innerHTML = list.map((r, i) => {
            const pct = Math.round((r.revenue / maxRevenue) * 100);
            const medals = ['🥇','🥈','🥉'];
            const rank = medals[i] || `<span class="fw-bold text-muted">${i+1}</span>`;
            const img = r.image
                ? `<img src="${r.image}" alt="${r.name}" class="dash-resto-img">`
                : `<div class="dash-resto-img dash-resto-placeholder"><i class="fas fa-store"></i></div>`;
            return `
            <tr>
                <td class="text-center" style="width:40px;">${rank}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        ${img}
                        <span class="fw-semibold">${r.name}</span>
                    </div>
                </td>
                <td class="text-center">${r.order_count}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-grow-1">
                            <div class="progress" style="height:6px; border-radius:99px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width:${pct}%; background:linear-gradient(90deg,#8b5cf6,#ec4899);">
                                </div>
                            </div>
                        </div>
                        <span class="fw-bold text-primary" style="min-width:110px; text-align:right;">${r.revenue_fmt}</span>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    // ── Recent Orders ──────────────────────────────────────────────────────────
    function renderRecentOrders(list) {
        const tbody = document.getElementById('recentOrdersBody');
        if (!tbody) return;

        if (!list || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Chưa có đơn hàng</td></tr>`;
            return;
        }

        tbody.innerHTML = list.map(o => `
            <tr>
                <td><a href="/admin/orders/${o.id}" class="fw-bold text-primary text-decoration-none">${o.code}</a></td>
                <td>${o.customer}</td>
                <td class="fw-semibold">${o.amount}</td>
                <td><span class="badge bg-${o.color}-subtle text-${o.color} border border-${o.color}-subtle px-2 py-1">${o.status}</span></td>
                <td class="text-muted small">${o.date}</td>
            </tr>
        `).join('');
    }

    // ── Utilities ──────────────────────────────────────────────────────────────
    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function fmtNum(n) {
        return new Intl.NumberFormat('vi-VN').format(n);
    }

    function fmtCurrency(n) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(n);
    }

    function fmtShort(n) {
        if (n >= 1_000_000_000) return (n / 1_000_000_000).toFixed(1) + 'B';
        if (n >= 1_000_000)     return (n / 1_000_000).toFixed(0) + 'M';
        if (n >= 1_000)         return (n / 1_000).toFixed(0) + 'K';
        return n;
    }
});

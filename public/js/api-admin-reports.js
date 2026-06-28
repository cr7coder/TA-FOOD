/**
 * Admin Reports – api-admin-reports.js
 * Connects to GET /api/v1/admin/reports (RESTful)
 * Configures Chart.js visual graphics and detailed tables
 */
document.addEventListener('DOMContentLoaded', function () {

    // Chart instances
    let trendChartInstance = null;
    let categoryChartInstance = null;
    let topShopsChartInstance = null;
    let comparisonChartInstance = null;

    // Boot
    loadReports();

    // Event listeners for filters
    document.getElementById('select-report-type')?.addEventListener('change', loadReports);
    document.getElementById('select-time-period')?.addEventListener('change', loadReports);

    // Fetch reports data
    async function loadReports() {
        const type = document.getElementById('select-report-type')?.value || 'revenue';
        const period = document.getElementById('select-time-period')?.value || '365';

        try {
            const res = await fetch(`/api/v1/admin/reports?type=${type}&period=${period}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (!json.success) throw new Error(json.message || 'Lỗi API');

            const data = json.data;

            // Render stats
            renderKPI(data.kpi);
            renderMonthlyTrendChart(data.monthly_trend);
            renderCategoryPieChart(data.category_dist);
            renderTopShopsChart(data.top_shops);
            renderComparisonChart(data.comparison);
            renderTable(data.monthly_trend);
            renderTopUsersTable(data.top_users);

        } catch (err) {
            console.error('Reports analytics loading error:', err);
        }
    }

    // KPI Bindings
    function renderKPI(kpi) {
        // Revenue Card
        setText('kpi-revenue', '₫' + kpi.revenue.fmt);
        renderChangeBadge('kpi-revenue-growth', kpi.revenue.change);
        setText('kpi-revenue-footer', kpi.revenue.label);

        // Orders Card
        setText('kpi-orders', kpi.orders.fmt);
        renderChangeBadge('kpi-orders-growth', kpi.orders.change);
        setText('kpi-orders-footer', kpi.orders.label);

        // AOV Card
        setText('kpi-aov', '₫' + kpi.aov.fmt);
        renderChangeBadge('kpi-aov-growth', kpi.aov.change);
        setText('kpi-aov-footer', kpi.aov.label);

        // Completion Card
        setText('kpi-completion', kpi.completion_rate.fmt);
        renderChangeBadge('kpi-completion-growth', kpi.completion_rate.change);
        setText('kpi-completion-footer', kpi.completion_rate.label);
    }

    function renderChangeBadge(id, val) {
        const el = document.getElementById(id);
        if (!el) return;
        const isUp = val >= 0;
        el.innerHTML = `<i class="fas fa-arrow-trend-${isUp ? 'up' : 'down'}"></i><span>${isUp ? '+' : ''}${val}%</span>`;
        el.className = `kpi-growth ${isUp ? 'up' : 'down'}`;
    }

    // Chart 1: Line Chart Monthly Trend
    function renderMonthlyTrendChart(monthly) {
        const ctx = document.getElementById('canvas-monthly-trend');
        if (!ctx) return;

        const labels = monthly.map(m => m.month);
        const revenue = monthly.map(m => m.revenue);

        if (trendChartInstance) trendChartInstance.destroy();

        // Create gradient fill
        const canvasCtx = ctx.getContext('2d');
        const gradient = canvasCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(139, 92, 246, 0.16)');
        gradient.addColorStop(1, 'rgba(139, 92, 246, 0.01)');

        trendChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (đ)',
                    data: revenue,
                    borderColor: '#8b5cf6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.45,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: ctx => ` Doanh thu: ${fmtCurrency(ctx.parsed.y)}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { family: 'Outfit', size: 12 } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Outfit', size: 12 },
                            callback: v => fmtShortNumber(v)
                        }
                    }
                }
            }
        });
    }

    // Chart 2: Category Bar Chart
    function renderCategoryPieChart(dist) {
        const ctx = document.getElementById('canvas-category-pie');
        if (!ctx) return;

        const colors = ['#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#6b7280', '#06b6d4', '#6366f1'];
        const labels = dist.map(d => d.name);
        const values = dist.map(d => d.value);

        if (categoryChartInstance) categoryChartInstance.destroy();

        categoryChartInstance = new Chart(ctx, {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, labels.length),
                    borderRadius: 8,
                    maxBarThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed}%`
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 4,
                        color: (context) => context.dataset.backgroundColor[context.dataIndex],
                        font: {
                            family: 'Outfit',
                            weight: 'bold',
                            size: 11
                        },
                        formatter: (value) => `${value}%`
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { family: 'Outfit', size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Outfit', size: 11 },
                            callback: v => v + '%'
                        }
                    }
                }
            }
        });

        // Build premium legend custom list below chart
        const legendContainer = document.getElementById('category-legend-container');
        if (legendContainer) {
            legendContainer.innerHTML = dist.map((d, index) => {
                const color = colors[index % colors.length];
                return `
                    <div class="category-legend-row">
                        <div class="category-legend-left">
                            <span class="category-legend-bullet" style="background:${color};"></span>
                            <span>${d.name}</span>
                        </div>
                        <span class="category-legend-value">${d.value}%</span>
                    </div>
                `;
            }).join('');
        }
    }

    // Chart 3: Top Shops Bar Chart
    function renderTopShopsChart(shops) {
        const ctx = document.getElementById('canvas-top-shops');
        if (!ctx) return;

        // Take only top 7 shops
        const activeShops = shops.slice(0, 7);
        const labels = activeShops.map(s => s.name);
        const revenue = activeShops.map(s => s.revenue);

        if (topShopsChartInstance) topShopsChartInstance.destroy();

        topShopsChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revenue,
                    backgroundColor: '#8b5cf6',
                    hoverBackgroundColor: '#7c3aed',
                    borderRadius: 8,
                    maxBarThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        callbacks: {
                            label: ctx => ` Doanh thu: ${fmtCurrency(ctx.parsed.y)}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Outfit', size: 10 },
                            callback: function(val, index) {
                                // Shorten long names on X Axis
                                const name = labels[index] || '';
                                return name.length > 12 ? name.substring(0, 10) + '...' : name;
                            }
                        }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Outfit', size: 11 },
                            callback: v => fmtShortNumber(v)
                        }
                    }
                }
            }
        });
    }

    // Chart 4: Grouped Bar YoY Comparison
    function renderComparisonChart(comp) {
        const ctx = document.getElementById('canvas-comparison-yoy');
        if (!ctx) return;

        const labels = comp.map(c => c.week);
        const thisYear = comp.map(c => c.this_year);
        const lastYear = comp.map(c => c.last_year);

        if (comparisonChartInstance) comparisonChartInstance.destroy();

        comparisonChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Năm nay',
                        data: thisYear,
                        backgroundColor: '#8b5cf6',
                        borderRadius: 6,
                        maxBarThickness: 20
                    },
                    {
                        label: 'Năm trước',
                        data: lastYear,
                        backgroundColor: '#ec4899',
                        borderRadius: 6,
                        maxBarThickness: 20
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${fmtCurrency(ctx.parsed.y)}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { family: 'Outfit', size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            color: '#6b7280',
                            font: { family: 'Outfit', size: 11 },
                            callback: v => fmtShortNumber(v)
                        }
                    }
                }
            }
        });
    }

    // Table renderer
    function renderTable(monthly) {
        const tbody = document.getElementById('table-report-body');
        if (!tbody) return;

        if (!monthly || monthly.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu chi tiết</td></tr>`;
            return;
        }

        tbody.innerHTML = monthly.map(m => {
            let badgeClass = 'neutral';
            let trendIcon = '';
            if (m.growth.startsWith('+')) {
                badgeClass = 'up';
                trendIcon = '<i class="fas fa-arrow-trend-up me-1"></i>';
            } else if (m.growth.startsWith('-')) {
                badgeClass = 'down';
                trendIcon = '<i class="fas fa-arrow-trend-down me-1"></i>';
            }

            const growthBadge = m.growth 
                ? `<span class="growth-badge ${badgeClass}">${trendIcon}${m.growth}</span>`
                : '<span class="growth-badge neutral">—</span>';

            return `
                <tr>
                    <td class="fw-bold">${m.month}</td>
                    <td class="td-revenue">${m.revenue_fmt}</td>
                    <td class="td-orders">${m.orders_fmt}</td>
                    <td>${m.aov_fmt}</td>
                    <td>${growthBadge}</td>
                </tr>
            `;
        }).join('');
    }

    // Top users renderer
    function renderTopUsersTable(users) {
        const tbody = document.getElementById('table-top-users-body');
        if (!tbody) return;

        if (!users || users.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu chi tiết</td></tr>`;
            return;
        }

        tbody.innerHTML = users.map((u, idx) => {
            const ranks = [
                '<span class="badge bg-warning text-dark"><i class="fas fa-medal"></i> 1</span>',
                '<span class="badge bg-secondary"><i class="fas fa-medal"></i> 2</span>',
                '<span class="badge bg-danger"><i class="fas fa-medal"></i> 3</span>',
                `<span class="badge bg-light text-dark">${idx + 1}</span>`,
                `<span class="badge bg-light text-dark">${idx + 1}</span>`
            ];

            return `
                <tr>
                    <td class="text-center">${ranks[idx] || (idx + 1)}</td>
                    <td class="fw-bold"><i class="fas fa-user-circle text-secondary me-2"></i>${u.name}</td>
                    <td><span class="badge bg-light text-dark font-monospace" style="font-size: 11.5px;">${u.phone}</span></td>
                    <td class="td-orders">${u.orders} đơn</td>
                    <td class="td-revenue">${u.total_spent_fmt}</td>
                </tr>
            `;
        }).join('');
    }

    // Utilities helpers
    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function fmtCurrency(n) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(n);
    }

    function fmtShortNumber(n) {
        if (n >= 1000000000) return (n / 1000000000).toFixed(1) + 'B';
        if (n >= 1000000) return (n / 1000000).toFixed(0) + 'M';
        if (n >= 1000) return (n / 1000).toFixed(0) + 'K';
        return n;
    }
});

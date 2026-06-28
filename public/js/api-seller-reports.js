document.addEventListener('DOMContentLoaded', function() {
    loadReportData('this_month');
});

// Expose to window for the onchange event in the blade file
window.loadReportData = async function(timeRange = 'this_month') {
    try {
        const response = await fetch(`/api/v1/seller/reports?time_range=${timeRange}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const result = await response.json();

        if (result.success) {
            updateLabels(timeRange);
            updateSummary(result.data.summary);
            
            // destroy old charts
            const revCanvas = document.getElementById('revenueChart');
            if (revCanvas && Chart.getChart(revCanvas)) Chart.getChart(revCanvas).destroy();
            const catCanvas = document.getElementById('categoryChart');
            if (catCanvas && Chart.getChart(catCanvas)) Chart.getChart(catCanvas).destroy();
            
            renderRevenueChart(result.data.monthly_revenue);
            updateMonthlyDetails(result.data.monthly_revenue);
            updateTopFoods(result.data.top_foods);
            renderCategoryChart(result.data.category_stats);
        } else {
            console.error('Failed to fetch report data:', result.message);
        }
    } catch (error) {
        console.error('Error fetching report data:', error);
    }
}

function updateLabels(timeRange) {
    let compareText = 'so với tháng trước';
    let titleText = 'Đơn hàng tháng này';
    
    if (timeRange === 'last_month') {
        compareText = 'so với tháng trước nữa';
        titleText = 'Đơn hàng tháng trước';
    } else if (timeRange === 'this_quarter') {
        compareText = 'so với quý trước';
        titleText = 'Đơn hàng quý này';
    } else if (timeRange === 'this_year') {
        compareText = 'so với năm trước';
        titleText = 'Đơn hàng năm nay';
    } else if (timeRange === 'all_time') {
        compareText = 'so với trước đây';
        titleText = 'Tổng đơn hàng';
    }

    document.querySelectorAll('.growth-compare-label').forEach(el => {
        el.textContent = compareText;
    });
    
    const titleEl = document.getElementById('orders-title-label');
    if (titleEl) titleEl.textContent = titleText;
}

function formatCurrency(value) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value).replace('₫', '₫ ');
}

function formatShortCurrency(value) {
    if (value >= 1000000) {
        return '₫ ' + (value / 1000000).toFixed(1) + 'M';
    } else if (value >= 1000) {
        return '₫ ' + (value / 1000).toFixed(1) + 'K';
    }
    return '₫ ' + value;
}

function updateSummary(summary) {
    // Revenue
    document.getElementById('revenue-value').textContent = formatCurrency(summary.revenue.value);
    const revGrowth = document.getElementById('revenue-growth');
    const revBadge = document.getElementById('revenue-growth-badge');
    revGrowth.textContent = `${summary.revenue.growth >= 0 ? '+' : ''}${summary.revenue.growth}%`;
    revBadge.className = `badge px-2 py-1 me-2 ${summary.revenue.growth >= 0 ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger'}`;
    revBadge.querySelector('i').className = `fas ${summary.revenue.growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'} me-1`;

    // Payout (Lợi nhuận thực nhận)
    const payoutVal = document.getElementById('payout-value');
    if (payoutVal && summary.payout) {
        payoutVal.textContent = formatCurrency(summary.payout.value);
        const payGrowth = document.getElementById('payout-growth');
        const payBadge = document.getElementById('payout-growth-badge');
        payGrowth.textContent = `${summary.payout.growth >= 0 ? '+' : ''}${summary.payout.growth}%`;
        payBadge.className = `badge px-2 py-1 me-2 ${summary.payout.growth >= 0 ? 'bg-info-soft text-info' : 'bg-danger-soft text-danger'}`;
        payBadge.querySelector('i').className = `fas ${summary.payout.growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'} me-1`;

        // Commission detail labels (payout card)
        const commRateEl = document.getElementById('commission-rate-label');
        const commAmtEl = document.getElementById('commission-amount-label');
        const rate = summary.payout.commission_rate ?? 15;
        if (commRateEl) commRateEl.textContent = rate + '%';
        if (commAmtEl) commAmtEl.textContent = formatCurrency(summary.payout.commission_amount ?? 0);

        // Update policy section with actual commission rate from API
        const policyRateDisplay = document.getElementById('policy-rate-display');
        const policyRateInline  = document.getElementById('policy-rate-inline');
        if (policyRateDisplay) policyRateDisplay.textContent = rate + '%';
        if (policyRateInline)  policyRateInline.textContent  = rate + '%';
    }

    // Orders
    document.getElementById('orders-value').textContent = summary.orders.value;
    const ordGrowth = document.getElementById('orders-growth');
    const ordBadge = document.getElementById('orders-growth-badge');
    ordGrowth.textContent = `${summary.orders.growth >= 0 ? '+' : ''}${summary.orders.growth}%`;
    ordBadge.className = `badge px-2 py-1 me-2 ${summary.orders.growth >= 0 ? 'bg-primary-soft text-primary' : 'bg-danger-soft text-danger'}`;
    ordBadge.querySelector('i').className = `fas ${summary.orders.growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'} me-1`;

    // AOV
    document.getElementById('aov-value').textContent = formatCurrency(summary.aov.value);
    const aovGrowth = document.getElementById('aov-growth');
    const aovBadge = document.getElementById('aov-growth-badge');
    aovGrowth.textContent = `${summary.aov.growth >= 0 ? '+' : ''}${summary.aov.growth}%`;
    aovBadge.className = `badge px-2 py-1 me-2 ${summary.aov.growth >= 0 ? 'bg-warning-soft text-warning' : 'bg-danger-soft text-danger'}`;
    aovBadge.querySelector('i').className = `fas ${summary.aov.growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'} me-1`;
}

function renderRevenueChart(data) {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    const labels = data.map(item => item.label);
    // Map 0 to null so minBarLength is ignored and no bar is drawn for 0 values
    const values = data.map(item => item.revenue === 0 ? null : item.revenue);
    const hasData = data.some(item => item.revenue > 0);

    if (!hasData) {
        const parent = canvas.parentElement;
        parent.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Chưa có dữ liệu doanh thu</div>';
        return;
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu',
                data: values,
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                hoverBackgroundColor: '#3b82f6',
                borderRadius: 6,
                barThickness: 24,
                minBarLength: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + formatCurrency(context.raw || 0);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { 
                        drawBorder: false,
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: value => (value / 1000000).toFixed(0) + 'M',
                        font: { size: 11 },
                        color: '#94a3b8'
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { weight: 'bold' },
                        color: '#475569'
                    }
                }
            }
        }
    });
}

function updateMonthlyDetails(data) {
    const list = document.getElementById('monthly-details-list');
    list.innerHTML = '';
    
    // Sort desc to show most recent first
    [...data].reverse().forEach(item => {
        const li = document.createElement('li');
        li.className = 'list-group-item border-0 py-3 d-flex justify-content-between align-items-center px-4';
        li.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="bg-light rounded p-2 me-3 fw-bold" style="width: 40px; text-align: center;">${item.label}</div>
                <div>
                    <div class="fw-bold text-dark">${formatCurrency(item.revenue)}</div>
                    <div class="small text-muted">${item.orders} đơn hàng</div>
                </div>
            </div>
            <i class="fas fa-chevron-right text-light small"></i>
        `;
        list.appendChild(li);
    });
}

function updateTopFoods(foods) {
    const body = document.getElementById('top-foods-body');
    if (!body) return;
    body.innerHTML = '';

    if (foods.length === 0) {
        body.innerHTML = '<tr><td colspan="3" class="text-center py-5 text-muted">Chưa có dữ liệu</td></tr>';
        return;
    }

    foods.forEach(food => {
        const tr = document.createElement('tr');
        tr.className = 'border-bottom';
        tr.innerHTML = `
            <td class="px-4 py-3">
                <div class="fw-medium text-dark">${food.name}</div>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="fw-bold">${food.sold}</span>
            </td>
            <td class="px-4 py-3 text-end">
                <div class="fw-bold text-dark">${formatShortCurrency(food.revenue)}</div>
            </td>
        `;
        body.appendChild(tr);
    });
}

function renderCategoryChart(stats) {
    const canvas = document.getElementById('categoryChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    const labels = stats.map(s => s.category);
    // Map 0 to null so minBarLength is ignored and no bar is drawn for 0 values
    const values = stats.map(s => s.revenue === 0 ? null : s.revenue);
    const total = stats.reduce((sum, s) => sum + s.revenue, 0);

    if (total === 0) {
        const parent = canvas.parentElement;
        parent.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Chưa có dữ liệu danh mục</div>';
        return;
    }
    
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.map(c => c + 'cc'),
                hoverBackgroundColor: colors,
                borderRadius: 6,
                barThickness: 24,
                minBarLength: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + formatCurrency(context.raw || 0);
                        }
                    }
                }
            },
            scales: {
                x: { display: false },
                y: { 
                    grid: { display: false },
                    ticks: {
                        font: { weight: 'bold' },
                        color: '#475569'
                    }
                }
            }
        }
    });

    // Legend
    const legend = document.getElementById('category-legend');
    if (!legend) return;
    legend.innerHTML = '';
    stats.forEach((s, i) => {
        let percent = total > 0 ? (s.revenue / total) * 100 : 0;
        let percentStr = percent < 1 && percent > 0 ? percent.toFixed(2) : percent.toFixed(1);
        percentStr = percentStr.replace(/\.0$/, '');
        
        const item = document.createElement('div');
        item.className = 'd-flex justify-content-between align-items-center mb-2';
        item.innerHTML = `
            <div class="d-flex align-items-center">
                <div style="width: 12px; height: 12px; background-color: ${colors[i % colors.length]}; border-radius: 3px; margin-right: 10px;"></div>
                <span class="small fw-medium">${s.category}</span>
            </div>
            <div class="small">
                <span class="fw-bold">${formatShortCurrency(s.revenue)}</span>
                <span class="text-muted ms-2">(${percentStr}%)</span>
            </div>
        `;
        legend.appendChild(item);
    });
}

@extends('admin.layouts.app')

@section('title', 'Báo cáo Analytics')

@section('styles')
<style>
    /* ── Custom Google Font for outfit ── */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .report-page {
        font-family: 'Outfit', system-ui, -apple-system, sans-serif;
        color: var(--text);
        padding-bottom: 2rem;
    }

    /* ── Premium Gradient Header Card ── */
    .report-header-card {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        border: none;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.25);
        color: #ffffff;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .report-header-card::before {
        content: "";
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        top: -150px;
        right: -100px;
        pointer-events: none;
    }

    .report-header-left {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .report-header-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        animation: pulseHeader 2s infinite ease-in-out;
    }

    @keyframes pulseHeader {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.04); }
    }

    .report-header-title h1 {
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .report-header-title p {
        font-size: 0.85rem;
        opacity: 0.85;
        margin: 4px 0 0;
        font-family: monospace;
    }

    .btn-pdf-export {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 0.6rem 1.4rem;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(5px);
    }

    .btn-pdf-export:hover {
        background: #ffffff;
        color: #8b5cf6;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    /* ── Glassmorphic Filter Card ── */
    .filter-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        padding: 1.1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .filter-label {
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .filter-label i {
        color: var(--primary);
    }

    .filter-select {
        background: var(--bg);
        color: var(--text);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.45rem 1.8rem 0.45rem 0.9rem;
        font-size: 0.88rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        outline: none;
    }

    .filter-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
    }

    /* ── KPI Grid & Cards ── */
    .kpis-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1200px) {
        .kpis-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .kpis-grid { grid-template-columns: 1fr; }
    }

    .kpi-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 1.4rem 1.6rem;
        box-shadow: var(--shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(2, 6, 23, 0.12);
        border-color: var(--primary);
    }

    .kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .kpi-avatar {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #ffffff;
    }

    .kpi-avatar.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .kpi-avatar.teal { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); }
    .kpi-avatar.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .kpi-avatar.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
    .kpi-avatar.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .kpi-growth {
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.3rem 0.6rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .kpi-growth.up {
        background-color: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }
    .kpi-growth.down {
        background-color: rgba(239, 68, 68, 0.12);
        color: #ef4444;
    }

    .kpi-body {
        margin-bottom: 0.5rem;
    }

    .kpi-card-title {
        font-size: 0.85rem;
        color: var(--muted);
        font-weight: 500;
        margin: 0 0 6px;
    }

    .kpi-card-value {
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0;
        color: var(--text);
        letter-spacing: -0.5px;
    }

    .kpi-footer {
        font-size: 0.76rem;
        color: var(--muted);
        font-weight: 500;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* ── Skeleton loader for KPI values ── */
    .skel-loading {
        background: linear-gradient(90deg, var(--border) 25%, var(--bg) 50%, var(--border) 75%);
        background-size: 200% 100%;
        animation: skelShimmer 1.5s infinite linear;
        border-radius: 8px;
        display: inline-block;
    }

    @keyframes skelShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ── Layout Grid for Charts ── */
    .charts-row-dual {
        display: grid;
        grid-template-columns: 1.8fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .charts-row-equal {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1024px) {
        .charts-row-dual { grid-template-columns: 1fr; }
        .charts-row-equal { grid-template-columns: 1fr; }
    }

    .chart-box {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: var(--shadow);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .chart-box-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chart-legend-wrap {
        display: flex;
        gap: 1rem;
        font-size: 0.78rem;
        font-weight: 500;
    }

    .chart-legend-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        color: var(--muted);
    }

    .legend-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .canvas-container {
        position: relative;
        flex-grow: 1;
        min-height: 290px;
    }

    /* ── Custom Styled Category Legend ── */
    .category-legend-list {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        margin-top: 1rem;
        border-top: 1px dashed var(--border);
        padding-top: 1rem;
    }

    .category-legend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .category-legend-left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text);
    }

    .category-legend-bullet {
        width: 10px;
        height: 10px;
        border-radius: 3px;
    }

    .category-legend-value {
        color: var(--muted);
    }

    /* ── Detailed Table Card ── */
    .table-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: var(--shadow);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .table-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1.25rem;
    }

    .report-table-wrapper {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border);
    }

    .table-custom {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-custom th {
        background: #f8fafc;
        color: var(--muted);
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.9rem 1.2rem;
        border-bottom: 2px solid var(--border);
    }

    body.theme-dark .table-custom th {
        background: #1e293b;
        color: #94a3b8;
    }

    .table-custom td {
        padding: 0.85rem 1.2rem;
        font-size: 0.88rem;
        font-weight: 500;
        color: var(--text);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    .table-custom tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-custom tbody tr:hover {
        background-color: var(--sb-hover);
    }

    /* Column colors formatting */
    .td-revenue {
        color: #10b981 !important;
        font-weight: 700;
    }

    .td-orders {
        font-weight: 600;
    }

    .growth-badge {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }

    .growth-badge.up {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .growth-badge.down {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .growth-badge.neutral {
        background: rgba(107, 114, 128, 0.1);
        color: #6b7280;
    }
</style>
@endsection

@section('content')
<div class="report-page">

    {{-- ── 1. Premium Header Card ── --}}
    <div class="report-header-card">
        <div class="report-header-left">
            <div class="report-header-icon">
                <i class="fas fa-chart-line text-white"></i>
            </div>
            <div class="report-header-title">
                <h1>Báo cáo</h1>
                <p>admin.report.analytics</p>
            </div>
        </div>
        <div>
            <button class="btn-pdf-export" id="btn-export-pdf" onclick="window.print()">
                <i class="fas fa-file-pdf"></i>
                <span>Export PDF</span>
            </button>
        </div>
    </div>

    {{-- ── 2. Filters Bar ── --}}
    <div class="filter-card">
        <div class="filter-label">
            <i class="fas fa-sliders"></i>
            <span>Lọc báo cáo:</span>
        </div>
        <select class="filter-select" id="select-report-type">
            <option value="revenue" selected>Doanh thu</option>
            <option value="orders">Đơn hàng</option>
        </select>
        <select class="filter-select" id="select-time-period">
            <option value="7">7 ngày qua</option>
            <option value="30">30 ngày qua</option>
            <option value="90">90 ngày qua</option>
            <option value="365" selected>12 tháng qua</option>
        </select>
    </div>

    {{-- ── 3. KPI Metrics Grid ── --}}
    <div class="kpis-grid">
        {{-- Card 1: Tổng Doanh Thu --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar green"><i class="fas fa-dollar-sign"></i></div>
                <div class="kpi-growth up" id="kpi-revenue-growth">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>+18.5%</span>
                </div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title" id="kpi-revenue-title">Tổng doanh thu</p>
                <h2 class="kpi-card-value" id="kpi-revenue">
                    <span class="skel-loading" style="width:130px; height:32px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer" id="kpi-revenue-footer">So với cùng kỳ</p>
        </div>

        {{-- Card 1.5: Thực thu của Sàn --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar teal"><i class="fas fa-money-bill-trend-up"></i></div>
                <div class="kpi-growth up" id="kpi-admin-net-growth">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>+0%</span>
                </div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title" id="kpi-admin-net-title">Thực thu của Sàn</p>
                <h2 class="kpi-card-value" id="kpi-admin-net">
                    <span class="skel-loading" style="width:130px; height:32px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer" id="kpi-admin-net-footer">So với cùng kỳ</p>
        </div>

        {{-- Card 2: Tổng Đơn Hàng --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar blue"><i class="fas fa-cart-shopping"></i></div>
                <div class="kpi-growth up" id="kpi-orders-growth">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>+12.3%</span>
                </div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title" id="kpi-orders-title">Tổng đơn hàng</p>
                <h2 class="kpi-card-value" id="kpi-orders">
                    <span class="skel-loading" style="width:80px; height:32px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer" id="kpi-orders-footer">So với tháng trước</p>
        </div>

        {{-- Card 3: Giá Trị TB/Đơn --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar purple"><i class="fas fa-tag"></i></div>
                <div class="kpi-growth up" id="kpi-aov-growth">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>+8.7%</span>
                </div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title" id="kpi-aov-title">Giá trị TB/đơn</p>
                <h2 class="kpi-card-value" id="kpi-aov">
                    <span class="skel-loading" style="width:100px; height:32px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer" id="kpi-aov-footer">Tăng so với Q3</p>
        </div>

        {{-- Card 4: Tỷ Lệ Hoàn Thành --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar orange"><i class="fas fa-circle-check"></i></div>
                <div class="kpi-growth up" id="kpi-completion-growth">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>+15.2%</span>
                </div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title" id="kpi-completion-title">Tỷ lệ hoàn thành</p>
                <h2 class="kpi-card-value" id="kpi-completion">
                    <span class="skel-loading" style="width:80px; height:32px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer" id="kpi-completion-footer">Đơn thành công</p>
        </div>
    </div>

    {{-- ── 4. Charts Row 1: Revenue Monthly & Category Distribution ── --}}
    <div class="charts-row-dual">
        {{-- Line Chart: Xu hướng doanh thu theo tháng --}}
        <div class="chart-box">
            <div class="chart-box-title">
                <span>Xu hướng doanh thu theo tháng</span>
                <div class="chart-legend-wrap">
                    <div class="chart-legend-item">
                        <span class="legend-indicator" style="background:#8b5cf6;"></span>
                        <span>Doanh thu (đ)</span>
                    </div>
                </div>
            </div>
            <div class="canvas-container">
                <canvas id="canvas-monthly-trend"></canvas>
            </div>
        </div>

        {{-- Pie/Donut Chart: Doanh thu theo danh mục --}}
        <div class="chart-box">
            <div class="chart-box-title">
                <span>Doanh thu theo danh mục</span>
            </div>
            <div class="canvas-container" style="min-height: 290px;">
                <canvas id="canvas-category-pie"></canvas>
            </div>
            <div class="category-legend-list" id="category-legend-container">
                <!-- Javascript populated -->
            </div>
        </div>
    </div>

    {{-- ── 5. Charts Row 2: Top Shops & Period YoY Comparison ── --}}
    <div class="charts-row-equal">
        {{-- Bar Chart: Top cửa hàng theo doanh thu --}}
        <div class="chart-box">
            <div class="chart-box-title">
                <span>Top cửa hàng theo doanh thu</span>
            </div>
            <div class="canvas-container">
                <canvas id="canvas-top-shops"></canvas>
            </div>
        </div>

        {{-- Grouped Bar Chart: So sánh cùng kỳ năm trước --}}
        <div class="chart-box">
            <div class="chart-box-title">
                <span>So sánh cùng kỳ năm trước</span>
                <div class="chart-legend-wrap">
                    <div class="chart-legend-item">
                        <span class="legend-indicator" style="background:#8b5cf6;"></span>
                        <span>Năm nay</span>
                    </div>
                    <div class="chart-legend-item">
                        <span class="legend-indicator" style="background:#ec4899;"></span>
                        <span>Năm trước</span>
                    </div>
                </div>
            </div>
            <div class="canvas-container">
                <canvas id="canvas-comparison-yoy"></canvas>
            </div>
        </div>
    </div>

    {{-- ── 6. Detailed Data Tables (Top Users & Monthly Revenue) ── --}}
    <div class="charts-row-equal">
        {{-- Table: Top khách hàng đặt nhiều nhất --}}
        <div class="table-card mb-0">
            <h3 class="table-title"><i class="fas fa-crown text-warning me-2"></i>Top khách hàng đặt nhiều nhất</h3>
            <div class="report-table-wrapper">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Hạng</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Số đơn</th>
                            <th>Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody id="table-top-users-body">
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Đang tải danh sách...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Table: Chi tiết doanh thu theo tháng --}}
        <div class="table-card mb-0">
            <h3 class="table-title"><i class="fas fa-calendar-alt text-primary me-2"></i>Chi tiết doanh thu theo tháng</h3>
            <div class="report-table-wrapper">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th>Doanh thu</th>
                            <th>Số đơn hàng</th>
                            <th>Giá trị TB/đơn</th>
                            <th>Tăng trưởng</th>
                        </tr>
                    </thead>
                    <tbody id="table-report-body">
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Đang tải chi tiết báo cáo...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
{{-- Include Chart.js UMD --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="{{ asset('js/api-admin-reports.js') }}?v={{ time() }}"></script>
@endsection

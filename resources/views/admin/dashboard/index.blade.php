@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
    /* ── Page layout ─────────────────────────────────────────────────────────── */
    .dash-page { padding: 0; }

    /* ── Header ─────────────────────────────────────────────────────────────── */
    .dash-header { margin-bottom: 1.5rem; }
    .dash-header h1 { font-size: 1.6rem; font-weight: 700; color: var(--text); margin: 0 0 4px; }
    .dash-header p  { margin: 0; color: var(--muted); font-size: 0.875rem; }

    /* ── KPI cards ───────────────────────────────────────────────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px)  { .kpi-grid { grid-template-columns: 1fr; } }

    .kpi-card {
        background: var(--card);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: .5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        transition: transform .2s, box-shadow .2s;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 16px 36px rgba(2,6,23,.12); }

    .kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kpi-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; color: #fff;
    }
    .kpi-icon.green  { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .kpi-icon.blue   { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .kpi-icon.violet { background: linear-gradient(135deg, #a78bfa, #7c3aed); }
    .kpi-icon.orange { background: linear-gradient(135deg, #fb923c, #ea580c); }

    .kpi-change { font-size: .78rem; font-weight: 600; }
    .kpi-change.up   { color: #16a34a; }
    .kpi-change.down { color: #dc2626; }

    .kpi-label { font-size: .8rem; color: var(--muted); margin: 0; }
    .kpi-value { font-size: 1.55rem; font-weight: 700; color: var(--text); margin: 0; line-height: 1.2; }

    /* ── Charts row ──────────────────────────────────────────────────────────── */
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1100px) { .charts-row { grid-template-columns: 1fr; } }

    .dash-card {
        background: var(--card);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }
    .dash-card-title {
        font-size: .95rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1.25rem;
    }
    .chart-legend {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        margin-top: .75rem;
    }
    .legend-item {
        display: flex; align-items: center; gap: .4rem;
        font-size: .8rem; color: var(--muted);
    }
    .legend-dot {
        width: 10px; height: 10px; border-radius: 50%;
        display: inline-block;
    }

    /* ── Bottom row ──────────────────────────────────────────────────────────── */
    .bottom-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 1100px) { .bottom-row { grid-template-columns: 1fr; } }

    /* ── Table shared ────────────────────────────────────────────────────────── */
    .dash-table { width: 100%; border-collapse: collapse; }
    .dash-table th {
        font-size: .75rem; font-weight: 600; text-transform: uppercase;
        color: var(--muted); padding: .5rem .75rem;
        border-bottom: 2px solid var(--border);
        white-space: nowrap;
    }
    .dash-table td {
        padding: .7rem .75rem;
        border-bottom: 1px solid var(--border);
        font-size: .875rem; color: var(--text);
        vertical-align: middle;
    }
    .dash-table tr:last-child td { border-bottom: none; }
    .dash-table tbody tr:hover { background: var(--sb-hover); }

    /* ── Restaurant image ────────────────────────────────────────────────────── */
    .dash-resto-img {
        width: 38px; height: 38px; border-radius: 10px;
        object-fit: cover; flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,.1);
    }
    .dash-resto-placeholder {
        background: var(--sb-hover); display: flex;
        align-items: center; justify-content: center;
        color: var(--muted); font-size: .85rem;
    }

    /* ── Skeleton loading ────────────────────────────────────────────────────── */
    .skel { background: linear-gradient(90deg, var(--border) 25%, #f3f4f6 50%, var(--border) 75%);
        background-size: 200%; animation: skel 1.4s infinite linear; border-radius: 6px; }
    @keyframes skel { from{background-position:200%} to{background-position:-200%} }

    /* badge subtle variants */
    .bg-warning-subtle  { background: rgba(245,158,11,.12) !important; }
    .bg-success-subtle  { background: rgba(16,185,129,.12) !important; }
    .bg-danger-subtle   { background: rgba(239,68,68,.12)  !important; }
    .bg-info-subtle     { background: rgba(59,130,246,.12) !important; }
    .bg-primary-subtle  { background: rgba(139,92,246,.12) !important; }
    .bg-secondary-subtle{ background: rgba(107,114,128,.12)!important; }
    .text-warning  { color: #d97706 !important; }
    .text-success  { color: #059669 !important; }
    .text-danger   { color: #dc2626 !important; }
    .text-info     { color: #2563eb !important; }
    .text-primary  { color: #7c3aed !important; }
    .text-secondary{ color: #6b7280 !important; }
    .border-warning-subtle  { border-color: rgba(245,158,11,.3) !important; }
    .border-success-subtle  { border-color: rgba(16,185,129,.3) !important; }
    .border-danger-subtle   { border-color: rgba(239,68,68,.3)  !important; }
    .border-info-subtle     { border-color: rgba(59,130,246,.3) !important; }
    .border-primary-subtle  { border-color: rgba(139,92,246,.3) !important; }
    .border-secondary-subtle{ border-color: rgba(107,114,128,.3)!important; }
</style>
@endsection

@section('content')
<div class="dash-page">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="dash-header">
        <h1>Dashboard</h1>
        <p>Tổng quan hệ thống TAFood</p>
    </div>

    {{-- ── KPI Cards ────────────────────────────────────────────────────────── --}}
    <div class="kpi-grid">
        {{-- Revenue --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon green"><i class="fas fa-dollar-sign"></i></div>
                <span id="kpi-revenue-change" class="kpi-change up">
                    <i class="fas fa-arrow-trend-up me-1"></i>—
                </span>
            </div>
            <p class="kpi-label">Doanh thu tháng này</p>
            <p class="kpi-value" id="kpi-revenue">
                <span class="skel d-inline-block" style="width:140px;height:28px;">&nbsp;</span>
            </p>
            <p class="kpi-subtext text-muted small mt-1 mb-0" id="kpi-revenue-alltime" style="font-size: 0.78rem;">
                Tích lũy: —
            </p>
        </div>

        {{-- Orders --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon blue"><i class="fas fa-bag-shopping"></i></div>
                <span id="kpi-orders-change" class="kpi-change up">
                    <i class="fas fa-arrow-trend-up me-1"></i>—
                </span>
            </div>
            <p class="kpi-label">Đơn hàng tháng này</p>
            <p class="kpi-value" id="kpi-orders">
                <span class="skel d-inline-block" style="width:80px;height:28px;">&nbsp;</span>
            </p>
            <p class="kpi-subtext text-muted small mt-1 mb-0" id="kpi-orders-alltime" style="font-size: 0.78rem;">
                Tổng đơn hàng: —
            </p>
        </div>

        {{-- Users --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon violet"><i class="fas fa-users"></i></div>
                <span id="kpi-users-change" class="kpi-change up">
                    <i class="fas fa-arrow-trend-up me-1"></i>—
                </span>
            </div>
            <p class="kpi-label">Người dùng mới tháng này</p>
            <p class="kpi-value" id="kpi-users">
                <span class="skel d-inline-block" style="width:80px;height:28px;">&nbsp;</span>
            </p>
            <p class="kpi-subtext text-muted small mt-1 mb-0" id="kpi-users-alltime" style="font-size: 0.78rem;">
                Tổng thành viên: —
            </p>
        </div>

        {{-- Restaurants --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon orange"><i class="fas fa-store"></i></div>
                <span id="kpi-restaurants-change" class="kpi-change down">
                    <i class="fas fa-arrow-trend-down me-1"></i>—
                </span>
            </div>
            <p class="kpi-label">Cửa hàng mới tháng này</p>
            <p class="kpi-value" id="kpi-restaurants">
                <span class="skel d-inline-block" style="width:60px;height:28px;">&nbsp;</span>
            </p>
            <p class="kpi-subtext text-muted small mt-1 mb-0" id="kpi-restaurants-alltime" style="font-size: 0.78rem;">
                Tổng cửa hàng: —
            </p>
        </div>
    </div>

    {{-- ── Charts Row ───────────────────────────────────────────────────────── --}}
    <div class="charts-row">
        {{-- Line chart: Revenue --}}
        <div class="dash-card">
            <div class="dash-card-title">Doanh thu theo tháng</div>
            <div style="height: 280px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-dot" style="background:#8b5cf6;"></span>Doanh thu (đ)
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background:#ec4899;"></span>Đơn hàng
                </div>
            </div>
        </div>

        {{-- Donut chart: Category distribution --}}
        <div class="dash-card">
            <div class="dash-card-title">Tỷ lệ món bán ra theo danh mục</div>
            <div style="height: 280px; position: relative;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Bottom Row ───────────────────────────────────────────────────────── --}}
    <div class="bottom-row">
        {{-- Top 5 Restaurants --}}
        <div class="dash-card">
            <div class="dash-card-title">Top 5 cửa hàng doanh thu cao nhất</div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cửa hàng</th>
                            <th class="text-center">Đơn</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody id="topRestaurantsBody">
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="dash-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="dash-card-title mb-0">Đơn hàng gần đây</div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm"
                    style="background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;border-radius:8px;font-size:.8rem;padding:4px 14px;">
                    Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tiền</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersBody">
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
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
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="{{ asset('js/api-admin-dashboard.js') }}"></script>
@endsection

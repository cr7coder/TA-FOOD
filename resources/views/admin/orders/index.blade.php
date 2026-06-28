@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('page_actions')
    <button type="button" class="btn btn-soft btn-sm" onclick="exportOrders()">
        <i class="fas fa-file-export me-1"></i> Export
    </button>
@endsection

@section('content')
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    .stat-card .count {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 0;
    }
    .stat-card .label {
        font-size: 0.85rem;
        color: var(--muted);
        font-weight: 600;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: transparent;
    }

    /* Stat cards colored underlines and numbers */
    .stat-card[data-status="Chờ xử lý"]::after {
        background: #d97706;
    }
    .stat-card[data-status="Chờ xử lý"] .count {
        color: #d97706;
    }
    .stat-card[data-status="Chờ xử lý"].active {
        border-color: #d97706;
        background: rgba(217, 119, 6, 0.05);
    }

    .stat-card[data-status="Đã xác nhận"]::after {
        background: #0891b2;
    }
    .stat-card[data-status="Đã xác nhận"] .count {
        color: #0891b2;
    }
    .stat-card[data-status="Đã xác nhận"].active {
        border-color: #0891b2;
        background: rgba(8, 145, 178, 0.05);
    }

    .stat-card[data-status="Đang chuẩn bị"]::after {
        background: #7c3aed;
    }
    .stat-card[data-status="Đang chuẩn bị"] .count {
        color: #7c3aed;
    }
    .stat-card[data-status="Đang chuẩn bị"].active {
        border-color: #7c3aed;
        background: rgba(124, 58, 237, 0.05);
    }

    .stat-card[data-status="Đang giao"]::after {
        background: #2563eb;
    }
    .stat-card[data-status="Đang giao"] .count {
        color: #2563eb;
    }
    .stat-card[data-status="Đang giao"].active {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.05);
    }

    .stat-card[data-status="Hoàn thành"]::after {
        background: #059669;
    }
    .stat-card[data-status="Hoàn thành"] .count {
        color: #059669;
    }
    .stat-card[data-status="Hoàn thành"].active {
        border-color: #059669;
        background: rgba(5, 150, 105, 0.05);
    }

    .stat-card[data-status="Hủy"]::after {
        background: #dc2626;
    }
    .stat-card[data-status="Hủy"] .count {
        color: #dc2626;
    }
    .stat-card[data-status="Hủy"].active {
        border-color: #dc2626;
        background: rgba(220, 38, 38, 0.05);
    }

    .table-container {
        border-radius: var(--radius);
        overflow: hidden;
    }
    .table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: 2px solid var(--border);
        padding: 1rem;
    }
    body.theme-dark .table thead th {
        background: #1e293b;
        color: #94a3b8;
        border-bottom-color: #334155;
    }
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
    }
    .badge-status {
        padding: 0.5em 1em;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border: none;
    }
    .status-dang-giao { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
    .status-dang-chuan-bi { background: rgba(139, 92, 246, 0.15); color: #7c3aed; }
    .status-cho-xac-nhan { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .status-hoan-thanh { background: rgba(16, 185, 129, 0.15); color: #059669; }
    .status-huy { background: rgba(239, 68, 68, 0.15); color: #dc2626; }
    .status-da-xac-nhan { background: rgba(6, 182, 212, 0.15); color: #0891b2; }
    .order-code {
        color: var(--primary);
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
    }
    .price-text {
        font-weight: 800;
        color: #10b981;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
        transition: 0.2s;
        background: rgba(139, 92, 246, 0.1);
        color: var(--primary);
        border: none;
    }
    .btn-action:hover {
        background: var(--primary);
        color: #fff;
    }
    
    .filter-section {
        background: var(--card);
        padding: 1.5rem;
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
    }

    /* Premium Custom Pagination */
    .pagination {
        display: flex;
        gap: 6px;
        padding: 0;
        margin: 0;
        list-style: none;
    }
    .pagination .page-item .page-link {
        border: none;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% !important;
        font-weight: 600;
        font-size: 13.5px;
        color: #475569;
        background: #f1f5f9;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }
    body.theme-dark .pagination .page-item .page-link {
        color: #94a3b8;
        background: #1e293b;
    }
    .pagination .page-item .page-link:hover {
        background: #e2e8f0;
        color: #0f172a;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
    body.theme-dark .pagination .page-item .page-link:hover {
        background: #334155;
        color: #f8fafc;
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(168, 85, 247, 0.35) !important;
    }
    .pagination .page-item.disabled .page-link {
        opacity: 0.4;
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    body.theme-dark .pagination .page-item.disabled .page-link {
        background: #0f172a;
        color: #475569;
    }
    .pagination .page-item.disabled span.page-link {
        background: transparent !important;
        box-shadow: none !important;
    }
</style>

<div class="container-fluid p-0">
    <!-- Header with Breadcrumb (Overriding layout if needed) -->
    <div class="page-header card-surface mb-4" style="background: #A855F7 !important; border: none; box-shadow: 0 10px 25px rgba(168, 85, 247, 0.3) !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3" style="background: rgba(255,255,255,0.15); padding: 12px 16px;">
                <i class="fas fa-shopping-cart" style="font-size: 24px; color: white;"></i>
            </div>
            <div>
                <h1 class="mb-0" style="font-size: 20px; font-weight: 700; color: white;">Quản lý đơn hàng</h1>
                <div class="crumb" style="color: rgba(255,255,255,0.8); font-size: 13px;">admin.order.list</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            @yield('page_actions')
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4" id="statsContainer">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card card-surface p-3" data-status="Chờ xử lý">
                <div class="label">Chờ xác nhận</div>
                <div class="count" id="count-cho-xu-ly">0</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card card-surface p-3" data-status="Đã xác nhận">
                <div class="label">Đã xác nhận</div>
                <div class="count" id="count-da-thanh-toan">0</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card card-surface p-3" data-status="Đang chuẩn bị">
                <div class="label">Đang chuẩn bị</div>
                <div class="count" id="count-dang-chuan-bi">0</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card card-surface p-3" data-status="Đang giao">
                <div class="label">Đang giao</div>
                <div class="count" id="count-dang-giao">0</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card card-surface p-3" data-status="Hoàn thành">
                <div class="label">Hoàn thành</div>
                <div class="count" id="count-hoan-thanh">0</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="stat-card card-surface p-3" data-status="Hủy">
                <div class="label">Đã hủy</div>
                <div class="count" id="count-huy">0</div>
            </div>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="card-surface p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h5 class="mb-0 fw-bold">Danh sách đơn hàng</h5>
            <div class="text-muted small">Tổng: <span id="total-orders-count">0</span> đơn hàng</div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Tìm theo mã đơn, tên khách hàng, cửa hàng...">
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <select id="statusFilter" class="form-select">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="Chờ xử lý">Chờ xác nhận</option>
                    <option value="Đã xác nhận">Đã xác nhận</option>
                    <option value="Đang chuẩn bị">Đang chuẩn bị</option>
                    <option value="Đang giao">Đang giao</option>
                    <option value="Hoàn thành">Hoàn thành</option>
                    <option value="Hủy">Đã hủy</option>
                </select>
            </div>
            <div class="col-6 col-lg-3 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i> Lọc
                </button>
                <button class="btn btn-outline-secondary" onclick="resetFilters()">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>

        <div class="table-responsive table-container">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Cửa hàng</th>
                        <th class="text-center">Món</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>Ngày đặt</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <!-- Data will be loaded via AJAX -->
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="mt-4 d-flex justify-content-center"></div>
    </div>
</div>

<!-- Modal chi tiết đơn hàng (Optional) -->
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-surface">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Chi tiết đơn hàng <span id="modalOrderCode" class="order-code"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <!-- Content via AJAX -->
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <div id="modalActions"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/api-admin-orders.js') }}"></script>
@endsection

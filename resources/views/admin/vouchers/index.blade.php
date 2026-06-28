@extends('admin.layouts.app')

@section('title', 'Quản lý Voucher')

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
    .status-cho-xac-nhan { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .status-huy { background: rgba(239, 68, 68, 0.15); color: #dc2626; }
    .status-hoan-thanh { background: rgba(16, 185, 129, 0.15); color: #059669; }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-grid;
        place-items: center;
        transition: 0.2s;
        border: none;
        text-decoration: none;
    }
    .btn-action-view {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    .btn-action-view:hover {
        background: #3b82f6;
        color: #fff;
    }
    .btn-action-edit {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    .btn-action-edit:hover {
        background: #f59e0b;
        color: #fff;
    }
    .btn-action-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    .btn-action-delete:hover {
        background: #ef4444;
        color: #fff;
    }

    .time-remaining { font-size: 11px; font-weight: 500; }

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
    <!-- Header Banner styled like Order Management -->
    <div class="page-header card-surface mb-4" style="background: #a855f7 !important; border: none; box-shadow: 0 10px 25px rgba(168, 85, 247, 0.3) !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3" style="background: rgba(255,255,255,0.15); padding: 12px 16px;">
                <i class="fas fa-ticket-alt" style="font-size: 24px; color: white;"></i>
            </div>
            <div>
                <h1 class="mb-0" style="font-size: 20px; font-weight: 700; color: white;">Quản lý Voucher</h1>
                <div class="crumb" style="color: rgba(255,255,255,0.8); font-size: 13px;">admin.vouchers.index</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-light btn-sm text-dark d-flex align-items-center gap-1" style="font-weight: 600; border-radius: 8px; font-size: 13.5px; padding: 6px 12px;">
                <i class="fas fa-plus"></i> Thêm Voucher
            </a>
        </div>
    </div>

    <!-- Stats Cards styled like Order Management -->
    <div class="row g-3 mb-4" id="statsContainer">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card card-surface p-3" style="border-bottom: 4px solid #3b82f6;">
                <div class="label">Tổng Voucher</div>
                <div class="count text-primary mt-1" id="statTotal">—</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card card-surface p-3" style="border-bottom: 4px solid #10b981;">
                <div class="label">Còn hiệu lực</div>
                <div class="count text-success mt-1" id="statActive">—</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card card-surface p-3" style="border-bottom: 4px solid #f59e0b;">
                <div class="label">Sắp hết hạn</div>
                <div class="count text-warning mt-1" id="statExpiringSoon">—</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card card-surface p-3" style="border-bottom: 4px solid #ef4444;">
                <div class="label">Hết hạn</div>
                <div class="count text-danger mt-1" id="statExpired">—</div>
            </div>
        </div>
    </div>

    <!-- Main Card containing Filters & Table -->
    <div class="card-surface p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h5 class="mb-0 fw-bold">Danh sách Voucher</h5>
            <div class="text-muted small" id="paginationInfo">Đang tải thông tin...</div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" id="filterForm" class="mb-4">
            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Tìm kiếm mã voucher...">
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active">Còn hiệu lực</option>
                        <option value="expired">Đã hết hạn</option>
                        <option value="upcoming">Sắp diễn ra</option>
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <select name="percent_from" class="form-select">
                        <option value="">Giảm từ</option>
                        <option value="10">10%</option>
                        <option value="20">20%</option>
                        <option value="30">30%</option>
                        <option value="50">50%</option>
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <select name="percent_to" class="form-select">
                        <option value="">Giảm đến</option>
                        <option value="20">20%</option>
                        <option value="30">30%</option>
                        <option value="50">50%</option>
                        <option value="100">100%</option>
                    </select>
                </div>
                <div class="col-6 col-lg-2 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1" type="submit">
                        <i class="fas fa-filter me-2"></i> Lọc
                    </button>
                    <button class="btn btn-outline-secondary" type="button" id="resetBtn">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive table-container">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">
                            <input type="checkbox" class="form-check-input" id="checkAll">
                        </th>
                        <th>Mã Voucher</th>
                        <th class="text-center">Mức giảm</th>
                        <th class="text-center">Đơn tối thiểu</th>
                        <th class="text-center">Đã dùng / Tối đa</th>
                        <th class="text-center">Tối đa/User</th>
                        <th class="text-center">Ngày BĐ</th>
                        <th class="text-center">Ngày KT</th>
                        <th class="text-center">Còn lại</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="voucherTableBody">
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination pagination-sm mb-0" id="paginationList"></ul>
            </nav>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/api-admin-vouchers.js') }}"></script>
@endsection
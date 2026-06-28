@extends('admin.layouts.app')

@section('title', 'Quản lý User')

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
    .stat-card.active::after {
        background: var(--primary);
    }
    .stat-card.active {
        background: var(--active-bg);
        border-color: var(--primary);
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

    /* Styled action buttons matching order & voucher page */
    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-grid;
        place-items: center;
        transition: 0.2s;
        border: none;
        padding: 0;
        text-decoration: none;
    }
    .btn-outline-warning.btn-action {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    .btn-outline-warning.btn-action:hover {
        background: #f59e0b;
        color: #fff;
    }
    .btn-outline-primary.btn-action {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    .btn-outline-primary.btn-action:hover {
        background: #3b82f6;
        color: #fff;
    }
    .btn-outline-danger.btn-action {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    .btn-outline-danger.btn-action:hover {
        background: #ef4444;
        color: #fff;
    }

    /* Badges styles */
    .role-badge {
        padding: 0.5em 1em;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border: none;
    }
    .role-admin { background: rgba(139, 92, 246, 0.15); color: #7c3aed; }
    .role-seller { background: rgba(16, 185, 129, 0.15); color: #059669; }
    .role-customer { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
    
    .status-active { background: rgba(16, 185, 129, 0.15); color: #059669; }
    .status-inactive { background: rgba(239, 68, 68, 0.15); color: #dc2626; }

    .btn-purple {
        background-color: #8b5cf6;
        color: white !important;
    }
    .btn-purple:hover {
        background-color: #7c3aed;
        color: white !important;
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
    <!-- Header with Breadcrumb -->
    <div class="page-header card-surface mb-4" style="background: #10B981 !important; border: none; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3) !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3" style="background: rgba(255,255,255,0.15); padding: 12px 16px;">
                <i class="fas fa-store" style="font-size: 24px; color: white;"></i>
            </div>
            <div>
                <h1 class="mb-0" style="font-size: 20px; font-weight: 700; color: white;">Quản lý Nhà hàng</h1>
                <div class="crumb" style="color: rgba(255,255,255,0.8); font-size: 13px;">admin.restaurant.list</div>
            </div>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="card-surface p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h5 class="mb-0 fw-bold">Danh sách Nhà hàng</h5>
            <div class="text-muted small" id="paginationInfo">Hiển thị 1 đến 10 của 0 kết quả</div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Tìm kiếm nhà hàng, SĐT...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select id="statusFilter" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Chờ duyệt">Chờ duyệt</option>
                    <option value="Hoạt động">Hoạt động</option>
                    <option value="Từ chối">Từ chối</option>
                </select>
            </div>
        </div>

        <div class="table-responsive table-container">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tên nhà hàng</th>
                        <th>Chủ sở hữu</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="restaurantTableBody">
                    <!-- Data will be loaded here via JS -->
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
            <ul class="pagination pagination-sm mb-0" id="paginationContainer">
                <!-- Pagination links will be loaded here -->
            </ul>
        </div>
    </div>
</div>

<!-- Script for API interaction -->
<script src="{{ asset('js/api-admin-restaurant.js') }}?v={{ time() }}"></script>
@endsection

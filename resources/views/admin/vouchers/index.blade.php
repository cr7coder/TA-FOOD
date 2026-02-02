@extends('admin.admin')

@section('title', 'Quản lý Voucher')

@section('content')
    <style>
        /* Điều chỉnh font size cho trang index */
        .stats-card {
            font-size: 12px;
        }

        .stats-card .h5 {
            font-size: 1.1rem;
        }

        .stats-card .text-xs {
            font-size: 10px;
        }

        .card-header h6 {
            font-size: 13px;
        }

        .btn-group .btn {
            font-size: 11px;
            padding: 0.3rem 0.6rem;
        }

        .table {
            font-size: 12px;
        }

        .table th,
        .table td {
            padding: 0.5rem 0.4rem;
        }

        .badge {
            font-size: 10px;
            padding: 0.3em 0.5em;
        }

        .form-control {
            font-size: 12px;
            padding: 0.4rem 0.6rem;
        }

        .form-label {
            font-size: 11px;
            font-weight: 600;
        }

        .alert {
            font-size: 12px;
            padding: 0.6rem 0.8rem;
        }

        .empty-state h5 {
            font-size: 1rem;
        }

        .empty-state p {
            font-size: 12px;
        }

        .voucher-code-badge code {
            font-size: 11px;
            padding: 0.2rem 0.4rem;
        }

        .pagination {
            font-size: 12px;
        }

        .modal-body {
            font-size: 13px;
        }

        .modal-header h5 {
            font-size: 14px;
        }

        .time-remaining {
            font-size: 11px;
            font-weight: 500;
        }

        .time-remaining i {
            margin-right: 3px;
        }
    </style>

    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row mb-3">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng Voucher</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $vouchers->total() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ticket-alt fa-lg text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Còn hiệu lực</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\GiamGia::active()->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-lg text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-warning shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sắp hết hạn</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\GiamGia::expiringSoon()->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-lg text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-danger shadow h-100 py-2 stats-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Hết hạn</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\GiamGia::expired()->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-lg text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card shadow mb-3">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list"></i> Danh sách Voucher
                    </h6>
                    <small class="text-muted" style="font-size: 10px;">Quản lý tất cả voucher giảm giá</small>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.vouchers.export') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                    <button type="button" class="btn btn-info btn-sm" onclick="importVouchers()">
                        <i class="fas fa-file-import"></i> Import
                    </button>
                    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Thêm
                    </a>
                </div>
            </div>

            <div class="card-body p-3">
                <!-- Advanced Search & Filter -->
                <div class="card mb-2">
                    <div class="card-header collapsed py-2" data-bs-toggle="collapse" data-bs-target="#searchFilter"
                        aria-expanded="false">
                        <small><i class="fas fa-search"></i> Tìm kiếm & Bộ lọc</small>
                        <i class="fas fa-chevron-down float-end"></i>
                    </div>
                    <div class="collapse" id="searchFilter">
                        <div class="card-body p-3">
                            <form method="GET" class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">Mã Voucher</label>
                                    <input type="text" name="search" class="form-control" placeholder="Nhập mã..."
                                        value="{{ request('search') }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                            Còn hiệu lực
                                        </option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>
                                            Hết hạn
                                        </option>
                                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>
                                            Chưa kích hoạt
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">% từ</label>
                                    <input type="number" name="percent_from" class="form-control" placeholder="0"
                                        value="{{ request('percent_from') }}" min="0" max="100">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">% đến</label>
                                    <input type="number" name="percent_to" class="form-control" placeholder="100"
                                        value="{{ request('percent_to') }}" min="0" max="100">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Hành động</label>
                                    <div class="d-flex gap-1">
                                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                            <i class="fas fa-search"></i> Tìm
                                        </button>
                                        <a href="{{ route('admin.vouchers.index') }}"
                                            class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Bulk Actions -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <input type="checkbox" id="selectAll" class="form-check-input">
                        <label for="selectAll" class="form-check-label ms-1" style="font-size: 11px;">Chọn tất cả</label>
                        <div class="btn-group ms-2" id="bulkActions" style="display: none;">
                            <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('activate')"
                                style="font-size: 10px;">
                                <i class="fas fa-play"></i> Kích hoạt
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="bulkAction('deactivate')"
                                style="font-size: 10px;">
                                <i class="fas fa-pause"></i> Tạm dừng
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')"
                                style="font-size: 10px;">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted" style="font-size: 10px;">
                            Hiển thị {{ $vouchers->firstItem() ?? 0 }} - {{ $vouchers->lastItem() ?? 0 }}
                            trong tổng số {{ $vouchers->total() }} voucher
                        </small>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                </th>
                                <th>Mã Voucher</th>
                                <th>%</th>
                                <th>Đơn hàng tối thiểu</th>
                                <th>Số lần tối đa/user</th>
                                <th>Ngày BĐ</th>
                                <th>Ngày KT</th>
                                <th>Còn lại</th>
                                <th>Trạng Thái</th>
                                <th width="100">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr class="voucher-row" data-id="{{ $voucher->MaGiamGia }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input voucher-checkbox"
                                            value="{{ $voucher->MaGiamGia }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="voucher-code-badge">
                                                <code class="bg-light px-1 py-1 rounded">{{ $voucher->MaCode }}</code>
                                            </div>
                                            <button class="btn btn-sm btn-outline-secondary ms-1 p-1"
                                                onclick="copyToClipboard('{{ $voucher->MaCode }}')" title="Copy">
                                                <i class="fas fa-copy" style="font-size: 10px;"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-gradient-info">
                                            {{ $voucher->PhanTram }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if($voucher->DonHangToiThieu)
                                            <span class="badge bg-secondary">
                                                {{ number_format($voucher->DonHangToiThieu, 0, ',', '.') }} VNĐ
                                            </span>
                                        @else
                                            <span class="text-muted">Không giới hạn</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($voucher->SoLanToiDa)
                                            <span class="badge bg-info">
                                                {{ $voucher->SoLanToiDa }} lần
                                            </span>
                                        @else
                                            <span class="text-muted">Không giới hạn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $voucher->NgayBatDau->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-times"></i>
                                            {{ $voucher->NgayKetThuc->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="time-remaining text-{{ $voucher->getTimeRemainingColor() }}">
                                            <i class="fas fa-clock"></i>
                                            {{ $voucher->getTimeRemainingShort() }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($voucher->isActive())
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Active
                                            </span>
                                        @elseif($voucher->isExpired())
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> Expired
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="#" class="btn btn-sm btn-outline-info p-1" title="Xem chi tiết">
                                                <i class="fas fa-eye" style="font-size: 10px;"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-warning p-1" title="Sửa">
                                                <i class="fas fa-edit" style="font-size: 10px;"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger p-1"
                                                onclick="confirmDelete({{ $voucher->MaGiamGia }}, '{{ $voucher->MaCode }}')"
                                                title="Xóa">
                                                <i class="fas fa-trash" style="font-size: 10px;"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-3">
                                        <div class="empty-state">
                                            <i class="fas fa-ticket-alt fa-2x text-muted mb-2"></i>
                                            <h5 class="text-muted">Không có voucher nào</h5>
                                            <p class="text-muted">Hãy tạo voucher đầu tiên của bạn!</p>
                                            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Tạo Voucher
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($vouchers->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted small mb-0" style="font-size: 10px;">
                                Hiển thị {{ $vouchers->firstItem() }} - {{ $vouchers->lastItem() }}
                                trong tổng số {{ $vouchers->total() }} kết quả
                            </p>
                        </div>
                        <div>
                            {{ $vouchers->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Xác nhận xóa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <p>Bạn có chắc chắn muốn xóa voucher <strong id="voucherCode"></strong> không?</p>
                    <p class="text-danger small">
                        <i class="fas fa-warning"></i> Hành động này không thể hoàn tác!
                    </p>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        .voucher-code-badge code {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, .075);
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #36b9cc, #258391);
            color: white;
        }
    </style>

    <script>
        // Auto refresh time remaining every minute
        setInterval(function () {
            location.reload();
        }, 60000); // Refresh every 60 seconds

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Đã copy: ' + text, 'success');
            });
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = `
                                                                                        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" style="font-size: 12px;">
                                                                                            <div class="d-flex">
                                                                                                <div class="toast-body">${message}</div>
                                                                                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                                                                            </div>
                                                                                        </div>
                                                                                    `;

            let toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toastContainer';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '1050';
                document.body.appendChild(toastContainer);
            }

            toastContainer.insertAdjacentHTML('beforeend', toast);
            const toastElement = toastContainer.lastElementChild;
            const bsToast = new bootstrap.Toast(toastElement);
            bsToast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        // Confirm delete function
        function confirmDelete(id, code) {
            document.getElementById('voucherCode').textContent = code;
            document.getElementById('deleteForm').action = `/admin/vouchers/${id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Select all functionality
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const selectAllHeader = document.getElementById('selectAllHeader');
            const checkboxes = document.querySelectorAll('.voucher-checkbox');
            const bulkActions = document.getElementById('bulkActions');

            [selectAll, selectAllHeader].forEach(checkbox => {
                checkbox?.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    [selectAll, selectAllHeader].forEach(cb => cb.checked = this.checked);
                    toggleBulkActions();
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleBulkActions);
            });

            function toggleBulkActions() {
                const checkedBoxes = document.querySelectorAll('.voucher-checkbox:checked');
                if (checkedBoxes.length > 0) {
                    bulkActions.style.display = 'inline-block';
                } else {
                    bulkActions.style.display = 'none';
                }
            }
        });

        // Bulk actions
        function bulkAction(action) {
            const checkedBoxes = document.querySelectorAll('.voucher-checkbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Vui lòng chọn ít nhất một voucher!');
                return;
            }

            const actions = {
                'delete': 'xóa',
                'activate': 'kích hoạt',
                'deactivate': 'tạm dừng'
            };

            if (confirm(`Bạn có chắc chắn muốn ${actions[action]} ${ids.length} voucher đã chọn?`)) {
                console.log(`Bulk ${action}:`, ids);
                showToast(`Đã ${actions[action]} ${ids.length} voucher thành công!`, 'success');
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endsection
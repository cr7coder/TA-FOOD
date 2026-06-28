@extends('seller.layouts.app')

@section('title', 'Báo cáo')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold text-dark mb-0">Báo cáo</h2>
        <select id="timeRangeSelector" class="form-select w-auto fw-medium shadow-sm border-0" onchange="loadReportData(this.value)">
            <option value="this_month" selected>Tháng này</option>
            <option value="last_month">Tháng trước</option>
            <option value="this_quarter">Quý này</option>
            <option value="this_year">Năm nay</option>
            <option value="all_time">Toàn thời gian</option>
        </select>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4 align-items-stretch g-3">
        <!-- Doanh thu món ăn -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 d-flex flex-column">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-wallet text-success"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0 fw-medium">Doanh thu món ăn</p>
                                <h3 class="h4 fw-bold mb-0" id="revenue-value">₫0</h3>
                            </div>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.78rem; min-height: 18px;">Doanh thu gốc trước khi trừ phí</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success-soft text-success px-2 py-1 me-2" id="revenue-growth-badge">
                            <i class="fas fa-arrow-up me-1"></i> <span id="revenue-growth">0%</span>
                        </span>
                        <span class="text-muted small growth-compare-label">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lợi nhuận thực nhận -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 d-flex flex-column">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-hand-holding-usd text-info"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0 fw-medium">Lợi nhuận thực nhận</p>
                                <h3 class="h4 fw-bold mb-0" id="payout-value">₫0</h3>
                            </div>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.78rem; min-height: 18px;">
                            Đã trừ <span id="commission-rate-label" class="fw-bold text-primary">15%</span> phí sàn (<span id="commission-amount-label" class="fw-bold text-danger">₫0</span>)
                        </p>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info-soft text-info px-2 py-1 me-2" id="payout-growth-badge">
                            <i class="fas fa-arrow-up me-1"></i> <span id="payout-growth">0%</span>
                        </span>
                        <span class="text-muted small growth-compare-label">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng tháng này -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 d-flex flex-column">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-shopping-bag text-primary"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0 fw-medium" id="orders-title-label">Đơn hàng tháng này</p>
                                <h3 class="h4 fw-bold mb-0" id="orders-value">0</h3>
                            </div>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.78rem; min-height: 18px;">Số lượng đơn giao thành công</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary-soft text-primary px-2 py-1 me-2" id="orders-growth-badge">
                            <i class="fas fa-arrow-up me-1"></i> <span id="orders-growth">0%</span>
                        </span>
                        <span class="text-muted small growth-compare-label">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trung bình đơn hàng -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 d-flex flex-column">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-line text-warning"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0 fw-medium">Trung bình đơn hàng</p>
                                <h3 class="h4 fw-bold mb-0" id="aov-value">₫0</h3>
                            </div>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.78rem; min-height: 18px;">Giá trị trung bình mỗi đơn</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning-soft text-warning px-2 py-1 me-2" id="aov-growth-badge">
                            <i class="fas fa-arrow-up me-1"></i> <span id="aov-growth">0%</span>
                        </span>
                        <span class="text-muted small growth-compare-label">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-4">
        <!-- Monthly Revenue Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">Doanh thu theo tháng</h5>
                    <button class="btn btn-link text-muted p-0"><i class="fas fa-ellipsis-h"></i></button>
                </div>
                <div class="card-body">
                    <div style="height: 320px; position: relative;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats side container for the chart detail (optional but looks like Figma) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Chi tiết theo tháng</h5>
                </div>
                <div class="card-body p-0" style="max-height: 380px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="monthly-details-list">
                        <!-- JS populated -->
                        <li class="list-group-item border-0 py-3 text-center text-muted">Đang tải...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Selling Foods -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Top món bán chạy</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="px-4 py-3">Món ăn</th>
                                    <th class="px-4 py-3 text-center">Đã bán</th>
                                    <th class="px-4 py-3 text-end">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody id="top-foods-body">
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">Đang tải...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Stats -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">Thống kê theo danh mục</h5>
                    <button class="btn btn-link text-muted p-0"><i class="fas fa-ellipsis-h"></i></button>
                </div>
                <div class="card-body">
                    <div style="height: 250px; position: relative;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div id="category-legend" class="mt-4">
                        <!-- JS populated -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================
         ĐIỀU KHOẢN & CHÍNH SÁCH HOA HỒNG NỀN TẢNG
    ======================================================= -->
    <div class="row mt-4 mb-2">
        <div class="col-12">
            <div class="policy-header-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="policy-icon-wrap">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Điều khoản & Chính sách Hoa hồng Nền tảng</h5>
                        <p class="mb-0 small opacity-75">TA-Food Platform · Phiên bản hiệu lực từ 01/01/2026</p>
                    </div>
                </div>
                <span class="policy-badge-live"><i class="fas fa-circle me-1" style="font-size:0.55rem;"></i>Đang áp dụng</span>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Cột trái: Bảng phí hoa hồng -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; overflow:hidden;">
                <div class="card-header border-0 py-3 px-4" style="background: linear-gradient(135deg,#6366f1,#4f46e5); color:#fff;">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-percent me-2"></i>Biểu phí hoa hồng</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0" style="font-size:0.88rem;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="px-4 py-3 fw-semibold text-muted" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Đối tượng</th>
                                <th class="px-4 py-3 fw-semibold text-muted text-end" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Tỷ lệ</th>
                                <th class="px-4 py-3 fw-semibold text-muted text-end" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Áp dụng lên</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px;background:rgba(99,102,241,0.1);">
                                            <i class="fas fa-store" style="color:#6366f1;font-size:0.75rem;"></i>
                                        </span>
                                        <span class="fw-medium">Cửa hàng (Seller)</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <span class="badge px-3 py-2" style="background:rgba(99,102,241,0.12);color:#6366f1;font-size:0.9rem;font-weight:700;border-radius:8px;" id="policy-rate-display">15%</span>
                                </td>
                                <td class="px-4 py-3 text-end text-muted small">Doanh thu món ăn</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f1f5f9; background:#fafafa;">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px;background:rgba(59,130,246,0.1);">
                                            <i class="fas fa-truck" style="color:#3b82f6;font-size:0.75rem;"></i>
                                        </span>
                                        <span class="fw-medium">Phí vận chuyển</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <span class="badge px-3 py-2" style="background:rgba(16,185,129,0.12);color:#10b981;font-size:0.9rem;font-weight:700;border-radius:8px;">0%</span>
                                </td>
                                <td class="px-4 py-3 text-end text-muted small">Chuyển thẳng (trung lập)</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px;background:rgba(245,158,11,0.1);">
                                            <i class="fas fa-tag" style="color:#f59e0b;font-size:0.75rem;"></i>
                                        </span>
                                        <span class="fw-medium">Voucher do Admin</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <span class="badge px-3 py-2" style="background:rgba(16,185,129,0.12);color:#10b981;font-size:0.9rem;font-weight:700;border-radius:8px;">0%</span>
                                </td>
                                <td class="px-4 py-3 text-end text-muted small">Nền tảng tự chịu</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer border-0 px-4 py-3" style="background:#f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle" style="color:#6366f1;"></i>
                        <small class="text-muted">Tỷ lệ hoa hồng áp dụng riêng cho từng cửa hàng và có thể thay đổi theo thỏa thuận.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Điều khoản chính -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div class="card-header border-0 py-3 px-4" style="background: linear-gradient(135deg,#0f172a,#1e293b); color:#fff;">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-scroll me-2"></i>Các điều khoản chính</h6>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="policy-term-list">

                        <div class="policy-term-item">
                            <div class="policy-term-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;">
                                <i class="fas fa-hand-holding-dollar"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-1" style="font-size:0.9rem;">Phương thức thu phí hoa hồng</p>
                                <p class="text-muted mb-0" style="font-size:0.82rem;">
                                    Nền tảng <strong>TA-Food</strong> sẽ tự động khấu trừ <strong id="policy-rate-inline">15%</strong> trên <em>doanh thu tiền món ăn</em> của mỗi đơn hàng hoàn thành. Phần còn lại sẽ được thanh toán về cửa hàng theo chu kỳ đối soát hàng tháng.
                                </p>
                            </div>
                        </div>

                        <div class="policy-term-item">
                            <div class="policy-term-icon" style="background:rgba(16,185,129,0.1);color:#10b981;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-1" style="font-size:0.9rem;">Chu kỳ đối soát & thanh toán</p>
                                <p class="text-muted mb-0" style="font-size:0.82rem;">
                                    Đối soát thực hiện vào <strong>cuối mỗi tháng</strong>. Admin sẽ kiểm tra, xác nhận và chuyển khoản phần lợi nhuận thực nhận cho cửa hàng trong vòng <strong>3–5 ngày làm việc</strong> sau khi đối soát.
                                </p>
                            </div>
                        </div>

                        <div class="policy-term-item">
                            <div class="policy-term-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-1" style="font-size:0.9rem;">Đơn hàng bị hủy</p>
                                <p class="text-muted mb-0" style="font-size:0.82rem;">
                                    Các đơn hàng bị hủy <strong>không được tính vào doanh thu</strong> và không phát sinh phí hoa hồng. Chỉ các đơn <strong>"Hoàn thành"</strong> mới được đưa vào bảng đối soát.
                                </p>
                            </div>
                        </div>

                        <div class="policy-term-item">
                            <div class="policy-term-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-1" style="font-size:0.9rem;">Phí vận chuyển</p>
                                <p class="text-muted mb-0" style="font-size:0.82rem;">
                                    Phí ship khách hàng trả sẽ được <strong>chuyển nguyên vẹn</strong> về cửa hàng (sau khi trừ chi phí thực tế trả cho đơn vị vận chuyển). Nền tảng <strong>không thu hoa hồng</strong> trên phí vận chuyển.
                                </p>
                            </div>
                        </div>

                        <div class="policy-term-item" style="border-bottom:none;">
                            <div class="policy-term-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-1" style="font-size:0.9rem;">Minh bạch doanh thu</p>
                                <p class="text-muted mb-0" style="font-size:0.82rem;">
                                    Cửa hàng có thể theo dõi <strong>chi tiết doanh thu, hoa hồng đã trừ và lợi nhuận thực nhận</strong> theo từng tháng ngay trên trang Báo cáo này. Mọi thắc mắc vui lòng liên hệ admin nền tảng.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-soft { background-color: rgba(40, 167, 69, 0.1); }
    .bg-primary-soft { background-color: rgba(0, 123, 255, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }
    .card { border-radius: 12px; }
    .fw-medium { font-weight: 500; }

    /* ---- Policy Section ---- */
    .policy-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
        border-radius: 16px;
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 8px 24px rgba(15,23,42,0.18);
    }

    .policy-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(99,102,241,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #a5b4fc;
        flex-shrink: 0;
    }

    .policy-badge-live {
        background: rgba(16,185,129,0.2);
        color: #34d399;
        border: 1px solid rgba(52,211,153,0.3);
        border-radius: 20px;
        padding: 0.35rem 0.9rem;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    .policy-term-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .policy-term-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.9rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .policy-term-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        margin-top: 2px;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/api-seller-reports.js') }}"></script>
@endpush

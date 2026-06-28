@extends('admin.layouts.app')

@section('title', 'Hạch toán & Đối soát')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .recon-page {
        font-family: 'Outfit', system-ui, -apple-system, sans-serif;
        color: var(--text);
        padding-bottom: 2rem;
    }

    /* Premium Header Card */
    .recon-header-card {
        background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
        border: none;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(168, 85, 247, 0.3);
        color: #ffffff;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
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
        cursor: pointer;
    }

    .btn-pdf-export:hover {
        background: #ffffff;
        color: #a855f7;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .recon-header-card::before {
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

    .recon-header-left {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .recon-header-icon {
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
    }

    .recon-header-title h1 {
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .recon-header-title p {
        font-size: 0.85rem;
        opacity: 0.85;
        margin: 4px 0 0;
        font-family: monospace;
    }

    /* Filters Bar */
    .filter-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        padding: 1.1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 1rem;
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
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
    }

    /* KPI Cards */
    .kpis-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1400px) {
        .kpis-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 900px) {
        .kpis-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .kpis-grid { grid-template-columns: 1fr; }
    }

    /* Breakdown receipt style inside KPI card */
    .breakdown-receipt {
        margin-top: 0.85rem;
        padding-top: 0.75rem;
        border-top: 1px dashed var(--border);
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .breakdown-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.76rem;
        font-weight: 500;
    }

    .breakdown-line .label { color: var(--muted); }
    .breakdown-line .val-green  { color: #10b981; font-weight: 700; }
    .breakdown-line .val-red    { color: #ef4444; font-weight: 700; }
    .breakdown-line .val-blue   { color: #3b82f6; font-weight: 700; }
    .breakdown-line .val-indigo { color: #6366f1; font-weight: 700; }

    .breakdown-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.4rem;
        padding-top: 0.4rem;
        border-top: 1.5px solid var(--border);
        font-size: 0.82rem;
        font-weight: 800;
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
    .kpi-avatar.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .kpi-avatar.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
    .kpi-avatar.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

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
        font-size: 1.5rem;
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
    }

    /* Tabs Styling */
    .recon-tabs-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: var(--shadow);
        padding: 1.5rem;
    }

    .nav-pills .nav-link {
        color: var(--muted);
        font-weight: 600;
        font-size: 0.9rem;
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        transition: all 0.2s;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
        color: #fff;
        box-shadow: 0 6px 15px rgba(168, 85, 247, 0.25);
    }

    /* Table custom styles */
    .table-responsive {
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-top: 1rem;
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
        font-size: 0.78rem;
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
        font-size: 0.85rem;
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

    .badge-status {
        padding: 0.35rem 0.65rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge-status.paid {
        background-color: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }

    .badge-status.unpaid {
        background-color: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }

    .btn-action-payout {
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        transition: all 0.2s;
    }

    .btn-action-payout.pay {
        background-color: var(--primary);
        color: #fff;
        border: none;
    }

    .btn-action-payout.pay:hover {
        background-color: #9333ea;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(168, 85, 247, 0.2);
    }

    .btn-action-payout.unpay {
        background-color: rgba(107, 114, 128, 0.12);
        color: var(--text);
        border: 1px solid var(--border);
    }

    .btn-action-payout.unpay:hover {
        background-color: rgba(107, 114, 128, 0.2);
    }

    .search-input {
        background: var(--bg);
        color: var(--text);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        width: 250px;
        outline: none;
        transition: all 0.2s;
    }

    .search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
    }
</style>
@endsection

@section('content')
<div class="recon-page">
    
    {{-- Premium Header Card --}}
    <div class="recon-header-card">
        <div class="recon-header-left">
            <div class="recon-header-icon">
                <i class="fas fa-wallet text-white"></i>
            </div>
            <div class="recon-header-title">
                <h1>Hạch toán & Đối soát tài chính</h1>
                <p>admin.financial.reconciliation</p>
            </div>
        </div>
        <div>
            <button class="btn-pdf-export" onclick="window.print()">
                <i class="fas fa-file-pdf"></i>
                <span>Xuất PDF đối soát</span>
            </button>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="filter-card">
        <div class="filter-group">
            <div class="filter-label">
                <i class="fas fa-calendar-alt"></i>
                <span>Kỳ đối soát:</span>
            </div>
            <select class="filter-select" id="select-month" onchange="loadReconciliationData()">
                <!-- Populated via Javascript -->
            </select>
        </div>
        <div class="filter-group" id="search-container">
            <input type="text" class="search-input" id="search-merchant" placeholder="Tìm kiếm cửa hàng..." onkeyup="filterSellersTable()">
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="kpis-grid">
        {{-- Card 1: Tổng thu khách hàng --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar green"><i class="fas fa-hand-holding-dollar"></i></div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title">Tổng thu từ khách</p>
                <h2 class="kpi-card-value" id="kpi-customer-paid">
                    <span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer">Tổng tiền thực nhận từ đơn hoàn thành</p>
        </div>

        {{-- Card 2: Tổng trả Seller --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar purple"><i class="fas fa-store"></i></div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title">Tổng trả Cửa hàng</p>
                <h2 class="kpi-card-value" id="kpi-seller-owed">
                    <span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer">Doanh thu món ăn + chênh lệch ship</p>
        </div>

        {{-- Card 3: Tổng trả Shipper --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar blue"><i class="fas fa-truck-ramp-box"></i></div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title">Tổng trả Vận chuyển</p>
                <h2 class="kpi-card-value" id="kpi-carrier-owed">
                    <span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer">Phí vận chuyển thực tế trả đối tác</p>
        </div>

        {{-- Card 4: Thực thu Hệ thống (detailed breakdown) --}}
        <div class="kpi-card" style="border-left: 3px solid #f59e0b;">
            <div class="kpi-top">
                <div class="kpi-avatar orange"><i class="fas fa-shield-cat"></i></div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title">Thực thu Hệ thống</p>
                <h2 class="kpi-card-value" id="kpi-net-admin">
                    <span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>
                </h2>
            </div>
            {{-- Breakdown receipt: Net = Commission - Voucher (ship is pass-through = 0) --}}
            <div class="breakdown-receipt">
                <div class="breakdown-line">
                    <span class="label"><i class="fas fa-percent me-1" style="color:#6366f1"></i>Hoa hồng Sellers</span>
                    <span class="val-indigo" id="bd-commission">...</span>
                </div>
                <div class="breakdown-line">
                    <span class="label"><i class="fas fa-tag me-1 text-danger"></i>Bù giảm giá voucher</span>
                    <span class="val-red" id="bd-voucher">...</span>
                </div>
                <div class="breakdown-line" style="opacity:0.5; font-style:italic;">
                    <span class="label"><i class="fas fa-truck me-1"></i>Phí ship</span>
                    <span style="color:var(--muted); font-size:0.72rem;">trung lập (chuyển hết)</span>
                </div>
                <div class="breakdown-total">
                    <span style="color:var(--muted);">= Admin giữ lại</span>
                    <span id="bd-net-total">...</span>
                </div>
            </div>
            <p class="kpi-footer mt-2">Tổng thu khách − Tổng trả đối tác</p>
        </div>

        {{-- Card 5: Hoa hồng thu được --}}
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-avatar" style="background: rgba(99,102,241,0.15); color:#6366f1;"><i class="fas fa-percent"></i></div>
            </div>
            <div class="kpi-body">
                <p class="kpi-card-title">Hoa hồng thu được</p>
                <h2 class="kpi-card-value" style="color:#6366f1;" id="kpi-commission">
                    <span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>
                </h2>
            </div>
            <p class="kpi-footer">Phí hoa hồng khấu trừ từ Sellers</p>
        </div>
    </div>

    {{-- Tabs section --}}
    <div class="recon-tabs-card">
        <ul class="nav nav-pills mb-3" id="reconTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="sellers-tab" data-bs-toggle="tab" data-bs-target="#sellers-pane" type="button" role="tab" aria-controls="sellers-pane" aria-selected="true" onclick="toggleSearch(true)">
                    <i class="fas fa-store me-1"></i> Đối soát Cửa hàng (Sellers)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="carriers-tab" data-bs-toggle="tab" data-bs-target="#carriers-pane" type="button" role="tab" aria-controls="carriers-pane" aria-selected="false" onclick="toggleSearch(false)">
                    <i class="fas fa-truck-flatbed me-1"></i> Đối soát Vận chuyển (Carriers)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="reconTabsContent">
            {{-- Sellers Tab Pane --}}
            <div class="tab-pane fade show active" id="sellers-pane" role="tabpanel" aria-labelledby="sellers-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Tên Cửa hàng</th>
                                <th>Chủ cửa hàng</th>
                                <th class="text-center">Số đơn</th>
                                <th class="text-end">Tiền đồ ăn</th>
                                <th class="text-end" style="color:#6366f1;">Hoa hồng (%)</th>
                                <th class="text-end" style="color:#6366f1;">Hoa hồng (đ)</th>
                                <th class="text-end">Ship thu khách</th>
                                <th class="text-end">Chi phí ship gốc</th>
                                <th class="text-end">Chênh lệch ship</th>
                                <th class="text-end text-success">Tổng trả Seller</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="sellers-table-body">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Carriers Tab Pane --}}
            <div class="tab-pane fade" id="carriers-pane" role="tabpanel" aria-labelledby="carriers-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Đơn vị vận chuyển</th>
                                <th class="text-center">Số đơn giao</th>
                                <th class="text-end text-success">Phí vận chuyển thực tế</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="carriers-table-body">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
{{-- SweetAlert2 CDN for modern alerts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize date months selector starting from current month going back 12 months
    function initMonthSelector() {
        const select = document.getElementById('select-month');
        const now = new Date();
        for (let i = 0; i < 12; i++) {
            const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
            const value = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
            const label = `Tháng ${date.getMonth() + 1}/${date.getFullYear()}`;
            
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            select.appendChild(option);
        }
    }

    // Toggle search bar visibility when switching tabs
    function toggleSearch(show) {
        document.getElementById('search-container').style.display = show ? 'block' : 'none';
    }

    // Filter local merchants list
    function filterSellersTable() {
        const query = document.getElementById('search-merchant').value.toLowerCase();
        const rows = document.querySelectorAll('#sellers-table-body tr');
        
        rows.forEach(row => {
            if (row.cells.length < 2) return; // Skip loading or empty row
            const merchantName = row.cells[0].textContent.toLowerCase();
            const ownerName = row.cells[1].textContent.toLowerCase();
            if (merchantName.includes(query) || ownerName.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Load statistics and list data
    function loadReconciliationData() {
        const month = document.getElementById('select-month').value;
        
        // Show skeleton loadings
        document.getElementById('kpi-customer-paid').innerHTML = '<span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>';
        document.getElementById('kpi-seller-owed').innerHTML = '<span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>';
        document.getElementById('kpi-carrier-owed').innerHTML = '<span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>';
        document.getElementById('kpi-net-admin').innerHTML = '<span class="skel-loading" style="width:120px; height:28px;">&nbsp;</span>';
        
        document.getElementById('sellers-table-body').innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Đang tải dữ liệu đối soát...
                </td>
            </tr>
        `;

        document.getElementById('carriers-table-body').innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Đang tải dữ liệu đối soát...
                </td>
            </tr>
        `;

        fetch(`/api/v1/admin/reconciliation?month=${month}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi tải dữ liệu',
                    text: res.message || 'Không thể lấy thông tin đối soát.',
                    confirmButtonColor: '#a855f7'
                });
                return;
            }

            const data = res.month === month ? res : null;
            if (!data) return;

            // Render KPIs
            document.getElementById('kpi-customer-paid').textContent = formatCurrency(data.summary.total_customer_paid);
            document.getElementById('kpi-seller-owed').textContent = formatCurrency(data.summary.total_seller_owed);
            document.getElementById('kpi-carrier-owed').textContent = formatCurrency(data.summary.total_carrier_owed);
            
            // Commission KPI
            const kpiCommissionEl = document.getElementById('kpi-commission');
            if (kpiCommissionEl) kpiCommissionEl.textContent = formatCurrency(data.summary.commission_earned || 0);

            // Net Admin KPI + breakdown
            const netAdmin = data.summary.admin_net_commission;
            const netAdminEl = document.getElementById('kpi-net-admin');
            netAdminEl.textContent = formatCurrency(netAdmin);
            netAdminEl.className = 'kpi-card-value ' + (netAdmin < 0 ? 'text-danger' : 'text-success');

            // Breakdown receipt: Net = Commission - Voucher
            // (Shipping is a pure pass-through, admin keeps ZERO from ship fees)
            const commission = data.summary.commission_earned || 0;
            const voucher    = data.summary.voucher_subsidy   || 0;

            const bdCommission = document.getElementById('bd-commission');
            const bdVoucher    = document.getElementById('bd-voucher');
            const bdNetTotal   = document.getElementById('bd-net-total');

            if (bdCommission) bdCommission.textContent = '+' + formatCurrency(commission);
            if (bdVoucher)    bdVoucher.textContent    = voucher > 0 ? '−' + formatCurrency(voucher) : formatCurrency(0);
            if (bdNetTotal) {
                bdNetTotal.textContent = formatCurrency(netAdmin);
                bdNetTotal.className   = netAdmin >= 0 ? 'val-green' : 'val-red';
            }

            // Render Sellers
            const sellersBody = document.getElementById('sellers-table-body');
            sellersBody.innerHTML = '';
            
            if (data.sellers.length === 0) {
                sellersBody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle me-1"></i> Không có đơn hàng hoàn thành nào trong tháng này.
                        </td>
                    </tr>
                `;
            } else {
                data.sellers.forEach(seller => {
                    const row = document.createElement('tr');
                    
                    const isPaid = seller.status === 'Paid';
                    const statusBadge = isPaid 
                        ? '<span class="badge-status paid"><i class="fas fa-check-circle"></i> Đã trả</span>'
                        : '<span class="badge-status unpaid"><i class="fas fa-clock"></i> Chờ thanh toán</span>';
                        
                    const actionBtn = isPaid
                        ? `<button class="btn-action-payout unpay" onclick="togglePayout('seller', '${seller.id}', false)">Đánh dấu chưa trả</button>`
                        : `<button class="btn-action-payout pay" onclick="togglePayout('seller', '${seller.id}', true)">Xác nhận đã trả</button>`;

                    const diffVal = seller.shipping_diff;
                    const diffText = (diffVal >= 0 ? '+' : '') + formatCurrency(diffVal);
                    const diffClass = diffVal >= 0 ? 'text-success' : 'text-danger';

                    row.innerHTML = `
                        <td><strong>${seller.name}</strong></td>
                        <td>${seller.owner}</td>
                        <td class="text-center"><strong>${seller.orders_count}</strong></td>
                        <td class="text-end">${formatCurrency(seller.food_revenue)}</td>
                        <td class="text-end" style="color:#6366f1; font-weight:600;">${seller.commission_rate}%</td>
                        <td class="text-end" style="color:#6366f1; font-weight:600;">-${formatCurrency(seller.commission_amount)}</td>
                        <td class="text-end">${formatCurrency(seller.shipping_fee)}</td>
                        <td class="text-end">${formatCurrency(seller.carrier_cost)}</td>
                        <td class="text-end ${diffClass}">${diffText}</td>
                        <td class="text-end text-success fw-bold">${formatCurrency(seller.total_merchant_owed)}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center">${actionBtn}</td>
                    `;
                    sellersBody.appendChild(row);
                });
            }

            // Render Carriers
            const carriersBody = document.getElementById('carriers-table-body');
            carriersBody.innerHTML = '';

            if (data.carriers.length === 0) {
                carriersBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle me-1"></i> Không có dữ liệu giao hàng của hãng vận chuyển.
                        </td>
                    </tr>
                `;
            } else {
                data.carriers.forEach(carrier => {
                    const row = document.createElement('tr');
                    
                    const isPaid = carrier.status === 'Paid';
                    const statusBadge = isPaid 
                        ? '<span class="badge-status paid"><i class="fas fa-check-circle"></i> Đã trả</span>'
                        : '<span class="badge-status unpaid"><i class="fas fa-clock"></i> Chờ thanh toán</span>';
                        
                    const actionBtn = isPaid
                        ? `<button class="btn-action-payout unpay" onclick="togglePayout('carrier', '${carrier.id}', false)">Đánh dấu chưa trả</button>`
                        : `<button class="btn-action-payout pay" onclick="togglePayout('carrier', '${carrier.id}', true)">Xác nhận đã trả</button>`;

                    row.innerHTML = `
                        <td><strong>${carrier.name}</strong></td>
                        <td class="text-center"><strong>${carrier.orders_count}</strong></td>
                        <td class="text-end text-success fw-bold">${formatCurrency(carrier.carrier_cost)}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center">${actionBtn}</td>
                    `;
                    carriersBody.appendChild(row);
                });
            }

            // Keep search filter applied if any query is written
            filterSellersTable();
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi tải dữ liệu',
                text: 'Không thể kết nối đến máy chủ.',
                confirmButtonColor: '#a855f7'
            });
        });
    }

    // Toggle Payout status via API
    function togglePayout(type, id, status) {
        const month = document.getElementById('select-month').value;
        const typeLabel = type === 'seller' ? 'cửa hàng' : 'đơn vị vận chuyển';
        const actionLabel = status ? 'đã thanh toán đối soát' : 'chưa thanh toán';

        Swal.fire({
            title: 'Xác nhận thay đổi?',
            text: `Bạn có chắc muốn đánh dấu ${typeLabel} này là ${actionLabel} cho kỳ ${month}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#a855f7',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/api/v1/admin/reconciliation/payout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        type: type,
                        id: id,
                        month: month,
                        status: status
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadReconciliationData();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: res.message,
                            confirmButtonColor: '#a855f7'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối',
                        text: 'Không thể kết nối để cập nhật trạng thái.',
                        confirmButtonColor: '#a855f7'
                    });
                });
            }
        });
    }

    // Format currency helper
    function formatCurrency(val) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(val)) + ' đ';
    }

    // On Load
    document.addEventListener('DOMContentLoaded', () => {
        initMonthSelector();
        loadReconciliationData();
    });
</script>
@endsection

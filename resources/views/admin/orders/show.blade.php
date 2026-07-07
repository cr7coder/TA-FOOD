@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<style>
    .order-detail-wrapper {
        display: flex;
        justify-content: center;
        padding: 2rem 1rem;
        background-color: transparent;
        min-height: calc(100vh - 60px);
    }
    .order-modal {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 1100px;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
        margin: 0 auto;
    }
    .modal-header-purple {
        background: #A855F7;
        padding: 24px;
        color: white;
        position: relative;
    }
    .modal-header-purple h3 {
        margin: 0 0 8px 0;
        font-size: 20px;
        font-weight: 600;
    }
    .header-meta {
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.9;
    }
    .close-btn {
        position: absolute;
        top: 24px;
        right: 24px;
        width: 32px;
        height: 32px;
        background: rgba(255,255,255,0.2);
        border: none;
        border-radius: 8px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .close-btn:hover {
        background: rgba(255,255,255,0.3);
        color: white;
    }
    .modal-body-content {
        padding: 24px;
    }
    .info-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    .info-card {
        background: #F8FAFC;
        border-radius: 8px;
        padding: 16px;
        border: 1px solid #e2e8f0;
    }
    .info-card.store-info {
        background: #FAF5FF;
        border-color: #e9d5ff;
    }
    .info-card h6 {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 0;
        margin-bottom: 12px;
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
    }
    .info-card p {
        margin: 0 0 8px 0;
        font-size: 14px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-card p:last-child {
        margin-bottom: 0;
    }
    .info-card p i {
        color: #64748b;
        width: 16px;
        text-align: center;
    }
    .section-heading {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #1e293b;
    }
    .table-custom {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }
    .table-custom th {
        background: #F8FAFC;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        padding: 12px;
        text-align: left;
    }
    .table-custom td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
        vertical-align: middle;
        color: #1e293b;
    }
    .item-img {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        object-fit: cover;
        margin-right: 12px;
    }
    .payment-box {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
        background: #fafafa;
    }
    .payment-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
        color: #475569;
    }
    .payment-row.total {
        border-top: 1px solid #e2e8f0;
        margin-top: 12px;
        padding-top: 12px;
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    .payment-row.total .val {
        color: #10B981;
        font-size: 18px;
    }
    .note-box {
        background: #FEF9C3;
        border: 1px solid #fef08a;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
    }
    .note-box h6 {
        margin: 0 0 8px 0;
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
    }
    .note-box p {
        margin: 0;
        font-size: 14px;
        color: #475569;
    }
    .status-update-box {
        border: 1px solid #e9d5ff;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
        background: #faf5ff;
    }
    .status-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .status-pill {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        background: #f1f5f9;
        color: #475569;
        border: none;
        transition: all 0.2s;
    }
    .status-pill:hover:not(:disabled):not(.active) {
        background: #e2e8f0;
    }
    .status-pill.active {
        background: #A855F7;
        color: white;
    }
    .status-pill:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .modal-footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
    }
    .btn-print {
        background: #475569;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-print:hover { background: #334155; color: white; text-decoration: none; }
    .btn-close-modal {
        background: #A855F7;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-close-modal:hover { background: #9333ea; color: white; text-decoration: none; }
    
    @media (max-width: 768px) {
        .info-cards { grid-template-columns: 1fr; }
    }
</style>

<div class="order-detail-wrapper" id="order-detail-content">
    <div class="text-center py-5" style="width: 100%;">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted">Đang tải thông tin đơn hàng...</p>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const orderId = {{ $id }};
    const detailContainer = document.getElementById('order-detail-content');
    let currentOrderData = null;

    async function loadDetail() {
        try {
            const res = await fetch(`/api/v1/admin/orders/${orderId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await res.json();
            if (result.success) {
                renderDetail(result.data, result.carriers);
            } else {
                detailContainer.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
            }
        } catch (err) {
            detailContainer.innerHTML = `<div class="alert alert-danger">Không thể tải dữ liệu</div>`;
        }
    }

    function renderDetail(data, carriers) {
        currentOrderData = data;
        let itemsHtml = data.Items.map(item => `
            <tr>
                <td>
                    <div style="display:flex; align-items:center;">
                        ${item.HinhAnh ? `<img src="/images/${item.HinhAnh}" class="item-img" alt="${item.TenMonAn}">` : ''}
                        <span style="font-weight:500;">${item.TenMonAn}</span>
                    </div>
                </td>
                <td style="text-align:center">x${item.SoLuong}</td>
                <td style="text-align:right">${item.Gia}</td>
                <td style="text-align:right; font-weight:700;">${item.ThanhTien}</td>
            </tr>
        `).join('');

        const statusText = data.TrangThai;

        detailContainer.innerHTML = `
            <div class="order-modal">
                <div class="modal-header-purple">
                    <h3>Chi tiết đơn hàng #${data.OrderCode}</h3>
                    <div class="header-meta" style="font-size: 14px; display: flex; align-items: center; gap: 12px;">
                        <span><i class="fas fa-info-circle"></i> Trạng thái: <strong style="text-decoration: underline;">${statusText}</strong></span>
                        <span>&bull;</span>
                        <span><i class="far fa-clock"></i> Ngày đặt đơn: <strong>${data.TimelineTime.Created || data.ThoiGianRaw}</strong></span>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="close-btn" title="Quay lại">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                
                <div class="modal-body-content">
                    ${data.ly_do_huy ? `
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert" style="border-radius: 8px; font-size: 14px; padding: 10px 15px;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 16px; margin-right: 8px;"></i>
                        <div>
                            <strong>Lý do hủy đơn:</strong> ${data.ly_do_huy} ${data.nguoi_huy ? `(Hủy bởi: ${
                                data.nguoi_huy === 'admin' ? 'Quản trị viên' :
                                data.nguoi_huy === 'customer' ? 'Khách hàng' :
                                data.nguoi_huy === 'system' ? 'Hệ thống' : 'Nhà hàng'
                            })` : ''}
                        </div>
                    </div>
                    ` : ''}
                    <div class="info-cards">
                        <div class="info-card">
                            <h6><i class="far fa-user" style="color:#3B82F6"></i> Thông tin khách hàng</h6>
                            <p style="color:#1e293b; font-weight:500;">${data.TenKhachHang}</p>
                            <p><i class="fas fa-phone-alt"></i> ${data.SoDienThoai}</p>
                            <p><i class="far fa-envelope"></i> ${data.Email}</p>
                            <p><i class="fas fa-map-marker-alt"></i> ${data.DiaChi}</p>
                        </div>
                        <div class="info-card store-info">
                            <h6 style="color:#A855F7"><i class="fas fa-store" style="color:#A855F7"></i> Thông tin cửa hàng</h6>
                            <p style="color:#1e293b; font-weight:500;">${data.TenNhaHang}</p>
                            <p><i class="fas fa-map-marker-alt"></i> ${data.DiaChiNhaHang}</p>
                            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #e9d5ff;">
                                <h6 style="color:#A855F7; margin-bottom: 8px; font-size: 14px;">
                                    <i class="fas fa-shipping-fast" style="color:#A855F7"></i> Đối tác vận chuyển 
                                    <small style="color: #6b7280; font-size: 11px; font-weight: normal; margin-left: 4px;">(Can thiệp khẩn cấp)</small>
                                </h6>
                                <select id="carrier-selector" class="form-control form-control-sm" onchange="changeCarrier(this.value)" ${data.TrangThai === 'Chờ xác nhận' || data.TrangThai === 'Hoàn thành' || data.TrangThai === 'Đã hủy' ? 'disabled style="opacity: 0.65; cursor: not-allowed; border-radius: 6px; font-weight: 500; height: 36px; padding: 2px 8px;"' : 'style="border-radius: 6px; font-weight: 500; height: 36px; padding: 2px 8px;"'}>
                                    <option value="">-- Chưa chọn đối tác --</option>
                                    ${(carriers || []).map(c => `
                                        <option value="${c.id}" ${data.MaDoiTacVanChuyen == c.id ? 'selected' : ''}>${c.ten_doi_tac}</option>
                                    `).join('')}
                                </select>
                            </div>

                            ${data.MaDoiTacVanChuyen ? `
                            <div style="margin-top: 10px; padding: 10px; background: rgba(255,255,255,0.6); border-radius: 6px; border: 1px dashed #d8b4fe; font-size: 12px; color: #581c87;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span>📍 Khoảng cách giao (ước tính):</span>
                                    <span style="font-weight:700; color:#3b82f6;">${data.Distance} km</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span>💸 Phí ship thu từ Khách:</span>
                                    <span style="font-weight:700; color:#16a34a;">${data.PhiShip}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span>📦 Phí thuê Shipper (${data.TenDoiTacVanChuyen}):</span>
                                    <span style="font-weight:700; color:#dc2626;">-${data.CarrierCostFormat}</span>
                                </div>
                                <div style="font-size:10px; color:#701a75; padding-left:18px; margin-bottom:6px;">
                                    (Cố định: ${data.CarrierBaseFeeFormat} + Km: ${data.CarrierKmFeeFormat}/km)
                                </div>
                                <hr style="border-top:1px dashed #d8b4fe; margin:6px 0;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                    <span style="font-weight:700;">Chênh lệch Ship (Ước tính):</span>
                                    <span style="font-weight:700; color:${data.PhiShipRaw >= data.CarrierCost ? '#16a34a' : '#dc2626'}; text-align:right;">
                                        ${data.PhiShipRaw >= data.CarrierCost ? '+' : ''}${(data.PhiShipRaw - data.CarrierCost).toLocaleString('vi-VN')} đ
                                        <small style="font-size:10px; display:block; font-weight:normal; color:#701a75;">
                                            (${data.PhiShipRaw >= data.CarrierCost ? 'Lãi vận chuyển' : 'Bù lỗ ship'})
                                        </small>
                                    </span>
                                </div>
                                <hr style="border-top:1px dashed #d8b4fe; margin:6px 0;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span>🍳 Tiền món ăn (Doanh thu đồ ăn):</span>
                                    <span style="font-weight:700; color:#111827;">${data.FoodRevenueFormat}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span>🚚 Chênh lệch ship (${data.PhiShipRaw >= data.CarrierCost ? 'Lãi' : 'Lỗ'}):</span>
                                    <span style="font-weight:700; color:${data.PhiShipRaw >= data.CarrierCost ? '#16a34a' : '#dc2626'};">
                                        ${data.PhiShipRaw >= data.CarrierCost ? '+' : ''}${(data.PhiShipRaw - data.CarrierCost).toLocaleString('vi-VN')} đ
                                    </span>
                                </div>
                                <div style="margin-top: 8px; padding: 8px; background: #eff6ff; border-radius: 4px; border: 1px solid #bfdbfe; color: #1e40af;">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <span style="font-weight:700;"><i class="fas fa-hand-holding-usd" style="color:#2563eb; margin-right:4px;"></i> Tổng tiền Quán nhận:</span>
                                        <span style="font-weight:800; color:#2563eb; font-size:13px;">${data.TotalMerchantOwedFormat}</span>
                                    </div>
                                    <div style="font-size: 10px; color:#1e40af; margin-top: 2px;">
                                        (Thanh toán đối soát tự động khi đơn hàng Hoàn thành)
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    <div class="section-heading"><i class="fas fa-box-open" style="color:#64748b"></i> Món ăn đã đặt</div>
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Món</th>
                                <th style="text-align:center">SL</th>
                                <th style="text-align:right">Đơn giá</th>
                                <th style="text-align:right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>

                    <div class="payment-box">
                        <div class="section-heading" style="margin-bottom: 12px;"><i class="far fa-credit-card" style="color:#64748b"></i> Thông tin thanh toán</div>
                        <div class="payment-row"><span>Tạm tính:</span><span>${data.TamTinh}</span></div>
                        <div class="payment-row"><span>Phí vận chuyển:</span><span>${data.PhiShip}</span></div>
                        <div class="payment-row"><span>Giảm giá:</span><span style="color:#EF4444">${data.GiamGia}</span></div>
                        
                        <div class="payment-row total">
                            <span>Tổng cộng:</span>
                            <span class="val">${data.TongTien}</span>
                        </div>
                        
                        <div class="payment-row" style="margin-top: 16px;">
                            <span>Phương thức:</span>
                            <span style="font-weight:600; color:#1e293b;">${data.PhuongThucThanhToan}</span>
                        </div>
                        <div class="payment-row">
                            <span>Trạng thái thanh toán:</span>
                            <span style="font-weight: 600; color: ${
                                data.ThanhToan === 'Đã hoàn tiền' ? '#2563eb' :
                                data.ThanhToan === 'Thất bại' ? '#dc2626' :
                                data.ThanhToan === 'Đã thanh toán' ? '#10b981' :
                                data.ThanhToan === 'Chờ xác nhận' ? '#d97706' : '#64748b'
                            };">${data.ThanhToan || 'Chưa thanh toán'}</span>
                        </div>

                        ${data.MinhChungThanhToan ? `
                        <div class="payment-row" style="margin-top: 14px; flex-direction: column; align-items: flex-start; gap: 8px; border-top: 1px dashed #e2e8f0; padding-top: 12px;">
                            <span style="font-weight:600; color:#475569;">📸 Minh chứng thanh toán:</span>
                            <div style="background:#fff; padding:6px; border-radius:8px; border:1px solid #e2e8f0; display:inline-block; max-width:100%;">
                                <a href="${data.MinhChungThanhToan}" target="_blank" title="Click để xem ảnh gốc">
                                    <img src="${data.MinhChungThanhToan}" alt="Minh chứng thanh toán" 
                                         style="max-width: 100%; max-height: 180px; border-radius: 4px; object-fit: contain; cursor: pointer;" />
                                </a>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <div class="note-box">
                        <h6>Ghi chú:</h6>
                        <p>${data.GhiChu || 'Không có ghi chú'}</p>
                    </div>

                    ${generatePaymentConfirmBanner(data)}

                    <div class="status-update-box">
                        <h6 style="margin: 0 0 12px 0; font-size:15px; font-weight:700; color:#1e293b;">Cập nhật trạng thái đơn hàng:</h6>
                        <div class="status-pills">
                            ${generateStatusPills(data.TrangThai)}
                        </div>
                    </div>

                    <div class="modal-footer-actions">
                        <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> In hóa đơn</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn-close-modal"><i class="fas fa-times"></i> Đóng</a>
                    </div>
                </div>
            </div>
        `;
    }

    function generatePaymentConfirmBanner(data) {
        // Hiển thị banner xác nhận tiền khi đơn đang chờ xác nhận CK
        const isPendingPayment = data.TrangThai === 'Chờ xác nhận' && data.ThanhToan === 'Chờ xác nhận';
        if (!isPendingPayment) return '';
        
        let proofHtml = '';
        if (data.MinhChungThanhToan) {
            proofHtml = `
                <div class="payment-proof-preview" style="margin-top: 14px; width: 100%;">
                    <span style="font-weight: 700; font-size:13.5px; color:#92400e; display:flex; align-items:center; gap:6px; margin-bottom:8px;">
                        📸 Hình ảnh minh chứng chuyển khoản:
                    </span>
                    <div style="background:#fff; padding:8px; border-radius:10px; border:1px solid #fde68a; display:inline-block;">
                        <a href="${data.MinhChungThanhToan}" target="_blank" title="Click để xem ảnh kích thước đầy đủ" style="display:block;">
                            <img src="${data.MinhChungThanhToan}" alt="Minh chứng chuyển khoản" 
                                 style="max-width: 100%; max-height: 300px; border-radius: 6px; cursor: pointer; transition: transform 0.2s; object-fit: contain;"
                                 onmouseover="this.style.transform='scale(1.02)'"
                                 onmouseout="this.style.transform='scale(1)'" />
                        </a>
                    </div>
                </div>
            `;
        } else {
            proofHtml = `
                <div style="margin-top: 10px; color:#b45309; font-size:12.5px; font-style:italic;">
                    ⚠️ Khách hàng chưa tải lên hình ảnh minh chứng.
                </div>
            `;
        }

        return `
            <div style="background: linear-gradient(135deg, #fef3c7, #fffbeb); border: 1.5px solid #f59e0b;
                        border-radius: 12px; padding: 20px; margin-bottom: 24px;
                        display: flex; flex-direction: column; gap: 16px; box-shadow: 0 4px 12px rgba(245,158,11,0.08);">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; border-bottom: 1px dashed #fcd34d; padding-bottom: 14px;">
                    <div style="display:flex; align-items:center; gap: 12px;">
                        <span style="font-size: 32px;">💳</span>
                        <div>
                            <div style="font-weight: 800; color: #92400e; font-size: 15px;">Yêu cầu xác nhận thanh toán online</div>
                            <div style="color: #78350f; font-size: 13px; margin-top: 2px;">
                                Vui lòng đối soát tài khoản ngân hàng TCB và hóa đơn của khách hàng.
                            </div>
                        </div>
                    </div>
                    <button onclick="confirmPaymentAdmin()" style="background: linear-gradient(135deg, #10b981, #059669);
                        color: white; border: none; border-radius: 8px; padding: 10px 20px;
                        font-weight: 700; font-size: 13.5px; cursor: pointer; white-space: nowrap;
                        box-shadow: 0 4px 12px rgba(16,185,129,0.3); transition: all 0.2s;"
                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(16,185,129,0.45)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16,185,129,0.3)';">
                        ✅ Xác nhận đã nhận tiền
                    </button>
                </div>
                ${proofHtml}
            </div>
        `;
    }

    function generateStatusPills(currentStatus) {
        const statuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đang chuẩn bị', 'Đang giao', 'Hoàn thành', 'Đã hủy'];
        
        return statuses.map(s => {
            const isActive = s === currentStatus;
            
            let isDisabled = true;
            if (isActive) {
                isDisabled = false;
            } else if (currentStatus === 'Chờ xác nhận' && s === 'Đã hủy') {
                // Admin chỉ có quyền hủy đơn khẩn cấp, không có quyền bấm xác nhận thay Nhà hàng (Seller)
                isDisabled = false;
            } else if (currentStatus === 'Đã xác nhận' && s === 'Đã hủy') {
                isDisabled = false;
            }
            
            return `<button class="status-pill ${isActive ? 'active' : ''}" ${isDisabled ? 'disabled style="opacity: 0.55; cursor: not-allowed;"' : ''} onclick="updateStatus('${s}')">${s}</button>`;
        }).join('');
    }

    async function confirmPaymentAdmin() {
        let siblingHtml = '';
        if (currentOrderData && currentOrderData.RelatedOrders && currentOrderData.RelatedOrders.length > 0) {
            siblingHtml = `
                <div class="alert alert-warning text-start mt-3" style="font-size: 13.5px; border-radius: 8px; border-left: 4px solid #f59e0b; background: #fffbeb; padding: 15px; border-top: 1px solid #fef3c7; border-right: 1px solid #fef3c7; border-bottom: 1px solid #fef3c7; text-align: left;">
                    <strong style="color: #b45309;"><i class="fas fa-exclamation-triangle"></i> Phát hiện đơn hàng gộp (cùng nội dung chuyển khoản):</strong>
                    <p class="mb-2 mt-1" style="color: #78350f; font-weight: 500;">Xác nhận này sẽ tự động duyệt thanh toán cho <strong>tất cả</strong> các đơn hàng con sau:</p>
                    <ul class="mb-0" style="padding-left: 20px; color: #78350f; font-size: 13px;">
                        <li><strong>Đơn hiện tại: #${currentOrderData.OrderCode}</strong> - ${currentOrderData.TenNhaHang} (${currentOrderData.TongTien})</li>
                        ${currentOrderData.RelatedOrders.map(sib => `
                            <li class="mt-1"><strong>Đơn liên kết: #${sib.order_code}</strong> - ${sib.store_name} (${sib.total_amount})</li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }

        const result = await Swal.fire({
            title: 'Xác nhận đã nhận tiền?',
            html: `
                <div>
                    Bạn xác nhận đã nhận được tiền chuyển khoản cho đơn hàng này?<br>
                    <small style="color:#6b7280;">Hành động này sẽ chuyển đơn sang <strong>Đã xác nhận</strong> và thông báo đến khách hàng & nhà hàng.</small>
                    ${siblingHtml}
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '✅ Xác nhận nhận tiền',
            cancelButtonText: 'Hủy'
        });
        if (!result.isConfirmed) return;

        try {
            const res = await fetch(`/api/v1/admin/orders/${orderId}/confirm-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await res.json();
            if (data.success) {
                await Swal.fire({ title: 'Thành công!', text: data.message, icon: 'success', timer: 2000, showConfirmButton: false });
                loadDetail();
            } else {
                Swal.fire({ title: 'Lỗi', text: data.message, icon: 'error' });
            }
        } catch (err) {
            Swal.fire({ title: 'Lỗi kết nối', text: 'Không thể kết nối đến server.', icon: 'error' });
        }
    }

    async function updateStatus(newStatus) {
        let finalReason = null;
        
        if (newStatus === 'Đã hủy') {
            const { value: cancelReason } = await Swal.fire({
                title: 'Xác nhận hủy đơn hàng?',
                text: 'Vui lòng chọn hoặc nhập lý do hủy đơn hàng này:',
                input: 'select',
                inputOptions: {
                    'Hết món / Quán hết nguyên liệu': 'Hết món / Quán hết nguyên liệu',
                    'Quán quá tải / Không chuẩn bị kịp': 'Quán quá tải / Không chuẩn bị kịp',
                    'Khách hàng yêu cầu hủy': 'Khách hàng yêu cầu hủy',
                    'Không liên hệ được shipper': 'Không liên hệ được shipper',
                    'Khác': 'Lý do khác (Nhập ở bên dưới)'
                },
                inputPlaceholder: '-- Chọn lý do hủy đơn --',
                showCancelButton: true,
                cancelButtonText: 'Đóng',
                confirmButtonText: 'Xác nhận hủy',
                confirmButtonColor: '#dc2626',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Bạn cần chọn một lý do!';
                    }
                }
            });

            if (!cancelReason) return;

            finalReason = cancelReason;
            if (cancelReason === 'Khác') {
                const { value: customReason } = await Swal.fire({
                    title: 'Nhập lý do hủy khác',
                    input: 'text',
                    inputPlaceholder: 'Nhập lý do chi tiết...',
                    showCancelButton: true,
                    cancelButtonText: 'Quay lại',
                    confirmButtonText: 'Xác nhận',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Vui lòng nhập lý do hủy!';
                        }
                    }
                });
                if (!customReason) return;
                finalReason = customReason;
            }
        } else {
            const result = await Swal.fire({
                title: 'Cập nhật trạng thái?',
                text: `Xác nhận chuyển trạng thái đơn hàng sang "${newStatus}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#A855F7',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy'
            });
            if (!result.isConfirmed) return;
        }

        try {
            const res = await fetch(`/api/v1/admin/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus, reason: finalReason })
            });
            const r = await res.json();
            if (r.success) { await Swal.fire({ title: 'Thành công!', icon: 'success', timer: 1200, showConfirmButton: false }); loadDetail(); }
            else Swal.fire({ title: 'Lỗi', text: r.message || 'Lỗi không xác định', icon: 'error' });
        } catch (err) { Swal.fire({ title: 'Lỗi kết nối', icon: 'error' }); }
    }

    async function changeCarrier(carrierId) {
        try {
            const res = await fetch(`/api/v1/admin/orders/${orderId}/carrier`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ carrier_id: carrierId || null })
            });
            const result = await res.json();
            if (result.success) {
                Swal.fire({
                    title: 'Thành công!',
                    text: result.message || 'Cập nhật đối tác vận chuyển thành công!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadDetail();
            } else {
                Swal.fire({
                    title: 'Thất bại!',
                    text: result.message || 'Không thể cập nhật đối tác vận chuyển.',
                    icon: 'error'
                });
            }
        } catch (err) {
            console.error('Error changing carrier:', err);
            Swal.fire({
                title: 'Lỗi!',
                text: 'Có lỗi xảy ra khi thực hiện cập nhật.',
                icon: 'error'
            });
        }
    }
    
    loadDetail();
</script>
@endsection

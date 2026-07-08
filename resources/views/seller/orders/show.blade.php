@extends('seller.layouts.app')
@section('title', 'Chi tiết đơn hàng')

@push('styles')
    <link href="{{ asset('css/seller-orders.css') }}" rel="stylesheet">
    <style>
        .order-detail-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1rem;
            color: #1a202c;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 1.5rem;
        }
        .info-group {
            margin-bottom: 1rem;
        }
        .info-label {
            font-size: 13px;
            color: #718096;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 15px;
            font-weight: 500;
            color: #1a202c;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .info-value i { color: #a0aec0; margin-top: 4px; }

        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th {
            color: #718096;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 0;
            border-bottom: 1px solid #edf2f7;
            text-align: left;
        }
        .items-table td {
            padding: 12px 0;
            border-bottom: 1px solid #edf2f7;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            font-size: 14px;
            color: #4a5568;
        }
        .summary-total {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 700;
            color: #3182ce;
        }

        .timeline {
            margin-top: 2rem;
            position: relative;
            padding-left: 24px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 5px;
            bottom: 5px;
            width: 2px;
            background: #edf2f7;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: -21px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #cbd5e0;
            border: 2px solid white;
        }
        .timeline-item.completed::after { background: #38a169; }
        .timeline-item.active::after { background: #3182ce; }
        .timeline-item.cancelled::after { background: #e53e3e; }
        
        .timeline-content { font-size: 14px; }
        .timeline-time { font-size: 12px; color: #a0aec0; }
        .top-meta { display: flex; align-items: center; gap: 15px; margin-bottom: 1.5rem; }

        @media (max-width: 992px) {
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
<div class="orders-container">
    <div class="orders-header mb-2">
        <a href="{{ route('seller.orders.index') }}" class="btn-detail p-0" style="border:none; background:none; text-decoration:none; color:#4a5568;">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
    
    <div id="order-detail-content">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Đang tải thông tin đơn hàng...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const orderId = {{ $id }};
    const detailContainer = document.getElementById('order-detail-content');

    async function loadDetail() {
        try {
            const res = await fetch(`/api/v1/seller/orders/${orderId}`, {
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
        const statusConfig = getStatusConfig(data.TrangThai);
        let itemsHtml = data.Items.map(item => `
            <tr>
                <td><div class="fw-bold">${item.TenMonAn}</div></td>
                <td class="text-center">${item.SoLuong}</td>
                <td class="text-end">${item.Gia}</td>
                <td class="text-end fw-bold">${item.ThanhTien}</td>
            </tr>
        `).join('');

        let actionBtns = '';
        if (data.TrangThai === 'Chờ xác nhận') {
            actionBtns = `<button onclick="updateStatus('Đã xác nhận')" class="btn btn-primary w-100 mb-2">Xác nhận đơn hàng</button>
                          <button onclick="updateStatus('Đã hủy')" class="btn btn-outline-danger w-100">Hủy đơn</button>`;
        } else if (data.TrangThai === 'Đã xác nhận') {
            actionBtns = `<button onclick="updateStatus('Đang chuẩn bị')" class="btn btn-info w-100 mb-2 text-white">Bắt đầu chế biến</button>`;
        } else if (data.TrangThai === 'Đang chuẩn bị') {
            actionBtns = `<button onclick="updateStatus('Đang giao')" class="btn btn-warning w-100 mb-2">Bắt đầu giao hàng</button>`;
        } else if (data.TrangThai === 'Đang giao') {
            actionBtns = `<button onclick="updateStatus('Hoàn thành')" class="btn btn-success w-100 mb-2">Đã giao thành công</button>`;
        }

        const timeline = buildTimeline(data);

        detailContainer.innerHTML = `
            <h4 class="mb-3 fw-bold">Chi tiết đơn hàng #${data.OrderCode}</h4>
            <div class="top-meta">
                <span class="badge-status ${statusConfig.class}" style="font-size: 14px;">
                    <i class="fas ${statusConfig.icon}"></i> ${data.TrangThai}
                </span>
                <span class="text-muted"><i class="far fa-clock"></i> ${data.ThoiGianRaw}</span>
            </div>

            ${data.ly_do_huy ? `
            <div class="alert alert-danger d-flex align-items-center mt-3 mb-2" role="alert" style="border-radius: 8px; font-size: 14px; padding: 10px 15px;">
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

            <div class="detail-grid">
                <div>
                    <div class="order-detail-card">
                        <div class="section-title"><i class="far fa-user"></i> Thông tin khách hàng</div>
                        <div class="info-group"><div class="info-label">Tên khách hàng</div><div class="info-value">${data.TenKhachHang}</div></div>
                        <div class="info-group"><div class="info-label">Số điện thoại</div><div class="info-value"><i class="fas fa-phone-alt"></i> ${data.SoDienThoai}</div></div>
                        <div class="info-group"><div class="info-label">Địa chỉ giao hàng</div><div class="info-value"><i class="fas fa-map-marker-alt"></i> ${data.DiaChi}</div></div>
                        
                        <div class="info-group mt-3 pt-3 border-top">
                            <div class="info-label font-weight-bold"><i class="fas fa-shipping-fast text-success mr-1"></i> Đối tác vận chuyển</div>
                            <div class="info-value">
                                <select id="carrier-selector" class="form-select form-select-sm mt-1" onchange="changeCarrier(this.value)" ${data.TrangThai === 'Hoàn thành' || data.TrangThai === 'Đã hủy' ? 'disabled style="opacity: 0.65; cursor: not-allowed; border-radius: 8px; font-weight: 500; height: 38px;"' : 'style="border-radius: 8px; font-weight: 500; height: 38px;"'}>
                                    <option value="">-- Chưa chọn đối tác --</option>
                                    ${(carriers || []).map(c => `
                                        <option value="${c.id}" ${data.MaDoiTacVanChuyen == c.id ? 'selected' : ''}>${c.ten_doi_tac}</option>
                                    `).join('')}
                                </select>
                            </div>
                        </div>

                        ${data.MaDoiTacVanChuyen ? `
                        <div class="mt-3 p-3" style="background: #f8fafc; border-radius: 8px; border: 1px dashed #e2e8f0; font-size: 13px; color: #334155;">
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-map-marker-alt text-primary mr-1"></i> Khoảng cách giao (ước tính):</span>
                                <span class="fw-bold text-primary">${data.Distance} km</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-coins text-warning mr-1"></i> Phí ship thu của Khách:</span>
                                <span class="fw-bold text-success">${data.PhiShip}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-receipt text-danger mr-1"></i> Phí thuê Shipper (${data.TenDoiTacVanChuyen}):</span>
                                <span class="fw-bold text-danger">-${data.CarrierCostFormat}</span>
                            </div>
                            <div style="font-size: 11px; color: #64748b; padding-left: 18px; margin-bottom: 8px;">
                                (Phí cố định: ${data.CarrierBaseFeeFormat} + Phí km: ${data.CarrierKmFeeFormat}/km)
                            </div>
                            <hr class="my-2" style="border-top: 1px dashed #cbd5e1; margin: 8px 0;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Chênh lệch Dòng tiền Ship:</span>
                                <span class="fw-bold ${data.PhiShipRaw >= data.CarrierCost ? 'text-success' : 'text-danger'}" style="text-align: right;">
                                    ${data.PhiShipRaw >= data.CarrierCost ? '+' : ''}${(data.PhiShipRaw - data.CarrierCost).toLocaleString('vi-VN')} đ
                                    <small style="font-size: 11px; display: block; font-weight: normal; color: #64748b;">
                                        (${data.PhiShipRaw >= data.CarrierCost ? 'Lãi vận chuyển' : 'Bù lỗ ship'})
                                    </small>
                                </span>
                            </div>
                            <hr class="my-2" style="border-top: 1px dashed #cbd5e1; margin: 8px 0;">
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-utensils text-info mr-1"></i> Tiền món ăn (Doanh thu đồ ăn):</span>
                                <span class="fw-bold text-dark">${data.FoodRevenueFormat}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-percentage text-danger mr-1"></i> Chiết khấu hoa hồng (${data.CommissionRate}%):</span>
                                <span class="fw-bold text-danger">-${data.CommissionAmountFormat}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-shipping-fast text-secondary mr-1"></i> Chênh lệch ship (${data.PhiShipRaw >= data.CarrierCost ? 'Lãi' : 'Lỗ'}):</span>
                                <span class="fw-bold ${data.PhiShipRaw >= data.CarrierCost ? 'text-success' : 'text-danger'}">
                                    ${data.PhiShipRaw >= data.CarrierCost ? '+' : ''}${(data.PhiShipRaw - data.CarrierCost).toLocaleString('vi-VN')} đ
                                </span>
                            </div>
                            <div class="p-2 mt-2" style="background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe;">
                                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 5px;">
                                    <span class="fw-bold" style="color: #1e40af; font-size: 12.5px;"><i class="fas fa-hand-holding-usd mr-1" style="color: #2563eb;"></i> Tổng nhận từ Admin:</span>
                                    <span class="fw-bold" style="color: #2563eb; font-size: 14.5px; white-space: nowrap;">${data.TotalMerchantOwedFormat}</span>
                                </div>
                                <div style="font-size: 11px; color: #1e40af; margin-top: 2px;">
                                    (Hệ thống tự đối soát & thanh toán cho quán sau khi hoàn thành)
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    <div class="order-detail-card">
                        <div class="section-title"><i class="far fa-credit-card"></i> Thông tin thanh toán</div>
                        <div class="info-group">
                            <div class="info-label">Phương thức thanh toán</div>
                            <div class="info-value">
                                ${data.ThanhToanInfo && data.ThanhToanInfo.IsOnline
                                    ? '<span style="display:inline-flex;align-items:center;gap:6px;background:#fff5f6;border:1px solid #fce4e7;border-radius:8px;padding:3px 10px;font-size:13px;font-weight:600;color:#e31837;"><i class="fas fa-qrcode mr-1"></i> VietQR - Chuyển khoản ngân hàng</span>'
                                    : (data.ThanhToanInfo ? data.ThanhToanInfo.PhuongThuc : data.PhuongThucThanhToan)
                                }
                            </div>
                        </div>
                        ${data.ThanhToanInfo && data.ThanhToanInfo.IsOnline ? `
                        <div class="info-group">
                            <div class="info-label">Trạng thái thanh toán</div>
                            <div class="info-value">
                                ${data.ThanhToanInfo.TrangThai === 'Đã thanh toán'
                                    ? '<span style="background:#d1fae5;color:#065f46;border-radius:20px;padding:3px 12px;font-size:13px;font-weight:600;">✅ Đã thanh toán</span>'
                                    : data.ThanhToanInfo.TrangThai === 'Chờ xác nhận'
                                    ? '<span style="background:#fef3c7;color:#92400e;border-radius:20px;padding:3px 12px;font-size:13px;font-weight:600;">⏳ Chờ xác nhận</span>'
                                    : data.ThanhToanInfo.TrangThai === 'Đã hoàn tiền'
                                    ? '<span style="background:#eff6ff;color:#1e40af;border-radius:20px;padding:3px 12px;font-size:13px;font-weight:600;">↩️ Đã hoàn tiền</span>'
                                    : data.ThanhToanInfo.TrangThai === 'Thất bại'
                                    ? '<span style="background:#fee2e2;color:#991b1b;border-radius:20px;padding:3px 12px;font-size:13px;font-weight:600;">❌ Thất bại (Đã hủy)</span>'
                                    : '<span style="background:#f3f4f6;color:#6b7280;border-radius:20px;padding:3px 12px;font-size:13px;font-weight:600;">💳 Chờ thanh toán</span>'
                                }
                            </div>
                        </div>
                        ${data.ThanhToanInfo.NgayThanhToan ? `
                        <div class="info-group">
                            <div class="info-label">Thời gian xác nhận CK</div>
                            <div class="info-value" style="color:#059669;font-weight:600;">
                                <i class="fas fa-check-circle" style="color:#059669;"></i>
                                ${data.ThanhToanInfo.NgayThanhToan}
                            </div>
                        </div>` : ''}
                        ${data.ThanhToanInfo.OrderCode ? `
                        <div class="info-group">
                            <div class="info-label">Mã nội dung CK</div>
                            <div class="info-value">
                                <code style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:13px;">${data.ThanhToanInfo.OrderCode}</code>
                            </div>
                        </div>` : ''}
                        ` : ''}
                        <div class="info-group"><div class="info-label">Ghi chú</div><div class="info-value text-muted italic">${data.GhiChu || '--'}</div></div>
                    </div>
                    <div class="mt-3">${actionBtns}</div>
                </div>
                <div class="order-detail-card">
                    <div class="section-title"><i class="fas fa-box"></i> Chi tiết món ăn</div>
                    <table class="items-table">
                        <thead><tr><th>Tên món</th><th class="text-center">Số lượng</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr></thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                    <div class="summary-section mt-3">
                        <div class="summary-row"><span>Tạm tính</span><span>${data.TamTinh}</span></div>
                        <div class="summary-row"><span>Phí giao hàng</span><span>${data.PhiShip}</span></div>
                        <div class="summary-row"><span>Giảm giá</span><span class="text-danger">${data.GiamGia}</span></div>
                        <div class="summary-total"><span>Tổng cộng</span><span>${data.TongTien}</span></div>
                    </div>
                    <div class="section-title mt-5"><i class="fas fa-history"></i> Lịch sử đơn hàng</div>
                    <div class="timeline">${timeline}</div>
                </div>
            </div>
        `;
    }

    function buildTimeline(data) {
        const timeline = data.TimelineTime;
        
        if (data.TrangThai === 'Đã hủy') {
            const steps = [
                { label: 'Đơn hàng đã được tạo', status: 'completed', date: timeline.Created }
            ];
            
            if (timeline.Confirmed !== '--') {
                steps.push({ label: 'Đơn hàng đã được xác nhận', status: 'completed', date: timeline.Confirmed });
            }
            if (timeline.Delivering !== '--') {
                steps.push({ label: 'Đơn hàng đang được giao', status: 'completed', date: timeline.Delivering });
            }
            
            steps.push({ 
                label: 'Đơn hàng đã bị hủy', 
                status: 'cancelled', 
                date: timeline.Cancelled 
            });
            
            return steps.map(s => `
                <div class="timeline-item ${s.status}">
                    <div class="timeline-content fw-bold text-danger">${s.label}</div>
                    <div class="timeline-time text-danger">${s.date}</div>
                </div>
            `).join('');
        }
        
        const steps = [
            { label: 'Đơn hàng đã được tạo', status: 'completed', date: timeline.Created },
            { label: 'Đơn hàng đã được xác nhận', status: timeline.Confirmed !== '--' ? 'completed' : 'pending', date: timeline.Confirmed },
            { label: 'Đơn hàng đang được giao', status: timeline.Delivering !== '--' ? 'completed' : 'pending', date: timeline.Delivering },
            { label: 'Đơn hàng đã hoàn thành', status: timeline.Completed !== '--' ? 'completed' : 'pending', date: timeline.Completed }
        ];
        return steps.map(s => `
            <div class="timeline-item ${s.status}">
                <div class="timeline-content fw-bold">${s.label}</div>
                <div class="timeline-time">${s.date}</div>
            </div>
        `).join('');
    }

    async function updateStatus(newStatus) {
        if (newStatus === 'Đã hủy') {
            const { value: finalReason } = await Swal.fire({
                title: 'Xác nhận hủy đơn?',
                html: `
                    <div style="text-align: left; margin-top: 10px;">
                        <label class="form-label text-muted" style="font-size: 14px;">Vui lòng chọn lý do hủy đơn hàng này:</label>
                        <select id="cancel-reason-select" class="form-select mb-3" onchange="document.getElementById('cancel-reason-text').style.display = this.value === 'Khác' ? 'block' : 'none';">
                            <option value="">-- Chọn lý do hủy đơn --</option>
                            <option value="Hết món / Quán hết nguyên liệu">Hết món / Quán hết nguyên liệu</option>
                            <option value="Quán quá tải / Không chuẩn bị kịp">Quán quá tải / Không chuẩn bị kịp</option>
                            <option value="Khách hàng yêu cầu hủy">Khách hàng yêu cầu hủy</option>
                            <option value="Không liên hệ được shipper">Không liên hệ được shipper</option>
                            <option value="Khác">Lý do khác...</option>
                        </select>
                        <textarea id="cancel-reason-text" class="form-control" style="display: none; resize: vertical;" placeholder="Nhập lý do chi tiết..." rows="3"></textarea>
                    </div>
                `,
                showCancelButton: true,
                cancelButtonText: 'Đóng',
                confirmButtonText: 'Xác nhận hủy',
                confirmButtonColor: '#d33',
                preConfirm: () => {
                    const select = document.getElementById('cancel-reason-select').value;
                    const text = document.getElementById('cancel-reason-text').value.trim();
                    if (!select) {
                        Swal.showValidationMessage('Bạn cần chọn một lý do!');
                        return false;
                    }
                    if (select === 'Khác' && !text) {
                        Swal.showValidationMessage('Vui lòng nhập lý do hủy chi tiết!');
                        return false;
                    }
                    return select === 'Khác' ? text : select;
                }
            });

            if (!finalReason) return;

            try {
                const res = await fetch(`/api/v1/seller/orders/${orderId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus, reason: finalReason })
                });
                const result = await res.json();
                if (result.success) {
                    Swal.fire({
                        title: 'Đã hủy!',
                        text: 'Đơn hàng đã được hủy thành công.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadDetail();
                    if (typeof checkOrders === 'function') checkOrders();
                } else {
                    Swal.fire('Lỗi!', result.message, 'error');
                }
            } catch (err) {
                Swal.fire('Lỗi!', 'Có lỗi xảy ra', 'error');
            }
            return;
        }

        if (newStatus === 'Đang giao') {
            const carrierSelector = document.getElementById('carrier-selector');
            if (carrierSelector && !carrierSelector.value) {
                Swal.fire({
                    title: 'Chưa chọn đối tác vận chuyển!',
                    text: 'Vui lòng chọn đối tác vận chuyển trước khi bắt đầu giao hàng.',
                    icon: 'warning',
                    confirmButtonText: 'Đã hiểu',
                    confirmButtonColor: '#e69d00'
                });
                return;
            }
        }

        // For other status changes (non-cancellation)
        const statusTextMap = {
            'Đã xác nhận': 'xác nhận đơn hàng này',
            'Đang chuẩn bị': 'bắt đầu chuẩn bị món ăn',
            'Đang giao': 'bắt đầu giao hàng',
            'Hoàn thành': 'xác nhận đơn hàng hoàn thành thành công'
        };
        const actionText = statusTextMap[newStatus] || `chuyển trạng thái sang "${newStatus}"`;

        const confirmResult = await Swal.fire({
            title: 'Xác nhận thay đổi?',
            text: `Bạn có chắc chắn muốn ${actionText}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy bỏ',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa'
        });

        if (!confirmResult.isConfirmed) return;

        try {
            const res = await fetch(`/api/v1/seller/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            });
            const result = await res.json();
            if (result.success) {
                Swal.fire({
                    title: 'Thành công!',
                    text: 'Trạng thái đơn hàng đã được cập nhật.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadDetail();
                if (typeof checkOrders === 'function') checkOrders();
            } else {
                Swal.fire('Thất bại!', result.message, 'error');
            }
        } catch (err) {
            Swal.fire('Lỗi!', 'Có lỗi xảy ra', 'error');
        }
    }

    async function changeCarrier(carrierId) {
        try {
            const res = await fetch(`/api/v1/seller/orders/${orderId}/carrier`, {
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

    function getStatusConfig(status) {
        switch(status) {
            case 'Hoàn thành': return { class: 'badge-completed', icon: 'fa-check-double' };
            case 'Đang giao': return { class: 'badge-delivering', icon: 'fa-shipping-fast' };
            case 'Đang chuẩn bị': return { class: 'badge-preparing', icon: 'fa-utensils' };
            case 'Chờ xác nhận': return { class: 'badge-pending', icon: 'fa-clock' };
            case 'Đã xác nhận': return { class: 'badge-confirmed', icon: 'fa-check-circle' };
            case 'Đã hủy': return { class: 'badge-cancelled', icon: 'fa-times-circle' };
            default: return { class: 'bg-secondary', icon: 'fa-question-circle' };
        }
    }
    loadDetail();
</script>
@endpush

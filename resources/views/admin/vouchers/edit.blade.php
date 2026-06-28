@extends('admin.layouts.app')

@section('title', 'Sửa Voucher')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-edit text-primary fs-4"></i>
            <div>
                <h4 class="mb-0 fw-bold">Sửa Voucher</h4>
                <small class="text-muted">Cập nhật thông tin voucher giảm giá</small>
            </div>
        </div>
        <button class="btn btn-light btn-sm border" onclick="handleTopBack()">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </button>
    </div>

    <!-- Steps Indicator -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between position-relative">
                <div class="step-item active text-center flex-grow-1" id="step-ind-1" style="cursor: pointer;" onclick="clickStep(1)">
                    <div class="step-icon mb-1 mx-auto" style="width: 30px; height: 30px; border-radius: 50%; background: #0d6efd; color: #fff; line-height: 30px; font-weight: bold;">1</div>
                    <small class="fw-bold">Thông tin cơ bản</small>
                </div>
                <div class="step-item text-center flex-grow-1" id="step-ind-2" style="cursor: pointer;" onclick="clickStep(2)">
                    <div class="step-icon mb-1 mx-auto" style="width: 30px; height: 30px; border-radius: 50%; background: #e9ecef; color: #6c757d; line-height: 30px; font-weight: bold;">2</div>
                    <small class="text-muted">Thời gian hiệu lực</small>
                </div>
                <div class="step-item text-center flex-grow-1" id="step-ind-3" style="cursor: pointer;" onclick="clickStep(3)">
                    <div class="step-icon mb-1 mx-auto" style="width: 30px; height: 30px; border-radius: 50%; background: #e9ecef; color: #6c757d; line-height: 30px; font-weight: bold;">3</div>
                    <small class="text-muted">Xác nhận</small>
                </div>
                <!-- Progress Line -->
                <div class="position-absolute top-50 start-0 end-0 translate-y-middle" style="height: 2px; background: #e9ecef; z-index: -1;">
                    <div class="progress-line" style="height: 100%; width: 0%; background: #0d6efd; transition: width 0.3s;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <form id="editVoucherForm">
                @csrf
                
                <!-- Step 1: Thông tin cơ bản -->
                <div class="form-step" id="step-1">
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-4" style="border-radius: 10px; background-color: #f0f7ff; border-color: #cce3ff;">
                        <i class="fas fa-info-circle text-primary"></i>
                        <div>
                            <div class="fw-bold text-primary">Thông tin cơ bản</div>
                            <small class="text-muted">Mã voucher phải là duy nhất và sẽ được cho khách hàng</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Mã Voucher <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-tag text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" id="MaCode" name="MaCode" placeholder="Nhập mã voucher...">
                            <button class="btn btn-outline-primary" type="button" onclick="generateRandomCode()">Tạo</button>
                        </div>
                        <div class="text-danger small mt-1 d-none" id="error-MaCode">Mã voucher là bắt buộc</div>
                        <small class="text-muted">Mã voucher phải là duy nhất và sẽ được cho khách hàng</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Loại Giảm Giá <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="LoaiGiamGia" id="typePhanTram" value="PhanTram" checked onchange="toggleDiscountType()">
                                <label class="form-check-label fw-bold text-dark" for="typePhanTram">Phần trăm (%)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="LoaiGiamGia" id="typeTienMat" value="TienMat" onchange="toggleDiscountType()">
                                <label class="form-check-label fw-bold text-dark" for="typeTienMat">Tiền mặt (VND)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                                          <!-- Percent Input (Only for PhanTram) -->
                                          <div class="col-md-6" id="inputPhanTramContainer">
                                              <label class="form-label fw-bold text-dark">Phần Trăm Giảm Giá <span class="text-danger">*</span></label>
                                              <div class="input-group">
                                                  <span class="input-group-text bg-light border-end-0"><i class="fas fa-percent text-muted"></i></span>
                                                  <input type="number" class="form-control border-start-0 border-end-0 text-center" id="PhanTram" name="PhanTram" value="0" min="0" max="100">
                                                  <button class="btn btn-outline-secondary border-start-0" type="button" onclick="adjustValue('PhanTram', -1)"><i class="fas fa-minus"></i></button>
                                                  <button class="btn btn-outline-secondary" type="button" onclick="adjustValue('PhanTram', 1)"><i class="fas fa-plus"></i></button>
                                              </div>
                                              <div class="text-danger small mt-1 d-none" id="error-PhanTram">Phần trăm giảm giá phải lớn hơn 0</div>
                                          </div>
                    
                                          <!-- Cash Discount Input (Only for TienMat) -->
                                          <div class="col-md-6 d-none" id="inputTienMatContainer">
                                              <label class="form-label fw-bold text-dark">Số Tiền Giảm (VND) <span class="text-danger">*</span></label>
                                              <div class="input-group">
                                                  <span class="input-group-text bg-light border-end-0"><i class="fas fa-money-bill-wave text-muted"></i></span>
                                                  <input type="number" class="form-control border-start-0" id="GiamToiDa_TienMat" name="GiamToiDa_TienMat" placeholder="0">
                                              </div>
                                              <div class="text-danger small mt-1 d-none" id="error-GiamToiDa_TienMat">Số tiền giảm phải lớn hơn 0</div>
                                          </div>
                    
                                          <!-- Minimum Order (For both types) -->
                                          <div class="col-md-6" id="inputDonHangToiThieuContainer">
                                              <label class="form-label fw-bold text-dark">Đơn Hàng Tối Thiểu</label>
                                              <div class="input-group">
                                                  <span class="input-group-text bg-light border-end-0"><i class="fas fa-shopping-cart text-muted"></i></span>
                                                  <input type="number" class="form-control border-start-0" id="DonHangToiThieu" name="DonHangToiThieu" placeholder="0">
                                                  <span class="input-group-text bg-light">VND</span>
                                              </div>
                                              <small class="text-muted">Để trống nếu không giới hạn</small>
                                          </div>
                                      </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Tổng Số Lượng Phát Hành</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-ticket-alt text-muted"></i></span>
                                <input type="number" class="form-control border-start-0" id="SoLuongToiDa" name="SoLuongToiDa" placeholder="0">
                                <span class="input-group-text bg-light">lượt</span>
                            </div>
                            <small class="text-muted">Để trống nếu không giới hạn số lượng phát hành</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Số Lần Tối Đa/1 User</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-users text-muted"></i></span>
                                <input type="number" class="form-control border-start-0" id="GioiHanNguoiDung" name="GioiHanNguoiDung" placeholder="1" value="1">
                                <span class="input-group-text bg-light">lần</span>
                            </div>
                            <small class="text-muted">Giới hạn số lần một tài khoản có thể sử dụng (mặc định là 1)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Mô Tả Voucher</label>
                        <textarea class="form-control" id="MoTa" name="MoTa" rows="3" placeholder="Nhập mô tả chi tiết voucher (ví dụ: Giảm giá ngày cuối tuần, áp dụng cho mọi đơn hàng...)"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Giá trị preset nhanh:</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetPercent(10)">10%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetPercent(20)">20%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetPercent(30)">30%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetPercent(50)">50%</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Thời gian hiệu lực -->
                <div class="form-step d-none" id="step-2">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="alert alert-info d-flex align-items-center gap-2 mb-4" style="border-radius: 10px; background-color: #f0f7ff; border-color: #cce3ff;">
                                <i class="fas fa-calendar-alt text-primary"></i>
                                <div>
                                    <div class="fw-bold text-primary">Thiết lập thời gian</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Ngày Bắt Đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary bg-light border-end-0" type="button" id="btnStartCalendar">
                                        <i class="far fa-calendar-alt text-muted"></i>
                                    </button>
                                    <input type="text" class="form-control border-start-0" id="NgayBatDau" name="NgayBatDau" placeholder="Chọn ngày...">
                                </div>
                                <div class="text-danger small mt-1 d-none" id="error-NgayBatDau">Ngày bắt đầu là bắt buộc</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Ngày Kết Thúc <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary bg-light border-end-0" type="button" id="btnEndCalendar">
                                        <i class="far fa-calendar-alt text-muted"></i>
                                    </button>
                                    <input type="text" class="form-control border-start-0" id="NgayKetThuc" name="NgayKetThuc" placeholder="Chọn ngày...">
                                </div>
                                <div class="text-danger small mt-1 d-none" id="error-NgayKetThuc">Ngày kết thúc là bắt buộc</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Chọn nhanh thời gian:</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetDuration(7)">7 ngày</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetDuration(14)">2 tuần</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetDuration(30)">1 tháng</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetDuration(60)">2 tháng</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPresetDuration(90)">3 tháng</button>
                                </div>
                            </div>

                            <div class="alert alert-warning py-2" style="border-radius: 10px; background-color: #fffbeb; border-color: #fef08a;">
                                <div class="d-flex gap-2">
                                    <i class="fas fa-exclamation-triangle text-warning mt-1"></i>
                                    <div>
                                        <div class="fw-bold text-warning">Lưu ý quan trọng</div>
                                        <small class="text-muted d-block">Voucher sẽ tự động hết hạn sau ngày kết thúc</small>
                                        <small class="text-muted d-block">Không thể thay đổi thời gian sau khi voucher đã được kích hoạt</small>
                                        <small class="text-muted d-block">Khách hàng chỉ có thể sử dụng voucher trong khoảng thời gian này</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light" style="border-radius: 12px;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="far fa-lightbulb text-warning fs-5"></i>
                                        <h6 class="mb-0 fw-bold">Gợi ý</h6>
                                    </div>
                                    <ul class="small text-muted ps-3 mb-0">
                                        <li>Nên đặt thời gian voucher phù hợp với chiến dịch marketing</li>
                                        <li>Voucher ngắn hạn (7-14 ngày) tạo cảm giác khẩn hiếm</li>
                                        <li>Voucher dài hạn (1-3 tháng) phù hợp cho khách hàng thân thiết</li>
                                        <li>Kiểm tra kỹ ngày bắt đầu và kết thúc trước khi lưu</li>
                                        <li>Có thể kết hợp với ngày lễ, sự kiện đặc biệt</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Xác nhận -->
                <div class="form-step d-none" id="step-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="far fa-eye text-primary"></i>
                        <h6 class="mb-0 fw-bold text-primary">Xem trước voucher</h6>
                    </div>

                    <!-- Preview Card -->
                    <div class="voucher-big-card mb-4" style="background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%); color: #fff; border-radius: 16px; padding: 2rem; text-align: center; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                        <div style="position: absolute; bottom: -50px; right: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                        
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                            <i class="fas fa-ticket-alt"></i>
                            <span style="text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; opacity: 0.9;">VOUCHER GIẢM GIÁ</span>
                        </div>
                        <div class="display-3 fw-bold mb-2" id="previewPhanTram">0%</div>
                        <div class="mb-3">
                            <span class="badge" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); color: #fff; font-size: 1.2rem; padding: 0.5rem 1.5rem; border-radius: 99px;" id="previewMaCode">------</span>
                        </div>
                        <div class="small opacity-75 d-flex justify-content-center align-items-center gap-2">
                            <i class="far fa-calendar-alt"></i>
                            <span id="previewDateRange">--/--/---- - --/--/----</span>
                        </div>
                    </div>

                    <!-- Preview Table -->
                    <div class="detail-table-container" style="background: #111827; color: #f9fafb; border-radius: 12px; overflow: hidden;">
                        <table class="table table-dark mb-0" style="--bs-table-bg: #111827; --bs-table-border-color: #374151;">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 fw-normal" style="font-size: 0.85rem; color: #9ca3af;">Thông tin</th>
                                    <th class="px-4 py-3 fw-normal" style="font-size: 0.85rem; color: #9ca3af;">Giá trị</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 py-3 border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-ticket-alt" style="color: #9ca3af;"></i>
                                            <span>Mã Voucher</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-secondary fw-bold" style="color: #ec4899;" id="tableMaCode">-</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-percent" style="color: #9ca3af;"></i>
                                            <span id="labelPhanTramOrTienMat">Phần trăm giảm giá</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-secondary">
                                        <span class="badge" style="background: #10b981; color: #fff;" id="tablePhanTram">0%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-shopping-cart" style="color: #9ca3af;"></i>
                                            <span>Đơn hàng tối thiểu</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-secondary" id="tableDonHangToiThieu">-</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-ticket-alt" style="color: #9ca3af;"></i>
                                            <span>Tổng số lượng phát hành</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-secondary" id="tableSoLuongToiDa">-</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-users" style="color: #9ca3af;"></i>
                                            <span>Số lần tối đa/1 User</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-secondary" id="tableGioiHanNguoiDung">-</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-calendar-alt" style="color: #9ca3af;"></i>
                                            <span>Thời gian hiệu lực</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-secondary" id="tableDateRange">-</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check" style="color: #9ca3af;"></i>
                                            <span>Số ngày có hiệu lực</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-secondary">
                                        <span class="badge" style="background: #0dcaf0; color: #fff;" id="tableSoNgayHieuLuc">0 ngày</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 border-secondary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-info-circle" style="color: #9ca3af;"></i>
                                            <span>Mô tả</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-secondary" id="tableMoTa">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light border d-none" id="btn-prev" onclick="goToStep(currentStep - 1)">Quay lại</button>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-light border me-2" onclick="window.location.href='{{ route('admin.vouchers.index') }}'">Hủy</button>
                        <button type="button" class="btn btn-primary" id="btn-next" onclick="goToStep(currentStep + 1)">Tiếp theo</button>
                        <button type="button" class="btn btn-success d-none" id="btn-submit" onclick="submitForm()">Cập nhật Voucher</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const VOUCHER_ID = {{ $id }};
    let currentStep = 1;
    const totalSteps = 3;
    let maxStepReached = 1;

    document.addEventListener('DOMContentLoaded', function() {
        fetchVoucherData();
    });

    async function fetchVoucherData() {
        try {
            const response = await fetch(`/api/v1/admin/vouchers/${VOUCHER_ID}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                alert(`Không thể tải dữ liệu voucher (Lỗi: ${response.status})`);
                return;
            }
            
            const result = await response.json();
            if (result.success) {
                          const voucher = result.data;
                          document.getElementById('MaCode').value = voucher.MaCode;
                          
                          if (voucher.LoaiGiamGia === 'TienMat') {
                              document.getElementById('typeTienMat').checked = true;
                              toggleDiscountType();
                              document.getElementById('GiamToiDa_TienMat').value = voucher.GiamToiDa;
                          } else {
                              document.getElementById('typePhanTram').checked = true;
                              toggleDiscountType();
                              document.getElementById('PhanTram').value = voucher.PhanTram;
                          }
                
                document.getElementById('SoLuongToiDa').value = voucher.SoLuongToiDa || '';
                document.getElementById('GioiHanNguoiDung').value = voucher.GioiHanNguoiDung || '1';
                document.getElementById('DonHangToiThieu').value = voucher.DonHangToiThieu || '';
                document.getElementById('MoTa').value = voucher.MoTa || '';
                
                function toLocalYMD(dateStr) {
                    if (!dateStr) return '';
                    const d = new Date(dateStr);
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }
                
                const startDate = toLocalYMD(voucher.NgayBatDau);
                const endDate = toLocalYMD(voucher.NgayKetThuc);
                
                document.getElementById('NgayBatDau').value = startDate;
                document.getElementById('NgayKetThuc').value = endDate;
                
                // Cập nhật Flatpickr
                if (window.fpStart) window.fpStart.setDate(startDate);
                if (window.fpEnd) window.fpEnd.setDate(endDate);
                
            } else {
                alert('Không thể tải dữ liệu voucher');
            }
        } catch (error) {
            console.error('Error fetching voucher:', error);
            alert('Lỗi: ' + error.message);
        }
    }

    function handleTopBack() {
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        } else {
            window.location.href = '{{ route('admin.vouchers.index') }}';
        }
    }

    function clickStep(step) {
        if (step <= maxStepReached) {
            goToStep(step);
        } else if (step === currentStep + 1) {
            goToStep(step);
        }
    }

    function goToStep(step) {
        if (step < 1 || step > totalSteps) return;
        
        // Validation for Step 1
              if (currentStep === 1 && step > 1) {
                  const maCodeEl = document.getElementById('MaCode');
                  const maCode = maCodeEl.value;
                  const isPhanTram = document.getElementById('typePhanTram').checked;
                  
                  let hasError = false;
                  
                  if (!maCode) {
                      document.getElementById('error-MaCode').classList.remove('d-none');
                      maCodeEl.classList.add('is-invalid');
                      hasError = true;
                  } else {
                      document.getElementById('error-MaCode').classList.add('d-none');
                      maCodeEl.classList.remove('is-invalid');
                  }
                  
                  if (isPhanTram) {
                      const phanTramEl = document.getElementById('PhanTram');
                      const phanTram = phanTramEl.value;
                      if (phanTram <= 0) {
                          document.getElementById('error-PhanTram').classList.remove('d-none');
                          phanTramEl.classList.add('is-invalid');
                          hasError = true;
                      } else {
                          document.getElementById('error-PhanTram').classList.add('d-none');
                          phanTramEl.classList.remove('is-invalid');
                      }
                  } else {
                      const giamToiDaEl = document.getElementById('GiamToiDa_TienMat');
                      const giamToiDa = giamToiDaEl.value;
                      if (giamToiDa <= 0) {
                          document.getElementById('error-GiamToiDa_TienMat').classList.remove('d-none');
                          giamToiDaEl.classList.add('is-invalid');
                          hasError = true;
                      } else {
                          document.getElementById('error-GiamToiDa_TienMat').classList.add('d-none');
                          giamToiDaEl.classList.remove('is-invalid');
                      }
                  }
                  
                  if (hasError) return;
              }
        
        // Validation for Step 2
        if (currentStep === 2 && step > 2) {
            const batDauEl = document.getElementById('NgayBatDau');
            const ketThucEl = document.getElementById('NgayKetThuc');
            const batDau = batDauEl.value;
            const ketThuc = ketThucEl.value;
            
            let hasError = false;
            
            if (!batDau) {
                document.getElementById('error-NgayBatDau').classList.remove('d-none');
                batDauEl.classList.add('is-invalid');
                hasError = true;
            } else {
                document.getElementById('error-NgayBatDau').classList.add('d-none');
                batDauEl.classList.remove('is-invalid');
            }
            
            if (!ketThuc) {
                document.getElementById('error-NgayKetThuc').classList.remove('d-none');
                ketThucEl.classList.add('is-invalid');
                hasError = true;
            } else {
                document.getElementById('error-NgayKetThuc').classList.add('d-none');
                ketThucEl.classList.remove('is-invalid');
            }
            
            if (!hasError && new Date(batDau) > new Date(ketThuc)) {
                document.getElementById('error-NgayKetThuc').textContent = 'Ngày kết thúc phải sau ngày bắt đầu!';
                document.getElementById('error-NgayKetThuc').classList.remove('d-none');
                ketThucEl.classList.add('is-invalid');
                return;
            }
            
            if (hasError) return;
        }

        currentStep = step;
        if (currentStep > maxStepReached) {
            maxStepReached = currentStep;
        }

        // Update UI
        document.querySelectorAll('.form-step').forEach(el => el.classList.add('d-none'));
        document.getElementById(`step-${currentStep}`).classList.remove('d-none');

        // Update Indicators
        for (let i = 1; i <= totalSteps; i++) {
            const ind = document.getElementById(`step-ind-${i}`);
            const icon = ind.querySelector('.step-icon');
            const text = ind.querySelector('small');
            
            if (i === currentStep) {
                icon.style.background = '#0d6efd';
                icon.style.color = '#fff';
                text.classList.add('fw-bold');
                text.classList.remove('text-muted');
            } else if (i < currentStep) {
                icon.style.background = '#198754'; // Success green for completed
                icon.style.color = '#fff';
                text.classList.remove('fw-bold');
                text.classList.remove('text-muted');
            } else {
                icon.style.background = '#e9ecef';
                icon.style.color = '#6c757d';
                text.classList.remove('fw-bold');
                text.classList.add('text-muted');
            }
        }

        // Update progress line
        const progressLine = document.querySelector('.progress-line');
        if (currentStep === 1) progressLine.style.width = '0%';
        if (currentStep === 2) progressLine.style.width = '50%';
        if (currentStep === 3) progressLine.style.width = '100%';

        // Update Buttons
        document.getElementById('btn-prev').classList.toggle('d-none', currentStep === 1);
        document.getElementById('btn-next').classList.toggle('d-none', currentStep === totalSteps);
        document.getElementById('btn-submit').classList.toggle('d-none', currentStep !== totalSteps);

        // Fill Preview in Step 3
              if (currentStep === 3) {
                  const maCode = document.getElementById('MaCode').value;
                  const isPhanTram = document.getElementById('typePhanTram').checked;
                  const phanTram = document.getElementById('PhanTram').value;
                  const giamToiDaTienMat = document.getElementById('GiamToiDa_TienMat').value;
                  const batDau = document.getElementById('NgayBatDau').value;
                  const ketThuc = document.getElementById('NgayKetThuc').value;
                  const minOrder = document.getElementById('DonHangToiThieu').value;
                  const maxUsage = document.getElementById('SoLuongToiDa').value;
                  const userLimit = document.getElementById('GioiHanNguoiDung').value;
                  const moTa = document.getElementById('MoTa').value;
        
                  // Cập nhật Big Card
                  const previewPhanTramEl = document.getElementById('previewPhanTram');
                  if (isPhanTram) {
                      previewPhanTramEl.textContent = phanTram + '%';
                      previewPhanTramEl.style.fontSize = '3rem'; // Reset font size
                  } else {
                      previewPhanTramEl.textContent = Number(giamToiDaTienMat).toLocaleString('vi-VN') + 'đ';
                      previewPhanTramEl.style.fontSize = '2rem'; // Thu nhỏ nếu chữ dài
                  }
                  document.getElementById('previewMaCode').textContent = maCode;
                  document.getElementById('previewDateRange').textContent = `${formatDate(batDau)} - ${formatDate(ketThuc)}`;
        
                  // Cập nhật Table
                  document.getElementById('tableMaCode').textContent = maCode;
                  
                  // Tính số ngày có hiệu lực
                  let daysDiff = 0;
                  if (batDau && ketThuc) {
                      const start = new Date(batDau);
                      const end = new Date(ketThuc);
                      const diffTime = Math.abs(end - start);
                      daysDiff = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                  }
                  document.getElementById('tableSoNgayHieuLuc').textContent = daysDiff + ' ngày';
                  
                  const labelEl = document.getElementById('labelPhanTramOrTienMat');
                  const iconEl = labelEl.previousElementSibling;
                  
                  if (isPhanTram) {
                      iconEl.className = 'fas fa-percent';
                      document.getElementById('tablePhanTram').textContent = phanTram + '%';
                      labelEl.textContent = 'Phần trăm giảm giá';
                  } else {
                      iconEl.className = 'fas fa-money-bill-wave';
                      document.getElementById('tablePhanTram').textContent = Number(giamToiDaTienMat).toLocaleString('vi-VN') + ' VND';
                      labelEl.textContent = 'Số tiền giảm';
                  }
            
            document.getElementById('tableDonHangToiThieu').textContent = minOrder ? Number(minOrder).toLocaleString('vi-VN') + ' VND' : 'Không giới hạn';
            document.getElementById('tableSoLuongToiDa').textContent = maxUsage ? maxUsage + ' lượt' : 'Không giới hạn';
            document.getElementById('tableGioiHanNguoiDung').textContent = userLimit ? userLimit + ' lần' : 'Không giới hạn';
            document.getElementById('tableDateRange').textContent = `${formatDate(batDau)} - ${formatDate(ketThuc)}`;
            document.getElementById('tableMoTa').textContent = moTa ? moTa : 'Không có';
        }
    }

    function toggleDiscountType() {
          const isPhanTram = document.getElementById('typePhanTram').checked;
          const phanTramContainer = document.getElementById('inputPhanTramContainer');
          const tienMatContainer = document.getElementById('inputTienMatContainer');
          
          if (isPhanTram) {
              phanTramContainer.classList.remove('d-none');
              tienMatContainer.classList.add('d-none');
          } else {
              phanTramContainer.classList.add('d-none');
              tienMatContainer.classList.remove('d-none');
          }
      }

    function generateRandomCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const input = document.getElementById('MaCode');
        input.value = code;
        
        // Xóa báo lỗi khi đã có giá trị mới
        document.getElementById('error-MaCode').classList.add('d-none');
        input.classList.remove('is-invalid');
    }

    function adjustValue(id, amount) {
        const el = document.getElementById(id);
        let val = parseInt(el.value) || 0;
        val += amount;
        if (id === 'PhanTram') {
            if (val < 0) val = 0;
            if (val > 100) val = 100;
        }
        el.value = val;
        
        // Xóa báo lỗi cho PhanTram nếu giá trị hợp lệ
        if (id === 'PhanTram' && val > 0) {
            document.getElementById('error-PhanTram').classList.add('d-none');
            el.classList.remove('is-invalid');
        }
    }

    function setPresetPercent(percent) {
        const el = document.getElementById('PhanTram');
        el.value = percent;
        
        // Xóa báo lỗi
        if (percent > 0) {
            document.getElementById('error-PhanTram').classList.add('d-none');
            el.classList.remove('is-invalid');
        }
    }

    function setPresetDuration(days) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const startStr = `${year}-${month}-${day}`;
        
        const endDate = new Date(today.getTime() + (days * 24 * 60 * 60 * 1000));
        const endYear = endDate.getFullYear();
        const endMonth = String(endDate.getMonth() + 1).padStart(2, '0');
        const endDay = String(endDate.getDate()).padStart(2, '0');
        const endStr = `${endYear}-${endMonth}-${endDay}`;
        
        const batDauEl = document.getElementById('NgayBatDau');
        const ketThucEl = document.getElementById('NgayKetThuc');
        
        batDauEl.value = startStr;
        ketThucEl.value = endStr;
        
        if (window.fpStart) window.fpStart.setDate(startStr);
        if (window.fpEnd) window.fpEnd.setDate(endStr);
        
        document.getElementById('error-NgayBatDau').classList.add('d-none');
        document.getElementById('error-NgayKetThuc').classList.add('d-none');
        batDauEl.classList.remove('is-invalid');
        ketThucEl.classList.remove('is-invalid');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('vi-VN');
    }



    function clearAjaxErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.ajax-error').forEach(el => {
            el.remove();
        });
        document.querySelectorAll('[id^="error-"]').forEach(el => {
            el.classList.add('d-none');
        });
    }

    function showAjaxErrors(errors) {
        clearAjaxErrors();
        
        let firstErrorStep = null;
        const fieldsByStep = {
            1: ['MaCode', 'PhanTram', 'GiamToiDa', 'GiamToiDa_TienMat', 'DonHangToiThieu', 'SoLuongToiDa', 'GioiHanNguoiDung', 'MoTa'],
            2: ['NgayBatDau', 'NgayKetThuc']
        };

        Object.entries(errors).forEach(([field, messages]) => {
            let inputName = field;
            if (field === 'GiamToiDa') {
                const isPhanTram = document.getElementById('typePhanTram').checked;
                if (!isPhanTram) {
                    inputName = 'GiamToiDa_TienMat';
                }
            }
            
            const input = document.querySelector(`[name="${inputName}"]`) || document.getElementById(inputName);
            if (!input) return;
            
            input.classList.add('is-invalid');
            
            for (const [stepNum, fields] of Object.entries(fieldsByStep)) {
                if (fields.includes(inputName) || fields.includes(field)) {
                    const stepInt = parseInt(stepNum);
                    if (firstErrorStep === null || stepInt < firstErrorStep) {
                        firstErrorStep = stepInt;
                    }
                }
            }
            
            const errorDivId = `error-${inputName}`;
            const predefinedError = document.getElementById(errorDivId);
            if (predefinedError) {
                predefinedError.textContent = messages[0];
                predefinedError.classList.remove('d-none');
            } else {
                const container = input.closest('.input-group') || input;
                const div = document.createElement('div');
                div.className = 'text-danger small mt-1 ajax-error';
                div.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${messages[0]}`;
                container.parentNode.insertBefore(div, container.nextSibling);
            }
        });
        
        if (firstErrorStep !== null) {
            goToStep(firstErrorStep);
        }
    }

    async function submitForm() {
        clearAjaxErrors();
        const form = document.getElementById('editVoucherForm');
        const formData = new FormData(form);
        
        const isPhanTram = document.getElementById('typePhanTram').checked;
        const loaiGiamGia = isPhanTram ? 'PhanTram' : 'TienMat';
        
        formData.append('LoaiGiamGia', loaiGiamGia);
        
        if (isPhanTram) {
            formData.set('GiamToiDa', 0);
        } else {
            const giamToiDa = document.getElementById('GiamToiDa_TienMat').value;
            formData.set('GiamToiDa', giamToiDa);
            formData.set('PhanTram', 0);
        }
        formData.delete('GiamToiDa_TienMat');

        // Hỗ trợ PUT trong Laravel qua FormData
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/api/v1/admin/vouchers/${VOUCHER_ID}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                localStorage.setItem('admin_success', 'Cập nhật voucher thành công!');
                window.location.href = '{{ route('admin.vouchers.index') }}';
            } else {
                if (result.errors) {
                    showAjaxErrors(result.errors);
                } else {
                    window.showAdminToast(result.message || 'Lỗi khi cập nhật voucher', 'error');
                }
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            window.showAdminToast('Không thể kết nối đến server', 'error');
        }
    }

    // Tự động ẩn lỗi
    document.querySelectorAll('#editVoucherForm input, #editVoucherForm textarea').forEach(input => {
        const handleClear = function() {
            this.classList.remove('is-invalid');
            const predefinedError = document.getElementById(`error-${this.id}`);
            if (predefinedError) {
                predefinedError.classList.add('d-none');
            }
            const container = this.closest('.input-group') || this;
            let sibling = container.nextSibling;
            while (sibling) {
                if (sibling.classList && sibling.classList.contains('ajax-error')) {
                    sibling.remove();
                    break;
                }
                sibling = sibling.nextSibling;
            }
        };
        input.addEventListener('input', handleClear);
        input.addEventListener('change', handleClear);
    });
</script>

<style>
    .table-dark {
        color: #f9fafb;
    }
    .table-dark td {
        border-color: #1f2937 !important;
    }
    .badge {
        font-weight: 600;
        padding: 0.35em 0.65em;
        border-radius: 6px;
    }
</style>
@endsection

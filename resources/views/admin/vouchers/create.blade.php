@extends('admin.admin')

@section('title', 'Thêm Voucher Mới')

@section('content')
    <style>
        /* Điều chỉnh font size và spacing cho trang create */
        .breadcrumb {
            font-size: 11px;
            margin-bottom: 1rem;
        }

        .card-header h4 {
            font-size: 1.1rem;
        }

        .card-header small {
            font-size: 10px;
        }

        .form-label {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .form-control {
            font-size: 12px;
            padding: 0.4rem 0.6rem;
        }

        .input-group-text {
            font-size: 11px;
            padding: 0.4rem 0.6rem;
        }

        .btn {
            font-size: 11px;
            padding: 0.3rem 0.6rem;
        }

        .btn-sm {
            font-size: 10px;
            padding: 0.25rem 0.5rem;
        }

        .form-text {
            font-size: 10px;
            margin-top: 0.2rem;
        }

        .alert {
            font-size: 11px;
            padding: 0.5rem 0.7rem;
            margin-bottom: 1rem;
        }

        .voucher-card {
            font-size: 12px;
        }

        .discount-amount {
            font-size: 2rem;
        }

        .voucher-header {
            font-size: 0.9rem;
        }

        .voucher-code {
            font-size: 11px;
            padding: 0.3rem 0.6rem;
        }

        .table {
            font-size: 11px;
        }

        .table th,
        .table td {
            padding: 0.3rem 0.4rem;
        }

        .badge {
            font-size: 9px;
        }

        .progress {
            height: 4px;
        }

        .step-text {
            font-size: 10px;
        }

        /* Giảm khoảng cách giữa các form group */
        .form-group {
            margin-bottom: 0.8rem;
        }

        /* Giảm khoảng cách giữa các step */
        .form-step {
            min-height: auto;
            padding: 0.5rem 0;
        }

        /* Giảm khoảng cách trong row */
        .row {
            margin-bottom: 0.5rem;
        }

        /* Compact card body */
        .card-body {
            padding: 1rem;
        }

        /* Compact alert */
        .alert ul {
            margin-bottom: 0;
            padding-left: 1rem;
        }

        .alert li {
            margin-bottom: 0.2rem;
        }

        /* Compact preset buttons */
        .btn-group .btn {
            margin: 0.1rem;
        }

        /* Giảm khoảng cách voucher preview */
        .voucher-preview {
            margin-bottom: 1rem;
        }

        .voucher-card {
            padding: 1rem;
            margin: 0.5rem 0;
        }

        /* Compact table */
        .table-responsive {
            margin-bottom: 1rem;
        }

        /* Step header compact */
        h5.text-primary {
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }

        .step-section {
            margin-bottom: 1rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .voucher-preview {
            perspective: 1000px;
        }

        .voucher-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .voucher-card:hover {
            transform: rotateY(2deg) rotateX(2deg);
        }

        .voucher-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(45deg,
                    transparent,
                    transparent 6px,
                    rgba(255, 255, 255, 0.1) 6px,
                    rgba(255, 255, 255, 0.1) 12px);
            animation: shimmer 20s linear infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%) translateY(-100%);
            }

            100% {
                transform: translateX(100%) translateY(100%);
            }
        }

        .voucher-header {
            font-weight: bold;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .discount-amount {
            font-weight: bold;
            margin: 0.5rem 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .voucher-code {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            margin: 0.5rem 0;
            backdrop-filter: blur(10px);
        }

        .voucher-dates {
            margin-top: 0.5rem;
            opacity: 0.8;
        }

        .btn-group .btn {
            transition: all 0.3s ease;
            margin: 0.1rem;
        }

        .btn-group .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
    </style>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.vouchers.index') }}">
                                <i class="fas fa-ticket-alt"></i> Quản lý Voucher
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Thêm voucher mới</li>
                    </ol>
                </nav>

                <!-- Main Form Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-primary text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">
                                    <i class="fas fa-plus-circle"></i> Tạo Voucher Mới
                                </h4>
                                <small class="opacity-75">Điền thông tin để tạo voucher giảm giá</small>
                            </div>
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('admin.vouchers.store') }}" method="POST" id="voucherForm">
                        @csrf
                        <div class="card-body">

                            <!-- Progress Steps -->
                            <div class="step-section">
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 33%"
                                        id="formProgress"></div>
                                </div>
                                <div class="d-flex justify-content-between step-text">
                                    <small class="text-primary font-weight-bold">Thông tin cơ bản</small>
                                    <small class="text-muted">Thời gian hiệu lực</small>
                                    <small class="text-muted">Xác nhận</small>
                                </div>
                            </div>

                            <!-- Step 1: Basic Information -->
                            <div class="form-step" id="step1">
                                <h5 class="text-primary">
                                    <i class="fas fa-info-circle"></i> Thông tin cơ bản
                                </h5>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="MaCode" class="form-label">
                                                Mã Voucher <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-tag"></i>
                                                </span>
                                                <input type="text"
                                                    class="form-control @error('MaCode') is-invalid @enderror" id="MaCode"
                                                    name="MaCode" value="{{ old('MaCode') }}"
                                                    placeholder="Nhập mã voucher...">
                                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    onclick="generateCode()" id="generateBtn">
                                                    <i class="fas fa-random"></i> Tạo
                                                </button>
                                            </div>

                                            <small class="form-text text-muted">
                                                Mã voucher phải là duy nhất và dễ nhớ cho khách hàng
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="PhanTram" class="form-label">
                                                Phần Trăm Giảm Giá <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-percentage"></i>
                                                </span>
                                                <input type="text" class="form-control @error('PhanTram') is-invalid @enderror" id="PhanTram" name="PhanTram"
                                                    value="{{ old('PhanTram') }}" placeholder="0" inputmode="numeric" autocomplete="off"
                                                    aria-describedby="pct-addon" onchange="updatePreview()">
                                                <span class="input-group-text">%</span>

                                                <button type="button" class="btn btn-outline-secondary" title="-1" onclick="stepField('PhanTram', -1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" title="+1" onclick="stepField('PhanTram', +1)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Điều kiện áp dụng -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="DonHangToiThieu" class="form-label">
                                                Đơn Hàng Tối Thiểu
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </span>
                                                <input type="text" 
                                                    class="form-control @error('DonHangToiThieu') is-invalid @enderror" 
                                                    id="DonHangToiThieu" 
                                                    name="DonHangToiThieu"
                                                    value="{{ old('DonHangToiThieu') }}" 
                                                    placeholder="0"
                                                    onchange="updatePreview()">
                                                <span class="input-group-text">VNĐ</span>
                                            </div>
                                            <small class="form-text text-muted">
                                                Để trống nếu không giới hạn giá trị đơn hàng
                                            </small>
                                            @error('DonHangToiThieu')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="SoLanToiDa" class="form-label">
                                                Số Lần Tối Đa/1 User
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-user-clock"></i>
                                                </span>
                                                <input type="text" 
                                                    class="form-control @error('SoLanToiDa') is-invalid @enderror" 
                                                    id="SoLanToiDa" 
                                                    name="SoLanToiDa"
                                                    value="{{ old('SoLanToiDa') }}" 
                                                    placeholder="0"
                                                    onchange="updatePreview()">
                                                <span class="input-group-text">lần</span>
                                            </div>
                                            <small class="form-text text-muted">
                                                Để trống nếu không giới hạn số lần sử dụng
                                            </small>
                                            @error('SoLanToiDa')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Preset Buttons -->
                                <div class="form-group">
                                    <label class="form-label">Giá trị preset nhanh:</label>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="setDiscount(5)">5%</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="setDiscount(10)">10%</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="setDiscount(15)">15%</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="setDiscount(20)">20%</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="setDiscount(25)">25%</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="setDiscount(50)">50%</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Date Range -->
                            <div class="form-step d-none" id="step2">
                                <h5 class="text-primary">
                                    <i class="fas fa-calendar-alt"></i> Thời gian hiệu lực
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NgayBatDau" class="form-label">
                                                Ngày Bắt Đầu <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </span>
                                                <input type="text"
                                                    class="form-control @error('NgayBatDau') is-invalid @enderror"
                                                    id="NgayBatDau" name="NgayBatDau" value="{{ old('NgayBatDau') }}"
                                                    placeholder="dd/mm/yyyy" autocomplete="off" onchange="updateDuration()">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    id="btnStartCalendar" title="Chọn ngày">
                                                    <i class="fas fa-calendar"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NgayKetThuc" class="form-label">
                                                Ngày Kết Thúc <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-calendar-times"></i>
                                                </span>
                                                <input type="text"
                                                    class="form-control @error('NgayKetThuc') is-invalid @enderror"
                                                    id="NgayKetThuc" name="NgayKetThuc" value="{{ old('NgayKetThuc') }}"
                                                    placeholder="dd/mm/yyyy" autocomplete="off" onchange="updateDuration()">
                                                <button class="btn btn-outline-secondary" type="button" id="btnEndCalendar"
                                                    title="Chọn ngày">
                                                    <i class="fas fa-calendar"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Duration Preview -->
                                    <div class="alert alert-info py-2" id="durationInfo" style="display: none;">
                                        <i class="fas fa-info-circle"></i>
                                        <span id="durationText"></span>
                                    </div>

                                    <!-- Quick Duration Buttons -->
                                    <div class="form-group">
                                        <label class="form-label">Thời gian preset nhanh:</label>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                onclick="setDuration(7)">7
                                                ngày</button>
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                onclick="setDuration(14)">2 tuần</button>
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                onclick="setDuration(30)">1 tháng</button>
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                onclick="setDuration(60)">2 tháng</button>
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                onclick="setDuration(90)">3 tháng</button>
                                        </div>
                                    </div>
                                </div>



                                <!-- Navigation & Important Notes -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="alert alert-warning py-2">
                                            <h6 style="font-size: 11px; margin-bottom: 0.3rem;">
                                                <i class="fas fa-exclamation-triangle"></i> Lưu ý quan trọng:
                                            </h6>
                                            <ul class="mb-0" style="font-size: 10px; padding-left: 1rem;">
                                                <li>Mã voucher phải là duy nhất trong hệ thống</li>
                                                <li>Phần trăm giảm giá phải từ 1% đến 100%</li>
                                                <li>Ngày kết thúc phải sau hoặc bằng ngày bắt đầu</li>
                                                <li>Voucher sẽ tự động kích hoạt vào ngày bắt đầu</li>
                                                <li>Không thể chỉnh sửa voucher sau khi đã được sử dụng</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center p-2">
                                                <i class="fas fa-lightbulb fa-lg text-warning mb-1"></i>
                                                <h6 style="font-size: 11px; margin-bottom: 0.3rem;">Gợi ý</h6>
                                                <small class="text-muted" style="font-size: 10px;">
                                                    Sử dụng mã voucher ngắn gọn và dễ nhớ để khách hàng dễ sử dụng
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Step 3: Preview & Confirm -->
                            <div class="form-step d-none" id="step3">
                                <h5 class="text-primary">
                                    <i class="fas fa-eye"></i> Xem trước voucher
                                </h5>

                                <!-- Voucher Preview -->
                                <div class="row justify-content-center voucher-preview">
                                    <div class="col-md-8">
                                        <div class="voucher-card">
                                            <div class="voucher-header">
                                                <i class="fas fa-gift"></i>
                                                <span>VOUCHER GIẢM GIÁ</span>
                                            </div>
                                            <div class="voucher-body">
                                                <div class="discount-amount" id="previewDiscount">0%</div>
                                                <div class="voucher-code" id="previewCode">VOUCHER-CODE</div>
                                                <div class="voucher-dates">
                                                    <small>
                                                        <i class="fas fa-calendar"></i>
                                                        <span id="previewDates">Chọn ngày bắt đầu và kết thúc</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Thông tin</th>
                                                <th>Giá trị</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><i class="fas fa-tag"></i> Mã Voucher</td>
                                                <td><code id="summaryCode">-</code></td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-percentage"></i> Phần trăm giảm giá</td>
                                                <td><span class="badge bg-success" id="summaryDiscount">0%</span></td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-shopping-cart"></i> Đơn hàng tối thiểu</td>
                                                <td><span id="summaryMinOrder">Không giới hạn</span></td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-user-clock"></i> Số lần tối đa/1 user</td>
                                                <td><span id="summaryMaxUsage">Không giới hạn</span></td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-calendar-alt"></i> Thời gian hiệu lực</td>
                                                <td id="summaryDuration">-</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-clock"></i> Số ngày có hiệu lực</td>
                                                <td><span class="badge bg-info" id="summaryDays">0 ngày</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Form Footer -->
                            <div class="card-footer bg-light py-2">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-secondary btn-sm" id="prevBtn"
                                            onclick="changeStep(-1)" style="display: none;">
                                            <i class="fas fa-arrow-left"></i> Quay lại
                                        </button>
                                    </div>

                                    <div>
                                        <button type="button" class="btn btn-primary btn-sm" id="nextBtn"
                                            onclick="changeStep(1)">
                                            Tiếp theo <i class="fas fa-arrow-right"></i>
                                        </button>
                                        <button type="submit" class="btn btn-success btn-sm" id="submitBtn"
                                            style="display: none;">
                                            <i class="fas fa-save"></i> Lưu Voucher
                                        </button>
                                        <a href="{{ route('admin.vouchers.index') }}"
                                            class="btn btn-outline-secondary btn-sm ms-1">
                                            <i class="fas fa-times"></i> Hủy
                                        </a>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 3;

        // Lấy CSRF token
        function csrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) return meta.content;
            const hidden = document.querySelector('#voucherForm input[name="_token"]');
            return hidden ? hidden.value : '';
        }

        // Map field theo step để clear lỗi đúng vùng
        const fieldsByStep = {
            1: ['MaCode', 'PhanTram', 'DonHangToiThieu', 'SoLanToiDa'],
            2: ['NgayBatDau', 'NgayKetThuc'],
            3: []
        };

        function clearAjaxErrors(step) {
            const names = fieldsByStep[step] || [];
            names.forEach(name => {
                const input = document.querySelector(`[name="${name}"]`);
                if (!input) return;
                input.classList.remove('is-invalid');
                const container = input.closest('.form-group') || input.parentNode;
                const old = container.querySelector('.ajax-error');
                if (old) old.remove();
            });
        }

        function showAjaxErrors(errors) {
            Object.entries(errors).forEach(([field, messages]) => {
                const input = document.querySelector(`[name="${field}"]`);
                if (!input) return;
                const container = input.closest('.form-group') || input.parentNode;

                // đánh dấu lỗi
                input.classList.add('is-invalid');

                // xóa lỗi cũ (ajax)
                const old = container.querySelector('.ajax-error');
                if (old) old.remove();

                // thêm lỗi mới (ajax)
                const div = document.createElement('div');
                div.className = 'invalid-feedback d-block ajax-error';
                div.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${messages[0]}`;
                container.appendChild(div);
            });
        }

        async function validateServerStep(step) {
            clearAjaxErrors(step);

            const form = document.getElementById('voucherForm');
            const formData = new FormData(form);
            formData.append('step', step);

            try {
                const res = await fetch('{{ route('admin.vouchers.validateStep') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (res.ok) {
                    return true;
                }

                if (res.status === 422) {
                    const data = await res.json();
                    showAjaxErrors(data.errors);

                    // focus vào field lỗi đầu tiên
                    const first = Object.keys(data.errors)[0];
                    const el = document.querySelector(`[name="${first}"]`);
                    if (el) el.focus();
                    return false;
                }

                showToast('Không thể kiểm tra hợp lệ. Vui lòng thử lại!', 'danger');
                return false;
            } catch (e) {
                showToast('Lỗi kết nối máy chủ. Vui lòng thử lại!', 'danger');
                return false;
            }
        }

        // Điều hướng step: khi tiến (direction=1) sẽ gọi validate server của step hiện tại
        async function changeStep(direction) {
            if (direction === 1) {
                const ok = await validateServerStep(currentStep);
                if (!ok) return; // có lỗi thì không cho sang bước sau
            }

            currentStep += direction;
            if (currentStep < 1) currentStep = 1;
            if (currentStep > totalSteps) currentStep = totalSteps;

            updateStepDisplay();
        }

        function updateStepDisplay() {
            document.querySelectorAll('.form-step').forEach(step => step.classList.add('d-none'));
            document.getElementById(`step${currentStep}`).classList.remove('d-none');

            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('formProgress').style.width = progress + '%';

            document.getElementById('prevBtn').style.display = currentStep > 1 ? 'inline-block' : 'none';
            document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'inline-block' : 'none';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-block' : 'none';

            if (currentStep === 3) {
                updatePreview();
            }
        }
        // Generate voucher code
        function generateCode() {
            const btn = document.getElementById('generateBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            fetch("{{ route('admin.vouchers.generateCode') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById('MaCode').value = data.code;
                    updatePreview();
                    showToast('Đã tạo mã: ' + data.code, 'success');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Có lỗi xảy ra', 'danger');
                })
                .finally(() => {
                    btn.innerHTML = '<i class="fas fa-random"></i> Tạo';
                    btn.disabled = false;
                });
        }

        // Set discount preset
        function setDiscount(percent) {
            document.getElementById('PhanTram').value = percent;
            updatePreview();
        }

        // Set duration preset
        function setDuration(days) {
            const today = new Date();
            const endDate = new Date(today.getTime() + (days * 24 * 60 * 60 * 1000));

            document.getElementById('NgayBatDau').value = today.toISOString().split('T')[0];
            document.getElementById('NgayKetThuc').value = endDate.toISOString().split('T')[0];

            updateDuration();
        }

        // Update duration info
        function updateDuration() {
            const startDate = document.getElementById('NgayBatDau').value;
            const endDate = document.getElementById('NgayKetThuc').value;

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays >= 0) {
                    document.getElementById('durationInfo').style.display = 'block';
                    document.getElementById('durationText').textContent =
                        `Voucher sẽ có hiệu lực trong ${diffDays} ngày`;
                }
            }

            updatePreview();
        }

        // Update preview
        function updatePreview() {
            const code = document.getElementById('MaCode').value || 'VOUCHER-CODE';
            const discount = document.getElementById('PhanTram').value || '0';
            const startDate = document.getElementById('NgayBatDau').value;
            const endDate = document.getElementById('NgayKetThuc').value;
            const minOrder = document.getElementById('DonHangToiThieu').value;
            const maxUsage = document.getElementById('SoLanToiDa').value;

            // Update preview card
            document.getElementById('previewCode').textContent = code;
            document.getElementById('previewDiscount').textContent = discount + '%';

            if (startDate && endDate) {
                const start = new Date(startDate).toLocaleDateString('vi-VN');
                const end = new Date(endDate).toLocaleDateString('vi-VN');
                document.getElementById('previewDates').innerHTML =
                    `<i class="fas fa-calendar"></i> ${start} - ${end}`;

                const diffTime = Math.abs(new Date(endDate) - new Date(startDate));
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                // Update summary
                document.getElementById('summaryDuration').textContent = `${start} - ${end}`;
                document.getElementById('summaryDays').textContent = `${diffDays} ngày`;
            }

            // Update summary table
            document.getElementById('summaryCode').textContent = code;
            document.getElementById('summaryDiscount').textContent = discount + '%';
            
            // Update điều kiện đơn hàng tối thiểu
            if (minOrder && parseFloat(minOrder) > 0) {
                document.getElementById('summaryMinOrder').textContent = 
                    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(minOrder);
            } else {
                document.getElementById('summaryMinOrder').textContent = 'Không giới hạn';
            }
            
            // Update số lần tối đa
            if (maxUsage && parseInt(maxUsage) > 0) {
                document.getElementById('summaryMaxUsage').textContent = maxUsage + ' lần';
            } else {
                document.getElementById('summaryMaxUsage').textContent = 'Không giới hạn';
            }
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toastHtml = `
                                                        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" style="font-size: 11px;">
                                                            <div class="d-flex">
                                                                <div class="toast-body">${message}</div>
                                                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                                            </div>
                                                        </div>
                                                    `;

            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '1060';
                document.body.appendChild(container);
            }

            container.insertAdjacentHTML('beforeend', toastHtml);
            const toast = container.lastElementChild;
            new bootstrap.Toast(toast).show();

            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            // Nếu có lỗi server sau khi submit, chuyển tới step chứa lỗi đầu
            const serverErrorFields = @json($errors->keys());
            if (serverErrorFields.length) {
                const map = fieldsByStep;
                currentStep = 1;
                for (const [step, fields] of Object.entries(map)) {
                    if (fields.some(f => serverErrorFields.includes(f))) {
                        currentStep = parseInt(step); break;
                    }
                }
                document.querySelector('.card-body')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // Khởi tạo hiển thị
            updateDuration?.();
            updateStepDisplay();

            document.getElementById('MaCode')?.addEventListener('input', updatePreview);
            document.getElementById('PhanTram')?.addEventListener('input', updatePreview);
        });

        // Submit: hiệu ứng loading
        document.getElementById('voucherForm').addEventListener('submit', function () {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
            submitBtn.disabled = true;
        });

        function syncDateInputs() {
                try {
                    if (window.fpStart) {
                        const altV = window.fpStart.altInput?.value?.trim();
                        // nếu input thật đang rỗng mà alt có dữ liệu, parse theo dd/mm/yyyy
                        if (!window.fpStart.input.value && altV) {
                            window.fpStart.setDate(altV, true, 'd/m/Y');
                        }
                    }
                    if (window.fpEnd) {
                        const altV = window.fpEnd.altInput?.value?.trim();
                        if (!window.fpEnd.input.value && altV) {
                            window.fpEnd.setDate(altV, true, 'd/m/Y');
                        }
                    }
                } catch (e) {
                    // ignore
                }
            }

            async function validateServerStep(step) {
                clearAjaxErrors(step);

                // QUAN TRỌNG: sync trước khi lấy FormData
                syncDateInputs();

                const form = document.getElementById('voucherForm');
                const formData = new FormData(form);
                formData.append('step', step);

                try {
                    const res = await fetch('{{ route('admin.vouchers.validateStep') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken(),
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    if (res.ok) return true;

                    if (res.status === 422) {
                        const data = await res.json();
                        showAjaxErrors(data.errors);
                        const first = Object.keys(data.errors)[0];
                        document.querySelector(`[name="${first}"]`)?.focus();
                        return false;
                    }

                    showToast('Không thể kiểm tra hợp lệ. Vui lòng thử lại!', 'danger');
                    return false;
                } catch (e) {
                    showToast('Lỗi kết nối máy chủ. Vui lòng thử lại!', 'danger');
                    return false;
                }
            }

            // Sửa preset nhanh: dùng flatpickr API thay vì gán .value
            function setDuration(days) {
                const start = new Date();
                const end = new Date(start.getTime() + days * 24 * 60 * 60 * 1000);

                if (window.fpStart) {
                    window.fpStart.setDate(start, true, 'Y-m-d');
                } else {
                    document.getElementById('NgayBatDau').value = start.toISOString().split('T')[0];
                }

                if (window.fpEnd) {
                    window.fpEnd.setDate(end, true, 'Y-m-d');
                } else {
                    document.getElementById('NgayKetThuc').value = end.toISOString().split('T')[0];
                }

                updateDuration();
            }

            // Đọc từ selectedDates (nếu có) để tính chính xác
            function updateDuration() {
                let start, end;

                if (window.fpStart?.selectedDates?.length) {
                    start = window.fpStart.selectedDates[0];
                }
                if (window.fpEnd?.selectedDates?.length) {
                    end = window.fpEnd.selectedDates[0];
                }

                // Fallback: đọc từ input thật (Y-m-d)
                if (!start) {
                    const s = document.getElementById('NgayBatDau')?.value;
                    if (s) start = new Date(s);
                }
                if (!end) {
                    const e = document.getElementById('NgayKetThuc')?.value;
                    if (e) end = new Date(e);
                }

                if (start && end && !Number.isNaN(start) && !Number.isNaN(end)) {
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    if (diffDays >= 0) {
                        document.getElementById('durationInfo').style.display = 'block';
                        document.getElementById('durationText').textContent = `Voucher sẽ có hiệu lực trong ${diffDays} ngày`;
                    }

                    const startStr = start.toLocaleDateString('vi-VN');
                    const endStr = end.toLocaleDateString('vi-VN');
                    document.getElementById('summaryDuration').textContent = `${startStr} - ${endStr}`;
                    document.getElementById('summaryDays').textContent = `${diffDays} ngày`;
                    document.getElementById('previewDates').innerHTML = `<i class="fas fa-calendar"></i> ${startStr} - ${endStr}`;
                }

                if (typeof updatePreview === 'function') {
                    // updatePreview cũng đã render code/discount; phần ngày đã set ở trên
                    const code = document.getElementById('MaCode').value || 'VOUCHER-CODE';
                    const discount = document.getElementById('PhanTram').value || '0';
                    document.getElementById('previewCode').textContent = code;
                    document.getElementById('previewDiscount').textContent = discount + '%';
                    document.getElementById('summaryCode').textContent = code;
                    document.getElementById('summaryDiscount').textContent = discount + '%';
                }
            }

            // Trước khi submit: sync để tránh required
            document.getElementById('voucherForm').addEventListener('submit', function () {
                syncDateInputs();
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
                submitBtn.disabled = true;
            });

            // (Tùy chọn) Nếu old('NgayBatDau/KetThuc') đang ở dạng dd/mm/yyyy thì normalize lại khi load
            document.addEventListener('DOMContentLoaded', function () {
                const s = document.getElementById('NgayBatDau')?.value;
                if (window.fpStart && /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s || '')) {
                    window.fpStart.setDate(s, true, 'd/m/Y');
                }
                const e = document.getElementById('NgayKetThuc')?.value;
                if (window.fpEnd && /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(e || '')) {
                    window.fpEnd.setDate(e, true, 'd/m/Y');
                }
            });
    </script>
    <script>
        // Đọc min/max từ data-attributes, fallback min=1, max=100
        function getBounds(el) {
            const min = parseInt(el.dataset.min, 10);
            const max = parseInt(el.dataset.max, 10);
            return {
                min: Number.isFinite(min) ? min : 1,
                max: Number.isFinite(max) ? max : 100
            };
        }

        function stepField(id, delta) {
            const el = document.getElementById(id);
            if (!el) return;
            const { min, max } = getBounds(el);

            let v = parseInt(el.value, 10);
            if (Number.isNaN(v)) {
                // Nếu đang là text không phải số, khi bấm step sẽ nhảy về min/max tùy hướng
                v = delta > 0 ? min : max;
            } else {
                v += delta;
            }
            // Áp min/max
            v = Math.min(max, Math.max(min, v));

            el.value = String(v);
            // Kích hoạt các hook hiện có (preview/validate AJAX)
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
            // Focus để người dùng thấy ngay
            el.focus();
            // Nếu có updatePreview() thì gọi
            if (typeof updatePreview === 'function') updatePreview();
        }

        // Hỗ trợ phím mũi tên lên/xuống để step
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('PhanTram');
            if (!el) return;

            el.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    stepField('PhanTram', +1);
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    stepField('PhanTram', -1);
                }
            });

            // (Tùy chọn) Lăn chuột để step khi đang focus
            el.addEventListener('wheel', function (e) {
                if (document.activeElement !== el) return;
                e.preventDefault();
                const delta = e.deltaY < 0 ? +1 : -1;
                stepField('PhanTram', delta);
            }, { passive: false });
        });
    </script>
@endsection
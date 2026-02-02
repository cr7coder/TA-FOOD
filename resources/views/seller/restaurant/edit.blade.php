@extends('seller.layout')
@section('title', 'Sửa thông tin nhà hàng')

@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin nhà hàng</h6>
                <a href="{{ route('seller.foods.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách món
                </a>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success py-2 auto-dismiss">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning py-2 auto-dismiss">{{ session('warning') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2 auto-dismiss">{{ session('error') }}</div>
                @endif

                <form id="restaurantForm" action="{{ route('seller.restaurant.update') }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-6">
                        <label class="form-label">Tên nhà hàng</label>
                        <input type="text" name="TenNhaHang" class="form-control @error('TenNhaHang') is-invalid @enderror"
                            maxlength="150" value="{{ old('TenNhaHang', $restaurant->TenNhaHang) }}">
                        {{-- @error('TenNhaHang') <div class="invalid-feedback">{{ $message }}</div> @enderror --}}
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="SoDienThoai"
                            class="form-control @error('SoDienThoai') is-invalid @enderror"
                            placeholder="VD: 0912345678 hoặc +84912345678"
                            value="{{ old('SoDienThoai', $restaurant->SoDienThoai) }}">
                        {{-- @error('SoDienThoai') <div class="invalid-feedback">{{ $message }}</div> @enderror --}}
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="DiaChi" class="form-control @error('DiaChi') is-invalid @enderror"
                            maxlength="255" value="{{ old('DiaChi', $restaurant->DiaChi) }}">
                        {{-- @error('DiaChi') <div class="invalid-feedback">{{ $message }}</div> @enderror --}}
                    </div>

                    <!-- Giờ mở cửa -->
                    <div class="col-md-3">
                        <label class="form-label">Giờ mở cửa</label>
                        <div class="input-group">
                            <input type="text" name="GioMoCua" class="form-control" placeholder="HH:mm"
                                value="{{ old('GioMoCua', $restaurant->GioMoCua ? \Illuminate\Support\Str::substr($restaurant->GioMoCua, 0, 5) : '') }}">
                            <button type="button" class="btn btn-outline-secondary time-btn" data-target="GioMoCua"
                                title="Chọn giờ">
                                <i class="fas fa-clock"></i>
                            </button>
                            <!-- proxy ẩn cho flatpickr -->
                            <input type="text" id="GioMoCua_picker" class="flatpickr-proxy" autocomplete="off"
                                style="position:absolute;left:-9999px;width:0;height:0;opacity:0;border:0;padding:0;" />
                        </div>
                    </div>

                    <!-- Giờ đóng cửa -->
                    <div class="col-md-3">
                        <label class="form-label">Giờ đóng cửa</label>
                        <div class="input-group">
                            <input type="text" name="GioDongCua" class="form-control" placeholder="HH:mm"
                                value="{{ old('GioDongCua', $restaurant->GioDongCua ? \Illuminate\Support\Str::substr($restaurant->GioDongCua, 0, 5) : '') }}">
                            <button type="button" class="btn btn-outline-secondary time-btn" data-target="GioDongCua"
                                title="Chọn giờ">
                                <i class="fas fa-clock"></i>
                            </button>
                            <!-- proxy ẩn cho flatpickr -->
                            <input type="text" id="GioDongCua_picker" class="flatpickr-proxy" autocomplete="off"
                                style="position:absolute;left:-9999px;width:0;height:0;opacity:0;border:0;padding:0;" />
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
                @push('scripts')
                    <script src="{{ asset('js/seller-restaurant-validator.js') }}"></script>
                @endpush
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.auto-dismiss').forEach(function (alert) {
                setTimeout(function () {
                    alert.style.transition = 'opacity .5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function () { alert.remove(); }, 500);
                }, 2000);
            });
        });
    </script>
@endsection
@push('scripts')
    <!-- Flatpickr (Time picker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chuẩn hóa HH:mm nếu DB trả HH:mm:ss (chỉ để hiển thị đẹp; không auto-sửa người dùng)
            ['GioMoCua', 'GioDongCua'].forEach((name) => {
                const vis = document.querySelector(`[name="${name}"]`);
                if (!vis) return;
                const m = (vis.value || '').match(/^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/);
                if (m) vis.value = `${m[1]}:${m[2]}`;
            });

            const instances = {};
            const baseCfg = {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',    // kết quả picker -> HH:mm
                time_24hr: true,
                minuteIncrement: 5,
                allowInput: false,    // không cho gõ trong proxy
                disableMobile: true,  // luôn dùng UI flatpickr, không dùng native
                clickOpens: false,    // QUAN TRỌNG: KHÔNG tự mở khi click vào proxy/input
                onChange: function (selDates, dateStr, fp) {
                    // Đồng bộ giá trị đã chọn HH:mm -> input hiển thị
                    const target = fp.element.__visibleTarget;
                    if (target && dateStr) target.value = dateStr;
                },
                onReady: function (selDates, dateStr, fp) {
                    // Chắc chắn proxy vẫn là text (phòng trường hợp môi trường đổi)
                    try { fp.input.setAttribute('type', 'text'); } catch (e) { }
                }
            };

            ['GioMoCua', 'GioDongCua'].forEach((name) => {
                const vis = document.querySelector(`[name="${name}"]`);
                const proxy = document.getElementById(`${name}_picker`);
                if (!vis || !proxy) return;

                // Proxy ẩn trong DOM (đừng đẩy ra ngoài màn hình để tránh popup render off-screen)
                proxy.style.position = 'absolute';
                proxy.style.width = '1px';
                proxy.style.height = '1px';
                proxy.style.opacity = '0';
                proxy.style.pointerEvents = 'none';

                // Popup định vị theo input hiển thị, render vào body
                const cfg = Object.assign({}, baseCfg, {
                    positionElement: vis,
                    appendTo: document.body
                });

                const fp = flatpickr(proxy, cfg);
                fp.element.__visibleTarget = vis;
                instances[name] = fp;

                // Nút mở picker
                const btn = vis.parentElement.querySelector(`.time-btn[data-target="${name}"]`);
                if (btn) {
                    btn.addEventListener('click', () => {
                        // Nếu input hiển thị đang có HH:mm thì preset cho picker
                        const v = (vis.value || '').trim();
                        const m = v.match(/^([01]?\d|2[0-3]):([0-5]\d)$/);
                        if (m) {
                            const hh = parseInt(m[1], 10), mm = parseInt(m[2], 10);
                            fp.setDate(new Date(1970, 0, 1, hh, mm, 0), false, 'H:i');
                        }
                        // Đảm bảo popup định vị theo input hiển thị rồi mở
                        if (typeof fp.setPositionElement === 'function') {
                            fp.setPositionElement(vis);
                        } else {
                            fp._positionElement = vis;
                        }
                        fp.open();
                    });
                }

                // KHÔNG mở khi focus vào input hiển thị
                // Nếu trước đó bạn có thêm: vis.addEventListener('focus', () => fp.open());
                // hãy xoá nó, ở đây không thêm handler focus nữa.
            });

            // (Tùy chọn) tăng z-index nếu bị che bởi layout
            const style = document.createElement('style');
            style.textContent = '.flatpickr-calendar { z-index: 1060 !important; }';
            document.head.appendChild(style);
        });
    </script>
@endpush
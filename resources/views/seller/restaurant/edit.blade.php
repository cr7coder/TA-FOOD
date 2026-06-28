@extends('seller.layouts.app')
@section('title', $restaurant->exists ? 'Sửa thông tin nhà hàng' : 'Đăng ký thông tin cửa hàng')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #sellerMap {
            height: 320px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
            margin-top: 10px;
            z-index: 1;
        }
        #googleMapsPaste {
            border: 1px dashed #3b82f6 !important;
            background-color: rgba(59, 130, 246, 0.02);
            transition: all 0.3s ease;
        }
        #googleMapsPaste:focus {
            border-color: #2563eb !important;
            background-color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid pb-5">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ $restaurant->exists ? 'Thông tin nhà hàng' : 'Đăng ký cửa hàng mới' }}</h6>
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

                @if(!$restaurant->exists)
                    <div class="alert alert-info border-left-info py-3 mb-4">
                        <i class="fas fa-info-circle fa-lg mr-2 text-info"></i>
                        <strong>Thông báo:</strong> Bạn chưa đăng ký thông tin cửa hàng, mời đăng ký để tham gia bán hàng!
                    </div>
                @endif

                <form id="restaurantForm" action="{{ route('api.seller.restaurant.update') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')

                    <!-- Restaurant Image Upload Section -->
                    <div class="col-12 mb-3">
                        <div class="p-3 border rounded bg-light d-flex align-items-center gap-3 flex-wrap" style="border-radius: 12px !important; border: 1px dashed #3b82f6 !important; background-color: rgba(59, 130, 246, 0.01) !important;">
                            <div class="restaurant-image-preview-container" style="width: 120px; height: 120px; border-radius: 12px; border: 2px solid #dee2e6; overflow: hidden; background: #e9ecef; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                <img id="restaurantImagePreview" src="{{ $restaurant->hinh_anh_url }}" alt="Restaurant Image" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <label class="form-label font-weight-bold" style="font-size: 14px; color: #1f2937;">Hình ảnh nhà hàng</label>
                                <input type="file" name="HinhAnh" id="restaurantImageInput" class="form-control form-control-sm" accept="image/*" style="max-width: 280px; padding: 6px 12px; height: auto; font-size: 13px;">
                                <small class="form-text text-muted mt-1" style="font-size: 12px;">Hình ảnh hiển thị đại diện cho nhà hàng trên trang chủ. Hỗ trợ JPG, PNG, GIF (Tối đa 2MB).</small>
                                <div class="invalid-feedback" id="HinhAnh-error" style="display: block;"></div>
                            </div>
                        </div>
                    </div>

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
                    <div class="col-md-6">
                        <label class="form-label">Email nhà hàng</label>
                        <input type="email" name="Email" class="form-control"
                            placeholder="VD: contact@nhahang.com"
                            value="{{ old('Email', $restaurant->Email) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Địa chỉ</label>
                        <div class="input-group">
                            <input type="text" id="addressInput" name="DiaChi" class="form-control @error('DiaChi') is-invalid @enderror"
                                maxlength="255" value="{{ old('DiaChi', $restaurant->DiaChi) }}">
                            <button type="button" id="syncAddressBtn" class="btn btn-outline-primary" title="Định vị địa chỉ này trên bản đồ">
                                <i class="fas fa-search-location"></i> Tìm vị trí
                            </button>
                            <button type="button" id="getCurrentLocationBtn" class="btn btn-success text-white font-weight-bold" style="border-top-right-radius: .25rem; border-bottom-right-radius: .25rem; z-index: 0;" title="Lấy vị trí GPS hiện tại của bạn">
                                <i class="fas fa-crosshairs"></i> Vị trí của tôi
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <label class="form-label text-primary font-weight-bold">
                            <i class="fab fa-google"></i> Dán liên kết Google Maps hoặc Tọa độ
                        </label>
                        <input type="text" id="googleMapsPaste" class="form-control"
                            placeholder="Dán link chia sẻ từ Google Maps, link trình duyệt, mã iframe hoặc tọa độ (VD: 21.0285, 105.8048) để tự động định vị...">
                        <small class="form-text text-muted">Hệ thống sẽ tự động phân tích liên kết/tọa độ, cập nhật bản đồ và tra cứu địa chỉ thực tế của bạn.</small>
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

                    <!-- Cấu hình vận chuyển -->
                    <div class="col-12 mt-4">
                        <div class="card border-left-success shadow-sm">
                            <div class="card-header bg-success text-white py-2">
                                <h6 class="m-0 font-weight-bold"><i class="fas fa-shipping-fast mr-1"></i> CẤU HÌNH VẬN CHUYỂN & ĐỊNH VỊ</h6>
                            </div>
                            <div class="card-body row g-3">
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold">Vĩ độ (Latitude)</label>
                                    <input type="text" id="latitudeInput" name="latitude" class="form-control"
                                        placeholder="VD: 21.081827" value="{{ old('latitude', $restaurant->latitude ?? '21.081827') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold">Kinh độ (Longitude)</label>
                                    <input type="text" id="longitudeInput" name="longitude" class="form-control"
                                        placeholder="VD: 105.842790" value="{{ old('longitude', $restaurant->longitude ?? '105.842790') }}">
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label class="form-label font-weight-bold text-success">
                                        <i class="fas fa-map-marked-alt"></i> Bản đồ vị trí cửa hàng
                                    </label>
                                    <div id="sellerMap"></div>
                                    <small class="form-text text-muted mt-1">Kéo thả ghim màu xanh trên bản đồ để căn chỉnh vị trí chính xác của cửa hàng.</small>
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-label font-weight-bold">Phí ship cơ bản (VND)</label>
                                    <input type="number" name="phi_ship_co_ban" class="form-control"
                                        min="0" placeholder="VD: 15000" value="{{ old('phi_ship_co_ban', (int)($restaurant->phi_ship_co_ban ?? 15000)) }}">
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-label font-weight-bold">Phí ship mỗi km tiếp theo (VND)</label>
                                    <input type="number" name="phi_ship_moi_km" class="form-control"
                                        min="0" placeholder="VD: 5000" value="{{ old('phi_ship_moi_km', (int)($restaurant->phi_ship_moi_km ?? 5000)) }}">
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-label font-weight-bold">Khoảng cách miễn phí ship (km)</label>
                                    <input type="number" name="km_mien_phi" class="form-control" step="0.1"
                                        min="0" placeholder="VD: 2.0" value="{{ old('km_mien_phi', $restaurant->km_mien_phi ?? '2.0') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4 d-flex justify-content-end">
                        <button class="btn btn-primary px-4">
                            <i class="fas fa-{{ $restaurant->exists ? 'save' : 'plus-circle' }} mr-1"></i> 
                            {{ $restaurant->exists ? 'Lưu thay đổi' : 'Đăng ký cửa hàng' }}
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
                    if (target && dateStr) {
                        target.value = dateStr;
                        target.dispatchEvent(new Event('input', { bubbles: true }));
                        target.dispatchEvent(new Event('change', { bubbles: true }));
                    }
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

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Leaflet Map Initialization
            const latInput = document.getElementById('latitudeInput');
            const lngInput = document.getElementById('longitudeInput');
            const addressInput = document.getElementById('addressInput');
            const googlePasteInput = document.getElementById('googleMapsPaste');
            let lastGeocodedAddress = addressInput.value || "";

            // Default coordinates if empty (Hanoi coordinates)
            let defaultLat = parseFloat(latInput.value) || 21.028511;
            let defaultLng = parseFloat(lngInput.value) || 105.804817;

            // Initialize Map
            const map = L.map('sellerMap').setView([defaultLat, defaultLng], 15);

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Create Draggable Marker
            let marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(map);

            // Fetch address from Nominatim (Reverse Geocoding)
            async function fetchAddress(lat, lng) {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=vi`);
                    const data = await response.json();
                    return data.display_name || `${lat}, ${lng}`;
                } catch (e) {
                    console.error("Reverse geocoding error:", e);
                    return `${lat}, ${lng}`;
                }
            }

            // Update marker and map position
            function updateMapAndMarker(lat, lng, address = '', zoom = 15) {
                latInput.value = parseFloat(lat).toFixed(6);
                lngInput.value = parseFloat(lng).toFixed(6);
                
                const newLatLng = new L.LatLng(lat, lng);
                marker.setLatLng(newLatLng);
                map.setView(newLatLng, zoom);

                if (address) {
                    marker.bindPopup(`<strong style="color: #3b82f6;">📍 Vị trí:</strong><br>${address}`).openPopup();
                } else {
                    marker.bindPopup(`<strong style="color: #3b82f6;">📍 Tọa độ:</strong><br>${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}`).openPopup();
                }
            }

            // Event: Marker dragend
            marker.on('dragend', async function (event) {
                const position = marker.getLatLng();
                const lat = position.lat;
                const lng = position.lng;
                
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);

                marker.bindPopup(`<strong style="color: #3b82f6;">📍 Đang tìm địa chỉ...</strong>`).openPopup();
                
                const resolvedAddress = await fetchAddress(lat, lng);
                lastGeocodedAddress = resolvedAddress;
                addressInput.value = resolvedAddress;
                
                marker.bindPopup(`<strong style="color: #3b82f6;">📍 Vị trí ghim mới:</strong><br>${resolvedAddress}`).openPopup();
            });

            // Event: Map click to place marker
            map.on('click', async function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                updateMapAndMarker(lat, lng);
                marker.bindPopup(`<strong style="color: #3b82f6;">📍 Đang tìm địa chỉ...</strong>`).openPopup();
                
                const resolvedAddress = await fetchAddress(lat, lng);
                lastGeocodedAddress = resolvedAddress;
                addressInput.value = resolvedAddress;
                
                marker.bindPopup(`<strong style="color: #3b82f6;">📍 Vị trí:</strong><br>${resolvedAddress}`).openPopup();
            });

            // Event: Coordinate inputs manually modified
            function handleCoordinateChange() {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    updateMapAndMarker(lat, lng, '', map.getZoom());
                }
            }
            latInput.addEventListener('change', handleCoordinateChange);
            lngInput.addEventListener('change', handleCoordinateChange);

            // Get Current Location via Geolocation API
            document.getElementById('getCurrentLocationBtn')?.addEventListener('click', function() {
                const btn = this;
                if (navigator.geolocation) {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Đang định vị...`;
                    btn.disabled = true;

                    navigator.geolocation.getCurrentPosition(async (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        updateMapAndMarker(lat, lng);
                        marker.bindPopup(`<strong style="color: #3b82f6;">📍 Đang tìm địa chỉ...</strong>`).openPopup();

                        const resolvedAddress = await fetchAddress(lat, lng);
                        lastGeocodedAddress = resolvedAddress;
                        addressInput.value = resolvedAddress;

                        marker.bindPopup(`<strong style="color: #3b82f6;">📍 Vị trí hiện tại:</strong><br>${resolvedAddress}`).openPopup();

                        Swal.fire({
                            title: 'Thành công',
                            text: 'Đã xác định vị trí hiện tại của bạn!',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }, (error) => {
                        console.warn("Lỗi định vị: " + error.message);
                        Swal.fire({
                            title: 'Lỗi định vị',
                            text: 'Không thể lấy được vị trí GPS. Vui lòng kiểm tra quyền truy cập vị trí trên trình duyệt.',
                            icon: 'error',
                            confirmButtonText: 'Đóng'
                        });
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    });
                } else {
                    Swal.fire({
                        title: 'Không hỗ trợ',
                        text: 'Trình duyệt của bạn không hỗ trợ định vị GPS.',
                        icon: 'warning',
                        confirmButtonText: 'Đóng'
                    });
                }
            });

            // Debounced Address Geocoding (OpenStreetMap Nominatim) with Smart Fallbacks
            let geocodeTimeout = null;
            async function geocodeAddressValue(address) {
                if (!address || address.trim().length <= 3) return;

                async function queryNominatim(q, delayMs = 0) {
                    if (delayMs > 0) {
                        await new Promise(resolve => setTimeout(resolve, delayMs));
                    }
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1&countrycodes=vn`);
                        if (!response.ok) return null;
                        const data = await response.json();
                        return (data && data.length > 0) ? data[0] : null;
                    } catch (e) {
                        return null;
                    }
                }

                try {
                    let result = await queryNominatim(address);

                    // Fallback 1: Split by comma and strip the most specific part
                    let currentQuery = address;
                    if (!result && currentQuery.includes(',')) {
                        const parts = currentQuery.split(',');
                        if (parts.length > 1) {
                            const fallbackQuery = parts.slice(1).join(',').trim();
                            result = await queryNominatim(fallbackQuery, 1000);
                        }
                    }

                    // Fallback 2: Strip specific prefixes like "Ngõ", "Ngách", "Hẻm", "Số", "Số nhà", "Thôn", "Xóm"
                    if (!result) {
                        let cleaned = currentQuery
                            .replace(/^(Số nhà|Số|Ngõ|Ngách|Hẻm|Thôn|Xóm|Tổ)\s+[0-9a-zA-Z\/\-\s]+/i, '')
                            .trim();
                        if (cleaned && cleaned !== currentQuery) {
                            result = await queryNominatim(cleaned, 1000);
                        }
                    }

                    // Fallback 3: Fallback directly to the last 2 words (usually district/province)
                    if (!result) {
                        const words = currentQuery.split(/\s+/);
                        if (words.length > 2) {
                            const fallbackQuery = words.slice(words.length - 2).join(' ').trim();
                            result = await queryNominatim(fallbackQuery, 1000);
                        }
                    }

                    if (result) {
                        const lat = parseFloat(result.lat);
                        const lng = parseFloat(result.lon);
                        lastGeocodedAddress = address;
                        updateMapAndMarker(lat, lng, address);
                    }
                } catch (e) {
                    console.warn("Geocoding address failed:", e);
                }
            }

            addressInput.addEventListener('input', function() {
                if (addressInput.value !== lastGeocodedAddress) {
                    clearTimeout(geocodeTimeout);
                    geocodeTimeout = setTimeout(() => {
                        geocodeAddressValue(addressInput.value);
                    }, 1200);
                }
            });
            addressInput.addEventListener('change', function() {
                if (addressInput.value !== lastGeocodedAddress) {
                    clearTimeout(geocodeTimeout);
                    geocodeAddressValue(addressInput.value);
                }
            });

            // Find location click handler
            document.getElementById('syncAddressBtn')?.addEventListener('click', function() {
                const address = addressInput.value.trim();
                if (address) {
                    geocodeAddressValue(address);
                }
            });

            // Google Maps URL / Coordinates Parser
            function parseGoogleMapsInput(input) {
                input = input.trim();
                if (!input) return null;

                // 1. Check for iframe embed code
                let iframeMatch = input.match(/src=["'](https:\/\/www\.google\.com\/maps\/embed[^"']+)["']/i);
                let url = iframeMatch ? iframeMatch[1] : input;

                // 2. Check for !2d (longitude) and !3d (latitude) (Google Maps embed format)
                let embedMatch = url.match(/!2d(-?\d+\.\d+)!3d(-?\d+\.\d+)/);
                if (embedMatch) {
                    return {
                        lat: parseFloat(embedMatch[2]),
                        lng: parseFloat(embedMatch[1])
                    };
                }

                // 3. Check for @lat,lng
                let atMatch = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
                if (atMatch) {
                    return {
                        lat: parseFloat(atMatch[1]),
                        lng: parseFloat(atMatch[2])
                    };
                }

                // 4. Check for q=lat,lng
                let qMatch = url.match(/[?&](q|query)=(-?\d+\.\d+),(-?\d+\.\d+)/);
                if (qMatch) {
                    return {
                        lat: parseFloat(qMatch[2]),
                        lng: parseFloat(qMatch[3])
                    };
                }

                // 5. Check for search/lat,lng
                let searchMatch = url.match(/\/search\/(-?\d+\.\d+),(-?\d+\.\d+)/);
                if (searchMatch) {
                    return {
                        lat: parseFloat(searchMatch[1]),
                        lng: parseFloat(searchMatch[2])
                    };
                }

                // 6. Check for raw coordinates: "lat, lng" or "lat lng"
                let rawMatch = url.match(/^(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)$/) || url.match(/^(-?\d+\.\d+)\s+(-?\d+\.\d+)$/);
                if (rawMatch) {
                    return {
                        lat: parseFloat(rawMatch[1]),
                        lng: parseFloat(rawMatch[2])
                    };
                }

                return null;
            }

            // Google Maps URL paste event handler
            googlePasteInput.addEventListener('input', async function() {
                let val = googlePasteInput.value.trim();
                if (!val) return;

                // Check if it looks like a shortened URL
                if (val.match(/https?:\/\/(maps\.app\.goo\.gl|goo\.gl\/maps)\/[A-Za-z0-9]+/i)) {
                    // Show loading
                    googlePasteInput.disabled = true;
                    googlePasteInput.classList.add('is-valid');
                    googlePasteInput.value = "⏳ Đang giải mã liên kết rút gọn...";
                    
                    try {
                        const response = await fetch("{{ route('api.seller.restaurant.resolve-url') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ url: val })
                        });
                        const result = await response.json();
                        if (result.success && result.final_url) {
                            val = result.final_url;
                        } else {
                            throw new Error(result.message || "Không giải mã được");
                        }
                    } catch (e) {
                        Swal.fire({
                            title: 'Lỗi',
                            text: 'Không thể giải mã liên kết rút gọn này. Bạn vui lòng thử dùng liên kết đầy đủ hoặc tọa độ.',
                            icon: 'error',
                            confirmButtonText: 'Đóng'
                        });
                        googlePasteInput.disabled = false;
                        googlePasteInput.classList.remove('is-valid');
                        googlePasteInput.value = "";
                        return;
                    } finally {
                        googlePasteInput.disabled = false;
                        googlePasteInput.classList.remove('is-valid');
                    }
                }

                const coords = parseGoogleMapsInput(val);
                if (coords) {
                    googlePasteInput.value = ""; // Clear input on success
                    
                    // Show popup loading on map
                    updateMapAndMarker(coords.lat, coords.lng);
                    marker.bindPopup(`<strong style="color: #3b82f6;">📍 Đang tìm địa chỉ...</strong>`).openPopup();

                    const resolvedAddress = await fetchAddress(coords.lat, coords.lng);
                    addressInput.value = resolvedAddress;
                    updateMapAndMarker(coords.lat, coords.lng, resolvedAddress);

                    Swal.fire({
                        title: 'Thành công',
                        text: 'Đã tự động xác định vị trí và điền địa chỉ cửa hàng của bạn!',
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    // Try parsing as raw address if not recognized as coords/URL
                    if (val.startsWith('http://') || val.startsWith('https://')) {
                        Swal.fire({
                            title: 'Cảnh báo',
                            text: 'Định dạng liên kết Google Maps chưa được hỗ trợ. Hãy dán liên kết chia sẻ hoặc tọa độ dạng số.',
                            icon: 'warning',
                            confirmButtonText: 'Đóng'
                        });
                    } else {
                        // Treat as text search query
                        addressInput.value = val;
                        googlePasteInput.value = "";
                        geocodeAddressValue(val);
                    }
                }
            });

            // Preview restaurant image
            const imgInput = document.getElementById('restaurantImageInput');
            const imgPreview = document.getElementById('restaurantImagePreview');
            if (imgInput && imgPreview) {
                imgInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(evt) {
                            imgPreview.src = evt.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Adjust leaflet rendering after page loads
            setTimeout(() => {
                map.invalidateSize();
            }, 500);
        });
    </script>
@endpush
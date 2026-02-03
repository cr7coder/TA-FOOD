<!DOCTYPE html>
<html>

<head>
    <!-- Basic -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(Auth::check())
        <meta name="user-id" content="{{ Auth::user()->MaNguoiDung }}">
        <meta name="user-name" content="{{ Auth::user()->HoTen }}">
        <meta name="user-role" content="{{ Auth::user()->VaiTro }}">
    @endif
    <title>TAFOOD</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css"
        integrity="PASTE_THE_EXACT_HASH_FROM_CDNJS_HERE" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Local Font Awesome (fallback) -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .header_section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: transparent;
            transition: .3s
        }

        .header_section.scrolled {
            background-color: rgba(34, 40, 49, .95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1)
        }

        .header-address-input {
            display: none;
            opacity: 0;
            transition: .3s;
            margin-left: 5px
        }

        .header-address-input.show {
            display: flex;
            opacity: 1
        }

        .header-address-input .form-control {
            background-color: rgba(212, 212, 212, .1);
            border: 1px solid #fff;
            color: #fff;
            width: 250px;
            font-size: 14px
        }

        .header-address-input .form-control::placeholder {
            color: rgba(255, 255, 255, .7)
        }

        .header-address-input .bi {
            color: rgba(255, 255, 255, .7)
        }

        .hero_area {
            padding-top: 80px
        }
    </style>
</head>

<body>
    @yield('content')

    <!-- footer section -->
    <footer class="footer_section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-col">
                    <div class="footer_contact">
                        <h4>Liên hệ với chúng tôi</h4>
                        <div class="contact_link_box">
                            <a href=""><i class="fa fa-map-marker" aria-hidden="true"></i><span>Địa chỉ: 175 Tây Sơn,
                                    Đống Đa, Hà Nội</span></a>
                            <a href=""><i class="fa fa-phone" aria-hidden="true"></i><span>Điện thoại +84
                                    123456789</span></a>
                            <a href=""><i class="fa fa-envelope"
                                    aria-hidden="true"></i><span>tafoods@gmail.com</span></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 footer-col">
                    <div class="footer_detail">
                        <a href="" class="footer-logo">TAFOOD</a>
                        <p>Trung tâm hỗ trợ</p>
                        <p>Câu hỏi thường gặp</p>
                        <p>Điều khoản và điều kiện</p>
                        <div class="footer_social">
                            <a href=""><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            <a href=""><i class="fa fa-twitter" aria-hidden="true"></i></a>
                            <a href=""><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                            <a href=""><i class="fa fa-instagram" aria-hidden="true"></i></a>
                            <a href=""><i class="fa fa-pinterest" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 footer-col">
                    <h4>Tải ứng dụng của chúng tôi</h4>
                    <img src="{{ asset('images/apple.png') }}" alt="">
                    <img src="{{ asset('images/android.png') }}" alt="">
                </div>
            </div>
            <div class="footer-info">
                <p>&copy; <span id="displayYear"></span> All Rights Reserved By <a
                        href="https://html.design/">TAFOOD</a><br><br>
                    &copy; <span id="displayYear"></span> Distributed By <a href="https://themewagon.com/"
                        target="_blank">Nguyễn Tuấn Anh</a></p>
            </div>
        </div>
    </footer>

    <!-- JS: ĐÚNG THỨ TỰ - CHỈ MỘT BẢN jQuery/Bootstrap -->
    <!-- 1) jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <!-- 2) Bootstrap bundle (kèm Popper) - dùng 1 bản -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- 3) jQuery plugins phụ thuộc vào jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>

    <!-- 4) Script của bạn (đã thêm guard để không lỗi khi plugin thiếu) -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <!-- 5) Google Maps: định nghĩa callback TRƯỚC khi nạp script -->
    <script>
        window.myMap = function () {
            var el = document.getElementById('googleMap');
            if (!el || !window.google || !google.maps) return;
            var center = { lat: 21.028511, lng: 105.804817 }; // Hà Nội mặc định
            var map = new google.maps.Map(el, {
                center: center,
                zoom: 13,
                mapTypeControl: false,
                streetViewControl: false
            });
            new google.maps.Marker({ position: center, map: map });
        };
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCh39n5U-4IoWpsVGUHWdqB6puEkhRLdmI&callback=myMap"></script>

    <!-- Các script khác (Leaflet, custom inline) -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const headerInput = document.getElementById("headerAddressInput");
            const mainInput = document.getElementById("addressInput");
            const mainLocation = document.getElementById("mainLocation");
            const headerSection = document.querySelector(".header_section");
            const headerAddressInput = document.querySelector(".header-address-input");

            async function fetchAddress(lat, lng) {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=vi`);
                    const data = await response.json();
                    return data.display_name || `${lat}, ${lng}`;
                } catch (e) {
                    alert("Không thể lấy địa chỉ từ API!");
                    return `${lat}, ${lng}`;
                }
            }

            function getLocationAndFillInputs() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(async (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const address = await fetchAddress(lat, lng);
                        if (headerInput) headerInput.value = address;
                        if (mainInput) mainInput.value = address;
                    }, (error) => {
                        alert("Không thể lấy vị trí: " + error.message);
                    });
                } else {
                    alert("Trình duyệt không hỗ trợ định vị!");
                }
            }

            function syncInputs(input1, input2) {
                if (!input1 || !input2) return;
                input1.addEventListener("input", () => input2.value = input1.value);
                input2.addEventListener("input", () => input1.value = input2.value);
            }
            if (headerInput && mainInput) syncInputs(headerInput, mainInput);

            window.addEventListener("scroll", () => {
                if (window.scrollY > 200) {
                    headerAddressInput?.classList.add("show");
                    headerSection?.classList.add("scrolled");
                    if (mainLocation) mainLocation.style.display = "none";
                } else {
                    headerAddressInput?.classList.remove("show");
                    headerSection?.classList.remove("scrolled");
                    if (mainLocation) mainLocation.style.display = "block";
                }
            });

            document.getElementById("getLocationBtn")?.addEventListener("click", () => getLocationAndFillInputs());
            document.getElementById("headerGetLocationBtn")?.addEventListener("click", () => getLocationAndFillInputs());
        });
    </script>
</body>

</html>
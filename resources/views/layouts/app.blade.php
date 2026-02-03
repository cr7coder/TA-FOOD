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
    <title>@yield('title', 'TAFOOD')</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .header_section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: rgba(34, 40, 49, .95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
            transition: .3s
        }

        body {
            padding-top: 80px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ffc107, #ff9800);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
            border-radius: 8px;
            padding: 8px 0;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all .2s;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 16px;
            margin-right: 10px;
        }

        .dropdown-header {
            padding: 10px 20px 5px;
            border-bottom: 1px solid #eee;
            margin-bottom: 5px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all .3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, .2);
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
            border-radius: 12px;
            transition: all .3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .text-primary {
            color: #007bff !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-info {
            background-color: #17a2b8 !important;
        }

        .bg-primary {
            background-color: #007bff !important;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }

        .badge-primary {
            background-color: #007bff;
            color: #fff;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Header -->
    <header class="header_section">
        <div class="container">
            <nav class="navbar navbar-expand-lg custom_nav-container">
                <a class="navbar-brand" href="{{ route('foods.index') }}">
                    <span style="color: white; font-size: 24px; font-weight: bold;">
                        TAFOOD
                    </span>
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class=""></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <div class="navbar-nav mx-auto">
                        <!-- Navigation items can be added here -->
                    </div>

                    <div class="quote_btn-container">
                        @if (!in_array(Route::currentRouteName(), ['orders.history', 'orders.show', 'reviews.create']))
                        <a href="{{ route('cart.index') }}" class="order_online" style="margin-right: 15px;">
                            <i class="fa fa-shopping-cart"></i> Giỏ hàng
                        </a>
                        @endif

                        @if(Auth::check())
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle user-dropdown" data-toggle="dropdown"
                                    style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                    <div class="user-avatar">
                                        {{ substr(Auth::user()->HoTen, 0, 1) }}
                                    </div>
                                    <span>{{ Auth::user()->HoTen }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-header">
                                        <strong>{{ Auth::user()->HoTen }}</strong>
                                        <small class="text-muted d-block">{{ Auth::user()->Email }}</small>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fa fa-user"></i> Thông tin cá nhân</a>
                                    <a class="dropdown-item" href="{{ route('orders.history') }}"><i class="fa fa-history"></i> Lịch sử đơn hàng</a>
                                    <a class="dropdown-item" href="#"><i class="fa fa-heart"></i> Món ăn yêu thích</a>
                                    <div class="dropdown-divider"></div>
                                    @if(Auth::user()->isAdmin())
                                        <a class="dropdown-item" href="#"><i class="fa fa-cog"></i> Quản trị</a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fa fa-sign-out-alt"></i> Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="order_online">
                                <i class="fa fa-sign-in-alt"></i> Đăng nhập/Đăng ký
                            </a>
                        @endif
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Content -->
    <main>
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer_section" style="margin-top: 50px;">
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
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info_detail">
                                <h4>Giới thiệu</h4>
                                <p>
                                    TAFOOD - Nền tảng đặt món ăn trực tuyến hàng đầu, mang đến cho bạn những trải
                                    nghiệm ẩm thực tuyệt vời.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info_detail">
                                <h4>Kết nối với chúng tôi</h4>
                                <div class="info_social">
                                    <a href=""><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                    <a href=""><i class="fa fa-twitter" aria-hidden="true"></i></a>
                                    <a href=""><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                                    <a href=""><i class="fa fa-instagram" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="footer_text text-center">
                        <p>&copy; 2025 TAFOOD. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    @yield('scripts')
</body>

</html>
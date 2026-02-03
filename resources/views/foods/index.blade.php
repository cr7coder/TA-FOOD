@extends('foods.master')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed"
            style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
        </div>

        <script>
            // Auto hide success message after 5 seconds
            setTimeout(function () {
                const alert = document.querySelector('.alert-success');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 2000);
        </script>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed"
            style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <script>
            setTimeout(function () {
                const alert = document.querySelector('.alert-danger');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        </script>
    @endif
    <style>
        .user-dropdown {
            padding: 8px 15px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .user-dropdown:hover {
            background: rgba(255, 255, 255, 0.2);
            text-decoration: none !important;
            color: white !important;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ffbe33;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            min-width: 250px;
        }

        .dropdown-header {
            padding: 10px 20px;
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        /* Rating stars */
        .rating i {
            font-size: 14px;
        }

        .rating .fa-star {
            color: #ffc107;
        }

        .rating .fa-star-half-alt {
            color: #ffc107;
        }

        .rating .text-muted {
            color: #dee2e6 !important;
        }

        /* Food card hover effect */
        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .box {
            transition: all 0.3s ease;
        }
    </style>

    <!-- Floating Cart Button -->
    <div id="floatingCart" class="floating-cart">
        <div class="cart-icon">
            <i class="fa fa-shopping-cart"></i>
            <span class="cart-count" id="cartCount">0</span>
        </div>
    </div>

    <!-- Cart Popup Modal -->
    <div id="cartModal" class="cart-modal">
        <div class="cart-modal-content">
            <div class="cart-header">
                <h3><i class="fa fa-shopping-cart"></i> Giỏ hàng của bạn</h3>
                <span class="close-cart" id="closeCart">&times;</span>
            </div>
            <div class="cart-body" id="cartBody">
                <div class="empty-cart" id="emptyCart">
                    <i class="fa fa-shopping-cart fa-3x text-muted"></i>
                    <p>Giỏ hàng trống</p>
                    <small>Hãy thêm món ăn vào giỏ hàng!</small>
                </div>
                <div class="cart-items" id="cartItems" style="display: none;">
                    <!-- Cart items sẽ được load bằng JavaScript -->
                </div>
            </div>
            <div class="cart-footer" id="cartFooter" style="display: none;">
                <div class="cart-total">
                    <strong>Tổng cộng: <span id="cartTotal">0</span> đ</strong>
                </div>
                <div class="cart-actions">
                    <button class="btn btn-outline-secondary" onclick="clearCart()">
                        <i class="fa fa-trash"></i> Xóa tất cả
                    </button>
                    <button class="btn btn-warning" onclick="goToCheckout()">
                        <i class="fa fa-credit-card"></i> Thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="hero_area">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>
        <!-- header section strats -->
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="index.html">
                        <span>
                            CabaFood
                        </span>
                    </a>

                    <!-- Header Address Input (shortened) -->
                    <div class="header-address-input d-flex align-items-center">
                        <div class="input-group">
                            <input id="headerAddressInput" type="text" class="form-control"
                                placeholder="Địa chỉ giao hàng..."
                                style="border-radius: 20px; width: 250px; height: 38px; font-size: 13px;">
                            <div class="input-group-append">
                                <span class="input-group-text bg-transparent border-0 cursor-pointer"
                                    id="headerGetLocationBtn" style="padding: 0 8px;">
                                    <i class="bi bi-crosshair" style="font-size: 14px;"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class=""></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <div class="navbar-nav mx-auto">
                            <form class="form-inline my-2 my-lg-0" id="searchForm">
                                <div class="input-group" style="gap: 5px; display: flex; align-items: center;">
                                    <input type="text" class="form-control rounded-pill" id="searchInput"
                                        placeholder="Tìm món hoặc quán ăn..." aria-label="Search"
                                        style="width: 160px; height: 38px; font-size: 13px;">
                                    <button class="btn btn-outline-light rounded-pill" type="submit"
                                        style="height: 38px; padding: 0 12px; margin-left: 1px;">
                                        <i class="fa fa-search" style="font-size: 14px; color: white;"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="user_option">
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
                                        <a class="dropdown-item" href="{{ route('orders.history') }}"><i
                                                class="fa fa-history"></i> Lịch sử đơn hàng</a>
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
                                <a href="{{ route('login') }}" class="order_online" style="padding: 8px 20px; font-size: 13px;">
                                    <i class="fa fa-sign-in-alt"></i> Đăng nhập/Đăng ký
                                </a>
                            @endif
                        </div>

                    </div>
                </nav>
            </div>
        </header>
        <div id="mainLocation" class="location"
            style="padding: 30px; background-color: rgba(0, 0, 0, 0.5); margin: 20px auto; width: 50%; border-radius: 20px; z-index: 999;">
            <h4 style="color: white; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8); margin-bottom: 15px;">
                Nhập địa chỉ bạn muốn giao món
            </h4>
            <form class="d-flex" style="position: relative; width: 100%;">
                <!-- Icon Location đầu -->
                <i class="bi bi-geo-alt-fill"
                    style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: gray; font-size: 18px;"></i>

                <!-- Input -->
                <input id="addressInput" class="form-control rounded-pill" type="search" placeholder="Nhập địa chỉ..."
                    aria-label="Search" style="padding-left: 40px; padding-right: 40px;">

                <!-- Icon Target cuối -->
                <i id="getLocationBtn" class="bi bi-crosshair"
                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: gray; font-size: 18px;"></i>
            </form>
        </div>
    </div>

    <section class="offer_section layout_padding-bottom">
        <div class="offer_container">
            <div class="container ">
                <div class="row">
                    <div class="col-md-6  ">
                        <div class="box ">
                            <div class="img-box">
                                <img src="{{ asset('images/o1.jpg') }}" alt="">
                            </div>
                            <div class="detail-box">
                                <h5>
                                    Ngày mới
                                </h5>
                                <h6>
                                    <span>20%</span> Off
                                </h6>
                                <a href="">
                                    Mua ngay <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                        viewBox="0 0 456.029 456.029" style="enable-background:new 0 0 456.029 456.029;"
                                        xml:space="preserve">
                                        <g>
                                            <g>
                                                <path
                                                    d="M345.6,338.862c-29.184,0-53.248,23.552-53.248,53.248c0,29.184,23.552,53.248,53.248,53.248
                                                                                                                                                                                                                 c29.184,0,53.248-23.552,53.248-53.248C398.336,362.926,374.784,338.862,345.6,338.862z" />
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <path
                                                    d="M439.296,84.91c-1.024,0-2.56-0.512-4.096-0.512H112.64l-5.12-34.304C104.448,27.566,84.992,10.67,61.952,10.67H20.48
                                                                                                                                                                                                                 C9.216,10.67,0,19.886,0,31.15c0,11.264,9.216,20.48,20.48,20.48h41.472c2.56,0,4.608,2.048,5.12,4.608l31.744,216.064
                                                                                                                                                                                                                 c4.096,27.136,27.648,47.616,55.296,47.616h212.992c26.624,0,49.664-18.944,55.296-45.056l33.28-166.4
                                                                                                                                                                                                                 C457.728,97.71,450.56,86.958,439.296,84.91z" />
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <path
                                                    d="M215.04,389.55c-1.024-28.16-24.576-50.688-52.736-50.688c-29.696,1.536-52.224,26.112-51.2,55.296
                                                                                                                                                                                                                 c1.024,28.16,24.064,50.688,52.224,50.688h1.024C193.536,443.31,216.576,418.734,215.04,389.55z" />
                                            </g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6  ">
                        <div class="box ">
                            <div class="img-box">
                                <img src="{{ asset('images/o2.jpg') }}" alt="">
                            </div>
                            <div class="detail-box">
                                <h5>
                                    Pizza Days
                                </h5>
                                <h6>
                                    <span>15%</span> Off
                                </h6>
                                <a href="">
                                    Mua ngay <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                        viewBox="0 0 456.029 456.029" style="enable-background:new 0 0 456.029 456.029;"
                                        xml:space="preserve">
                                        <g>
                                            <g>
                                                <path
                                                    d="M345.6,338.862c-29.184,0-53.248,23.552-53.248,53.248c0,29.184,23.552,53.248,53.248,53.248
                                                                                                                                                                                                                 c29.184,0,53.248-23.552,53.248-53.248C398.336,362.926,374.784,338.862,345.6,338.862z" />
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <path
                                                    d="M439.296,84.91c-1.024,0-2.56-0.512-4.096-0.512H112.64l-5.12-34.304C104.448,27.566,84.992,10.67,61.952,10.67H20.48
                                                                                                                                                                                                                 C9.216,10.67,0,19.886,0,31.15c0,11.264,9.216,20.48,20.48,20.48h41.472c2.56,0,4.608,2.048,5.12,4.608l31.744,216.064
                                                                                                                                                                                                                 c4.096,27.136,27.648,47.616,55.296,47.616h212.992c26.624,0,49.664-18.944,55.296-45.056l33.28-166.4
                                                                                                                                                                                                                 C457.728,97.71,450.56,86.958,439.296,84.91z" />
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <path
                                                    d="M215.04,389.55c-1.024-28.16-24.576-50.688-52.736-50.688c-29.696,1.536-52.224,26.112-51.2,55.296
                                                                                                                                                                                                                 c1.024,28.16,24.064,50.688,52.224,50.688h1.024C193.536,443.31,216.576,418.734,215.04,389.55z" />
                                            </g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                        <g>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- end offer section -->
    <!-- danh sách món ăn -->
    <!-- food section -->
    <!-- food section -->
    <section class="food_section layout_padding-bottom">
        <div class="container">
            <div class="heading_container heading_center">
                <h2>Món ngon gần bạn</h2>
            </div>

            {{-- Menu filter theo Loại Món --}}
            <ul class="filters_menu">
                <li class="active" data-filter="*">Tất cả</li>
                <li data-filter=".com">Cơm</li>
                <li data-filter=".bun">Bún</li>
                <li data-filter=".pho">Phở</li>
                <li data-filter=".mi">Mì</li>
                <li data-filter=".tra">Trà</li>
            </ul>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-warning" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Đang tải...</span>
                </div>
                <p class="mt-3 text-muted">Đang tìm kiếm...</p>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                <i class="fa fa-exclamation-circle"></i> 
                <span id="errorText"></span>
                <button class="btn btn-warning mt-2" onclick="loadFoods(1, currentFilters)">Thử lại</button>
            </div>

            <div class="filters-content">
                <div class="row grid">
                    @foreach($foods as $food)
                        <div
                            class="col-sm-6 col-lg-4 all {{ $food->loai_mon_class }} {{ $food->TrangThai == 'Còn bán' ? 'con-ban' : 'ngung-ban' }}}">
                            <div class="box">
                                <div>
                                    <div class="img-box">
                                        <img src="{{ $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png') }}"
                                            alt="{{ $food->TenMonAn }}">
                                    </div>
                                    <div class="detail-box">
                                        <h5>
                                            <a href="{{ route('food.detail', $food->MaMonAn) }}"
                                                class="text-decoration-none text-light">
                                                {{ $food->TenMonAn }}
                                            </a>
                                        </h5>
                                        @if($food->MoTa)
                                            <p>{{ \Illuminate\Support\Str::limit($food->MoTa, 20, '...') }}</p>
                                        @endif
                                        <div class="rating mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($food->diem_trung_binh))
                                                    <i class="fa fa-star text-warning"></i>
                                                @elseif($i == ceil($food->diem_trung_binh) && $food->diem_trung_binh - floor($food->diem_trung_binh) >= 0.5)
                                                    <i class="fa fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="fa fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                            <small class="text-muted ms-1">({{ $food->tong_binh_luan }})</small>
                                        </div>
                                        <div class="options">
                                            <h6>{{ number_format($food->Gia, 0, ',', '.') }} đ</h6>
                                            <div class="d-flex gap-3 align-items-center">
                                                <a href="{{ route('food.detail', $food->MaMonAn) }}"
                                                    class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                    style="width: 45px; height: 45px; transition: all 0.3s ease;"
                                                    onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(255, 193, 7, 0.4)';"
                                                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if($food->TrangThai == 'Còn bán')
                                                    <button type="button"
                                                        class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm add-to-cart-btn"
                                                        style="width: 45px; height: 45px; transition: all 0.3s ease;"
                                                        data-id="{{ $food->MaMonAn }}"
                                                        data-name="{{ $food->TenMonAn }}" 
                                                        data-price="{{ $food->Gia }}"
                                                        data-image="{{ $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png') }}"
                                                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(255, 193, 7, 0.4)';"
                                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';">
                                                        <i class="fa fa-shopping-cart"></i>
                                                    </button>
                                                @else
                                                    <span class="badge bg-danger">Ngừng bán</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination Container for Search Results -->
                <div class="pagination-container mt-4"></div>
            </div>
        </div>
    </section>
    <!-- end food section -->

    <section class="restaurant_section layout_padding-bottom">
        <div class="container">
            <div class="heading_container heading_center mb-4">
                <h2>Quán ngon quanh đây</h2>
            </div>

            <div class="row">
                @forelse($restaurants as $res)
                    <div class="col-sm-6 col-lg-4 mb-4" style="--delay: {{ $loop->index * 80 }}ms;">
                        <div class="card restaurant-card h-100 shadow-sm">
                            <span class="rest-badge">
                                <i class="fa fa-utensils"></i> {{ $res->mon_an_count }} món
                            </span>

                            <div class="card-body d-flex flex-column">
                                <div class="rest-avatar" data-letter="{{ mb_substr($res->TenNhaHang, 0, 1, 'UTF-8') }}"></div>

                                <h5 class="card-title mb-2 rest-name" style="font-weight: 600;">{{ $res->TenNhaHang }}</h5>
                                <p class="card-text text-muted mb-1 rest-address">
                                    <i class="fa fa-map-marker"></i>
                                    {{ \Illuminate\Support\Str::limit($res->DiaChi, 80, '...') }}
                                </p>

                                @if($res->SoDienThoai)
                                    <p class="card-text text-muted rest-phone">
                                        <i class="fa fa-phone"></i> {{ $res->SoDienThoai }}
                                    </p>
                                @endif

                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <small class="text-muted"></small>
                                    <a href="{{ route('restaurants.show', $res->MaNhaHang) }}"
                                        class="btn btn-warning btn-sm rest-cta">
                                        <i class="fa fa-eye"></i> Xem món
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted">Chưa có nhà hàng nào.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>



    <!-- about section -->
    <section class="about_section layout_padding">
        <div class="container  ">

            <div class="row">
                <div class="col-md-6 ">
                    <div class="img-box">
                        <img src="{{ asset('images/about-img.png') }}" alt="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-box">
                        <div class="heading_container">
                            <h2>
                                We Are CabaFood
                            </h2>
                        </div>
                        <p>
                            Trang web đặt đồ ăn của chúng tôi được xây dựng nhằm mang đến cho bạn trải nghiệm ẩm
                            thực
                            tiện lợi và nhanh chóng ngay
                            tại nhà. Chỉ với vài thao tác đơn giản trên điện thoại hoặc máy tính, bạn có thể dễ dàng
                            lựa chọn hàng trăm món ăn hấp
                            dẫn từ nhiều nhà hàng uy tín khác nhau, từ cơm, bún, phở, mì cho đến các loại trà sữa,
                            trà đào, cà phê và đồ ăn vặt.
                            Chúng tôi cam kết cung cấp thực đơn đa dạng, giá cả minh bạch, hình ảnh món ăn rõ ràng
                            và thông tin chi tiết để bạn lựa
                            chọn đúng khẩu vị. Đặc biệt, hệ thống hỗ trợ tìm kiếm thông minh giúp bạn phân loại theo
                            món ăn, nhà hàng hoặc mức giá,
                            tiết kiệm thời gian đặt hàng. Mọi đơn hàng sẽ được xử lý nhanh chóng và giao tận nơi
                            đúng giờ, đảm bảo món ăn luôn nóng
                            hổi và tươi ngon. Với phương châm “Ăn ngon – Giao nhanh – Tiện lợi”, chúng tôi hy vọng
                            mang lại sự hài lòng tuyệt đối
                            cho mỗi khách hàng. Hãy để chúng tôi đồng hành cùng bạn trong từng bữa ăn ngon miệng mỗi
                            ngày.
                        </p>
                        <a href="">
                            Đọc thêm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- end about section -->

    <!-- client section -->

    <section class="client_section layout_padding-bottom">
        <div class="container">
            <div class="heading_container heading_center psudo_white_primary mb_45">
                <h2>
                    Bình luận
                </h2>
            </div>
            <div class="carousel-wrap row ">
                <div class="owl-carousel client_owl-carousel">
                    <div class="item">
                        <div class="box">
                            <div class="detail-box">
                                <p>
                                    Cơm đặt ở đây ngon lắm nha, ship cũng nhanh nữa. Sẽ ủng hộ dài dài.
                                </p>
                                <h6>
                                    Doraemon
                                </h6>
                            </div>
                            <div class="img-box">
                                <img src="{{ asset('images/client3.png') }}" alt="" class="box-img">
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="box">
                            <div class="detail-box">
                                <p>
                                    Giá tốt, món ăn đa dạng, nhân viên thân thiện. Rất hài lòng về dịch vụ.
                                </p>
                                <h6>
                                    Moana Michell
                                </h6>
                            </div>
                            <div class="img-box">
                                <img src="{{ asset('images/client1.jpg') }}" alt="" class="box-img">
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="box">
                            <div class="detail-box">
                                <p>
                                    Giao hàng nhanh, món ăn ngon, giá cả hợp lý. Rất hài lòng về dịch vụ.
                                </p>
                                <h6>
                                    Mike Hamell
                                </h6>
                            </div>
                            <div class="img-box">
                                <img src="{{ asset('images/client2.jpg') }}" alt="" class="box-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Floating Cart Styles */
        .floating-cart {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cart-icon {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            position: relative;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            animation: pulse 2s infinite;
        }

        .cart-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.6);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid white;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            }

            50% {
                box-shadow: 0 4px 25px rgba(255, 107, 53, 0.8);
            }

            100% {
                box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            }
        }

        /* Cart Modal Styles */
        /* Cart modal: giữ header/foot cố định, phần body cuộn */
        .cart-modal {
            display: none;
            position: fixed;
            z-index: 1001;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .cart-modal-content {
            position: absolute;
            right: 20px;
            top: 20px;
            /* Hiển thị theo cột: header - body (cuộn) - footer */
            display: flex;
            flex-direction: column;

            width: 400px;
            /* Cao tối đa còn chừa 20px trên + 20px phải + 20px dưới (tùy bạn), để footer luôn nằm trong */
            max-height: calc(100vh - 40px);
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            /* vẫn giữ để bo góc đẹp, phần cuộn đặt ở .cart-body */
        }

        .cart-header {
            flex: 0 0 auto;
            /* cố định chiều cao theo nội dung */
        }

        .cart-body {
            flex: 1 1 auto;
            /* chiếm toàn bộ phần còn lại */
            overflow-y: auto;
            /* phần này cuộn, footer không bị đẩy ra ngoài */
            padding: 20px;
            max-height: none !important;
            /* bỏ giới hạn 400px cũ nếu có */
        }

        .cart-footer {
            flex: 0 0 auto;
            /* cố định ở đáy modal content */
            padding: 20px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
        }

        /* Mobile responsive giữ nguyên ý tưởng flex */
        @media (max-width: 768px) {
            .cart-modal-content {
                width: 95%;
                right: 2.5%;
                left: auto;
                top: 20px;
                max-height: calc(100vh - 40px);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .cart-header {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .close-cart {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close-cart:hover {
            transform: scale(1.2);
        }

        .cart-body {
            max-height: 400px;
            overflow-y: auto;
            padding: 20px;
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-cart i {
            margin-bottom: 15px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: #ff6b35;
            font-weight: 600;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: #e9ecef;
        }

        .quantity {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        .remove-item {
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
        }

        .remove-item:hover {
            color: #c82333;
        }

        .cart-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
        }

        .cart-total {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .cart-actions {
            display: flex;
            gap: 10px;
        }

        .cart-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary {
            background: white;
            color: #6c757d;
            border: 1px solid #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .cart-modal-content {
                width: 95%;
                right: 2.5%;
            }

            .floating-cart {
                bottom: 20px;
                right: 20px;
            }

            .cart-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }

        /* Add to cart button animation */
        .add-to-cart-btn {
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover {
            transform: scale(1.1);
            background: #ff6b35 !important;
        }

        .add-to-cart-btn.adding {
            animation: addToCartPulse 0.6s ease;
        }

        @keyframes addToCartPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.3);
                background: #28a745;
            }

            100% {
                transform: scale(1);
            }
        }
    </style>

    <script>
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
                                                                                                                                                    @keyframes slideInRight {
                                                                                                                                                        from { transform: translateX(100%); opacity: 0; }
                                                                                                                                                        to { transform: translateX(0); opacity: 1; }
                                                                                                                                                    }
                                                                                                                                                    @keyframes slideOutRight {
                                                                                                                                                        from { transform: translateX(0); opacity: 1; }
                                                                                                                                                        to { transform: translateX(100%); opacity: 0; }
                                                                                                                                                    }
                                                                                                                                                `;
        document.head.appendChild(style);
    </script>
    
    <script>
        // Dropdown click outside to close
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.querySelector('.dropdown');
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            if (dropdownToggle && dropdownMenu) {
                // Toggle dropdown on click
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (dropdown && !dropdown.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>

    <!-- Load foods from RESTful API -->
    <script src="{{ asset('js/api-food-loader.js') }}"></script>
    
    {{--
    <script src="{{ asset('js/cart-validator.js') }}"></script> --}}
    @include('foods._cart-scripts')
@endsection
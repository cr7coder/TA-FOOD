@extends('foods.master')

@section('content')
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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed"
            style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function () {
                const alert = document.querySelector('.alert-success');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 3000);
        </script>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed"
            style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
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

    <div class="hero_area">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>
        <!-- header section strats -->
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="/">
                        <span>CabaFood</span>
                    </a>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <div class="user_option ml-auto">
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
                                        <a class="dropdown-item" href="{{ route('orders.history') }}"><i class="fa fa-list-alt"></i> Đơn hàng của tôi</a>
                                        <div class="dropdown-divider"></div>
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
    </div>

    <section class="food_detail_section layout_padding">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('foods.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $food->TenMonAn }}</li>
                </ol>
            </nav>

            <div class="row">
                <!-- Food Detail -->
                <div class="col-lg-6 mb-4">
                    <div class="food-image-container">
                        <img src="{{ $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png') }}"
                            alt="{{ $food->TenMonAn }}" class="img-fluid rounded shadow">
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="food-info">
                        <h1 class="food-title">{{ $food->TenMonAn }}</h1>

                        <div class="rating-overview mb-3">
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($food->diem_trung_binh))
                                        <i class="fa fa-star text-warning"></i>
                                    @elseif($i == ceil($food->diem_trung_binh) && $food->diem_trung_binh - floor($food->diem_trung_binh) >= 0.5)
                                        <i class="fa fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="fa fa-star text-muted"></i>
                                    @endif
                                @endfor
                                <span class="rating-text ms-2">
                                    {{ number_format($food->diem_trung_binh, 1) }}/5
                                    ({{ $food->tong_binh_luan }} đánh giá)
                                </span>
                            </div>
                        </div>

                        <div class="price mb-3">
                            <h3 class="text-warning">{{ number_format($food->Gia, 0, ',', '.') }} đ</h3>
                        </div>

                        @if($food->MoTa)
                            <div class="description mb-4">
                                <h5>Mô tả:</h5>
                                <p>{{ $food->MoTa }}</p>
                            </div>
                        @endif

                        <div class="restaurant-info mb-4">
                            <h5>Nhà hàng:</h5>
                            <div class="d-flex align-items-center">
                                <div class="restaurant-avatar me-3">
                                    {{ substr($food->nhaHang->TenNhaHang, 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ $food->nhaHang->TenNhaHang }}</strong>
                                    <p class="text-muted mb-0">{{ $food->nhaHang->DiaChi }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="food-actions">
                            @if($food->TrangThai == 'Còn bán')
                                <button type="button" class="btn btn-warning btn-lg add-to-cart-btn"
                                    data-id="{{ $food->MaMonAn }}" data-name="{{ $food->TenMonAn }}"
                                    data-price="{{ $food->Gia }}"
                                    data-image="{{ $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png') }}">
                                    <i class="fa fa-shopping-cart me-2"></i>
                                    Thêm vào giỏ hàng
                                </button>
                            @else
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="fa fa-times me-2"></i>
                                    Ngừng bán
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            {{-- <div class="reviews-section mt-5">
                <div class="row">
                    <div class="col-lg-6">
                        <h3 class="mb-4">Đánh giá & Bình luận</h3>

                        @if(Auth::check())
                        <!-- Review Form -->
                        <div class="review-form mb-5">
                            <h5>Viết đánh giá của bạn</h5>
                            <form action="{{ route('food.binh-luan', $food->MaMonAn) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <!-- Star Rating -->
                                <div class="form-group mb-3">
                                    <label>Đánh giá sao:</label>
                                    <div class="star-rating">
                                        @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="diem_danh_gia" value="{{ $i }}" id="star{{ $i }}"
                                            required>
                                        <label for="star{{ $i }}" class="star">★</label>
                                        @endfor
                                    </div>
                                    @error('diem_danh_gia')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Comment -->
                                <div class="form-group mb-3">
                                    <label for="noi_dung">Bình luận:</label>
                                    <textarea name="noi_dung" id="noi_dung" class="form-control" rows="4"
                                        placeholder="Chia sẻ trải nghiệm của bạn về món ăn này..."
                                        required>{{ old('noi_dung') }}</textarea>
                                    @error('noi_dung')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Image Upload -->
                                <div class="form-group mb-3">
                                    <label for="hinh_anh">Ảnh (không bắt buộc):</label>
                                    <input type="file" name="hinh_anh" id="hinh_anh" class="form-control" accept="image/*">
                                    @error('hinh_anh')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-paper-plane me-2"></i>
                                    Gửi đánh giá
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            <a href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá
                        </div>
                        @endif
                    </div> --}}

                    <div class="col-lg-6">
                        <!-- Reviews List -->
                        <div class="reviews-list">
                            @if($food->binhLuans->count() > 0)
                                @foreach($food->binhLuans as $binhLuan)
                                    <div class="review-item mb-4 p-3 border rounded">
                                        <div class="review-header d-flex justify-content-between align-items-start mb-2">
                                            <div class="reviewer-info d-flex align-items-center">
                                                <div class="reviewer-avatar me-3">
                                                    {{ substr($binhLuan->nguoiDung->HoTen, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $binhLuan->nguoiDung->HoTen }}</strong>
                                                    <div class="review-stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $binhLuan->diem_danh_gia)
                                                                <i class="fa fa-star text-warning"></i>
                                                            @else
                                                                <i class="fa fa-star text-muted"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $binhLuan->created_at->diffForHumans() }}</small>
                                        </div>

                                        <div class="review-content">
                                            <p>{{ $binhLuan->noi_dung }}</p>

                                            @if($binhLuan->hinh_anh)
                                                <div class="review-image mt-2">
                                                    <img src="{{ asset('images/' . $binhLuan->hinh_anh) }}" alt="Review image"
                                                        class="img-thumbnail" style="max-width: 200px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted">
                                    <i class="fa fa-comments fa-3x mb-3"></i>
                                    <p>Chưa có đánh giá nào cho món ăn này.</p>
                                    <p>Hãy là người đầu tiên đánh giá!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Related Foods -->
            @if($relatedFoods->count() > 0)
            <div class="related-foods-section mt-5">
                <h3 class="mb-4">Món ăn liên quan</h3>
                <div class="row">
                    @foreach($relatedFoods as $relatedFood)
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="box h-100">
                            <div class="img-box">
                                <img src="{{ $relatedFood->HinhAnh ? asset('images/' . $relatedFood->HinhAnh) : asset('images/no-image.png') }}"
                                    alt="{{ $relatedFood->TenMonAn }}">
                            </div>
                            <div class="detail-box">
                                <h6>
                                    <a href="{{ route('food.detail', $relatedFood->MaMonAn) }}"
                                        class="text-decoration-none">
                                        {{ $relatedFood->TenMonAn }}
                                    </a>
                                </h6>
                                <div class="options">
                                    <h6>{{ number_format($relatedFood->Gia, 0, ',', '.') }} đ</h6>
                                    <button type="button"
                                        class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center add-to-cart-btn"
                                        style="width: 40px; height: 40px;" data-id="{{ $relatedFood->MaMonAn }}"
                                        data-name="{{ $relatedFood->TenMonAn }}" data-price="{{ $relatedFood->Gia }}"
                                        data-image="{{ $relatedFood->HinhAnh ? asset('images/' . $relatedFood->HinhAnh) : asset('images/no-image.png') }}">
                                        <i class="fa fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section> --}}

    <style>
        .food_detail_section {
            min-height: 80vh;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #f7931e;
            text-decoration: none;
        }

        .food-image-container img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .food-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .rating-overview .stars i {
            font-size: 18px;
        }

        .rating-text {
            color: #666;
            font-size: 14px;
        }

        .restaurant-avatar,
        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f7931e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        .reviewer-avatar {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }

        /* Star Rating Form */
        .star-rating {
            direction: rtl;
            display: inline-block;
        }

        .star-rating input[type=radio] {
            display: none;
        }

        .star-rating label {
            color: #ddd;
            font-size: 30px;
            padding: 0 5px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input[type=radio]:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #ffb400;
        }

        .review-item {
            background: #f8f9fa;
            border: 1px solid #e9ecef !important;
        }

        .review-stars i {
            font-size: 14px;
        }

        .add-to-cart-btn {
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover {
            transform: scale(1.05);
            background: #ff6b35 !important;
        }

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
            display: flex;
            flex-direction: column;
            width: 400px;
            max-height: calc(100vh - 40px);
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .cart-header {
            flex: 0 0 auto;
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
            flex: 1 1 auto;
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
            flex: 0 0 auto;
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

        @media (max-width: 768px) {
            .cart-modal-content {
                width: 95%;
                right: 2.5%;
                left: auto;
                top: 20px;
                max-height: calc(100vh - 40px);
            }
        }
    </style>

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

    <!-- Load food detail from RESTful API -->
    <script src="{{ asset('js/api-food-detail.js') }}"></script>
    
    @include('foods._cart-scripts')
@endsection
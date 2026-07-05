<!-- header section starts -->
<header class="header_section">
    <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container">
            <a class="navbar-brand" href="/">
                <span>TAFOOD</span>
            </a>

            <div class="user_option">
                @if(Auth::check())
                    {{-- Notification Bell --}}
                    <div class="dropdown notification-dropdown">
                        <a href="#" class="dropdown-toggle no-caret position-relative" id="notificationBell" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: white; font-size: 23px; transition: transform 0.2s; padding: 4px 6px; margin-right: 4px;" onmouseover="this.style.transform='scale(1.15)';" onmouseout="this.style.transform='scale(1)';">
                            <i class="fa fa-bell"></i>
                            <span class="badge bg-danger rounded-circle position-absolute d-none" id="notificationBadge" style="font-size: 9px; padding: 3px 5px; top: -3px; right: -3px; border: 1px solid #222831; color: white;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right p-0 shadow-lg border-0" aria-labelledby="notificationBell" style="width: 350px; border-radius: 16px; overflow: visible; background: #222831; margin-top: 12px;">
                            <div class="p-3 d-flex justify-content-between align-items-center" style="background: #222831; border-top-left-radius: 16px; border-top-right-radius: 16px; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                <h6 class="mb-0 fw-bold d-flex align-items-center" style="font-size: 14.5px; font-weight: 700; color: #ffffff; letter-spacing: 0.2px;"><i class="fa fa-bell mr-2 animate-ring" style="font-size: 15px; color: #ffbe33 !important;"></i> Thông báo đơn hàng</h6>
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" id="markAllReadBtn" style="font-size: 12.5px; color: #ffbe33 !important; outline: none; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">Đọc tất cả</button>
                            </div>
                            <div class="notification-list" style="max-height: 275px; overflow-y: auto; overflow-x: hidden !important; padding: 4px 0;">
                                <div class="text-center py-5 text-muted" id="emptyNotifications" style="display: block;">
                                    <i class="fa fa-bell-slash fa-2x mb-3 text-muted" style="opacity: 0.4; display: block; margin: 0 auto;"></i>
                                    <p class="mb-0" style="font-size: 13.5px; font-weight: 500;">Chưa có thông báo nào</p>
                                </div>
                                <div id="notificationsContainer"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Cart Link to Cart Page --}}
                    <a href="{{ route('cart.index') }}" style="color: white; font-size: 22px; padding: 4px 6px; margin-right: 15px; margin-left: 10px; transition: transform 0.2s; position: relative; display: inline-block;" onmouseover="this.style.transform='scale(1.15)';" onmouseout="this.style.transform='scale(1)';" title="Giỏ hàng của tôi">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="badge bg-danger rounded-circle position-absolute" id="headerCartCount" style="font-size: 9px; padding: 3px 5px; top: -3px; right: -3px; border: 1px solid #222831; color: white; display: none;">0</span>
                    </a>

                    {{-- User Dropdown --}}
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle user-dropdown" data-toggle="dropdown"
                            style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                            <div class="user-avatar" style="overflow: hidden; padding: 0;">
                                @if(Auth::user()->AnhDaiDien)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ substr(Auth::user()->HoTen, 0, 1) }}
                                @endif
                            </div>
                            <span>{{ Auth::user()->HoTen }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header">
                                <strong>{{ Auth::user()->HoTen }}</strong>
                                <small class="text-muted d-block">{{ Auth::user()->Email }}</small>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}"><i class="fa fa-user"></i> Thông tin cá nhân</a>
                            <a class="dropdown-item {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.history') }}"><i class="fa fa-history"></i> Lịch sử đơn hàng</a>
                            <a class="dropdown-item {{ request()->routeIs('foods.favorites') ? 'active' : '' }}" href="{{ route('foods.favorites') }}"><i class="fa fa-heart"></i> Món ăn yêu thích</a>
                            <div class="dropdown-divider"></div>
                            @if(Auth::user()->isAdmin())
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fa fa-cog"></i> Quản trị</a>
                                <div class="dropdown-divider"></div>
                            @elseif(Auth::user()->isSeller())
                                <a class="dropdown-item" href="{{ route('seller.dashboard') }}"><i class="fa fa-store"></i> Kênh người bán</a>
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
                        <i class="fa fa-sign-in-alt"></i>
                        <span class="d-none d-sm-inline">Đăng nhập/Đăng ký</span>
                        <span class="d-inline d-sm-none">Đăng nhập</span>
                    </a>
                @endif
            </div>

            {{-- Search bar (same as homepage) --}}
            <form class="my-2 my-lg-0 header-search-form" id="searchForm" action="{{ route('foods.index') }}" method="GET">
                <div class="search-box-wrapper">
                    <input type="text" name="search" id="searchInput" placeholder="Tìm món hoặc quán ăn..." class="search-input" value="{{ request('search') }}">
                    <button type="submit" class="search-submit-btn" title="Tìm kiếm">
                        <i class="fa fa-search" style="font-size: 13px;"></i>
                    </button>
                </div>
            </form>

        </nav>
    </div>
</header>

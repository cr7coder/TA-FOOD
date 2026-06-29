<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Seller Panel</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #3b82f6;
            --sidebar-bg: #f4f7fa;
            --header-bg: #4da6ff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--text-main);
            margin: 0;
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 1001;
        }

        .sidebar-brand {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .sidebar-brand i {
            width: 32px;
            height: 32px;
            background-color: var(--bs-primary);
            color: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-content {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-group-title {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .nav-item {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1.5rem;
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background-color: rgba(59, 130, 246, 0.05);
            color: var(--bs-primary);
        }

        .nav-link.active {
            background-color: white;
            color: var(--bs-primary);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }

        .nav-link i {
            color: var(--text-muted);
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .nav-link.active i {
            color: var(--bs-primary);
        }

        /* Main Content Styles */
        .main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        .header {
            background-color: var(--header-bg);
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .breadcrumb-container {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .breadcrumb-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header-icon {
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
            background: none;
            border: none;
            padding: 0;
        }

        .header-icon:hover {
            opacity: 1;
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .user-name {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background-color: var(--bs-primary);
            border: 2px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .content {
            padding: 2rem;
            flex: 1;
            min-width: 0;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <a href="{{ route('seller.dashboard') }}" class="sidebar-brand">
            <i class="fas fa-square"></i>
            <span>Seller Panel</span>
        </a>

        <div class="sidebar-content">
            <div class="nav-group">
                <a href="#" class="nav-link" style="color: var(--text-muted);">
                    <i class="fas fa-compass"></i> Khu vực Người bán
                </a>
            </div>

            <div class="nav-group mt-3">
                <div class="nav-group-title">Tổng quan</div>
                <ul class="nav-item">
                    <li>
                        <a href="{{ route('seller.dashboard') }}" class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-th-large"></i> Dashboard
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-group mt-3">
                <div class="nav-group-title">Quản lý</div>
                <ul class="nav-item">
                    <li>
                        <a href="{{ route('seller.foods.index') }}" class="nav-link {{ request()->routeIs('seller.foods.*') ? 'active' : '' }}">
                            <i class="fas fa-list-ul"></i> Danh sách món
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('seller.restaurant.edit') }}" class="nav-link {{ request()->routeIs('seller.restaurant.*') ? 'active' : '' }}">
                            <i class="fas fa-store"></i> Cửa hàng của tôi
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-group mt-3">
                <div class="nav-group-title">Tiện ích</div>
                <ul class="nav-item">
                    <li>
                        <a href="{{ route('seller.orders.index') }}" class="nav-link {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i> Đơn hàng
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('seller.reports.index') }}" class="nav-link {{ request()->routeIs('seller.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i> Báo cáo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('seller.reviews.index') }}" class="nav-link {{ request()->routeIs('seller.reviews.*') ? 'active' : '' }}">
                            <i class="fas fa-star"></i> Đánh giá
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="p-3 border-top">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <header class="header">
            <div class="breadcrumb-container">
                <div class="breadcrumb-item text-white">Seller Panel</div>
                <span class="text-white">TAFood</span>
            </div>

            <div class="header-actions">
                <button class="header-icon" title="Theme"><i class="fas fa-sun"></i></button>
                <div class="position-relative" data-bs-toggle="offcanvas" data-bs-target="#notificationOffcanvas" style="cursor: pointer;" onclick="stopNewOrderSound()">
                    <i class="fas fa-bell header-icon"></i>
                    <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 10px; padding: 4px 6px;">0</span>
                </div>
                
                <div class="dropdown">
                    <div class="user-nav dropdown-toggle" data-bs-toggle="dropdown">
                        <span class="user-name d-none d-md-block">{{ auth()->user()?->HoTen ?? 'Seller' }}</span>
                        <div class="user-avatar" style="overflow: hidden; padding: 0;">
                            @if(auth()->user()?->AnhDaiDien)
                                <img src="{{ auth()->user()?->avatar_url }}" alt="avatar" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ collect(explode(' ', auth()->user()?->HoTen ?? 'S'))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('') ?: 'S' }}
                            @endif
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="border-radius: 10px;">
                        <li><a class="dropdown-item py-2" href="#"><i class="fas fa-user-circle me-2"></i> Hồ sơ</a></li>
                        <li><a class="dropdown-item py-2" href="#"><i class="fas fa-cog me-2"></i> Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content">
            @php
                $sellerRestaurant = \App\Models\NhaHang::where('MaNguoiDung', auth()->user()->MaNguoiDung)->first();
                $restaurantStatus = $sellerRestaurant?->TrangThai;
                $isBlocked = $sellerRestaurant && in_array($restaurantStatus, ['Đóng cửa', 'Từ chối', 'Chờ duyệt']);
            @endphp

            @if($isBlocked)
                {{-- Overlay chặn khi cửa hàng không hoạt động --}}
                <div style="position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.7); backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center;">
                    <div style="background:#fff; border-radius:20px; padding:40px 50px; max-width:500px; width:90%; text-align:center; box-shadow:0 25px 60px rgba(0,0,0,0.3);">
                        @if($restaurantStatus === 'Chờ duyệt')
                            <div style="width:80px;height:80px;border-radius:50%;background:#fff8e1;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                <i class="fas fa-hourglass-half fa-2x" style="color:#f59e0b;"></i>
                            </div>
                            <h4 style="font-weight:700;color:#92400e;margin-bottom:10px;">Đang chờ phê duyệt</h4>
                            <p style="color:#78350f;font-size:15px;margin-bottom:8px;">
                                Cửa hàng <strong>{{ $sellerRestaurant->TenNhaHang }}</strong> của bạn đang chờ Quản trị viên xem xét.
                            </p>
                            <p style="color:#92400e;font-size:13px;">Bạn sẽ nhận được thông báo ngay khi có kết quả. Trong thời gian này các chức năng bị tạm khóa.</p>
                        @elseif($restaurantStatus === 'Từ chối')
                            <div style="width:80px;height:80px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                <i class="fas fa-times-circle fa-2x" style="color:#ef4444;"></i>
                            </div>
                            <h4 style="font-weight:700;color:#991b1b;margin-bottom:10px;">Cửa hàng bị từ chối</h4>
                            <p style="color:#7f1d1d;font-size:15px;margin-bottom:8px;">
                                Cửa hàng <strong>{{ $sellerRestaurant->TenNhaHang }}</strong> chưa đáp ứng yêu cầu của hệ thống.
                            </p>
                            <p style="color:#991b1b;font-size:13px;">Vui lòng liên hệ Quản trị viên để được hỗ trợ.</p>
                        @elseif($restaurantStatus === 'Đóng cửa')
                            <div style="width:80px;height:80px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                <i class="fas fa-store-slash fa-2x" style="color:#d97706;"></i>
                            </div>
                            <h4 style="font-weight:700;color:#92400e;margin-bottom:10px;">Cửa hàng đang bị tạm ngưng</h4>
                            <p style="color:#78350f;font-size:15px;margin-bottom:8px;">
                                Cửa hàng <strong>{{ $sellerRestaurant->TenNhaHang }}</strong> đã bị Quản trị viên tạm ngưng hoạt động.
                            </p>
                            <p style="color:#92400e;font-size:13px;">Vui lòng liên hệ Quản trị viên để được mở lại. Trong thời gian này các chức năng bị tạm khóa.</p>
                        @endif
                        <a href="{{ route('seller.restaurant.edit') }}" class="btn btn-sm mt-3" style="background:#3b82f6;color:#fff;border-radius:8px;padding:8px 24px;font-weight:600;">
                            <i class="fas fa-eye me-1"></i> Xem thông tin cửa hàng
                        </a>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Notification Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationOffcanvas" aria-labelledby="notificationOffcanvasLabel" style="width: 420px; border-left: none; box-shadow: -5px 0 15px rgba(0,0,0,0.05);">
        <div class="offcanvas-header border-bottom bg-light py-3">
            <h5 class="offcanvas-title fw-bold" id="notificationOffcanvasLabel"><i class="fas fa-bell text-warning me-2"></i> Trung tâm thông báo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-fill bg-light pt-2 border-bottom-0" id="notificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-dark border-0 border-bottom border-3 border-success" id="orders-tab" data-bs-toggle="tab" data-bs-target="#notif-orders" type="button" role="tab" style="font-size: 0.95rem; border-radius: 0; background: transparent;" onclick="this.classList.add('border-success'); document.getElementById('reviews-tab').classList.remove('border-info'); document.getElementById('system-tab').classList.remove('border-primary');">
                        Đơn hàng <span id="tab-badge-orders" class="badge bg-success rounded-pill ms-1">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark border-0 border-bottom border-3" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#notif-reviews" type="button" role="tab" style="font-size: 0.95rem; border-radius: 0; background: transparent; border-color: transparent;" onclick="this.classList.add('border-info'); document.getElementById('orders-tab').classList.remove('border-success'); document.getElementById('system-tab').classList.remove('border-primary');">
                        Đánh giá <span id="tab-badge-reviews" class="badge bg-info rounded-pill ms-1">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark border-0 border-bottom border-3" id="system-tab" data-bs-toggle="tab" data-bs-target="#notif-system" type="button" role="tab" style="font-size: 0.95rem; border-radius: 0; background: transparent; border-color: transparent;" onclick="this.classList.add('border-primary'); document.getElementById('orders-tab').classList.remove('border-success'); document.getElementById('reviews-tab').classList.remove('border-info');">
                        Hệ thống <span id="tab-badge-system" class="badge bg-primary rounded-pill ms-1">0</span>
                    </button>
                </li>
            </ul>
            <!-- Tabs Content -->
            <div class="tab-content flex-grow-1 overflow-auto" id="notificationTabsContent">
                <div class="tab-pane fade show active h-100" id="notif-orders" role="tabpanel">
                    <div id="offcanvas-orders-list" class="list-group list-group-flush">
                        <!-- Populated via JS -->
                    </div>
                </div>
                <div class="tab-pane fade h-100" id="notif-reviews" role="tabpanel">
                    <div id="offcanvas-reviews-list" class="list-group list-group-flush">
                        <!-- Populated via JS -->
                    </div>
                </div>
                <div class="tab-pane fade h-100" id="notif-system" role="tabpanel">
                    <div id="offcanvas-system-list" class="list-group list-group-flush">
                        <!-- Populated via JS -->
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3 bg-light">
            <button class="btn btn-outline-primary w-100 fw-bold" id="markAllReadBtn" onclick="markAllNotificationsAsRead()">
                <i class="fas fa-check-double me-2"></i> Đánh dấu tất cả đã đọc
            </button>
        </div>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Notification Sound -->
    <!-- Notification Sound (Looping) -->
    <audio id="new-order-sound" src="https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3" preload="auto" loop></audio>
    <audio id="cancel-order-sound" src="https://assets.mixkit.co/active_storage/sfx/2569/2569-preview.mp3" preload="auto"></audio>
    <audio id="new-review-sound" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3" preload="auto"></audio>

    <script>
        // Auto-hide alerts after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-success, .alert-info');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.8s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 800);
                });
            }, 3000);
        });

        // Real-time Notification Logic for Seller (Persistent)
        let knownOrders = new Set(JSON.parse(localStorage.getItem('knownOrders') || '[]'));
        let knownReviews = new Set(JSON.parse(localStorage.getItem('knownReviews') || '[]'));
        let knownNotifications = new Set(JSON.parse(localStorage.getItem('knownNotifications') || '[]'));
        let currentNewOrders = [];
        let currentNewReviews = [];
        let currentDbNotifications = [];
        let isFirstLoad = true;

        function savePersistence() {
            localStorage.setItem('knownOrders', JSON.stringify([...knownOrders]));
            localStorage.setItem('knownReviews', JSON.stringify([...knownReviews]));
            localStorage.setItem('knownNotifications', JSON.stringify([...knownNotifications]));
        }

        // Request Browser Notification Permission
        if (Notification.permission !== "granted" && Notification.permission !== "denied") {
            Notification.requestPermission();
        }

        function showNativeNotification(title, body, iconUrl = '/images/logo.png') {
            if (Notification.permission === "granted") {
                new Notification(title, {
                    body: body,
                    icon: iconUrl
                });
            }
        }

        function stopNewOrderSound() {
            const sound = document.getElementById('new-order-sound');
            if (sound) {
                sound.pause();
                sound.currentTime = 0;
            }
            localStorage.setItem('last_sound_stop', Date.now());
        }

        async function checkOrders() {
            try {
                // Check for multi-tab sound coordination
                const lastStop = parseInt(localStorage.getItem('last_sound_stop') || '0');
                if (Date.now() - lastStop < 2000) {
                    stopNewOrderSound();
                }

                const res = await fetch('/api/v1/seller/orders', {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await res.json();
                
                if (result.success) {
                    const orders = result.data;
                    let newOrdersCount = 0;

                    // Update current new orders list for the bell icon
                    currentNewOrders = orders.filter(o => o.TrangThai === 'Chờ xác nhận');

                    // Compare with known orders to detect changes
                    orders.forEach(order => {
                        const orderKey = `${order.MaDonHang}_${order.TrangThai}`;
                        
                        // Detect New Orders or Status Changes
                        if (!isFirstLoad && !knownOrders.has(orderKey)) {
                            if (order.TrangThai === 'Chờ xác nhận') {
                                newOrdersCount++;
                            } else if (order.TrangThai === 'Đã hủy') {
                                notifyCancelledOrder(order);
                            }
                        }
                        knownOrders.add(orderKey);
                    });

                    // Group new orders into one notification if multiple
                    if (newOrdersCount > 1) {
                        notifyMultipleNewOrders(newOrdersCount);
                    } else if (newOrdersCount === 1) {
                        const newestOrder = orders.find(o => o.TrangThai === 'Chờ xác nhận' && !knownOrders.has(`${o.MaDonHang}_Chờ xác nhận`));
                        // Re-add because the find might fail due to race condition with Set
                        notifyNewOrder(newestOrder || orders.filter(o => o.TrangThai === 'Chờ xác nhận')[0]);
                    }

                    savePersistence();
                }

                // --- Check Reviews ---
                const resReview = await fetch('/api/v1/seller/reviews', {
                    headers: { 'Accept': 'application/json' }
                });
                const reviewResult = await resReview.json();
                if (reviewResult.success) {
                    const allReviews = reviewResult.data;
                    currentNewReviews = allReviews.filter(r => !r.DaPhanHoi);

                    allReviews.forEach(review => {
                        if (!isFirstLoad && !knownReviews.has(review.id)) {
                            notifyNewReview(review);
                        }
                        knownReviews.add(review.id);
                    });
                    savePersistence();
                }

                // --- Check System Notifications ---
                const resNotif = await fetch('/api/v1/notifications', {
                    headers: { 'Accept': 'application/json' }
                });
                const notifResult = await resNotif.json();
                if (notifResult.success) {
                    currentDbNotifications = notifResult.data.notifications || [];
                    
                    // Show alert/toast for any NEW unread notifications
                    currentDbNotifications.forEach(n => {
                        const notifKey = `db_notif_${n.id}`;
                        if (!isFirstLoad && !n.is_read && !knownNotifications.has(notifKey)) {
                            notifyDbNotification(n);
                        }
                        knownNotifications.add(notifKey);
                    });
                    savePersistence();
                }

                // --- Update Unified Badge ---
                updateNotificationBadge();

                isFirstLoad = false;
            } catch (err) {
                console.error("Error polling notifications:", err);
            }
        }

        function notifyDbNotification(n) {
            // Play notification sound
            const sound = document.getElementById('new-review-sound');
            if (sound) sound.play().catch(e => console.log("Audio blocked"));

            // Native browser notification
            showNativeNotification(n.title || 'Thông báo mới', n.message);

            // SweetAlert2 toast
            Swal.fire({
                title: n.title || '🔔 Thông báo hệ thống',
                html: n.message,
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: true,
                confirmButtonText: 'Xem',
                showCancelButton: true,
                cancelButtonText: 'Đóng',
                timer: 15000,
                timerProgressBar: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mark as read
                    fetch(`/api/v1/notifications/${n.id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    if (n.order_id) {
                        window.location.href = `/seller/orders/${n.order_id}`;
                    }
                }
            });
        }

        function updateNotificationBadge() {
            const badge = document.getElementById('notification-badge');
            const unreadDbCount = currentDbNotifications.filter(n => !n.is_read).length;
            const totalCount = currentNewOrders.length + currentNewReviews.length + unreadDbCount;
            
            if (badge) {
                if (totalCount > 0) {
                    badge.textContent = totalCount;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }

            // Update Tab Badges inside Offcanvas
            const tabOrders = document.getElementById('tab-badge-orders');
            if(tabOrders) tabOrders.textContent = currentNewOrders.length;
            
            const tabReviews = document.getElementById('tab-badge-reviews');
            if(tabReviews) tabReviews.textContent = currentNewReviews.length;
            
            const tabSystem = document.getElementById('tab-badge-system');
            if(tabSystem) tabSystem.textContent = unreadDbCount;

            // Re-render offcanvas content
            renderNotificationsToOffcanvas();
        }

        function notifyNewReview(review) {
            // 1. Sound
            const sound = document.getElementById('new-review-sound');
            if (sound) sound.play().catch(e => console.log("Audio blocked"));

            // 2. Browser Native Notification
            showNativeNotification('⭐ Có đánh giá mới!', `Khách hàng ${review.TenKhachHang} vừa đánh giá ${review.DiemDanhGia} sao cho món ${review.TenMonAn}`);

            // 3. UI Toast
            Swal.fire({
                title: '⭐ Có đánh giá mới!',
                html: `Khách hàng <b>${review.TenKhachHang}</b> vừa đánh giá <b>${review.DiemDanhGia} sao</b> cho món <i>${review.TenMonAn}</i>`,
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: true,
                confirmButtonText: 'Phản hồi ngay',
                showCancelButton: true,
                cancelButtonText: 'Đóng',
                timer: 15000,
                timerProgressBar: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/seller/reviews`;
                }
            });
        }

        function notifyNewOrder(order) {
            if (!order) return;
            // 1. Sound
            const sound = document.getElementById('new-order-sound');
            if (sound) sound.play().catch(e => console.log("Audio blocked"));

            // 2. Browser Native Notification
            showNativeNotification('🎉 Có đơn hàng mới!', `Khách hàng ${order.TenKhachHang} vừa đặt đơn (${order.TongTien})`);

            // 3. UI Toast
            Swal.fire({
                title: '🎉 Có đơn hàng mới!',
                html: `Khách hàng <b>${order.TenKhachHang}</b> vừa đặt đơn mới (${order.TongTien})`,
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: true,
                confirmButtonText: 'Xem ngay',
                showCancelButton: true,
                cancelButtonText: 'Đóng',
                timer: 30000,
                timerProgressBar: true
            }).then((result) => {
                stopNewOrderSound();
                if (result.isConfirmed) {
                    window.location.href = `/seller/orders/${order.MaDonHang}`;
                }
            });

            if (typeof loadOrders === 'function') loadOrders();
        }

        function notifyMultipleNewOrders(count) {
            const sound = document.getElementById('new-order-sound');
            if (sound) sound.play().catch(e => console.log("Audio blocked"));

            showNativeNotification('🚀 Có nhiều đơn mới!', `Bạn có ${count} đơn hàng mới cần xử lý`);

            Swal.fire({
                title: '🚀 Có nhiều đơn mới!',
                html: `Bạn vừa nhận được <b>${count} đơn hàng mới</b>.`,
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: true,
                confirmButtonText: 'Kiểm tra ngay',
                showCancelButton: true,
                cancelButtonText: 'Đóng',
                timer: 30000,
                timerProgressBar: true
            }).then((result) => {
                stopNewOrderSound();
                if (result.isConfirmed) {
                    window.location.href = "{{ route('seller.orders.index') }}";
                }
            });

            if (typeof loadOrders === 'function') loadOrders();
        }

        function notifyCancelledOrder(order) {
            if (order.nguoi_huy === 'seller') {
                if (typeof loadOrders === 'function') loadOrders();
                return;
            }

            // 1. Sound
            const sound = document.getElementById('cancel-order-sound');
            if (sound) sound.play().catch(e => console.log("Audio blocked"));

            let cancelActor = 'Khách hàng';
            let cancelReasonText = order.ly_do_huy || (order.nguoi_huy === 'customer' ? 'Khách hàng chủ động hủy đơn' : 'Không có lý do cụ thể');
            if (order.nguoi_huy === 'admin') {
                cancelActor = 'Quản trị viên';
            } else if (order.nguoi_huy === 'system') {
                cancelActor = 'Hệ thống';
            }

            // 2. Browser Native Notification
            showNativeNotification('⚠️ Đơn hàng bị hủy!', `${cancelActor} đã hủy đơn hàng ${order.TongTien}. Lý do: ${cancelReasonText}`);

            // 3. UI Toast
            Swal.fire({
                title: '⚠️ Đơn hàng đã bị hủy!',
                html: `<b>${cancelActor}</b> vừa hủy đơn hàng (${order.TongTien})<br>Lý do: <i>${cancelReasonText}</i>`,
                icon: 'error',
                toast: true,
                position: 'top-end',
                showConfirmButton: true,
                confirmButtonText: 'Kiểm tra',
                timer: 15000,
                timerProgressBar: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/seller/orders/${order.MaDonHang}`;
                }
            });

            if (typeof loadOrders === 'function') loadOrders();
        }

        function renderNotificationsToOffcanvas() {
            // Render Orders
            const ordersList = document.getElementById('offcanvas-orders-list');
            if (ordersList) {
                if (currentNewOrders.length === 0) {
                    ordersList.innerHTML = `<div class="text-center p-5 text-muted mt-4"><i class="fas fa-check-circle fa-3x mb-3 text-success opacity-50"></i><br>Không có đơn hàng mới</div>`;
                } else {
                    ordersList.innerHTML = currentNewOrders.map(o => `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-bottom" style="border-left: 4px solid #198754; background-color: #f8fff9;">
                            <div style="flex: 1; padding-right: 15px;">
                                <div class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">${o.TenKhachHang}</div>
                                <small class="text-muted d-block mb-1"><i class="far fa-clock"></i> ${o.ThoiGian}</small>
                                <div class="text-secondary" style="font-size: 0.9rem;">${o.MonAn}</div>
                            </div>
                            <div class="text-end" style="min-width: 90px;">
                                <div class="fw-bold text-danger mb-2">${o.TongTien}</div>
                                <a href="/seller/orders/${o.MaDonHang}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">Xử lý</a>
                            </div>
                        </div>
                    `).join('');
                }
            }

            // Render Reviews
            const reviewsList = document.getElementById('offcanvas-reviews-list');
            if (reviewsList) {
                if (currentNewReviews.length === 0) {
                    reviewsList.innerHTML = `<div class="text-center p-5 text-muted mt-4"><i class="fas fa-star fa-3x mb-3 text-info opacity-50"></i><br>Không có đánh giá mới</div>`;
                } else {
                    reviewsList.innerHTML = currentNewReviews.map(r => `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-bottom" style="border-left: 4px solid #0dcaf0; background-color: #f0fcfc;">
                            <div style="flex: 1; padding-right: 15px;">
                                <div class="fw-bold text-dark mb-1">${r.TenKhachHang}</div>
                                <div class="text-warning mb-1" style="font-size: 0.85rem;">
                                    ${'★'.repeat(r.DiemDanhGia)}${'☆'.repeat(5 - r.DiemDanhGia)}
                                </div>
                                <div class="text-secondary" style="font-size: 0.85rem;">Món: ${r.TenMonAn}</div>
                            </div>
                            <div class="text-end" style="min-width: 80px;">
                                <small class="text-muted d-block mb-2">${r.ThoiGian}</small>
                                <a href="/seller/reviews" class="btn btn-sm btn-outline-info rounded-pill px-3">Phản hồi</a>
                            </div>
                        </div>
                    `).join('');
                }
            }

            // Render System Notifications
            const systemList = document.getElementById('offcanvas-system-list');
            if (systemList) {
                if (currentDbNotifications.length === 0) {
                    systemList.innerHTML = `<div class="text-center p-5 text-muted mt-4"><i class="fas fa-bell fa-3x mb-3 text-primary opacity-50"></i><br>Không có thông báo hệ thống</div>`;
                } else {
                    systemList.innerHTML = currentDbNotifications.map(n => {
                        const unreadStyle = !n.is_read ? 'background-color: #f0f7ff; border-left: 4px solid #0d6efd;' : 'border-left: 4px solid transparent; opacity: 0.8;';
                        const orderUrl = n.order_id ? `/seller/orders/${n.order_id}` : '#';
                        return `
                            <div class="list-group-item list-group-item-action p-3 border-bottom" style="${unreadStyle} cursor: pointer;" onclick="readSystemNotification(${n.id}, '${orderUrl}')">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">${n.title || 'Thông báo'}</h6>
                                    ${!n.is_read ? '<span class="badge bg-primary rounded-pill" style="font-size: 0.7rem;">Mới</span>' : ''}
                                </div>
                                <p class="mb-1 text-secondary mt-2" style="font-size: 0.85rem; line-height: 1.4;">${n.message}</p>
                                <small class="text-muted mt-1 d-block"><i class="far fa-clock"></i> ${n.time_diff || ''}</small>
                            </div>
                        `;
                    }).join('');
                }
            }
        }

        async function readSystemNotification(id, url) {
            await fetch(`/api/v1/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            if (url && url !== '#') {
                window.location.href = url;
            } else {
                checkOrders();
            }
        }

        function markAllNotificationsAsRead() {
            fetch('/api/v1/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(() => {
                checkOrders();
                const btn = document.getElementById('markAllReadBtn');
                if(btn) {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Đã đánh dấu';
                    btn.classList.replace('btn-outline-primary', 'btn-success');
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.replace('btn-success', 'btn-outline-primary');
                    }, 2000);
                }
            });
        }

        if ("{{ Auth::check() }}") {
            setInterval(checkOrders, 5000); // Poll every 5s for real-time responsiveness
            setTimeout(checkOrders, 2000);   // Initial check
        }
    </script>
    
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi truy cập',
                text: "{!! addslashes(session('error')) !!}",
                confirmButtonColor: '#ff6b35'
            });
        });
    </script>
    @endif

    @if(session('success') || session('toast_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: "{!! addslashes(session('success') ?? session('toast_success')) !!}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
    @endif
    
    @stack('scripts')
</body>

</html>
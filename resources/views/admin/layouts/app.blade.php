<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin Panel</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {{-- (Tùy chọn) Locale Tiếng Việt --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    @yield('styles')
    <style>
        :root {
            --navbar-h: 60px;
            --sidebar-w: 260px;
            --radius: 14px;

            /* Light */
            --bg: #f5f7fb;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #6b7280;
            --border: #e5e7eb;
            --shadow: 0 12px 28px rgba(2, 6, 23, .08);

            /* Brand */
            --primary: #8b5cf6;
            /* violet-500 */
            --primary-2: #ec4899;
            /* pink-500 */
            --accent: #f59e0b;
            /* amber-500 */

            /* Sidebar glass */
            --sb-bg: rgba(255, 255, 255, 0.8);
            --sb-border: rgba(2, 6, 23, .08);
            --sb-hover: rgba(139, 92, 246, .12);
            --active-bg: linear-gradient(135deg, rgba(139, 92, 246, .18), rgba(236, 72, 153, .18));
            --glass: blur(10px) saturate(130%);
        }

        body.theme-dark {
            --bg: #0b1020;
            --card: #0f172a;
            --text: #f8fafc;
            --muted: #9ca3af;
            --border: #1f2a44;
            --shadow: 0 10px 26px rgba(2, 6, 23, .55);

            --sb-bg: rgba(15, 23, 42, 0.70);
            --sb-border: rgba(148, 163, 184, .12);
            --sb-hover: rgba(139, 92, 246, .18);
            --active-bg: linear-gradient(135deg, rgba(139, 92, 246, .16), rgba(236, 72, 153, .16));
            --glass: blur(12px) saturate(150%);
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: var(--bg);
            color: var(--text);
            font: 14px/1.55 system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans";
        }

        /* Topbar */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--navbar-h);
            z-index: 1040;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: 0 10px 28px rgba(139, 92, 246, .25);
            transition: left .25s ease;
        }

        .topbar .wrap {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: .3px;
        }

        .brand i {
            color: #fff;
            background: rgba(255, 255, 255, .18);
            padding: .5rem;
            border-radius: 10px;
        }

        .icon-btn {
            color: #fff;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .22);
            padding: .42rem .6rem;
            border-radius: 10px;
            backdrop-filter: var(--glass);
            transition: .2s ease;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .12);
        }

        .icon-btn:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, .22);
        }

        .user-dd .btn {
            color: #fff;
            border-color: rgba(255, 255, 255, .28);
            background: rgba(255, 255, 255, .12);
        }

        .avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-grid;
            place-items: center;
            font-size: 12px;
            font-weight: 700;
            background: #fff;
            color: var(--primary);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sb-bg);
            backdrop-filter: var(--glass);
            border-right: 1px solid var(--sb-border);
            box-shadow: var(--shadow);
            z-index: 1030;
            transition: transform .25s ease;
        }

        .sb-head {
            padding: 1rem .9rem;
            border-bottom: 1px dashed var(--sb-border);
        }

        .sb-title {
            margin: 0;
            font-size: .95rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            letter-spacing: .4px;
        }

        .sb-section {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: .7rem .9rem .3rem;
        }

        .menu {
            list-style: none;
            margin: 0;
            padding: .6rem;
        }

        .menu .nav-link {
            display: flex;
            align-items: center;
            gap: .8rem;
            color: var(--text);
            text-decoration: none;
            padding: .6rem .7rem;
            border-radius: 10px;
            transition: .18s ease;
            position: relative;
        }

        .menu .nav-link i {
            width: 18px;
            text-align: center;
            color: var(--muted);
        }

        .menu .nav-link:hover {
            background: var(--sb-hover);
        }

        .menu .nav-link.active {
            background: var(--active-bg);
            box-shadow: inset 0 0 0 1px var(--sb-border);
        }

        .menu .nav-link.active::before {
            content: "";
            position: absolute;
            left: -6px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 22px;
            border-radius: 4px;
            background: linear-gradient(180deg, var(--primary), var(--primary-2));
        }

        /* Content */
        .content {
            margin: var(--navbar-h) 0 0 var(--sidebar-w);
            min-height: calc(100vh - var(--navbar-h));
            padding: 1.2rem;
        }

        .card-surface {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .page-header {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            color: #fff;
            padding: 1rem 1.2rem;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: 0 14px 32px rgba(245, 158, 11, .20);
        }

        .page-header h1 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .crumb {
            opacity: .9;
            font-size: 12px;
        }

        .btn-soft {
            background: rgba(255, 255, 255, .14);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .25);
        }

        .btn-soft:hover {
            background: rgba(255, 255, 255, .22);
            color: #fff;
        }

        /* Tables, forms, etc. */
        .table {
            font-size: 13px;
        }

        .form-label {
            font-weight: 600;
        }

        /* Mobile */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .topbar {
                left: 0;
            }

            .content {
                margin: var(--navbar-h) 0 0 0;
                padding: .9rem;
            }
        }

        .sb-overlay {
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, .45);
            z-index: 1025;
            display: none;
        }

        .sb-overlay.show {
            display: block;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #d8b4fe;
            border-radius: 8px;
        }

        body.theme-dark ::-webkit-scrollbar-thumb {
            background: #4338ca;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Topbar -->
    <nav class="topbar">
        <div class="wrap container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn btn-sm icon-btn me-2 d-lg-none" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="brand" href="{{ route('admin.vouchers.index') }}">
                    <i class="fas fa-crown"></i> <span>Admin Panel</span>
                    <span class="badge text-bg-light text-dark ms-1" style="border-radius: 999px;">TAFOOD</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm icon-btn" title="Đổi giao diện" onclick="toggleTheme()"><i
                        class="fas fa-moon"></i></button>
                <div class="dropdown" id="adminNotifDropdown">
                    <button class="btn btn-sm icon-btn position-relative" title="Thông báo"
                            data-bs-toggle="dropdown" aria-expanded="false" id="adminBellBtn"
                            onclick="loadAdminNotifications()">
                        <i class="fas fa-bell"></i>
                        <span id="adminNotifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                              style="font-size:9px; padding:3px 5px;"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="width:340px; border-radius:12px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.15);">
                        <div style="background:linear-gradient(135deg,#a855f7,#7c3aed); padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                            <span style="color:#fff; font-weight:700; font-size:14px;"><i class="fas fa-bell me-2"></i>Thông báo</span>
                            <button onclick="markAllAdminRead()" style="background:rgba(255,255,255,0.2); border:none; border-radius:6px; color:#fff; font-size:11px; padding:3px 8px; cursor:pointer;">Đọc tất cả</button>
                        </div>
                        <div id="adminNotifList" style="max-height:360px; overflow-y:auto;">
                            <div class="text-center py-4 text-muted" style="font-size:13px;">
                                <i class="fas fa-spinner fa-spin"></i> Đang tải...
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown user-dd">
                    @php
                        $fullName = auth()->user()->name ?? 'Admin';
                        $initials = collect(explode(' ', trim($fullName)))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                    @endphp
                    <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2"
                        data-bs-toggle="dropdown">
                        <span class="avatar">{{ $initials ?: 'A' }}</span>
                        <span class="d-none d-sm-inline text-white">{{ $fullName }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-gear"></i> Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-bell"></i> Thông báo</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-gear"></i> Cài đặt</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">@csrf
                                <button class="dropdown-item"><i class="fas fa-right-from-bracket"></i> Đăng
                                    xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar + overlay -->
    <div id="sbOverlay" class="sb-overlay" onclick="closeSidebar()"></div>
    <aside id="sidebar" class="sidebar">
        <div class="sb-head">
            <h6 class="sb-title"><i class="fas fa-shield-halved text-warning"></i> Khu vực Quản trị</h6>
        </div>

        <div class="sb-section">Tổng quan</div>
        <ul class="menu">
            <li>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-gauge"></i> <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sb-section">Quản lý</div>
        <ul class="menu">
            <li>
                <a class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}"
                    href="{{ route('admin.vouchers.index') }}">
                    <i class="fas fa-ticket-alt"></i> <span>Quản lý Voucher</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.doi-tac-van-chuyen.*') ? 'active' : '' }}"
                    href="{{ route('admin.doi-tac-van-chuyen.index') }}">
                    <i class="fas fa-truck"></i> <span>Đối tác vận chuyển</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i> <span>Quản lý User</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.restaurants.*') ? 'active' : '' }}" href="{{ route('admin.restaurants.index') }}">
                    <i class="fas fa-store"></i> <span>Quản lý Nhà hàng</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-cart-shopping"></i> <span>Quản lý Đơn hàng</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.reconciliation.*') ? 'active' : '' }}" href="{{ route('admin.reconciliation.index') }}">
                    <i class="fas fa-wallet"></i> <span>Hạch toán đối soát</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.danh-muc.*') ? 'active' : '' }}"
                    href="{{ route('admin.danh-muc.index') }}">
                    <i class="fas fa-tags"></i> <span>Quản lý Danh mục</span>
                </a>
            </li>
        </ul>

        <div class="sb-section">Báo cáo & Cài đặt</div>
        <ul class="menu">
            <li>
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-chart-line"></i> <span>Báo cáo</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                    <i class="fas fa-gears"></i> <span>Cài đặt hệ thống</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="content">
        @if(!request()->routeIs('admin.vouchers.index') && !request()->routeIs('admin.vouchers.create') && !request()->routeIs('admin.vouchers.edit') && !request()->routeIs('admin.vouchers.show') && !request()->routeIs('admin.doi-tac-van-chuyen.index') && !request()->routeIs('admin.doi-tac-van-chuyen.create') && !request()->routeIs('admin.doi-tac-van-chuyen.edit') && !request()->routeIs('admin.doi-tac-van-chuyen.show') && !request()->routeIs('admin.users.index') && !request()->routeIs('admin.users.create') && !request()->routeIs('admin.users.edit') && !request()->routeIs('admin.users.show') && !request()->routeIs('admin.orders.index') && !request()->routeIs('admin.orders.show') && !request()->routeIs('admin.danh-muc.index') && !request()->routeIs('admin.danh-muc.create') && !request()->routeIs('admin.danh-muc.edit') && !request()->routeIs('admin.danh-muc.show') && !request()->routeIs('admin.dashboard') && !request()->routeIs('admin.reports.index') && !request()->routeIs('admin.reconciliation.index') && !request()->routeIs('admin.restaurants.index'))
        <section class="page-header card-surface mb-3">
            <div>
                <h1><i class="fas fa-sitemap"></i> @yield('title', 'Bảng điều khiển')</h1>
                <div class="crumb">
                    <i class="fas fa-location-arrow me-1"></i>
                    {{ optional(request()->route())->getName() }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @yield('page_actions')
                <button type="button" class="btn btn-soft btn-sm"><i class="fas fa-download"></i> Export</button>
            </div>
        </section>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme
        function applyTheme() {
            const theme = localStorage.getItem('admin-theme') || 'light';
            document.body.classList.toggle('theme-dark', theme === 'dark');
        }
        function toggleTheme() {
            const cur = localStorage.getItem('admin-theme') || 'light';
            localStorage.setItem('admin-theme', cur === 'light' ? 'dark' : 'light');
            applyTheme();
        }
        applyTheme();

        // Sidebar toggle (mobile)
        function toggleSidebar() {
            const sb = document.getElementById('sidebar');
            const ov = document.getElementById('sbOverlay');
            sb.classList.toggle('show');
            ov.classList.toggle('show');
            document.body.style.overflow = sb.classList.contains('show') ? 'hidden' : '';
        }
        function closeSidebar() {
            const sb = document.getElementById('sidebar');
            const ov = document.getElementById('sbOverlay');
            sb.classList.remove('show'); ov.classList.remove('show'); document.body.style.overflow = '';
        }
        window.addEventListener('resize', () => { if (window.innerWidth >= 992) closeSidebar(); });

        // Auto-hide alerts like previous layout (optional)
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(el => {
                const a = new bootstrap.Alert(el); a.close();
            });
        }, 5000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script>
        // Khởi tạo với strict parse, không tự sửa ngày sai
        window.fpStart = flatpickr('#NgayBatDau', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true,
            clickOpens: false,
            locale: 'vn',
            parseDate: (datestr, format) => {
                if (format === 'd/m/Y') return window.parseDMYStrict(datestr);
                if (format === 'Y-m-d') return window.parseYMDStrict(datestr);
                return undefined;
            },
            onChange: () => typeof updateDuration === 'function' && updateDuration(),
        });

        window.fpEnd = flatpickr('#NgayKetThuc', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true,
            clickOpens: false,
            locale: 'vn',
            parseDate: (datestr, format) => {
                if (format === 'd/m/Y') return window.parseDMYStrict(datestr);
                if (format === 'Y-m-d') return window.parseYMDStrict(datestr);
                return undefined;
            },
            onChange: () => typeof updateDuration === 'function' && updateDuration(),
        });

        // Nút mở lịch
        document.getElementById('btnStartCalendar')?.addEventListener('click', () => window.fpStart.open());
        document.getElementById('btnEndCalendar')?.addEventListener('click', () => window.fpEnd.open());
    </script>
    <!-- Global Admin Toast/Alert Modal (Centered & Glassmorphic) -->
    <div class="position-fixed top-50 start-50 translate-middle p-3" style="z-index: 10099; pointer-events: none;">
        <div id="adminActionToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="pointer-events: auto; min-width: 320px; border-radius: 20px; background: rgba(30, 36, 45, 0.98) !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; backdrop-filter: blur(15px);">
            <div class="p-4 text-center">
                <div class="mb-3" id="adminToastIcon" style="font-size: 55px; animation: globalAlertBounce 0.5s ease;">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="fw-bold mb-3 fs-6" id="adminToastMessage" style="color: #ffffff; line-height: 1.5;"></div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light btn-sm fw-bold px-4 py-2" data-bs-dismiss="toast" style="border-radius: 10px; font-size: 13.5px; background: #ffffff; color: #1e242d; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Confirm Modal (Centered & Glassmorphic) -->
    <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 10050;">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 380px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: rgba(30, 36, 45, 0.98); color: #fff; border: 1px solid rgba(255, 255, 255, 0.12); backdrop-filter: blur(15px);">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3" style="font-size: 55px; color: #ef4444; animation: globalAlertBounce 0.5s ease;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-white" id="globalConfirmTitle">Xác nhận</h5>
                    <p class="small text-muted mb-4" id="globalConfirmMessage" style="color: #cbd5e1 !important; line-height: 1.5;"></p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-4 py-2 border-0 fw-semibold text-white-50" data-bs-dismiss="modal" style="border-radius: 10px; background: rgba(255,255,255,0.05);">Hủy</button>
                        <button type="button" class="btn btn-danger btn-sm px-4 py-2 fw-semibold" id="globalConfirmBtn" style="border-radius: 10px; background: #ef4444; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">Đồng ý</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes globalAlertBounce {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>

    <script>
        window.showAdminToast = function(msg, type = 'success') {
            const el = document.getElementById('adminActionToast');
            if (!el) return;
            
            // Set message
            document.getElementById('adminToastMessage').innerHTML = msg.replace(/\n/g, '<br>');
            
            // Set icon based on type
            const iconEl = document.getElementById('adminToastIcon');
            let iconHtml = '';
            if (type === 'success') {
                iconHtml = '<i class="fas fa-check-circle" style="color: #10b981;"></i>';
            } else if (type === 'error' || type === 'danger') {
                iconHtml = '<i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>';
            } else if (type === 'warning') {
                iconHtml = '<i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>';
            } else {
                iconHtml = '<i class="fas fa-info-circle" style="color: #3b82f6;"></i>';
            }
            if (iconEl) iconEl.innerHTML = iconHtml;

            const toast = new bootstrap.Toast(el, { delay: 4000 });
            toast.show();
        };

        let globalConfirmCallback = null;
        window.showConfirmModal = function(title, message, onConfirm) {
            document.getElementById('globalConfirmTitle').textContent = title;
            document.getElementById('globalConfirmMessage').innerHTML = message;
            globalConfirmCallback = onConfirm;
            const modal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));
            modal.show();
        };

        document.getElementById('globalConfirmBtn')?.addEventListener('click', function() {
            if (globalConfirmCallback) {
                globalConfirmCallback();
            }
            bootstrap.Modal.getInstance(document.getElementById('globalConfirmModal'))?.hide();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const successMsg = localStorage.getItem('admin_success');
            if (successMsg) {
                window.showAdminToast(successMsg, 'success');
                localStorage.removeItem('admin_success');
            }
            const errorMsg = localStorage.getItem('admin_error');
            if (errorMsg) {
                window.showAdminToast(errorMsg, 'danger');
                localStorage.removeItem('admin_error');
            }
        });
    </script>
    <script>
    // ── Admin Notification System ──
    const ADMIN_NOTIF_API = '/api/v1/admin/notifications';
    const ADMIN_CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
    let seenNotifIds = new Set();
    let isInitialNotifLoad = true;

    // sound player helper using Web Audio API
    function playNotificationSound() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            
            // Tone 1: C5
            const osc1 = audioCtx.createOscillator();
            const gain1 = audioCtx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(523.25, audioCtx.currentTime); 
            gain1.gain.setValueAtTime(0.08, audioCtx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
            osc1.connect(gain1);
            gain1.connect(audioCtx.destination);
            osc1.start();
            osc1.stop(audioCtx.currentTime + 0.3);
            
            // Tone 2: E5 (delayed by 100ms)
            setTimeout(() => {
                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(659.25, audioCtx.currentTime); 
                gain2.gain.setValueAtTime(0.08, audioCtx.currentTime);
                gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start();
                osc2.stop(audioCtx.currentTime + 0.4);
            }, 100);
        } catch (e) {
            console.warn('Audio context blocked or failed:', e);
        }
    }

    function showAdminNotificationToast(n) {
        const el = document.getElementById('adminActionToast');
        if (!el) return;
        
        document.getElementById('adminToastMessage').innerHTML = `
            <div style="font-size:14.5px; font-weight:700; color:#fff; margin-bottom:5px;">${n.title}</div>
            <div style="font-size:13px; font-weight:normal; color:#cbd5e1; line-height:1.4;">${n.message}</div>
        `;
        
        const iconEl = document.getElementById('adminToastIcon');
        if (iconEl) {
            iconEl.innerHTML = '<i class="fas fa-wallet" style="color:#f59e0b; animation: globalAlertBounce 0.5s ease;"></i>';
        }

        const confirmBtn = el.querySelector('.d-flex button');
        if (confirmBtn) {
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
            
            if (n.order_id) {
                newConfirmBtn.addEventListener('click', function() {
                    markAdminNotifRead(n.id, n.order_id);
                    bootstrap.Toast.getInstance(el)?.hide();
                });
                newConfirmBtn.textContent = 'Xem chi tiết';
            } else {
                newConfirmBtn.addEventListener('click', function() {
                    markAdminNotifRead(n.id, null);
                    bootstrap.Toast.getInstance(el)?.hide();
                });
                newConfirmBtn.textContent = 'Đã hiểu';
            }
        }

        // Show the toast for 10 seconds to make sure the admin sees it
        const toast = new bootstrap.Toast(el, { autohide: true, delay: 10000 });
        toast.show();
    }

    async function loadAdminNotifications() {
        try {
            const res = await fetch(ADMIN_NOTIF_API, { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (json.success) {
                const notifications = json.data.notifications || [];
                renderAdminNotifications(notifications);
                
                if (isInitialNotifLoad) {
                    // Populate seenNotifIds on initial page load so we don't alert old ones
                    notifications.forEach(n => seenNotifIds.add(n.id));
                    isInitialNotifLoad = false;
                } else {
                    let hasNew = false;
                    notifications.forEach(n => {
                        if (!seenNotifIds.has(n.id)) {
                            seenNotifIds.add(n.id);
                            if (!n.is_read) {
                                hasNew = true;
                                showAdminNotificationToast(n);
                            }
                        }
                    });
                    
                    if (hasNew) {
                        playNotificationSound();
                    }
                }
            }
        } catch(e) {}
    }

    function renderAdminNotifications(notifications) {
        const list = document.getElementById('adminNotifList');
        const badge = document.getElementById('adminNotifBadge');
        const unread = notifications.filter(n => !n.is_read).length;

        // Update badge
        if (unread > 0) {
            badge.textContent = unread > 9 ? '9+' : unread;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }

        if (!notifications.length) {
            list.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-bell-slash fa-2x mb-2"></i><br>Không có thông báo nào</div>`;
            return;
        }

        list.innerHTML = notifications.slice(0, 15).map(n => `
            <div onclick="markAdminNotifRead('${n.id}', ${n.order_id || 'null'})"
                 style="padding:12px 16px; border-bottom:1px solid #f1f5f9; cursor:pointer;
                        background:${n.is_read ? '#fff' : '#faf5ff'};
                        transition:background 0.2s;"
                 onmouseover="this.style.background='#f5f3ff'"
                 onmouseout="this.style.background='${n.is_read ? '#fff' : '#faf5ff'}'">
                <div style="display:flex; gap:10px; align-items:flex-start;">
                    <div style="width:36px; height:36px; border-radius:50%; background:${n.is_read ? '#e9d5ff' : '#a855f7'};
                                display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-bell" style="color:${n.is_read ? '#a855f7' : '#fff'}; font-size:14px;"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:${n.is_read ? '500' : '700'}; font-size:13px; color:#1e293b; margin-bottom:2px;">
                            ${n.title}
                            ${!n.is_read ? '<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#a855f7;margin-left:4px;vertical-align:middle;"></span>' : ''}
                        </div>
                        <div style="font-size:12px; color:#64748b; line-height:1.4; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ${n.message}
                        </div>
                        <div style="font-size:11px; color:#94a3b8; margin-top:4px;">${n.time_diff || n.created_at}</div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async function markAdminNotifRead(id, orderId) {
        try {
            await fetch(`${ADMIN_NOTIF_API}/${id}/read`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': ADMIN_CSRF }
            });
        } catch(e) {}
        if (orderId) {
            window.location.href = `/admin/orders/${orderId}`;
        } else {
            loadAdminNotifications();
        }
    }

    async function markAllAdminRead() {
        try {
            await fetch(`${ADMIN_NOTIF_API}/read-all`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': ADMIN_CSRF }
            });
            loadAdminNotifications();
        } catch(e) {}
    }

    // Initial load
    loadAdminNotifications();

    // Fast polling every 3 seconds for real-time notification
    setInterval(loadAdminNotifications, 3000);
    </script>
    <script src="{{ asset('js/date-utils-strict.js') }}"></script>

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi truy cập',
                    text: "{!! addslashes(session('error')) !!}",
                    confirmButtonColor: '#ff6b35'
                });
            } else {
                alert("{!! addslashes(session('error')) !!}");
            }
        });
    </script>
    @endif

    @if(session('success') || session('toast_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
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
            }
        });
    </script>
    @endif

    @yield('scripts')

</body>

</html>
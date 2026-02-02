<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Seller Panel</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --navbar-h: 60px;
            --sidebar-w: 260px;
            --radius: 14px;

            /* Light theme */
            --bg: #f4f6f8;
            --card: #ffffff;
            --muted: #6b7280;
            --text: #0f172a;
            --primary: #0ea5e9;
            /* cyan-500 */
            --primary-2: #6366f1;
            /* indigo-500 */
            --accent: #10b981;
            /* emerald-500 */
            --border: #e5e7eb;
            --sidebar: rgba(255, 255, 255, 0.75);
            --sidebar-border: rgba(15, 23, 42, 0.08);
            --sidebar-hover: rgba(99, 102, 241, 0.12);
            --active: linear-gradient(135deg, rgba(14, 165, 233, .18), rgba(99, 102, 241, .18));
            --shadow: 0 12px 30px rgba(2, 6, 23, 0.08);
            --glass: blur(10px) saturate(120%);
        }

        body.theme-dark {
            /* Dark theme */
            --bg: #0b1020;
            --card: #0f172a;
            --muted: #9ca3af;
            --text: #f8fafc;
            --primary: #22d3ee;
            /* cyan-400 */
            --primary-2: #818cf8;
            /* indigo-400 */
            --accent: #34d399;
            /* emerald-400 */
            --border: #1f2a44;
            --sidebar: rgba(15, 23, 42, 0.7);
            --sidebar-border: rgba(148, 163, 184, 0.12);
            --sidebar-hover: rgba(99, 102, 241, 0.18);
            --active: linear-gradient(135deg, rgba(34, 211, 238, .14), rgba(129, 140, 248, .14));
            --shadow: 0 10px 28px rgba(2, 6, 23, 0.55);
            --glass: blur(12px) saturate(140%);
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
            font: 14px/1.5 system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
        }

        /* Top Navbar (gradient) */
        .topbar {
            position: fixed;
            inset: 0 0 auto 0;
            height: var(--navbar-h);
            z-index: 1040;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: 0 8px 24px rgba(14, 165, 233, .25);
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

        .top-actions {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .icon-btn {
            color: #fff;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .22);
            padding: .4rem .6rem;
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

        /* Sidebar (glassmorphism) */
        .sidebar {
            position: fixed;
            top: var(--navbar-h);
            left: 0;
            width: var(--sidebar-w);
            height: calc(100vh - var(--navbar-h));
            background: var(--sidebar);
            backdrop-filter: var(--glass);
            border-right: 1px solid var(--sidebar-border);
            box-shadow: var(--shadow);
            z-index: 1030;
            transition: transform .25s ease;
        }

        .sidebar .head {
            padding: 1rem .9rem;
            border-bottom: 1px dashed var(--sidebar-border);
        }

        .sidebar .head .title {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin: 0;
            font-size: .95rem;
            letter-spacing: .4px;
            color: var(--text);
        }

        .menu {
            padding: .6rem;
            margin: 0;
            list-style: none;
        }

        .menu .nav-link {
            display: flex;
            align-items: center;
            gap: .8rem;
            color: var(--text);
            text-decoration: none;
            padding: .58rem .7rem;
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
            background: var(--sidebar-hover);
        }

        .menu .nav-link.active {
            background: var(--active);
            box-shadow: inset 0 0 0 1px var(--sidebar-border);
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

        .menu .subtle {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: .6rem .8rem .2rem;
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
            box-shadow: 0 14px 32px rgba(16, 185, 129, .18);
        }

        .page-header h1 {
            font-size: 1.1rem;
            margin: 0;
            font-weight: 700;
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

        /* Utilities */
        .muted {
            color: var(--muted);
        }

        .shadow-in {
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
        }

        /* Scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 8px;
        }

        body.theme-dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }

        /* Mobile */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content {
                margin: var(--navbar-h) 0 0 0;
                padding: .9rem;
            }
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, .45);
            z-index: 1025;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Topbar -->
    <nav class="topbar">
        <div class="wrap container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn btn-sm icon-btn me-2 d-lg-none" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="brand" href="#">
                    <i class="fas fa-store"></i> <span>Seller Panel</span>
                    <span class="badge text-bg-light text-dark ms-1" style="border-radius: 999px;">CabaFood</span>
                </a>
            </div>

            <div class="top-actions">
                <button class="btn btn-sm icon-btn" title="Đổi giao diện" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>

                <button class="btn btn-sm icon-btn" title="Thông báo">
                    <i class="fas fa-bell"></i>
                </button>

                <div class="dropdown user-dd">
                    @php
                        $fullName = auth()->user()->name ?? 'Seller';
                        $initials = collect(explode(' ', trim($fullName)))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                    @endphp
                    <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2"
                        data-bs-toggle="dropdown">
                        <span class="avatar">{{ $initials ?: 'S' }}</span>
                        <span class="d-none d-sm-inline text-white">{{ $fullName }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if (\Illuminate\Support\Facades\Route::has('seller.restaurant.edit'))
                            <li><a class="dropdown-item" href="{{ route('seller.restaurant.edit') }}"><i
                                        class="fas fa-store"></i> Cửa hàng của tôi</a></li>
                        @endif
                        <li><a class="dropdown-item" href="#"><i class="fas fa-id-card"></i> Hồ sơ</a></li>
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
    <div id="sbOverlay" class="sidebar-overlay" onclick="closeSidebar()"></div>
    <aside id="sidebar" class="sidebar">
        <div class="head">
            <h6 class="title">
                <i class="fas fa-user-tie text-primary"></i>
                Khu vực Người bán
            </h6>
        </div>

        <div class="subtle">Tổng quan</div>
        <ul class="menu">
            <li>
                <a class="nav-link {{ request()->routeIs('#') ? 'active' : '' }}" href="#">
                    <i class="fas fa-gauge"></i> <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="subtle">Quản lý</div>
        <ul class="menu">
            <li>
                <a class="nav-link {{ request()->routeIs('seller.foods.index') ? 'active' : '' }}"
                    href="{{ route('seller.foods.index') }}">
                    <i class="fas fa-bowl-food"></i> <span>Danh sách món</span>
                </a>
            </li>
            {{-- <li>
                <a class="nav-link {{ request()->routeIs('seller.foods.create') ? 'active' : '' }}"
                    href="{{ route('seller.foods.create') }}">
                    <i class="fas fa-plus"></i> <span>Thêm món</span>
                </a>
            </li> --}}

            @if (\Illuminate\Support\Facades\Route::has('seller.restaurant.edit'))
                <li>
                    <a class="nav-link {{ request()->routeIs('seller.restaurant.edit') ? 'active' : '' }}"
                        href="{{ route('seller.restaurant.edit') }}">
                        <i class="fas fa-store"></i> <span>Cửa hàng của tôi</span>
                    </a>
                </li>
            @endif
        </ul>

        <div class="subtle">Tiện ích</div>
        <ul class="menu mb-3">
            <li>
                <a class="nav-link" href="#">
                    <i class="fas fa-receipt"></i> <span>Đơn hàng</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-line"></i> <span>Báo cáo</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="content">
        <section class="page-header card-surface mb-3">
            <div>
                <h1><i class="fas fa-store"></i> @yield('title', 'Bảng điều khiển Người bán')</h1>
                <div class="crumb">
                    <i class="fas fa-location-arrow me-1"></i>
                    {{ request()->route()->getName() }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @yield('page_actions')

                @if (\Illuminate\Support\Facades\Route::has('seller.restaurant.edit') && !request()->routeIs('seller.restaurant.edit'))
                    <a href="{{ route('seller.restaurant.edit') }}" class="btn btn-soft btn-sm">
                        <i class="fas fa-pen-to-square"></i> Sửa thông tin nhà hàng
                    </a>
                @endif

                <button class="btn btn-soft btn-sm">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </section>

        @yield('content')
    </main>
    @stack('scripts')
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme toggle
        function applyTheme() {
            const theme = localStorage.getItem('seller-theme') || 'light';
            document.body.classList.toggle('theme-dark', theme === 'dark');
        }
        function toggleTheme() {
            const cur = localStorage.getItem('seller-theme') || 'light';
            localStorage.setItem('seller-theme', cur === 'light' ? 'dark' : 'light');
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
    </script>
</body>

</html>
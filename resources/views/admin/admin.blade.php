<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Panel</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {{-- (Tùy chọn) Locale Tiếng Việt --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
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
            inset: 0 0 auto 0;
            height: var(--navbar-h);
            z-index: 1040;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: 0 10px 28px rgba(139, 92, 246, .25);
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
            top: var(--navbar-h);
            left: 0;
            width: var(--sidebar-w);
            height: calc(100vh - var(--navbar-h));
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
                <button class="btn btn-sm icon-btn" title="Thông báo"><i class="fas fa-bell"></i></button>

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
                <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" href="{{ url('/admin') }}">
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
                <a class="nav-link" href="#">
                    <i class="fas fa-users"></i> <span>Quản lý User</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="#">
                    <i class="fas fa-cart-shopping"></i> <span>Quản lý Đơn hàng</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="#">
                    <i class="fas fa-tags"></i> <span>Quản lý Danh mục</span>
                </a>
            </li>
        </ul>

        <div class="sb-section">Báo cáo & Cài đặt</div>
        <ul class="menu">
            <li>
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-line"></i> <span>Báo cáo</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="#">
                    <i class="fas fa-gears"></i> <span>Cài đặt hệ thống</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="content">
        <section class="page-header card-surface mb-3">
            <div>
                <h1><i class="fas fa-sitemap"></i> @yield('title', 'Bảng điều khiển')</h1>
                <div class="crumb">
                    <i class="fas fa-location-arrow me-1"></i>
                    {{ request()->route()->getName() }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @yield('page_actions')
                <button type="button" class="btn btn-soft btn-sm"><i class="fas fa-download"></i> Export</button>
            </div>
        </section>

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
    <script src="{{ asset('js/date-utils-strict.js') }}"></script>
    <script src="{{ asset('js/voucher-create-validator.js') }}"></script>
</body>

</html>
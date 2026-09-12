<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Glow & Charm Station') }} - @yield('title', 'Dashboard')</title>

    {{-- Bootstrap 5.3 + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Tailwind (Play CDN) used for a few glass utilities — preflight disabled so it never fights Bootstrap --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } };
    </script>

    <style>
        :root {
            --sidebar-w: 270px;
            --sidebar-w-collapsed: 84px;
            --glass-bg: rgba(255, 255, 255, 0.66);
            --glass-border: rgba(255, 255, 255, 0.55);
            --sidebar-tint: rgba(10, 12, 30, 0.04);
            --spring: cubic-bezier(0.22, 1, 0.36, 1);
        }

        html, body { height: 100%; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background:
                radial-gradient(1200px 600px at 100% -10%, rgba(255, 183, 213, 0.18), transparent 60%),
                radial-gradient(1000px 500px at -10% 110%, rgba(150, 190, 255, 0.18), transparent 60%),
                #eef1f7;
            color: #1d1d1f;
            -webkit-font-smoothing: antialiased;
        }

        /* ---------- Sidebar (Liquid Glass) ---------- */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            z-index: 1045;
            background: var(--glass-bg);
            backdrop-filter: saturate(180%) blur(30px);
            -webkit-backdrop-filter: saturate(180%) blur(30px);
            border-right: 1px solid var(--glass-border);
            box-shadow: 0 12px 50px rgba(20, 24, 60, 0.12);
            transition: transform .45s var(--spring), width .45s var(--spring);
            will-change: transform;
        }
        .sidebar::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.35), rgba(255,255,255,0) 30%);
            pointer-events: none;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 18px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.15rem;
            white-space: nowrap;
            overflow: hidden;
        }
        .brand img {
            height: 32px;
            width: 32px;
            object-fit: contain;
            border-radius: 8px;
        }
        .brand .bi-stars {
            font-size: 1.4rem;
            background: linear-gradient(135deg, #ff7eb3, #9b6bff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar-collapse-btn {
            margin-left: auto;
            width: 34px; height: 34px;
            display: inline-flex; align-items: center; justify-content: center;
            border: none; border-radius: 12px;
            background: rgba(0,0,0,0.05);
            color: #1d1d1f;
            cursor: pointer;
            transition: background .25s var(--spring), transform .25s var(--spring);
        }
        .sidebar-collapse-btn:hover { background: rgba(0,0,0,0.09); }
        .sidebar-collapse-btn:active { transform: scale(0.94); }

        body.dashboard-page .sidebar { width: var(--sidebar-w) !important; }
        body.dashboard-page .main-content { margin-left: var(--sidebar-w) !important; }
        body.dashboard-page .sidebar-collapse-btn { display: none !important; }
        body.dashboard-page .sidebar-label,
        body.dashboard-page .brand-text,
        body.dashboard-page .sidebar-footer .meta { display: inline !important; }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 14px;
            color: #3a3a3c;
            font-weight: 550;
            white-space: nowrap;
            transition: background .25s var(--spring), color .25s var(--spring), transform .15s var(--spring);
        }
        .sidebar-nav .nav-link .bi {
            font-size: 1.25rem;
            width: 24px; text-align: center;
            background: rgba(255,255,255,0.55);
            border-radius: 10px;
            padding: 6px 0;
            transition: background .25s var(--spring), color .25s var(--spring);
        }
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.7);
            color: #1d1d1f;
            transform: translateX(2px);
        }
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,0.85);
            color: #1d1d1f;
            box-shadow: 0 6px 18px rgba(20,24,60,0.10), inset 0 0 0 1px rgba(255,255,255,0.6);
        }
        .sidebar-nav .nav-link.active .bi {
            background: linear-gradient(135deg, #ff7eb3, #9b6bff);
            color: #fff;
        }

        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
            overflow: hidden;
        }
        .avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff;
            background: linear-gradient(135deg, #9b6bff, #5b8def);
            flex: 0 0 auto;
        }

        /* ---------- Backdrop ---------- */
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 17, 35, 0.35);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            z-index: 1040;
            transition: opacity .35s var(--spring), visibility .35s var(--spring);
        }

        /* ---------- Main ---------- */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            transition: margin-left .45s var(--spring);
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            background: var(--glass-bg);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid var(--glass-border);
        }
        .hamburger {
            width: 40px; height: 40px;
            display: inline-flex; align-items: center; justify-content: center;
            border: none; border-radius: 12px;
            background: rgba(0,0,0,0.05);
            color: #1d1d1f; font-size: 1.3rem;
            cursor: pointer;
            transition: background .25s var(--spring), transform .15s var(--spring);
        }
        .hamburger:hover { background: rgba(0,0,0,0.09); }
        .hamburger:active { transform: scale(0.94); }

        .content-area { flex: 1; }

        .card-stat {
            border: none;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(20,24,60,0.08);
            background: rgba(255,255,255,0.72);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .card-stat .icon {
            width: 50px; height: 50px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        .card-hover { transition: transform .2s ease, box-shadow .2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(20,24,60,0.12); }

        /* ---------- Desktop collapsed rail ---------- */
        @media (min-width: 768px) {
            body.sidebar-collapsed .sidebar { width: var(--sidebar-w-collapsed); }
            body.sidebar-collapsed .main-content { margin-left: var(--sidebar-w-collapsed); }
            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .brand-text,
            body.sidebar-collapsed .sidebar-footer .meta { display: none; }
            body.sidebar-collapsed .sidebar-nav .nav-link { justify-content: center; }
            body.sidebar-collapsed .sidebar-collapse-btn .bi { transform: rotate(180deg); }
        }

        /* ---------- Mobile: off-canvas drawer ---------- */
        @media (max-width: 767.98px) {
            .sidebar {
                width: min(82vw, var(--sidebar-w));
                transform: translateX(-100%);
                box-shadow: 0 20px 60px rgba(20,24,60,0.28);
            }
            .main-content { margin-left: 0; }
            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-open .sidebar-backdrop { opacity: 1; visibility: visible; }
        }

        @media (prefers-color-scheme: dark) {
            :root { --glass-bg: rgba(28, 30, 48, 0.66); --glass-border: rgba(255,255,255,0.12); }
            body { background: #0c0e1a; color: #f2f2f7; }
            .sidebar-nav .nav-link { color: #d1d1d6; }
            .sidebar-nav .nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
            .sidebar-nav .nav-link.active { background: rgba(255,255,255,0.14); color: #fff; }
            .sidebar-nav .nav-link .bi { background: rgba(255,255,255,0.10); }
            .top-navbar, .card-stat { background: rgba(28,30,48,0.6); color: #f2f2f7; }
            .hamburger, .sidebar-collapse-btn { background: rgba(255,255,255,0.10); color: #f2f2f7; }
        }
    </style>
</head>
<body @if(request()->routeIs('dashboard')) class="dashboard-page" @endif>
    @auth
        {{-- Slide-out sidebar --}}
        <aside class="sidebar backdrop-blur-2xl" id="sidebar" aria-label="Main navigation">
            <div class="sidebar-header">
                <div class="brand">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    @else
                        <i class="bi bi-stars"></i>
                    @endif
                    <span class="brand-text">Glow &amp; Charm</span>
                </div>
                <button class="sidebar-collapse-btn d-none d-md-inline-flex" id="collapseBtn" type="button" aria-label="Collapse sidebar">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->canDo('dashboard.view'))
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i><span class="sidebar-label">Dashboard</span>
                </a>
                @endif
                @if(auth()->user()->canDo('categories.view'))
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="bi bi-tags"></i><span class="sidebar-label">Categories</span>
                </a>
                @endif
                @if(auth()->user()->canDo('products.view'))
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="bi bi-box-seam"></i><span class="sidebar-label">Products</span>
                </a>
                @endif
                @if(auth()->user()->canDo('services.view'))
                <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                    <i class="bi bi-gem"></i><span class="sidebar-label">Services</span>
                </a>
                @endif
                @if(auth()->user()->canDo('customers.view'))
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                    <i class="bi bi-people"></i><span class="sidebar-label">Customers</span>
                </a>
                @endif
                @if(auth()->user()->canDo('suppliers.view'))
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                    <i class="bi bi-truck"></i><span class="sidebar-label">Suppliers</span>
                </a>
                @endif
                @if(auth()->user()->canDo('orders.view'))
                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="bi bi-cart3"></i><span class="sidebar-label">Orders</span>
                </a>
                @endif
                @if(auth()->user()->isAdmin())
                <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">
                    <i class="bi bi-person-gear"></i><span class="sidebar-label">Staff</span>
                </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="meta">
                    <div class="fw-semibold" style="font-size:.9rem; line-height:1.1;">{{ Auth::user()->name }}</div>
                    <div class="text-muted" style="font-size:.75rem;">Administrator</div>
                </div>
            </div>
        </aside>

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="main-content">
            <nav class="top-navbar backdrop-blur-xl">
                <button class="hamburger d-md-none" id="sidebarToggle" type="button" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand fw-semibold mb-0">@yield('title', 'Dashboard')</span>
                <div class="d-flex align-items-center ms-auto">
                    <span class="me-3 d-none d-sm-inline text-secondary">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-box-arrow-right"></i><span class="d-none d-sm-inline ms-1">Logout</span>
                        </button>
                    </form>
                </div>
            </nav>

            <div class="content-area px-3 px-md-4 pb-4 pt-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content', $slot ?? '')
            </div>
        </div>
    @else
        <div class="min-h-screen">
            @yield('content', $slot ?? '')
        </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            'use strict';

            const body = document.body;
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const toggle = document.getElementById('sidebarToggle');
            const collapse = document.getElementById('collapseBtn');

            const isMobile = () => window.matchMedia('(max-width: 767.98px)').matches;

            const openSidebar = () => body.classList.add('sidebar-open');
            const closeSidebar = () => body.classList.remove('sidebar-open');
            const toggleSidebar = () => body.classList.toggle('sidebar-open');
            const toggleCollapse = () => body.classList.toggle('sidebar-collapsed');

            // Mobile drawer open/close
            toggle?.addEventListener('click', toggleSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            // Desktop rail collapse
            collapse?.addEventListener('click', toggleCollapse);

            // Close drawer after tapping a link on mobile (feels native)
            sidebar?.querySelectorAll('.nav-link').forEach((link) => {
                link.addEventListener('click', () => { if (isMobile()) closeSidebar(); });
            });

            // ESC closes the drawer
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && body.classList.contains('sidebar-open')) closeSidebar();
            });

            // iOS-style swipe-to-close on the drawer
            let startX = 0, tracking = false;
            sidebar?.addEventListener('touchstart', (e) => {
                if (!isMobile() || !body.classList.contains('sidebar-open')) return;
                startX = e.touches[0].clientX;
                tracking = true;
            }, { passive: true });

            sidebar?.addEventListener('touchmove', (e) => {
                if (!tracking) return;
                const dx = e.touches[0].clientX - startX;
                if (dx < 0) sidebar.style.transform = `translateX(${dx}px)`;
            }, { passive: true });

            const endSwipe = () => {
                if (!tracking) return;
                tracking = false;
                const current = sidebar.style.transform;
                sidebar.style.transform = '';
                if (current && current.includes('-') && parseInt(current.replace(/[^-\d]/g, ''), 10) < -60) {
                    closeSidebar();
                }
            };
            sidebar?.addEventListener('touchend', endSwipe);
            sidebar?.addEventListener('touchcancel', endSwipe);

            // Reset transform when leaving mobile so the inline style never sticks
            window.addEventListener('resize', () => { if (!isMobile()) sidebar.style.transform = ''; });
        })();
    </script>
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SiKeuangan')</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
@include('sidebar')

{{-- CONTENT --}}
<div class="content">

    {{-- MOBILE HEADER --}}
    <div class="mobile-header">
        <button
            id="openSidebar"
            class="btn-hamburger"
            aria-label="Buka menu"
        >
            ☰
        </button>

        <div class="mobile-title">
            <span class="mobile-page-title">
                @yield('title', 'Dashboard')
            </span>
        </div>
    </div>

    {{-- PAGE CONTENT --}}
    @yield('content')
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    const btnDesktopBurger = document.getElementById('btnSidebarToggle');
    const btnMobileOpen    = document.getElementById('openSidebar'); 
    const btnMobileClose   = document.getElementById('closeSidebar'); 

    if (btnDesktopBurger) {
        btnDesktopBurger.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
    }

    if (btnMobileOpen) {
        btnMobileOpen.addEventListener('click', function () {
            sidebar.classList.add('show');
            overlay.classList.add('show');
        });
    }

    if (btnMobileClose) {
        btnMobileClose.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    });

});
</script>

@stack('scripts')

</body>
</html>

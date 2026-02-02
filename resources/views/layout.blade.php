<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SiKeuangan')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
</head>
<body>

@include('sidebar')

<div class="content">
    @yield('content')
</div>

<script>
const sidebar = document.getElementById('sidebar');
const btnArrow = document.getElementById('btnArrow');
const btnHamburger = document.getElementById('btnHamburger');
const overlay = document.getElementById('overlay');

if (btnArrow) {
    btnArrow.addEventListener('click', () => {
        sidebar.classList.add('collapsed');
    });
}

if (btnHamburger) {
    btnHamburger.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('show');
            overlay.classList.add('show');
        } else {
            sidebar.classList.remove('collapsed');
        }
    });
}

if (overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
}

window.addEventListener('resize', () => {
    if (window.innerWidth <= 768) {
        sidebar.classList.remove('collapsed');
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    }
});
</script>

@stack('scripts')

</body>
</html>

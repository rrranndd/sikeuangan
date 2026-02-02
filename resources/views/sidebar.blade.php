<div class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <h2 class="logo">SiKeuangan</h2>

        <button class="btn-arrow" id="btnArrow">←</button>
        <button class="btn-hamburger" id="btnHamburger">☰</button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="7" r="4"/>
                <path d="M5.5 21a6.5 6.5 0 0 1 13 0"/>
            </svg>
        </div>

        <div class="user-info">
            <strong>{{ session('user_name') }}</strong>
        </div>
    </div>

    <nav class="sidebar-menu">

        <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11h-6v-7h-6v7H3z"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="/transaksi" class="{{ request()->is('transaksi') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <polyline points="7 10 12 5 17 10"/>
                <line x1="12" y1="5" x2="12" y2="19"/>
                <polyline points="17 14 12 19 7 14"/>
            </svg>
            <span>Transaksi</span>
        </a>

        <a href="/laporan" class="{{ request()->is('laporan') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <line x1="4" y1="20" x2="20" y2="20"/>
                <line x1="7" y1="16" x2="7" y2="10"/>
                <line x1="12" y1="16" x2="12" y2="6"/>
                <line x1="17" y1="16" x2="17" y2="12"/>
            </svg>
            <span>Laporan</span>
        </a>

        <a href="/profile" class="{{ request()->is('profile') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="7" r="4"/>
                <path d="M4 21v-2a4 4 0 0 1 4-4h8
                         a4 4 0 0 1 4 4v2"/>
            </svg>
            <span>Profile</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <svg viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5
                             a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                <span>Logout</span>
            </button>
        </form>

    </nav>
</div>

<div class="overlay" id="overlay"></div>

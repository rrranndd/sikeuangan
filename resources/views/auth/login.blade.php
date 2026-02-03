<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SiKeuangan</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="auth-title">SiKeuangan</h1>
        <p class="auth-subtitle">Silakan login</p>

        @if(session('error'))
            <div class="auth-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="auth-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="auth-group password-group">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password"
                    required
                >

                <button type="button"
                        class="toggle-password"
                        onclick="togglePassword('password', this)">
                    <svg class="eye-open" viewBox="0 0 24 24">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>

                    <svg class="eye-closed" viewBox="0 0 24 24">
                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19
                                c-7 0-11-7-11-7a21.77 21.77 0 0 1 5.08-5.94"/>
                        <path d="M1 1l22 22"/>
                    </svg>
                </button>
            </div>

            <button type="submit" class="auth-button">Login</button>
        </form>

        <p class="auth-footer">
            Belum punya akun?
            <a href="/register">Daftar</a>
        </p>
    </div>
</div>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const eyeOpen = btn.querySelector('.eye-open');
    const eyeClosed = btn.querySelector('.eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
    } else {
        input.type = 'password';
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
    }
}
</script>


</body>
</html>

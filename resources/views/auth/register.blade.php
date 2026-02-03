<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | SiKeuangan</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="auth-title">SiKeuangan</h1>
        <p class="auth-subtitle">Buat akun baru</p>

        @if ($errors->any())
            <div class="auth-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf

            <div class="auth-group">
                <input type="text" name="name" placeholder="Nama lengkap" required>
            </div>

            <div class="auth-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="auth-group password-group">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password (min. 8 karakter)"
                    required
                    oninput="checkPasswordStrength(this.value)"
                >

                <button type="button"
                        class="toggle-password"
                        onclick="togglePassword('password', this)">
                    <svg class="eye-open" viewBox="0 0 24 24">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg class="eye-closed" viewBox="0 0 24 24">
                        <path d="M1 1l22 22"/>
                    </svg>
                </button>
            </div>

            <div class="auth-group password-group">
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirm"
                    placeholder="Konfirmasi Password"
                    required
                >

                <button type="button"
                        class="toggle-password"
                        onclick="togglePassword('password_confirm', this)">
                    <svg class="eye-open" viewBox="0 0 24 24">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg class="eye-closed" viewBox="0 0 24 24">
                        <path d="M1 1l22 22"/>
                    </svg>
                </button>
            </div>

            <div class="password-meter">
                <div class="password-meter-bar" id="passwordMeter"></div>
            </div>
            <p class="password-hint" id="passwordHint">
                Gunakan minimal 8 karakter
            </p>

            <button type="submit" class="auth-button">
                Daftar
            </button>
        </form>

        <p class="auth-footer">
            Sudah punya akun?
            <a href="/login">Login</a>
        </p>
    </div>
</div>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const open  = btn.querySelector('.eye-open');
    const close = btn.querySelector('.eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        open.style.display = 'none';
        close.style.display = 'block';
    } else {
        input.type = 'password';
        open.style.display = 'block';
        close.style.display = 'none';
    }
}

function checkPasswordStrength(password) {
    const meter = document.getElementById('passwordMeter');
    const hint  = document.getElementById('passwordHint');

    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    meter.className = 'password-meter-bar';

    if (!password) {
        meter.style.width = '0%';
        hint.textContent = 'Gunakan minimal 8 karakter';
        return;
    }

    if (strength <= 1) {
        meter.style.width = '25%';
        meter.classList.add('password-weak');
        hint.textContent = 'Password lemah';
    } else if (strength <= 3) {
        meter.style.width = '60%';
        meter.classList.add('password-medium');
        hint.textContent = 'Password cukup kuat';
    } else {
        meter.style.width = '100%';
        meter.classList.add('password-strong');
        hint.textContent = 'Password sangat kuat';
    }
}
</script>

</body>
</html>

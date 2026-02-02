@extends('layout')

@section('title', 'Profile | SiKeuangan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')

<div class="page-title">
    <h1>Profil Akun</h1>
    <p>Kelola informasi akun Anda</p>
</div>

<div class="profile-wrapper">

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="profile-card">
        <form method="POST" action="/profile/update" class="profile-form">
            @csrf

            <div class="form-group">
                <label>Nama</label>
                <input
                    type="text"
                    name="name"
                    value="{{ $user->name }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    value="{{ $user->email }}"
                    disabled
                >
            </div>

            <hr>

            <div class="form-group">
                <label>Password Baru (opsional)</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Minimal 8 karakter"
                >
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input
                    type="password"
                    name="password_confirmation"
                >
            </div>

            <div class="profile-actions">
                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>

@endsection

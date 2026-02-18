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
        <form method="POST"
            action="/profile/update"
            enctype="multipart/form-data"
            class="profile-form">

            @csrf

            <div class="profile-photo">
                <img
                    id="photoPreview"
                    src="{{ $user->photo
                        ? asset('storage/'.$user->photo)
                        : asset('img/default-user.png') }}"
                    alt="Foto Profile">

                <input
                    type="file"
                    name="photo"
                    id="photoInput"
                    accept="image/*">

            </div>

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

<script>
document.getElementById('photoInput')
.addEventListener('change', function(e){

    const file = e.target.files[0];

    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Hanya file gambar yang diperbolehkan!');
        e.target.value = '';
        return;
    }

    const reader = new FileReader();

    reader.onload = function(ev){
        document.getElementById('photoPreview')
            .src = ev.target.result;
    };

    reader.readAsDataURL(file);
});
</script>


@endsection

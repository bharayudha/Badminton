@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="auth-icon"><i class="bi bi-shuttlecock"></i></div>
    <span class="eyebrow">SELAMAT DATANG KEMBALI</span>
    <h1>Masuk ke akun</h1>
    <p class="muted-copy">Kelola pemain dan susun sesi badminton Anda.</p>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('login.store') }}" class="form-stack">
        @csrf
        <div><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required autofocus></div>
        <div><label class="form-label" for="password">Kata sandi</label><input class="form-control" id="password" name="password" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required></div>
        <label class="form-check"><input class="form-check-input" type="checkbox" name="remember" value="1"><span class="form-check-label">Ingat saya</span></label>
        <button class="btn btn-primary w-100 btn-lg">Masuk <i class="bi bi-arrow-right ms-1"></i></button>
    </form>
    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai pemain</a></p>
@endsection

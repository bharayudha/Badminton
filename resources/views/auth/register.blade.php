@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
    <div class="auth-icon"><i class="bi bi-person-plus"></i></div>
    <span class="eyebrow">MULAI BERMAIN BERSAMA</span>
    <h1>Buat akun pemain</h1>
    <p class="muted-copy">Daftarkan diri Anda. Level kemampuan dapat diisi sendiri.</p>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('register.store') }}" class="form-stack">
        @csrf
        <div><label class="form-label" for="name">Nama lengkap</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Nama Anda" autocomplete="name" required autofocus></div>
        <div><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required></div>
        <div><label class="form-label" for="password">Kata sandi</label><input class="form-control" id="password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required></div>
        <div><label class="form-label" for="password_confirmation">Ulangi kata sandi</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" autocomplete="new-password" required></div>
        <button class="btn btn-primary w-100 btn-lg">Buat akun <i class="bi bi-arrow-right ms-1"></i></button>
    </form>
    <p class="auth-switch">Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
@endsection

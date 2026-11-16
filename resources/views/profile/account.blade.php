@extends('layouts.app')
@section('title', 'Profil Saya')
@section('breadcrumb', 'Profil saya')
@section('content')
<div class="page-heading"><div><span class="eyebrow">AKUN SAYA</span><h1>Profil administrator</h1><p>Perbarui informasi akun administrator Anda.</p></div></div>
<form method="POST" action="{{ route('profile.update') }}" class="panel form-panel">@csrf @method('PUT')
    <div class="form-section-title"><span class="form-section-icon"><i class="bi bi-person-vcard"></i></span><div><strong>Informasi akun</strong><small>Akun administrator ini tidak terdaftar sebagai pemain.</small></div></div>
    <div class="row g-3"><div class="col-md-6"><label class="form-label" for="name">Nama lengkap</label><input class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required></div><div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required></div></div>
    <div class="form-actions"><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Simpan profil</button></div>
</form>
@endsection

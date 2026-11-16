<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Partner Badminton</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark"><i class="bi bi-dribbble"></i></span>
            <span><strong>Rally<span class="brand-accent">Match</span></strong><small>BADMINTON MANAGER</small></span>
        </a>
        <div class="nav-caption">MENU UTAMA</div>
        <nav class="side-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('players.index') }}" class="{{ request()->routeIs('players.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Data pemain</a>
                <a href="{{ route('pairing.create') }}" class="{{ request()->routeIs('pairing.create') ? 'active' : '' }}"><i class="bi bi-shuffle"></i> Pengaturan partner</a>
                <a href="{{ route('pairing.index') }}" class="{{ request()->routeIs('pairing.index', 'pairing.show') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> Riwayat pengaturan</a>
                <div class="nav-caption nav-caption-spaced">ADMINISTRASI</div>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-person-gear"></i> Manajemen pengguna</a>
            @endif
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}"><i class="bi bi-person-circle"></i> Profil saya</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="sidebar-help"><div class="help-icon"><i class="bi bi-info-circle"></i></div><strong>Racik tim terbaikmu</strong><p>Atur pemain dengan kriteria yang transparan dan terukur.</p><a href="{{ route('pairing.create') }}">Mulai pengaturan <i class="bi bi-arrow-up-right"></i></a></div>
            <div class="user-mini">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-mini-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Pemain' }}</small></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="icon-button" title="Keluar" aria-label="Keluar"><i class="bi bi-box-arrow-right"></i></button></form>
            </div>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <button type="button" class="icon-button mobile-menu" id="menuToggle" aria-label="Buka navigasi"><i class="bi bi-list"></i></button>
            <div class="breadcrumb-label">@yield('breadcrumb', 'Ringkasan')</div>
            <div class="topbar-right"><span class="today-label"><i class="bi bi-calendar3"></i> {{ now()->translatedFormat('l, d F Y') }}</span><div class="avatar avatar-small">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div></div>
        </header>
        <div class="page-container">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Periksa kembali data yang dimasukkan.</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>

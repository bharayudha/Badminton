@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Ringkasan')

@section('content')
    <div class="welcome-banner">
        <div class="welcome-copy"><span class="eyebrow eyebrow-light">DASHBOARD</span><h1>Halo, {{ auth()->user()->name }} <span>👋</span></h1><p>Siap menyusun sesi badminton yang lebih seru hari ini?</p><div class="welcome-actions">@if(auth()->user()->role === 'admin')<a href="{{ route('pairing.create') }}" class="btn btn-light">Mulai pengaturan <i class="bi bi-arrow-right ms-1"></i></a>@else<a href="{{ route('profile.edit') }}" class="btn btn-light">Lengkapi profil <i class="bi bi-arrow-right ms-1"></i></a>@endif</div></div>
        <div class="banner-art"><div class="banner-circle circle-one"></div><div class="banner-circle circle-two"></div><i class="bi bi-trophy-fill"></i><span>PLAY<br>BETTER<br>TOGETHER</span></div>
    </div>

    <div class="section-heading"><div><span class="eyebrow">SEKILAS DATA</span><h2>Statistik pemain</h2></div>@if(auth()->user()->role === 'admin')<a href="{{ route('players.index') }}" class="text-link">Lihat pemain <i class="bi bi-arrow-right"></i></a>@endif</div>
    <div class="stats-grid">
        <article class="stat-card"><div class="stat-icon stat-blue"><i class="bi bi-people"></i></div><div><span>Total pemain</span><strong>{{ number_format($totalPlayers) }}</strong><small>pemain terdaftar</small></div><i class="bi bi-arrow-up-right stat-corner"></i></article>
        <article class="stat-card"><div class="stat-icon stat-orange"><i class="bi bi-lightning-charge"></i></div><div><span>Pemula</span><strong>{{ number_format($beginnerPlayers) }}</strong><small>level kemampuan</small></div><i class="bi bi-arrow-up-right stat-corner"></i></article>
        <article class="stat-card"><div class="stat-icon stat-purple"><i class="bi bi-activity"></i></div><div><span>Menengah</span><strong>{{ number_format($intermediatePlayers) }}</strong><small>level kemampuan</small></div><i class="bi bi-arrow-up-right stat-corner"></i></article>
        <article class="stat-card"><div class="stat-icon stat-green"><i class="bi bi-stars"></i></div><div><span>Mahir</span><strong>{{ number_format($advancedPlayers) }}</strong><small>level kemampuan</small></div><i class="bi bi-arrow-up-right stat-corner"></i></article>
    </div>
    <div class="dashboard-bottom">
        <section class="panel activity-panel">
            <div class="panel-heading"><div><span class="eyebrow">AKTIVITAS</span><h2>Aktivitas terbaru</h2></div><span class="activity-total"><i class="bi bi-arrow-repeat"></i> {{ $pairingSessionCount }} sesi</span></div>
            @forelse($recentSessions as $session)
                <a class="activity-row" href="{{ auth()->user()->role === 'admin' ? route('pairing.show', $session) : route('profile.edit') }}"><span class="activity-icon"><i class="bi bi-shuffle"></i></span><span class="activity-text"><strong>{{ $session->mode === 'tim_seimbang' ? 'Pembagian tim seimbang' : 'Pencarian partner' }}</strong><small>{{ $session->createdBy?->name ?? 'Pengguna' }} · {{ $session->created_at->diffForHumans() }}</small></span><i class="bi bi-chevron-right activity-arrow"></i></a>
            @empty
                <div class="empty-state"><span class="empty-icon"><i class="bi bi-calendar2-plus"></i></span><strong>Belum ada aktivitas</strong><p>Sesi pengaturan yang dibuat akan tampil di sini.</p>@if(auth()->user()->role === 'admin')<a href="{{ route('pairing.create') }}" class="btn btn-primary btn-sm">Buat sesi pertama</a>@endif</div>
            @endforelse
        </section>
        <aside class="panel insight-panel"><div class="insight-top"><span class="insight-icon"><i class="bi bi-lightbulb"></i></span><span class="eyebrow">CATATAN</span></div><h3>Profil yang lengkap, sesi lebih bermakna.</h3><p>Data level dan pengalaman digunakan sebagai masukan skor prioritas. Pemain mengisi levelnya sendiri, dan bobot ditentukan pada setiap sesi.</p><a href="{{ route('profile.edit') }}" class="text-link">Perbarui profil <i class="bi bi-arrow-right"></i></a><div class="insight-decoration"><i class="bi bi-shuttlecock"></i></div></aside>
    </div>
@endsection

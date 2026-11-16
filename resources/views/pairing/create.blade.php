@extends('layouts.app')
@section('title', 'Pengaturan Partner')
@section('breadcrumb', 'Pengaturan partner')
@section('content')
<div class="page-heading"><div><span class="eyebrow">RACIK SESI</span><h1>Pengaturan partner</h1><p>Pilih pemain, tetapkan bobot sesi, lalu tinjau susunan awal.</p></div><a href="{{ route('pairing.index') }}" class="btn btn-outline-secondary"><i class="bi bi-clock-history me-1"></i> Riwayat</a></div>
<form method="POST" action="{{ route('pairing.store') }}" id="pairingForm">@csrf
<div class="pairing-layout"><section class="panel form-panel player-picker">
    <div class="panel-heading"><div><span class="eyebrow">LANGKAH 1</span><h2>Pilih pemain</h2></div><span class="selected-count"><strong id="selectedCount">0</strong> dipilih</span></div>
    <div class="search-box picker-search"><i class="bi bi-search"></i><input type="search" id="playerSearch" placeholder="Cari nama pemain..." aria-label="Cari pemain"></div>
    <div class="picker-list">
        @forelse($players as $player)
        <label class="picker-row" data-player-name="{{ strtolower($player->user->name) }}"><input class="form-check-input player-checkbox" type="checkbox" name="player_ids[]" value="{{ $player->id }}" @checked(in_array($player->id, old('player_ids', [])))><span class="avatar avatar-small">{{ strtoupper(substr($player->user->name, 0, 1)) }}</span><span class="picker-player"><strong>{{ $player->user->name }}</strong><small>{{ ucfirst(str_replace('_', ' ', $player->skill_level)) }} · {{ $player->experience?->experience_years ?? 0 }} thn pengalaman</small></span><i class="bi bi-check-circle-fill picker-check"></i></label>
        @empty<div class="empty-state"><strong>Belum ada pemain</strong><p>Tambahkan pemain sebelum membuat sesi.</p><a href="{{ route('players.create') }}" class="btn btn-primary btn-sm">Tambah pemain</a></div>@endforelse
    </div>
</section>
<aside class="pairing-side">
    <section class="panel form-panel"><div class="panel-heading"><div><span class="eyebrow">LANGKAH 2</span><h2>Mode pengaturan</h2></div><span class="step-number">02</span></div>
        <label class="mode-option"><input class="form-check-input" type="radio" name="mode" value="pencarian_partner" @checked(old('mode', 'pencarian_partner') === 'pencarian_partner')><span class="mode-icon mode-blue"><i class="bi bi-person-arms-up"></i></span><span><strong>Pencarian partner</strong><small>Pasangkan pemain berdasarkan urutan skor.</small></span></label>
        <label class="mode-option"><input class="form-check-input" type="radio" name="mode" value="tim_seimbang" @checked(old('mode') === 'tim_seimbang')><span class="mode-icon mode-purple"><i class="bi bi-people"></i></span><span><strong>Pembagian tim seimbang</strong><small>Bagikan pemain ke dua tim dengan pola skor [1, 2, 2, 1] berulang.</small></span></label>
    </section>
    <section class="panel form-panel"><div class="panel-heading"><div><span class="eyebrow">LANGKAH 3</span><h2>Bobot prioritas</h2></div><span class="step-number">03</span></div><p class="form-hint mt-0">Bobot dipilih untuk sesi ini; belum ada bobot final penelitian. Total wajib 100%.</p>
        <div class="weight-row"><label for="weightLevel">Level kemampuan</label><div class="weight-input"><input class="form-control weight-field" id="weightLevel" name="weights[level]" type="number" min="0" max="100" value="{{ old('weights.level') }}" placeholder="—" required><span>%</span></div></div>
        <div class="weight-row"><label for="weightExperience">Pengalaman (tahun)</label><div class="weight-input"><input class="form-control weight-field" id="weightExperience" name="weights[experience]" type="number" min="0" max="100" value="{{ old('weights.experience') }}" placeholder="—" required><span>%</span></div></div>
        <div class="weight-total"><span>Total bobot</span><strong id="weightTotal">0%</strong></div>
        <label class="form-label mt-3" for="notes">Catatan sesi (opsional)</label><textarea class="form-control" name="notes" id="notes" rows="2" maxlength="2000" placeholder="Tambahkan catatan...">{{ old('notes') }}</textarea>
    </section>
    <div class="algorithm-note"><i class="bi bi-info-circle"></i><span>Hasil merupakan susunan awal berbasis skor dan aturan sederhana, bukan klaim pasangan optimal. Nilai level: Pemula 1, Menengah 2, Mahir 3, Belum diketahui 0; pengalaman memakai jumlah tahun.</span></div>
    <button class="btn btn-primary btn-lg w-100" type="submit"><i class="bi bi-shuffle me-2"></i>Proses pengaturan</button>
</aside></div></form>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const checks = [...document.querySelectorAll('.player-checkbox')];
    const count = document.getElementById('selectedCount');
    const search = document.getElementById('playerSearch');
    const weightFields = [...document.querySelectorAll('.weight-field')];
    const total = document.getElementById('weightTotal');
    const update = () => { count.textContent = checks.filter((item) => item.checked).length; };
    const updateWeight = () => { const sum = weightFields.reduce((value, item) => value + (Number(item.value) || 0), 0); total.textContent = `${sum}%`; total.classList.toggle('text-success', sum === 100); };
    checks.forEach((item) => item.addEventListener('change', update));
    weightFields.forEach((item) => item.addEventListener('input', updateWeight));
    search.addEventListener('input', () => { const term = search.value.toLowerCase(); document.querySelectorAll('.picker-row').forEach((row) => { row.hidden = !row.dataset.playerName.includes(term); }); });
    document.getElementById('pairingForm').addEventListener('submit', (event) => {
        if (weightFields.reduce((value, item) => value + (Number(item.value) || 0), 0) !== 100) { event.preventDefault(); alert('Total bobot harus berjumlah tepat 100%.'); }
    });
    update(); updateWeight();
});
</script>
@endpush
@endsection

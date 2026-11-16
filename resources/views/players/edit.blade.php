@extends('layouts.app')
@section('title', 'Edit Pemain')
@section('breadcrumb', 'Data pemain / Edit')
@section('content')
<div class="page-heading"><div><span class="eyebrow">DIREKTORI</span><h1>Edit pemain</h1><p>Perbarui informasi {{ $player->user->name }}.</p></div><a href="{{ route('players.show', $player) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a></div>
<form class="panel form-panel" method="POST" action="{{ route('players.update', $player) }}">@csrf @method('PUT') @include('players._form', ['player' => $player])<p class="form-hint mt-3">Untuk mengganti kata sandi, gunakan fitur reset kata sandi pada tahap berikutnya.</p></form>
@endsection

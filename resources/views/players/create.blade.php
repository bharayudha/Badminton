@extends('layouts.app')
@section('title', 'Tambah Pemain')
@section('breadcrumb', 'Data pemain / Tambah')
@section('content')
<div class="page-heading"><div><span class="eyebrow">DIREKTORI</span><h1>Tambah pemain</h1><p>Buat akun pemain dan lengkapi data badminton.</p></div><a href="{{ route('players.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a></div>
<form class="panel form-panel" method="POST" action="{{ route('players.store') }}">@csrf @include('players._form', ['player' => null])</form>
@endsection

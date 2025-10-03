@extends('layouts.dashboard')

@section('title', 'Riwayat SPPD Saya')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Memanggil partial tabel dengan data pribadi --}}
    @include('partials.table.table-sppd-acceptance', ['sppds' => $sppds, 'isApprovalPage' => false])
</div>
@endsection

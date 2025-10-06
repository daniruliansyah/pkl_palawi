@extends('layouts.dashboard')

@section('title', 'Persetujuan SPPD')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Kotak 1: Menunggu Persetujuan --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h3 class="text-xl font-semibold text-gray-800">
                Menunggu Persetujuan Anda
            </h3>
            <p class="text-sm text-gray-500">Daftar pengajuan SPPD yang memerlukan tindakan dari Anda.</p>
        </div>
        
        @include('partials.table.table-sppd-acceptance', ['sppds' => $sppdsForApproval, 'isApprovalPage' => true])
    </div>

    {{-- Kotak 2: Riwayat Tindakan --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h3 class="text-xl font-semibold text-gray-800">
                Riwayat Tindakan Anda (SPPD)
            </h3>
            <p class="text-sm text-gray-500">Daftar pengajuan SPPD yang telah Anda proses.</p>
        </div>

        @include('partials.table.table-sppd-acceptance', ['sppds' => $sppdsHistory, 'isApprovalPage' => false])
    </div>
</div>
@endsection
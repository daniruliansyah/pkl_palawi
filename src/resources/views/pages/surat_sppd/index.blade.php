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

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">
                    Riwayat Pengajuan SPPD Anda
                </h3>
                <p class="text-sm text-gray-500">
                    Daftar semua surat perjalanan dinas yang pernah Anda ajukan.
                </p>
            </div>
            <div>
                <a href="{{ route('sppd.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700">
                    Ajukan SPPD Baru
                </a>
            </div>
        </div>

        {{-- Memanggil partial tabel yang sudah bersih --}}
        @include('partials.table.table-sppd-acceptance', ['sppds' => $sppds, 'isApprovalPage' => false])
    </div>
</div>
@endsection
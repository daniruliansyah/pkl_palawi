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
    {{-- Form Download Laporan --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <h3 class="text-lg font-semibold text-gray-800">Download Laporan Arsip SPPD</h3>
        <p class="text-sm text-gray-500 mb-4">Pilih periode untuk mengunduh semua SPPD yang disetujui dalam format ZIP.</p>

        {{-- Arahkan 'action' ke route SPPD --}}
        <form action="{{ route('sppd.downloadReport') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4">
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                <select name="bulan" id="bulan" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="all">Semua Bulan (Tahunan)</option>
                    @foreach(range(1, 12) as $month)
                        {{-- Gunakan Carbon::create() --}}
                        <option value="{{ $month }}" {{ request('bulan', date('m')) == $month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($month)->isoFormat('MMMM') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                <select name="tahun" id="tahun" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach(range(date('Y'), date('Y') - 5) as $year)
                        <option value="{{ $year }}" {{ request('tahun', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="mt-4 sm:mt-0 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                Download Laporan
            </button>
        </form>
    </div>

    {{-- ... Sisa konten halaman (tabel persetujuan, dll) ... --}}

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

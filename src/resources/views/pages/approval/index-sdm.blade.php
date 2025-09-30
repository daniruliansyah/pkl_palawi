@extends('layouts.dashboard')

@section('title', 'Persetujuan Cuti SDM')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Tabel 1: Menunggu Persetujuan --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Pengajuan Cuti (Menunggu Persetujuan SDM)</h3>
            <p class="text-sm text-gray-500">Berikut adalah daftar pengajuan cuti yang memerlukan tindakan dari Anda.</p>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Pemohon</th>
                        <th class="py-3 px-4 font-medium">Jabatan</th>
                        <th class="py-3 px-4 font-medium">Jenis Izin</th>
                        <th class="py-3 px-4 font-medium">Tanggal</th>
                        <th class="py-3 px-4 font-medium">Keterangan</th>
                        <th class="py-3 px-4 font-medium">Lampiran</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cutisForApproval as $cuti)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $cuti->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $cuti->user->jabatanTerbaru->jabatan->nama_jabatan ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $cuti->jenis_izin }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $cuti->keterangan }}">{{ $cuti->keterangan }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                @if($cuti->file_izin)
                                    <a href="{{ Storage::url($cuti->file_izin) }}" target="_blank" class="text-blue-600 hover:underline">Lihat File</a>
                                @else - @endif
                            </td>
                            <td class="px-4 py-3 text-center" x-data="{ openModal: false }">
                                <div class="flex items-center justify-center space-x-2">
                                    <form action="{{ route('approvals.update', $cuti->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui pengajuan ini?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Disetujui">
                                        <button type="submit" class="rounded-md bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">Setujui</button>
                                    </form>
                                    <button @click="openModal = true" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Tolak</button>
                                </div>
                                <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
                                    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                                        <h3 class="text-lg font-semibold mb-4 text-gray-800 text-left">Alasan Penolakan</h3>
                                        <form action="{{ route('approvals.update', $cuti->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Ditolak">
                                            <textarea name="alasan_penolakan" rows="4" class="w-full rounded border-gray-300 p-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tuliskan alasan penolakan di sini..." required></textarea>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <button type="button" @click="openModal = false" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                                                <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Kirim Penolakan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-500">Tidak ada pengajuan cuti yang menunggu persetujuan Anda saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Download Laporan --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <h3 class="text-lg font-semibold text-gray-800">Download Laporan Arsip Surat Cuti</h3>
        <p class="text-sm text-gray-500 mb-4">Pilih periode untuk mengunduh semua surat cuti yang disetujui dalam format ZIP.</p>
        <form action="{{ route('approvals.downloadReport') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4">
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                <select name="bulan" id="bulan" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="all">Semua Bulan (Tahunan)</option>
                    @foreach(range(1, 12) as $month)
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

    {{-- Tabel 2: Riwayat Pengajuan Cuti --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Pengajuan Cuti (Semua Karyawan)</h3>
            <p class="text-sm text-gray-500">Daftar semua pengajuan cuti yang telah selesai diproses.</p>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                 <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Pemohon</th>
                        <th class="py-3 px-4 font-medium">Jenis Izin</th>
                        <th class="py-3 px-4 font-medium">Tanggal Cuti</th>
                        <th class="py-3 px-4 font-medium">Status Final</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cutisHistory as $cuti)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $cuti->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $cuti->jenis_izin }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($cuti->status_gm == 'Disetujui')
                                    <span class="font-semibold text-green-600">Disetujui</span>
                                @else
                                    <span class="font-semibold text-red-600">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($cuti->status_gm == 'Disetujui' && $cuti->file_surat)
                                    <a href="{{ Storage::url($cuti->file_surat) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">
                                        Download Surat
                                    </a>
                                @else - @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-500">Belum ada riwayat pengajuan cuti yang selesai diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@extends('layouts.dashboard')

@section('title', 'Persetujuan SPPD General Manager')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Tabel 1: Daftar Pengajuan SPPD (Menunggu Persetujuan Final) --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Pengajuan SPPD (Persetujuan Final GM)</h3>
            <p class="text-sm text-gray-500">Berikut adalah daftar pengajuan SPPD yang memerlukan tindakan final dari Anda.</p>
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
                    @forelse($sppdsForApproval as $sppd)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $sppd->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $sppd->user->jabatanTerbaru->jabatan->nama_jabatan ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $sppd->jenis_izin }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sppd->tgl_mulai)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $sppd->keterangan }}">{{ $sppd->keterangan }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                @if($sppd->file_izin)
                                    <a href="{{ Storage::url($sppd->file_izin) }}" target="_blank" class="text-blue-600 hover:underline">Lihat File</a>
                                @else - @endif
                            </td>
                            <td class="px-4 py-3 text-center" x-data="{ openModal: false }">
                                <div class="flex items-center justify-center space-x-2">
                                    <form action="{{ route('sppd.approvals.update', $sppd->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui pengajuan SPPD ini?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Disetujui">
                                        {{-- <input type="hidden" name="level" value="GM"> --}}
                                          <button type="submit" class="rounded-md bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">Setujui</button>
                                    </form>
                                    <button @click="openModal = true" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Tolak</button>
                                </div>

                                {{-- Modal Penolakan --}}
                                <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
                                    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                                        <h3 class="text-lg font-semibold mb-4 text-gray-800 text-left">Alasan Penolakan SPPD</h3>
                                        <form action="{{ route('sppd.approvals.update', $sppd->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Ditolak">
                                            <input type="hidden" name="level" value="GM">
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
                        <tr><td colspan="7" class="py-8 text-center text-gray-500">Tidak ada pengajuan SPPD yang menunggu persetujuan Anda saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Download Laporan ARSIP SPPD (PASTIKAN DIV INI ADA) --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <h3 class="text-lg font-semibold text-gray-800">Download Laporan Arsip Surat SPPD</h3>
        <p class="text-sm text-gray-500 mb-4">Pilih periode untuk mengunduh semua surat SPPD yang disetujui dalam format ZIP.</p>

        <form action="{{ route('sppd.downloadReport') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4">
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

    {{-- Tabel 2: Riwayat Tindakan --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
      <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Pengajuan SPPD (Semua Karyawan)</h3>
            <p class="text-sm text-gray-500">Daftar semua pengajuan SPPD yang telah selesai diproses.</p>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Pemohon</th>
                        <th class="py-3 px-4 font-medium">Jenis Izin</th>
                        <th class="py-3 px-4 font-medium">Tanggal SPPD</th>
                        <th class="py-3 px-4 font-medium">Keputusan Anda (GM)</th>
                        <th class="py-3 px-4 font-medium">Status Final</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sppdsHistory as $sppd)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $sppd->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $sppd->jenis_izin }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sppd->tgl_mulai)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    'bg-green-100 text-green-800' => $sppd->status_gm == 'Disetujui',
                                    'bg-red-100 text-red-800' => $sppd->status_gm == 'Ditolak',
                                ])>{{ $sppd->status_gm }}</span>
                                @if($sppd->tgl_persetujuan_gm)
                                <span class="block text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($sppd->tgl_persetujuan_gm)->format('d M Y') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($sppd->status_gm == 'Disetujui')
                                    <span class="font-semibold text-green-600">Diterbitkan</span>
                                @else
                                    <span class="font-semibold text-red-600">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($sppd->status_gm == 'Disetujui' && $sppd->file_surat)
                                    <a href="{{ Storage::url($sppd->file_surat) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L10 11.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M10 1a1 1 0 011 1v9a1 1 0 11-2 0V2a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                        <span>Download SPPD</span>
                                    </a>
                                @else - @endif
                            </td>

                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-500">Belum ada riwayat pengajuan SPPD yang selesai diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

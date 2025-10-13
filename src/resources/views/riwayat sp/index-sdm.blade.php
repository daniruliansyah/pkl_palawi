@extends('layouts.dashboard')

@section('title', 'Persetujuan Surat Peringatan SDM')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Tabel 1: Menunggu Persetujuan SDM --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Daftar Pengajuan SP (Menunggu Persetujuan SDM)
                </h3>
                <p class="text-sm text-gray-500">Berikut adalah daftar Surat Peringatan yang perlu Anda verifikasi dan setujui untuk dilanjutkan ke GM.</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Karyawan</th>
                        <th class="py-3 px-4 font-medium">Jabatan</th>
                        <th class="py-3 px-4 font-medium">Jenis SP</th>
                        <th class="py-3 px-4 font-medium">Tgl. Berlaku</th>
                        <th class="py-3 px-4 font-medium">Hal Surat</th>
                        <th class="py-3 px-4 font-medium">Bukti</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{-- Menggunakan $spsForApproval --}}
                    @forelse($spsForApproval as $sp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $sp->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $sp->user->jabatanTerbaru->jabatan->nama_jabatan ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $sp->jenis_sp }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($sp->tgl_selesai)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $sp->hal_surat }}">{{ $sp->hal_surat }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                @if($sp->file_bukti)
                                    <a href="{{ Storage::url($sp->file_bukti) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
                                @else - @endif
                            </td>
                            <td class="px-4 py-3 text-center" x-data="{ openModal: false }">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Menggunakan route SDM, diasumsikan 'sp.sdm.approve' --}}
                                    <form action="{{ route('sp.sdm.approve', $sp->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui SP ini dan meneruskannya ke General Manager?');">
                                        @csrf
                                        @method('PUT')
                                        {{-- Menggunakan input hidden status untuk dikirim ke controller --}}
                                        <input type="hidden" name="status" value="Disetujui SDM">
                                        <button type="submit" class="rounded-md bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">Setujui</button>
                                    </form>
                                    <button @click="openModal = true" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Tolak</button>
                                </div>
                                {{-- Modal Penolakan --}}
                                <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
                                    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                                        <h3 class="text-lg font-semibold mb-4 text-gray-800 text-left">Alasan Penolakan SP</h3>
                                        {{-- Menggunakan route SDM, diasumsikan 'sp.sdm.reject' --}}
                                        <form action="{{ route('sp.sdm.reject', $sp->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Ditolak SDM">
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
                        <tr><td colspan="7" class="py-8 text-center text-gray-500">Tidak ada Surat Peringatan yang menunggu persetujuan SDM saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Download Laporan --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <h3 class="text-lg font-semibold text-gray-800">Download Arsip Surat Peringatan</h3>
        <p class="text-sm text-gray-500 mb-4">Pilih periode untuk mengunduh semua Surat Peringatan yang telah diterbitkan (status final Disetujui) dalam format ZIP.</p>
        {{-- Menggunakan route yang sesuai untuk download SP, misalnya 'sp.downloadReport' --}}
        <form action="{{ route('sp.downloadReport') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4">
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
                Download Arsip
            </button>
        </form>
    </div>

    {{-- Tabel 2: Riwayat Pengajuan Cuti --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Pengajuan SP (Semua Karyawan)</h3>
            <p class="text-sm text-gray-500">Daftar semua Surat Peringatan yang telah selesai diproses oleh GM.</p>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Pemohon</th>
                        <th class="py-3 px-4 font-medium">Jenis SP</th>
                        <th class="py-3 px-4 font-medium">Tgl. Terbit</th>
                        {{-- Menambahkan status SDM --}}
                        <th class="py-3 px-4 font-medium">Status SDM</th>
                        <th class="py-3 px-4 font-medium">Status Final (GM)</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{-- Menggunakan $spsHistory --}}
                    @forelse($spsHistory as $sp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $sp->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $sp->jenis_sp }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_sp_terbit)->format('d M Y') }}</td>
                            {{-- Status SDM (Keputusan Anda) --}}
                            <td class="px-4 py-3">
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    'bg-green-100 text-green-800' => $sp->status_sdm == 'Disetujui SDM',
                                    'bg-red-100 text-red-800' => $sp->status_sdm == 'Ditolak SDM',
                                    'bg-yellow-100 text-yellow-800' => $sp->status_sdm == 'Menunggu',
                                ])>{{ $sp->status_sdm ?? 'Menunggu' }}</span>
                            </td>
                            {{-- Status Final (GM) --}}
                            <td class="px-4 py-3">
                                @if($sp->status_gm == 'Disetujui')
                                    <span class="font-semibold text-green-600">Diterbitkan</span>
                                @elseif($sp->status_gm == 'Ditolak')
                                    <span class="font-semibold text-red-600">Ditolak GM</span>
                                @else
                                    <span class="font-semibold text-yellow-600">Menunggu GM</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($sp->status_gm == 'Disetujui' && $sp->file_sp)
                                    <a href="{{ Storage::url($sp->file_sp) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">
                                        Download SP
                                    </a>
                                @else - @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-500">Belum ada riwayat pengajuan Surat Peringatan yang selesai diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

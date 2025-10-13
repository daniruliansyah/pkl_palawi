@extends('layouts.dashboard')

@section('title', 'Persetujuan Surat Peringatan General Manager')

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
            <h3 class="text-lg font-semibold text-gray-800">Daftar Pengajuan SP (Persetujuan Final GM)</h3>
            <p class="text-sm text-gray-500">Berikut adalah daftar Surat Peringatan yang memerlukan tindakan final dari Anda.</p>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Karyawan</th>
                        <th class="py-3 px-4 font-medium">Jabatan</th>
                        <th class="py-3 px-4 font-medium">Jenis SP</th>
                        <th class="py-3 px-4 font-medium">Tgl. Terbit</th>
                        <th class="py-3 px-4 font-medium">Mulai Berlaku</th>
                        <th class="py-3 px-4 font-medium">Akhir Berlaku</th>
                        <th class="py-3 px-4 font-medium">Bukti</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{-- Menggunakan $spsForApproval sesuai konteks SP --}}
                    @forelse($spsForApproval as $sp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $sp->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $sp->user->jabatanTerbaru->jabatan->nama_jabatan ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $sp->jenis_sp }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_sp_terbit)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_mulai)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_selesai)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                {{-- Mengubah file_izin menjadi file_bukti --}}
                                @if($sp->file_bukti)
                                    <a href="{{ Storage::url($sp->file_bukti) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
                                @else - @endif
                            </td>
                            <td class="px-4 py-3 text-center" x-data="{ openModal: false }">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- PERBAIKAN: Menggunakan route yang sesuai untuk SP, misalnya 'sp.approve' --}}
                                    <form action="{{ route('sp.approve', $sp->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin MENERBITKAN Surat Peringatan ini? Tindakan ini bersifat final.');">
                                        @csrf
                                        @method('PUT')
                                        {{-- Menggunakan status yang sesuai jika SP tidak memiliki kolom status_gm, tetapi status_gm ini penting untuk approval flow. Asumsikan ada kolom 'status_gm' atau 'status' di model SP untuk approval flow. Jika tidak ada, Anda perlu menambahkannya atau menyesuaikan controller. Untuk contoh ini, saya asumsikan ada kolom status/status_gm --}}
                                        <input type="hidden" name="status" value="Disetujui">
                                        <button type="submit" class="rounded-md bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">Terbitkan SP</button>
                                    </form>
                                    {{-- Tombol Tolak --}}
                                    <button @click="openModal = true" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Tolak</button>
                                </div>

                                {{-- Modal Penolakan --}}
                                <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
                                    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                                        <h3 class="text-lg font-semibold mb-4 text-gray-800 text-left">Alasan Penolakan SP</h3>
                                        {{-- PERBAIKAN: Menggunakan route yang sesuai untuk penolakan SP, misalnya 'sp.reject' --}}
                                        <form action="{{ route('sp.reject', $sp->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Ditolak">
                                            {{-- Kolom untuk alasan penolakan, asumsi ada di database/controller --}}
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
                        <tr><td colspan="8" class="py-8 text-center text-gray-500">Tidak ada Surat Peringatan yang menunggu persetujuan Anda saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    ---

    {{-- Form Download Laporan --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <h3 class="text-lg font-semibold text-gray-800">Download Arsip Surat Peringatan</h3>
        <p class="text-sm text-gray-500 mb-4">Pilih periode untuk mengunduh semua Surat Peringatan yang telah diterbitkan dalam format ZIP.</p>
        {{-- PERBAIKAN: Menggunakan route yang sesuai untuk download SP, misalnya 'sp.downloadReport' --}}
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

    ---

    {{-- Tabel 2: Riwayat Tindakan --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Tindakan Anda</h3>
            <p class="text-sm text-gray-500">Daftar Surat Peringatan yang telah Anda proses (Disetujui/Ditolak).</p>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Karyawan</th>
                        <th class="py-3 px-4 font-medium">Jenis SP</th>
                        <th class="py-3 px-4 font-medium">Tanggal SP</th>
                        {{-- Mengganti "Keputusan Anda" dengan "Keputusan GM" --}}
                        <th class="py-3 px-4 font-medium">Keputusan GM</th>
                        <th class="py-3 px-4 font-medium">Status Final</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{-- Menggunakan $spsHistory sesuai konteks SP --}}
                    @forelse($spsHistory as $sp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $sp->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $sp->jenis_sp }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_sp_terbit)->format('d M Y') }}</td>
                            {{-- Asumsi ada kolom status_gm di model SP untuk melacak persetujuan GM --}}
                            <td class="px-4 py-3">
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    'bg-green-100 text-green-800' => $sp->status_gm == 'Disetujui',
                                    'bg-red-100 text-red-800' => $sp->status_gm == 'Ditolak',
                                ])>{{ $sp->status_gm ?? 'N/A' }}</span>
                                @if(isset($sp->tgl_persetujuan_gm))
                                <span class="block text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($sp->tgl_persetujuan_gm)->format('d M Y') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(isset($sp->status_gm) && $sp->status_gm == 'Disetujui')
                                    <span class="font-semibold text-green-600">Diterbitkan</span>
                                @else
                                    <span class="font-semibold text-red-600">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{-- Mengubah file_surat menjadi file_sp --}}
                                @if(isset($sp->status_gm) && $sp->status_gm == 'Disetujui' && $sp->file_sp)
                                    <a href="{{ Storage::url($sp->file_sp) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">
                                        Download SP
                                    </a>
                                @else - @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-500">Anda belum memiliki riwayat tindakan untuk Surat Peringatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

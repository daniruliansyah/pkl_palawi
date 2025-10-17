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
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Pengajuan SP (Menunggu Persetujuan SDM)</h3>
            <p class="text-sm text-gray-500">Berikut adalah daftar SP yang perlu Anda verifikasi untuk dilanjutkan ke GM.</p>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Karyawan</th>
                        <th class="py-3 px-4 font-medium">Jabatan</th>
                        <th class="py-3 px-4 font-medium">Jenis SP</th>
                        <th class="py-3 px-4 font-medium">Hal Surat</th>
                        <th class="py-3 px-4 font-medium">Bukti</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($spsForApproval as $sp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $sp->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $sp->user->jabatanTerbaru->jabatan->nama_jabatan ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $sp->jenis_sp }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $sp->hal_surat }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                @if($sp->file_bukti)
                                    <a href="{{ Storage::url($sp->file_bukti) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
                                @else - @endif
                            </td>
                            <td class="px-4 py-3 text-center" x-data="{ openModal: false }">
                                <div class="flex items-center justify-center space-x-2">
                                    <form action="{{ route('sp.approvals.update', $sp->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="Disetujui">
                                        <button type="submit" class="rounded-md bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">Setujui</button>
                                    </form>
                                    <button @click="openModal = true" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Tolak</button>
                                </div>

                                {{-- Modal Penolakan --}}
                                <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
                                    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl" @click.stop>
                                        <h3 class="text-lg font-semibold mb-4 text-gray-800 text-left">Alasan Penolakan SP</h3>
                                        <form action="{{ route('sp.approvals.update', $sp->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="Ditolak">
                                            <textarea name="alasan_penolakan" rows="4" class="w-full rounded-lg border-gray-300 p-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tuliskan alasan penolakan di sini..." required></textarea>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <button type="button" @click="openModal = false" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                                                <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">Kirim Penolakan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-500">Tidak ada Surat Peringatan yang menunggu persetujuan Anda saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <hr class="my-8">

    {{-- Tabel 2: Riwayat Tindakan --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Pengajuan SP (Semua Karyawan)</h3>
            <p class="text-sm text-gray-500">Daftar semua pengajuan SP yang telah selesai diproses.</p>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Karyawan</th>
                        <th class="py-3 px-4 font-medium">Jenis SP</th>
                        <th class="py-3 px-4 font-medium">Tanggal SP</th>
                        <th class="py-3 px-4 font-medium">Keputusan SDM</th>
                        <th class="py-3 px-4 font-medium">Keputusan GM</th> <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($spsHistory as $sp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $sp->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $sp->jenis_sp }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_sp_terbit)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    'bg-green-100 text-green-800' => $sp->status_sdm == 'Disetujui SDM',
                                    'bg-red-100 text-red-800' => $sp->status_sdm == 'Ditolak SDM',
                                    'bg-yellow-100 text-yellow-800' => $sp->status_sdm == 'Menunggu Persetujuan',
                                    'bg-gray-100 text-gray-800' => in_array($sp->status_sdm, [null, 'Menunggu']),
                                ])>{{ $sp->status_sdm ?? 'N/A' }}</span>
                                @if(isset($sp->tgl_persetujuan_sdm))
                                    <span class="block text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($sp->tgl_persetujuan_sdm)->format('d M Y') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    'bg-green-100 text-green-800' => $sp->status_gm == 'Disetujui',
                                    'bg-red-100 text-red-800' => $sp->status_gm == 'Ditolak',
                                    // PERBAIKAN: Logika badge lebih jelas untuk status GM
                                    'bg-yellow-100 text-yellow-800' => $sp->status_gm == 'Menunggu Persetujuan',
                                    'bg-gray-100 text-gray-800' => in_array($sp->status_gm, [null, 'Menunggu']),
                                ])>
                                    {{-- PERBAIKAN: Tampilkan 'Belum Diproses' jika masih menunggu keputusan SDM --}}
                                    @if($sp->status_sdm != 'Disetujui SDM' && $sp->status_sdm != 'Ditolak SDM')
                                        Belum Diproses
                                    @else
                                        {{ $sp->status_gm }}
                                    @endif
                                </span>
                            </td>
                             <td class="px-4 py-3 text-center">
                                @if($sp->status_gm == 'Disetujui' && $sp->file_sp)
                                    <a href="{{ route('sp.download', $sp->id) }}" target="_blank" class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L10 11.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M10 1a1 1 0 011 1v9a1 1 0 11-2 0V2a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                        <span>Download SP</span>
                                    </a>
                                @else
                                    -
                                @endif
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

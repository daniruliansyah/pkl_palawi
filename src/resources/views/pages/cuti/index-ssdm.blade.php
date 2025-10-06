@extends('layouts.dashboard')

@section('title', 'Persetujuan Cuti Atasan')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Notifikasi Sukses atau Error --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabel 1: Menunggu Persetujuan --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Daftar Pengajuan Cuti (Menunggu Persetujuan Anda)
                </h3>
                <p class="text-sm text-gray-500">Berikut adalah daftar pengajuan cuti yang memerlukan tindakan.</p>
            </div>
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
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center" x-data="{ openModal: false }">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Tombol Setuju --}}
                                    <form action="{{ route('cuti.updateStatus', $cuti->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui pengajuan ini?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Disetujui">
                                        <button type="submit" class="rounded-md bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">
                                            Setujui
                                        </button>
                                    </form>

                                    {{-- Tombol Tolak --}}
                                    <button @click="openModal = true" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">
                                        Tolak
                                    </button>
                                </div>

                                {{-- Modal Alasan Penolakan --}}
                                <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
                                    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                                        <h3 class="text-lg font-semibold mb-4 text-gray-800 text-left">Alasan Penolakan</h3>
                                        <form action="{{ route('cuti.updateStatus', $cuti->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Ditolak">
                                            <div>
                                                <label for="alasan_penolakan_{{ $cuti->id }}" class="sr-only">Alasan Penolakan</label>
                                                <textarea name="alasan_penolakan" id="alasan_penolakan_{{ $cuti->id }}" rows="4" class="w-full rounded border-gray-300 p-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tuliskan alasan penolakan di sini..." required></textarea>
                                            </div>
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
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                Tidak ada pengajuan cuti yang menunggu persetujuan Anda saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel 2: Riwayat Tindakan --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Riwayat Tindakan Anda
                </h3>
                <p class="text-sm text-gray-500">Daftar pengajuan cuti yang telah Anda proses.</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                 <thead>
                    <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                        <th class="py-3 px-4 font-medium">Pemohon</th>
                        <th class="py-3 px-4 font-medium">Jenis Izin</th>
                        <th class="py-3 px-4 font-medium">Tanggal Cuti</th>
                        <th class="py-3 px-4 font-medium">Keputusan Anda</th>
                        <th class="py-3 px-4 font-medium">Status Final</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cutisHistory as $cuti)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $cuti->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $cuti->jenis_izin }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    'bg-green-100 text-green-800' => $cuti->status_ssdm == 'Disetujui',
                                    'bg-red-100 text-red-800' => $cuti->status_ssdm == 'Ditolak',
                                ])>{{ $cuti->status_ssdm }}</span>
                                @if($cuti->tgl_persetujuan_ssdm)
                                <span class="block text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($cuti->tgl_persetujuan_ssdm)->format('d M Y') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($cuti->status_gm == 'Disetujui')
                                    <span class="font-semibold text-green-600">Disetujui</span>
                                @elseif($cuti->status_ssdm == 'Ditolak' || $cuti->status_sdm == 'Ditolak' || $cuti->status_gm == 'Ditolak')
                                    <span class="font-semibold text-red-600">Ditolak</span>
                                @else
                                    <span class="text-yellow-600">Dalam Proses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                Anda belum memiliki riwayat tindakan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


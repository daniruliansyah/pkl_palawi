{{-- Kotak informasi sisa jatah cuti --}}
<div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700">
    <p><span class="font-semibold">Sisa Jatah Cuti Tahunan Anda:</span> {{ $sisaCuti ?? 'N/A' }} hari.</p>
</div>

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Pengajuan Cuti Anda</h3>
        </div>
        <div>
            <a href="{{ route('cuti.create') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                Ajukan Cuti Baru
            </a>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                    <th class="py-3 px-4 font-medium">No. Surat</th>
                    <th class="py-3 px-4 font-medium">Jenis Cuti</th>
                    <th class="py-3 px-4 font-medium">Tanggal Diajukan</th>
                    <th class="py-3 px-4 font-medium">Status Senior</th>
                    <th class="py-3 px-4 font-medium">Status SDM</th>
                    <th class="py-3 px-4 font-medium">Status GM</th>
                    <th class="py-3 px-4 font-medium">Status Final</th>
                    <th class="py-3 px-4 font-medium text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($cutis as $cuti)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $cuti->no_surat ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $cuti->jenis_izin }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $cuti->created_at->format('d-m-Y') }}</td>
                        {{-- Status Senior --}}
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 text-xs font-medium rounded-full',
                                'bg-green-100 text-green-800' => $cuti->status_ssdm == 'Disetujui',
                                'bg-red-100 text-red-800' => $cuti->status_ssdm == 'Ditolak',
                                'bg-yellow-100 text-yellow-800' => $cuti->status_ssdm == 'Menunggu Persetujuan',
                            ])>{{ $cuti->status_ssdm }}</span>
                        </td>
                        {{-- Status SDM --}}
                        <td class="px-4 py-3">
                             @php
                                 $statusSdm = ($cuti->status_ssdm == 'Ditolak') ? 'Ditolak' : $cuti->status_sdm;
                             @endphp
                             <span @class([
                                'px-2 py-1 text-xs font-medium rounded-full',
                                'bg-green-100 text-green-800' => $statusSdm == 'Disetujui',
                                'bg-red-100 text-red-800' => $statusSdm == 'Ditolak',
                                'bg-yellow-100 text-yellow-800' => $statusSdm == 'Menunggu Persetujuan',
                                'bg-gray-100 text-gray-800' => $statusSdm == 'Menunggu',
                            ])>{{ $statusSdm }}</span>
                        </td>
                        {{-- Status GM --}}
                        <td class="px-4 py-3">
                            @php
                                $statusGm = ($cuti->status_ssdm == 'Ditolak' || $cuti->status_sdm == 'Ditolak') ? 'Ditolak' : $cuti->status_gm;
                            @endphp
                            <span @class([
                                'px-2 py-1 text-xs font-medium rounded-full',
                                'bg-green-100 text-green-800' => $statusGm == 'Disetujui',
                                'bg-red-100 text-red-800' => $statusGm == 'Ditolak',
                                'bg-yellow-100 text-yellow-800' => $statusGm == 'Menunggu Persetujuan',
                                'bg-gray-100 text-gray-800' => $statusGm == 'Menunggu',
                            ])>{{ $statusGm }}</span>
                        </td>
                        {{-- Status Final --}}
                        <td class="px-4 py-3">
                            @if($cuti->status_gm == 'Disetujui')
                                <span class="font-semibold text-green-600">Disetujui</span>
                            @elseif($cuti->status_ssdm == 'Ditolak' || $cuti->status_sdm == 'Ditolak' || $cuti->status_gm == 'Ditolak')
                                <div class="text-red-600">
                                    <span class="font-semibold">Ditolak</span>
                                    @php
                                        $penolak = null;
                                        if ($cuti->status_gm == 'Ditolak') $penolak = $cuti->gm;
                                        elseif ($cuti->status_sdm == 'Ditolak') $penolak = $cuti->sdm;
                                        elseif ($cuti->status_ssdm == 'Ditolak') $penolak = $cuti->ssdm;
                                    @endphp
                                    @if($penolak)
                                        <p class="text-xs text-gray-500 italic">oleh: {{ Str::words($penolak->nama_lengkap, 2, '') }}</p>
                                    @endif
                                    @if($cuti->alasan_penolakan)
                                    <p class="text-xs text-gray-500 italic" title="{{ $cuti->alasan_penolakan }}">Alasan: {{ Str::limit($cuti->alasan_penolakan, 20) }}</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-yellow-600">Dalam Proses</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($cuti->status_gm == 'Disetujui' && $cuti->file_surat)
                                <a href="{{ Storage::url($cuti->file_surat) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">
                                    Download Surat
                                </a>
                            @elseif($cuti->status_ssdm == 'Menunggu Persetujuan')
                                <form action="{{ route('cuti.cancel', $cuti->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin membatalkan pengajuan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm font-medium">
                                        Batalkan
                                    </button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            Anda belum memiliki riwayat pengajuan cuti.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


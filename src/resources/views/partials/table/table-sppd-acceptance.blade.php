{{-- resources/views/components/table-sppd-acceptance.blade.php --}}
<div class="w-full overflow-x-auto">
    <table class="min-w-full text-sm text-left">
        <thead>
            <tr class="border-y border-gray-100 bg-gray-50/50 text-xs text-gray-600">
                <th class="py-3 px-4 font-medium">Nama Surat</th>
                <th class="py-3 px-4 font-medium">Pemohon</th>
                <th class="py-3 px-4 font-medium">Tanggal Dibuat</th>
                <th class="py-3 px-4 font-medium">Persetujuan</th>
                <th class="py-3 px-4 font-medium">Status Final</th>

                {{-- --- PERUBAHAN KOLOM KONDISIONAL --- --}}
                @if(isset($isApprovalPage) && $isApprovalPage)
                    {{-- Di Halaman Approval, tampilkan Maksud Perjalanan --}}
                    <th class="py-3 px-4 font-medium">Maksud Perjalanan Dinas</th>
                @else
                    {{-- Di Halaman Riwayat/Index, tampilkan link Download Surat --}}
                    <th class="py-3 px-4 font-medium text-center">Surat</th>
                @endif
                {{-- --- BATAS PERUBAHAN --- --}}

                <th class="py-3 px-4 font-medium text-center">Pertanggungjawaban</th>

                {{-- Kolom Aksi Persetujuan (Hanya muncul di halaman approval) --}}
                @if(isset($isApprovalPage) && $isApprovalPage)
                    <th class="py-3 px-4 font-medium text-center">Aksi</th>
                @endif
            </tr>
        </thead>

        {{-- PASTIKAN TIDAK ADA KODE FORM DOWNLOAD DI SINI --}}

        <tbody class="divide-y divide-gray-100">
            @forelse($sppds as $sppd)
            <tr class="hover:bg-gray-50">
                {{-- Nama Surat --}}
                <td class="px-4 py-3 text-gray-700 font-medium">SPPD - {{ $sppd->lokasi_tujuan }}</td>

                {{-- Pemohon --}}
                <td class="px-4 py-3 text-gray-500">{{ $sppd->user->nama_lengkap ?? 'N/A' }}</td>

                {{-- Tanggal Dibuat --}}
                <td class="px-4 py-3 text-gray-500">{{ $sppd->created_at->format('d-m-Y') }}</td>

                {{-- Persetujuan --}}
                <td class="px-4 py-3">
                    @php
                        $badgeClass = 'bg-gray-100 text-gray-800';
                        $statusText = 'Status Tidak Diketahui';
                        if ($sppd->status === 'menunggu') {
                            $badgeClass = 'bg-yellow-100 text-yellow-800';
                            $statusText = 'Menunggu Persetujuan';
                        } elseif ($sppd->status === 'Disetujui') {
                            $badgeClass = 'bg-green-100 text-green-800';
                            $statusText = 'Disetujui';
                        } elseif ($sppd->status === 'Ditolak') {
                            $badgeClass = 'bg-red-100 text-red-800';
                            $statusText = 'Ditolak';
                        }
                    @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $badgeClass }}">{{ $statusText }}</span>
                    @if($sppd->penyetuju)
                        <p class="text-xs text-gray-500 italic mt-1">oleh: {{ Str::words($sppd->penyetuju->nama_lengkap, 2, '') }}</p>
                    @endif
                </td>

                {{-- Status Final --}}
                <td class="px-4 py-3">
                    @if($sppd->status === 'Disetujui')
                        <span class="font-semibold text-green-600">Selesai</span>
                    @elseif($sppd->status === 'Ditolak')
                        <div class="text-red-600">
                            <span class="font-semibold">Ditolak</span>
                            @if ($sppd->alasan_penolakan)
                                <p class="text-xs text-gray-500 italic" title="{{ $sppd->alasan_penolakan }}">Alasan: {{ Str::limit($sppd->alasan_penolakan, 20) }}</p>
                            @endif
                        </div>
                    @else
                        <span class="text-yellow-600">Dalam Proses</span>
                    @endif
                </td>

                {{-- --- PERUBAHAN KOLOM KONDISIONAL --- --}}
                @if(isset($isApprovalPage) && $isApprovalPage)
                    {{-- Di Halaman Approval: Tampilkan Maksud Perjalanan --}}
                    <td class="px-4 py-3 text-gray-500" title="{{ $sppd->keterangan_sppd ?? '' }}">
                        {{ Str::limit($sppd->keterangan_sppd ?? '-', 40) }}
                    </td>
                @else
                    {{-- Di Halaman Riwayat/Index: Tampilkan link Download Surat --}}
                    <td class="px-4 py-3 text-center">
                        @if($sppd->status == 'Disetujui' && $sppd->file_sppd)
                        <a href="{{ route('sppd.download', $sppd->id) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">
                            Download Surat
                        </a>
                        @else - @endif
                    </td>
                @endif
                {{-- --- BATAS PERUBAHAN --- --}}

                {{-- Pertanggungjawaban --}}
                <td class="px-4 py-3 text-center">
                    @if ($sppd->status === 'Disetujui')
                        @if (Auth::user()->nip == $sppd->nip_user)
                            @if ($sppd->pertanggungjawaban)
                                <a href="{{ route('pertanggungjawaban.download', $sppd->pertanggungjawaban->id) }}" class="text-green-600 hover:underline">Unduh Kuitansi</a>
                            @else
                                <a href="{{ route('pertanggungjawaban.create', $sppd->id) }}" class="text-blue-600 hover:underline">Buat Laporan</a>
                            @endif
                        @elseif ($sppd->pertanggungjawaban)
                            <a href="{{ route('pertanggungjawaban.download', $sppd->pertanggungjawaban->id) }}" class="text-gray-500 hover:underline">Lihat Kuitansi</a>
                        @else
                            -
                        @endif
                    @else
                        -
                    @endif
                </td>

                {{-- Kolom Aksi Persetujuan (Hanya muncul di halaman approval) --}}
                @if(isset($isApprovalPage) && $isApprovalPage)
                <td class="px-4 py-3 text-center" x-data="{ openModal: false }">
                    @if($sppd->status === 'menunggu')
                        <div class="flex items-center justify-center space-x-2">
                            <form action="{{ route('sppd.approvals.update', $sppd->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui pengajuan ini?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="Disetujui">
                                <button type="submit" class="rounded-md bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">Setujui</button>
                            </form>
                            <button @click="openModal = true" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Tolak</button>
                        </div>

                        {{-- Modal untuk alasan penolakan --}}
                        <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
                            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 text-left">Alasan Penolakan</h3>
                                <form action="{{ route('sppd.approvals.update', $sppd->id) }}" method="POST">
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
                    @else
                        -
                    @endif
                </td>
                @endif
            </tr>
            @empty
            <tr>
                {{-- Penyesuaian colspan (8 jika ada kolom Aksi, 7 jika tidak) --}}
                <td colspan="{{ (isset($isApprovalPage) && $isApprovalPage) ? 8 : 7 }}" class="py-8 text-center text-gray-500">
                    Tidak ada data pengajuan SPPD.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

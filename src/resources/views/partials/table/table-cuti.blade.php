<div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-gray-900 sm:px-6">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                Daftar Pengajuan Cuti
            </h3>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('cuti.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                + Ajukan Cuti
            </a>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="border-y border-gray-100 bg-gray-50 dark:border-gray-800">
                    {{-- Kolom Baru Ditambahkan dan Disesuaikan --}}
                    <th class="px-4 py-3">No. Surat</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Tgl. Upload</th>
                    <th class="px-4 py-3">Status Pengajuan</th>
                    <th class="px-4 py-3">Persetujuan Staf SDM</th>
                    <th class="px-4 py-3">Persetujuan SDM</th>
                    <th class="px-4 py-3">Persetujuan GM</th>
                    <th class="px-4 py-3">File Izin</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($cutis as $item)
                    <tr class="dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $item->no_surat ?? '-' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                            {{ $item->keterangan }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{-- Format tanggal agar mudah dibaca --}}
                            {{ $item->tgl_upload ? \Carbon\Carbon::parse($item->tgl_upload)->format('d-m-Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 font-medium text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                {{ $item->status_pengajuan }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $item->tgl_persetujuan_ssdm ? \Carbon\Carbon::parse($item->tgl_persetujuan_ssdm)->format('d-m-Y H:i') : 'Menunggu' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $item->tgl_persetujuan_sdm ? \Carbon\Carbon::parse($item->tgl_persetujuan_sdm)->format('d-m-Y H:i') : 'Menunggu' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $item->tgl_persetujuan_gm ? \Carbon\Carbon::parse($item->tgl_persetujuan_gm)->format('d-m-Y H:i') : 'Menunggu' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($item->file_izin)
                                <a href="{{ asset('storage/' . $item->file_izin) }}" target="_blank"
                                   class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
                                    Lihat File
                                </a>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">Tidak ada</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{-- Sesuaikan colspan dengan jumlah kolom baru (8) --}}
                        <td colspan="8" class="py-4 text-center text-gray-500 dark:text-gray-400">
                            Belum ada pengajuan Cuti.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
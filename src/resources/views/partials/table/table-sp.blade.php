<div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-gray-900 sm:px-6">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                Daftar Surat Peringatan 
            </h3>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sp.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                + Buat Surat Peringatan
            </a>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="border-y border-gray-100 bg-gray-50 dark:border-gray-800">
                    {{-- Kolom Baru Ditambahkan dan Disesuaikan --}}
                    <th class="px-4 py-3">No. Surat</th>
                    <th class="px-4 py-3">Nama Karyawan</th> {{-- <-- KOLOM BARU --}}
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Tgl. SP Terbit</th>
                    <th class="px-4 py-3">Tgl. Mulai</th>
                    <th class="px-4 py-3">Tgl. Selesai</th>
                    <th class="px-4 py-3">File Surat</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($sps as $item)
                    <tr class="dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $item->no_surat ?? '-' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                            {{ $item['nama_lengkap'] }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                            {{ $item->ket_peringatan }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{-- Format tanggal agar mudah dibaca --}}
                            {{ $item->tgl_sp_terbit ? \Carbon\Carbon::parse($item->tgl_sp_terbit)->format('d-m-Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{-- Format tanggal agar mudah dibaca --}}
                            {{ $item->tgl_mulai ? \Carbon\Carbon::parse($item->tgl_mulai)->format('d-m-Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{-- Format tanggal agar mudah dibaca --}}
                            {{ $item->tgl_selesai ? \Carbon\Carbon::parse($item->tgl_selesai)->format('d-m-Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($item->file_sp)
                                <a href="{{ asset('storage/' . $item->file_sp) }}" download
                                    class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
                                    Download File
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
                            Belum ada surat peringatan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
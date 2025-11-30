<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Surat Peringatan (SP)</h3>
        </div>
        <div>
            {{-- Asumsi rute untuk membuat SP baru adalah 'sp.create' --}}
            <a href="{{ route('sp.create') }}" class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">
                Terbitkan SP Baru
            </a>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                    <th class="py-3 px-4 font-medium">No. Surat</th>
                    <th class="py-3 px-4 font-medium">Karyawan</th>
                    <th class="py-3 px-4 font-medium">Tgl. Terbit</th>
                    <th class="py-3 px-4 font-medium">Tgl. Mulai Berlaku</th>
                    <th class="py-3 px-4 font-medium">Tgl. Selesai Berlaku</th>
                    <th class="py-3 px-4 font-medium text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($sp as $sp)
                    <tr class="hover:bg-gray-50">
                        {{-- Nomor Surat --}}
                        <td class="px-4 py-3 text-gray-500">{{ $sp->no_surat ?? '-' }}</td>

                        {{-- Nama Karyawan (menggunakan relasi user) --}}
                        <td class="px-4 py-3 text-gray-700">
                            {{ $sp->user->nama_lengkap ?? $sp->nip_user }}
                        </td>

                        {{-- Tanggal SP Terbit --}}
                        <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_sp_terbit)->format('d-m-Y') }}</td>

                        {{-- Tanggal Mulai --}}
                        <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_mulai)->format('d-m-Y') }}</td>

                        {{-- Tanggal Selesai --}}
                        <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($sp->tgl_selesai)->format('d-m-Y') }}</td>

                        {{-- Aksi (Download File) --}}
                        <td class="px-4 py-3 text-center">
                            @if($sp->file_sp)
                                <a href="{{ Storage::url($sp->file_sp) }}" target="_blank" class="text-red-600 hover:underline text-sm font-medium">
                                    Download File SP
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            Tidak ada data Surat Peringatan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

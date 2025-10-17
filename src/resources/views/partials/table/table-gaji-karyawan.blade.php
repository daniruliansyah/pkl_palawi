<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">
    {{-- Header: Judul dan Tombol Aksi --}}
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                Riwayat Gaji untuk: <span class="text-blue-600">{{ $user->nama_lengkap }}</span>
            </h3>
        </div>
        <div>
            <a href="{{ route('gaji.create', $user->id) }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                Buat Gaji Baru
            </a>
        </div>
    </div>

    {{-- Tabel Riwayat Gaji --}}
    <div class="w-full overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                    <th class="py-3 px-4 font-medium">Periode</th>
                    <th class="py-3 px-4 font-medium">Gaji Pokok</th>
                    <th class="py-3 px-4 font-medium">Total Potongan</th>
                    <th class="py-3 px-4 font-medium">Gaji Diterima</th>
                    <th class="py-3 px-4 font-medium text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($gajiHistory as $gaji)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-700">
                            {{ \Carbon\Carbon::create()->month($gaji->bulan)->format('F') }} {{ $gaji->tahun }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            Rp {{ number_format($gaji->gaji_diterima, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-4">
                                {{-- Tombol Lihat/Cetak Slip --}}
                                <a href="{{ route('gaji.cetak', $gaji->id) }}" class="text-blue-600 hover:underline font-medium">
                                    Lihat Slip
                                </a>
                                
                                {{-- Tombol Hapus Gaji --}}
                                <form action="{{ route('gaji.destroy', $gaji->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus data gaji ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            Belum ada riwayat gaji untuk karyawan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    @if ($gajiHistory->hasPages())
        <div class="mt-4 px-2">
            {{ $gajiHistory->links() }}
        </div>
    @endif
</div>
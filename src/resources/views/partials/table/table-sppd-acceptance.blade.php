{{-- Alert sukses (Menggunakan style modern) --}}
@if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="fixed top-4 right-4 z-50 rounded-xl bg-green-500 text-white p-4 shadow-xl transition-transform duration-300 ease-out transform scale-100 hover:scale-105">
        {{ session('success') }}
    </div>
@endif

{{-- Wrapper utama menggunakan styling card dari Cuti Index --}}
<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 shadow-sm sm:px-6">

    {{-- Header dan Tombol Ajukan Cuti Baru (Menggunakan styling header Cuti Index) --}}
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Pengajuan SPPD Anda</h3>
        </div>
        <div>
            <a href="{{ route('sppd.create') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                Ajukan SPPD Baru
            </a>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="w-full overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                {{-- Baris Header Tabel (Menggunakan styling Cuti Index) --}}
                <tr class="border-y border-gray-100 bg-gray-50 text-gray-600">
                    {{-- Kolom SPPD --}}
                    <th class="py-3 px-4 font-medium">Nama Surat</th>
                    <th class="py-3 px-4 font-medium">Dibuat Oleh</th>
                    <th class="py-3 px-4 font-medium">Tanggal Dibuat</th>
                    <th class="py-3 px-4 font-medium">Persetujuan</th>
                    <th class="py-3 px-4 font-medium">Status Final</th>
                    <th class="py-3 px-4 font-medium text-center">Surat</th>
                    {{-- Kondisi untuk menampilkan kolom Aksi (Logic tetap sama) --}}
                    @if(Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'General Manager' || Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'Senior Analis Keuangan, SDM & Umum')
                    <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sppds as $sppd)
                <tr class="hover:bg-gray-50">
                    {{-- Nama Surat --}}
                    <td class="px-4 py-3 text-gray-700 font-medium">
                        SPPD - {{ $sppd->lokasi_tujuan }}
                    </td>
                    {{-- Dibuat Oleh --}}
                    <td class="px-4 py-3 text-gray-500">
                        {{ $sppd->user->nama_lengkap ?? 'User Tidak Ditemukan' }}
                    </td>
                    {{-- Tanggal Dibuat --}}
                    <td class="px-4 py-3 text-gray-500">
                        {{ $sppd->created_at->format('d-m-Y') }}
                    </td>

                    {{-- Persetujuan (Menggunakan badge style dari Cuti Index) --}}
                    <td class="px-4 py-3">
                        @php
                            // Tentukan kelas badge berdasarkan status
                            $badgeClass = 'bg-gray-100 text-gray-800';
                            $statusText = '';

                            if ($sppd->status === 'menunggu') {
                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                $statusText = 'Menunggu Persetujuan ' . $sppd->pemberi_tugas;
                            } elseif ($sppd->status === 'Disetujui') {
                                $badgeClass = 'bg-green-100 text-green-800';
                                $statusText = 'Disetujui oleh ' . $sppd->pemberi_tugas;
                            } elseif ($sppd->status === 'Ditolak') {
                                $badgeClass = 'bg-red-100 text-red-800';
                                $statusText = 'Ditolak oleh ' . $sppd->pemberi_tugas;
                            }
                        @endphp
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>

                    {{-- Status Final (Menggunakan styling font weight/color dari Cuti Index) --}}
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

                    {{-- Surat --}}
                    <td class="px-4 py-3 text-center">
                       @if($sppd->status === 'Disetujui' && $sppd->file_sppd)
                            <a href="{{ route('sppd.download', $sppd->id) }}" class="text-blue-600 hover:underline mx-1">
                                {{-- Mengganti ikon lama dengan SVG icon yang lebih clean --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 2h2v2H6V6zm2 4H6v2h2v-2zm2-2h2v2h-2V8zm2 2h-2v2h2v-2zm-2 4H6v2h2v-2zm2 0h2v2h-2v-2z" />
                                </svg>
                                <span class="sr-only">Unduh Surat</span>
                            </a>
                        @else
                            <span class="text-gray-400">Belum Tersedia</span>
                        @endif
                    </td>

                    {{-- Kolom Aksi (Logic tetap sama, styling tombol lebih ringkas) --}}
                    @if(Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'General Manager' || Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'Senior Analis Keuangan, SDM & Umum')
                    <td class="px-4 py-3 text-center">
                        @if($sppd->status === 'menunggu')
                            @if(Auth::user()->jabatanTerbaru->jabatan->id === $sppd->pemberi_tugas_id)
                                <form action="{{ route('sppd.updateStatus', $sppd->id) }}" method="POST" class="inline-block mr-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Disetujui">
                                    {{-- Tombol Setujui (gaya text link) --}}
                                    <button type="submit" class="text-green-600 hover:underline text-sm font-medium">
                                        Setujui
                                    </button>
                                </form>
                                {{-- Tombol Tolak (gaya text link) --}}
                                <button onclick="showRejectModal('{{ $sppd->id }}')"
                                    class="text-red-600 hover:underline text-sm font-medium">
                                    Tolak
                                </button>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                    <tr>
                        @php
                            // Menghitung colspan berdasarkan ketersediaan kolom Aksi
                            $colspan = (Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'General Manager' || Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'Senior Analis Keuangan, SDM & Umum') ? 7 : 6;
                        @endphp
                        <td colspan="{{ $colspan }}" class="py-8 text-center text-gray-500">
                            Anda belum memiliki riwayat pengajuan SPPD.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal untuk alasan penolakan (Modal tidak diubah) --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                Alasan Penolakan
            </h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500">
                    Mohon berikan alasan penolakan pengajuan SPPD ini.
                </p>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="Ditolak">
                <div class="mt-4">
                    <label for="alasan_penolakan" class="block text-sm font-medium text-gray-700">Alasan</label>
                    <textarea name="alasan_penolakan" id="alasan_penolakan" rows="4" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required></textarea>
                </div>
                <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tolak Surat
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fungsi JavaScript untuk modal penolakan tetap sama
   function showRejectModal(sppdId) {
    document.getElementById('rejectModal').classList.remove('hidden');
    const form = document.getElementById('rejectForm');
    // Menggunakan route helper jika tersedia, atau path relatif:
    form.action = `{{ url('sppd') }}/${sppdId}/status`;
}

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('alasan_penolakan').value = '';
    }
</script>
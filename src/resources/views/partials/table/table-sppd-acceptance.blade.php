<div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md mb-6">
    <h2 class="text-xl font-semibold text-gray-800">Daftar Pengajuan SPPD</h2>
    <a href="{{ route('sppd.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        + Ajukan SPPD Baru
    </a>
</div>

{{-- Alert sukses --}}
@if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="fixed top-4 right-4 z-50 rounded-lg bg-green-500 text-white p-4 shadow-lg">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-hidden bg-white rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Surat
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dibuat Oleh
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal Dibuat
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Persetujuan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status Final
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Surat
                    </th>
                    {{-- Kondisi untuk menampilkan kolom Aksi, biar ga tampil di user karyawan --}}
                    @if(Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'General Manager' || Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'Senior Analis Keuangan, SDM & Umum')
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sppds as $sppd)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        SPPD - {{ $sppd->lokasi_tujuan }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $sppd->user->nama_lengkap ?? 'User Tidak Ditemukan' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $sppd->created_at->format('d-m-Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($sppd->status === 'menunggu')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Menunggu Persetujuan {{ $sppd->pemberi_tugas }}
                        </span>
                        @elseif($sppd->status === 'Disetujui')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Disetujui oleh {{ $sppd->pemberi_tugas }}
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Ditolak oleh {{ $sppd->pemberi_tugas }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($sppd->status === 'menunggu')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Proses
                        </span>
                        @elseif($sppd->status === 'Disetujui')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Selesai
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Ditolak
                        </span>
                        @if ($sppd->alasan_penolakan)
                            <p class="text-xs mt-1 text-gray-500">Alasan: {{ $sppd->alasan_penolakan }}</p>
                        @endif
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        {{-- @if($sppd->status === 'Disetujui' && $sppd->file_sppd)
                            <a href="{{ route('sppd.download', $sppd->id) }}" class="text-indigo-600 hover:text-indigo-900 mx-1">
                                <i class="fas fa-download mr-1"></i> Unduh Surat
                            </a>
                        @else --}}

                         @if($sppd->status === 'Disetujui' && $sppd->file_sppd)
                            <a href="{{ route('sppd.download', $sppd->id) }}" class="text-indigo-600 hover:text-indigo-900 mx-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 2h2v2H6V6zm2 4H6v2h2v-2zm2-2h2v2h-2V8zm2 2h-2v2h2v-2zm-2 4H6v2h2v-2zm2 0h2v2h-2v-2z" />
                                </svg>
                            </a>
                            @else
                            <span class="text-gray-400">Belum Tersedia</span>
                        @endif
                    </td>
                    {{-- Kondisi untuk menampilkan kolom Aksi --}}
                    @if(Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'General Manager' || Auth::user()->jabatanTerbaru->jabatan->nama_jabatan === 'Senior Analis Keuangan, SDM & Umum')
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                        @if($sppd->status === 'menunggu')
                            @if(Auth::user()->jabatanTerbaru->jabatan->id === $sppd->pemberi_tugas_id)
                                <form action="{{ route('sppd.updateStatus', $sppd->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Disetujui">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Setujui
                                    </button>
                                </form>
                                <button onclick="showRejectModal('{{ $sppd->id }}')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Tolak
                                </button>
                            @else
                                <span class="text-gray-500">Menunggu</span>
                            @endif
                        @else
                            <span class="text-gray-500">Tidak Ada Aksi</span>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada pengajuan SPPD yang perlu Anda setujui.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal untuk alasan penolakan (sudah benar) --}}
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
   function showRejectModal(sppdId) {
    document.getElementById('rejectModal').classList.remove('hidden');
    const form = document.getElementById('rejectForm');
    form.action = `/sppd/${sppdId}/status`; // Ini perubahannya!
}

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('alasan_penolakan').value = '';
    }
</script>

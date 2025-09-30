<div
     class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md mb-6">
    <h2 class="text-xl font-semibold text-gray-800">Daftar Pengajuan SP</h2>
    <a href="{{ route('sp.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        + Ajukan SP Baru
    </a>
</div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        No. Surat
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Karyawan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Tanggal Terbit
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Periode Berlaku
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Keterangan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Surat
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($sp as $item)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $item->no_surat }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ $item->user->nama_lengkap ?? 'N/A' }} <br>
                            <span class="text-xs text-gray-400">{{ $item->nip_user }}</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($item->tgl_sp_terbit)->translatedFormat('d F Y') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($item->tgl_mulai)->translatedFormat('d F Y') }} s/d <br>
                            {{ \Carbon\Carbon::parse($item->tgl_selesai)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($item->ket_peringatan, 50, '...') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                            <a href="{{ route('sp.download', $item->id) }}" class="text-green-600 hover:text-green-900 mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.707-8.707a1 1 0 000 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 9.586V3a1 1 0 10-2 0v6.586L6.707 8.293a1 1 0 00-1.414 1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            @if ($item->file_bukti)
                            <a href="{{ route('sp.downloadBukti', $item->id) }}" class="text-blue-600 hover:text-blue-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 2h2v2H6V6zm2 4H6v2h2v-2zm2-2h2v2h-2V8zm2 2h-2v2h2v-2zm-2 4H6v2h2v-2zm2 0h2v2h-2v-2z" />
                                </svg>
                            </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Belum ada Surat Peringatan yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


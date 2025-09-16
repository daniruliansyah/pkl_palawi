<div
  class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6"
>
  <div
    class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between"
  >
    <div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
        Daftar Pengajuan SPPD
      </h3>
    </div>

    <div class="flex items-center gap-3">
      <a
        href="{{ route('sppd.create') }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-blue-600 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-blue-700"
      >
        + Ajukan SPPD Baru
      </a>
    </div>
  </div>

  <div class="w-full overflow-x-auto">
    <table class="min-w-full text-sm text-left">
      <thead>
        <tr class="border-y border-gray-100 dark:border-gray-800 bg-gray-50">
          <th class="py-3 px-4">Nama Surat</th>
          <th class="py-3 px-4">Tanggal Dibuat</th>
          <th class="py-3 px-4">Persetujuan GM</th>
          <th class="py-3 px-4">Persetujuan SDM</th>
          <th class="py-3 px-4">Status</th>
          <th class="py-3 px-4">Surat</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($sppds as $item)
          <tr>
            <td class="py-3 px-4">SPPD - {{ $item->keterangan }}</td>
            <td class="py-3 px-4">{{ $item->created_at->format('d-m-Y') }}</td>
            <td class="py-3 px-4">
              <span class="px-2 py-1 rounded-full text-xs font-medium
                {{ $item->status_gm === 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ $item->status_gm ?? 'Menunggu' }}
              </span>
            </td>
            <td class="py-3 px-4">
              <span class="px-2 py-1 rounded-full text-xs font-medium
                {{ $item->status_sdm 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
              </span>
            </td>
            <td class="py-3 px-4">
              <span class="px-2 py-1 rounded-full text-xs font-medium
                {{ $item->status_pengajuan === 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $item->status_pengajuan }}
              </span>
            </td>
            <td class="py-3 px-4">
              @if($item->status_gm === 'Disetujui' || $item->status_sdm === 'Disetujui')
                <a href="{{ route('sppd.download', $item->id) }}"
                   class="px-3 py-1 text-xs font-medium rounded bg-blue-500 text-white hover:bg-blue-600">
                  Download PDF
                </a>
              @else
                <span class="text-gray-400 text-xs">Belum tersedia</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-500">
              Belum ada pengajuan SPPD.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

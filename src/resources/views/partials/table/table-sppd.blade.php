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
              <tr class="dark:hover:bg-gray-700">
                  <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                      SPPD - {{ $item->keterangan }}
                  </td>
                  <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                      {{ $item->created_at->format('d-m-Y') }}
                  </td>
                  <td class="px-4 py-3">
                      <span @class([
                          'px-2 py-1 font-medium text-xs rounded-full',
                          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => $item->status_gm == 'Disetujui',
                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $item->status_gm == 'Ditolak',
                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => $item->status_gm != 'Disetujui' && $item->status_gm != 'Ditolak',
                      ])>
                          {{ $item->status_gm }}
                      </span>
                  </td>
                  <td class="px-4 py-3">
                      <span @class([
                          'px-2 py-1 font-medium text-xs rounded-full',
                          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => $item->status_sdm == 'Disetujui',
                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $item->status_sdm == 'Ditolak',
                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => $item->status_sdm != 'Disetujui' && $item->status_sdm != 'Ditolak',
                      ])>
                          {{ $item->status_sdm }}
                      </span>
                  </td>
                  <td class="px-4 py-3 font-medium
                      @if($item->status_gm === 'Disetujui' && $item->status_sdm === 'Disetujui')
                          text-green-700 dark:text-green-400
                      @elseif($item->status_gm === 'Ditolak' || $item->status_sdm === 'Ditolak')
                          text-red-700 dark:text-red-400
                      @else
                          text-gray-500 dark:text-gray-400
                      @endif">
                      @if($item->status_gm === 'Disetujui' && $item->status_sdm === 'Disetujui')
                          Selesai
                      @elseif($item->status_gm === 'Ditolak' || $item->status_sdm === 'Ditolak')
                          Ditolak
                      @else
                          Dalam Proses
                      @endif
                  </td>
                  <td class="px-4 py-3">
                      {{-- Tombol Download Surat hanya muncul jika semua setuju --}}
                      @if($item->status_gm === 'Disetujui' && $item->status_sdm === 'Disetujui')
                          <a href="{{-- route('sppd.download', $item->id) --}}"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-blue-700">
                              Download
                          </a>
                      @else
                          <span class="text-xs text-gray-400 dark:text-gray-500">Belum tersedia</span>
                      @endif
                  </td>
              </tr>
          @empty
              <tr>
                  <td colspan="6" class="py-4 text-center text-gray-500 dark:text-gray-400">
                      Belum ada pengajuan SPPD.
                  </td>
              </tr>
          @endforelse
      </tbody>
    </table>
  </div>
</div>

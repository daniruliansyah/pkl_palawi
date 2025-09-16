<div
  class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6"
>
  <div
    class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between"
  >
    <div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
        Data Karyawan
      </h3>
    </div>

    <div class="flex items-center gap-3">
      <a
        href="{{ route('kalender.create') }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-blue-600 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-blue-700"
      >
        + Tambah Karyawan
      </a>
    </div>
  </div>

  <div class="w-full overflow-x-auto">
    <table class="min-w-full text-sm text-left">
      <thead>
        <tr class="border-y border-gray-100 dark:border-gray-800 bg-gray-50">
          <th class="py-3 px-4">Foto</th>
          <th class="py-3 px-4">Nama</th>
          <th class="py-3 px-4">NIP</th>
          <th class="py-3 px-4">NIK</th>
          <th class="py-3 px-4">No. Telp</th>
          <th class="py-3 px-4">Email</th>
          <th class="py-3 px-4">JK</th>
          <th class="py-3 px-4">Alamat</th>
          <th class="py-3 px-4">Tgl Lahir</th>
          <th class="py-3 px-4">Tempat Lahir</th>
          <th class="py-3 px-4">Agama</th>
          <th class="py-3 px-4">Status Kawin</th>
          <th class="py-3 px-4">Area</th>
          <th class="py-3 px-4">Aktif</th>
          <th class="py-3 px-4">NPK</th>
          <th class="py-3 px-4">NPWP</th>
          <th class="py-3 px-4">Join</th>
          <th class="py-3 px-4">Cuti</th>
          <th class="py-3 px-4">Aksi</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($karyawan as $item)
          <tr>
            <td class="py-3 px-4">
                @if ($item->foto)
                    <img src="{{ asset('storage/' . $item->foto) }}"
                        alt="Foto"
                        class="h-12 w-12 rounded-full object-cover">
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            <td class="py-3 px-4">{{ $item->nama_lengkap }}</td>
            <td class="py-3 px-4">{{ $item->nip ?? '-' }}</td>
            <td class="py-3 px-4">{{ $item->nik }}</td>
            <td class="py-3 px-4">{{ $item->no_telp }}</td>
            <td class="py-3 px-4">{{ $item->email }}</td>
            <td class="py-3 px-4">
              {{ $item->jenis_kelamin ? 'L' : 'P' }}
            </td>
            <td class="py-3 px-4">{{ $item->alamat }}</td>
            <td class="py-3 px-4">{{ $item->tgl_lahir }}</td>
            <td class="py-3 px-4">{{ $item->tempat_lahir }}</td>
            <td class="py-3 px-4">{{ $item->agama }}</td>
            <td class="py-3 px-4">{{ $item->status_perkawinan }}</td>
            <td class="py-3 px-4">{{ $item->area_bekerja }}</td>
            <td class="py-3 px-4">
              <span class="px-2 py-1 rounded-full text-xs font-medium
                {{ $item->status_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="py-3 px-4">{{ $item->npk_baru }}</td>
            <td class="py-3 px-4">{{ $item->npwp }}</td>
            <td class="py-3 px-4">{{ $item->join_date }}</td>
            <td class="py-3 px-4">{{ $item->jatah_cuti }}</td>
            <td class="py-3 px-4 flex gap-2">
              <a
                href="{{ route('karyawan.edit', $item->id) }}"
                class="px-3 py-1 text-xs font-medium rounded bg-yellow-400 text-white hover:bg-yellow-500"
              >
                Edit
              </a>
              <form action="{{ route('karyawan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                @csrf
                @method('DELETE')
                <button
                  type="submit"
                  class="px-3 py-1 text-xs font-medium rounded bg-red-500 text-white hover:bg-red-600"
                >
                  Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="18" class="text-center py-4 text-gray-500">
              Belum ada data karyawan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

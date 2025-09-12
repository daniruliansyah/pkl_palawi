<a href="{{ route('karyawan.show', $karyawan->id) }}" class="group flex items-center bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition p-4 min-h-[150px]">
    <!-- Foto di kiri, vertikal center -->
    <div class="w-24 h-24 rounded-full overflow-hidden border border-gray-300 flex-shrink-0">
        <img
            src="{{ $karyawan->foto ? asset('storage/' . $karyawan->foto) : asset('images/default.png') }}"
            alt="{{ $karyawan->nama_lengkap }}"
            class="w-full h-full object-cover"
        >
    </div>

    <!-- Info di kanan -->
    <div class="flex flex-col justify-center ml-4">
        <h3 class="text-lg font-semibold text-gray-800">{{ $karyawan->nama_lengkap }}</h3>
        <p class="text-sm text-gray-600">NIP: {{ $karyawan->nip ?? '-' }}</p>
        <p class="text-sm text-gray-600">Alamat: {{ $karyawan->alamat ?? '-' }}</p>
    </div>
</a>

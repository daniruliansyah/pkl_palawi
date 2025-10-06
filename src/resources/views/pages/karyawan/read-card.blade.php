@extends('layouts.dashboard')

@section('title', 'Daftar Karyawan')

@section('content')
<div class="p-6 w-full">
    <div class="mb-6 p-6 bg-gray-100" style="background-color: transparent">

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Karyawan</h1>

            <a href="{{ route('karyawan.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition duration-150 ease-in-out">
                + Tambah Karyawan
            </a>
        </div>

        {{-- BLOK PENCARIAN (Diambil dari Updated upstream karena memiliki fitur suggestion/autocomplete) --}}
        <div class="mb-4 relative">
            <form id="searchForm" action="{{ route('karyawan.index') }}" method="GET" class="flex items-center space-x-2">
                <div class="relative w-full">
                    <input
                        type="search"
                        id="searchInput"
                        name="search"
                        placeholder="Cari nama karyawan..."
                        class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        autocomplete="off"
                        value="{{ request('search') }}" {{-- Menambahkan nilai pencarian saat ini dari versi Stashed changes --}}
                    >
                    <ul id="suggestionList"
                        class="absolute left-0 right-0 bg-white border border-gray-300 mt-1 rounded-lg shadow-lg hidden z-10 max-h-48 overflow-y-auto">
                    </ul>
                </div>
                {{-- Menambahkan tombol submit dari versi Stashed changes untuk pencarian manual --}}
                <button type="submit"
                        class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                    </svg>
                </button>
            </form>
        </div>
        {{-- AKHIR BLOK PENCARIAN --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($karyawan as $item)
                @include('partials.profile.card-karyawan', ['karyawan' => $item])
            @endforeach
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const suggestionList = document.getElementById('suggestionList');
    const form = document.getElementById('searchForm');

    let timeout = null;

    // Untuk mencegah form auto-submit saat user tekan Enter saat fokus di input.
    // Kita akan biarkan form disubmit secara manual melalui tombol atau suggestion click.
    form.addEventListener('submit', function (e) {
        // Jika form disubmit via tombol, kita biarkan.
        // Jika ini adalah event Enter, kita bisa cegah jika tidak ingin pencarian terjadi pada Enter.
        // Namun, membiarkan form disubmit via Enter adalah perilaku standar yang baik,
        // kecuali jika tujuan utamanya HANYA melalui suggestion.
        // Saya hapus preventDefault yang ada di versi Updated upstream untuk membiarkan tombol submit bekerja.
    });

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(timeout);

        // Jika query kosong, sembunyikan suggestion
        if (query.length === 0) {
            suggestionList.classList.add('hidden');
            return;
        }

        timeout = setTimeout(() => {
            if (query.length > 1) { // Mulai mencari setelah 2 karakter
                // Pastikan rute ini sudah didefinisikan di Laravel (misalnya: GET /karyawan/cari?q=)
                fetch(`{{ route('karyawan.cari') }}?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Data diterima:', data); // debug
                        suggestionList.innerHTML = '';

                        const results = data.results || [];

                        if (results.length > 0) {
                            results.forEach(item => {
                                const li = document.createElement('li');
                                li.textContent = item.text;
                                li.className = 'p-2 hover:bg-blue-100 cursor-pointer';
                                li.addEventListener('click', () => {
                                    searchInput.value = item.text;
                                    suggestionList.classList.add('hidden');
                                    // Langsung submit form setelah memilih suggestion
                                    form.submit();
                                });
                                suggestionList.appendChild(li);
                            });
                            suggestionList.classList.remove('hidden');
                        } else {
                            // Tampilkan pesan "Tidak Ditemukan" jika perlu, atau cukup sembunyikan
                            suggestionList.classList.add('hidden');
                        }
                    })
                    .catch(err => console.error('Error fetching suggestions:', err));
            } else {
                suggestionList.classList.add('hidden');
            }
        }, 300); // debounce
    });

    // Klik di luar -> sembunyikan dropdown
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !suggestionList.contains(e.target)) {
            suggestionList.classList.add('hidden');
        }
    });
});
</script>
@endsection

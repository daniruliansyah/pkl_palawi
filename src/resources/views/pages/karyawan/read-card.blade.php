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

        <div class="mb-4 relative">
            <form id="searchForm" action="{{ route('karyawan.index') }}" method="GET" class="flex items-center space-x-2" onsubmit="return false;">
                <div class="relative w-full">
                    <input 
                        type="search" 
                        id="searchInput"
                        name="search" 
                        placeholder="Cari nama karyawan..." 
                        class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        autocomplete="off"
                    >
                    <!-- Dropdown suggestion -->
                    <ul id="suggestionList" 
                        class="absolute left-0 right-0 bg-white border border-gray-300 mt-1 rounded-lg shadow-lg hidden z-10 max-h-48 overflow-y-auto">
                    </ul>
                </div>
            </form>
        </div>

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

    // Cegah form auto-submit saat user tekan Enter
    form.addEventListener('submit', function (e) {
        e.preventDefault();
    });

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            if (query.length > 1) {
                fetch(`{{ route('karyawan.cari') }}?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Data diterima:', data); // debug
                        suggestionList.innerHTML = '';

                        if (data.results && data.results.length > 0) {
                            data.results.forEach(item => {
                                const li = document.createElement('li');
                                li.textContent = item.text;
                                li.className = 'p-2 hover:bg-blue-100 cursor-pointer';
                                li.addEventListener('click', () => {
                                    searchInput.value = item.text;
                                    suggestionList.classList.add('hidden');
                                    // Auto submit form (kalau mau langsung cari)
                                    window.location.href = `/karyawan/${item.id}`;
                                });
                                suggestionList.appendChild(li);
                            });
                            suggestionList.classList.remove('hidden');
                        } else {
                            suggestionList.classList.add('hidden');
                        }
                    })
                    .catch(err => console.error('Error:', err));
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

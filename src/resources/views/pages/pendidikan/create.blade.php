@extends('layouts.dashboard') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Tambah Riwayat Pendidikan - ' . $karyawan->nama_lengkap)

@section('content')
<div class="flex items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow-md dark:bg-gray-800">
        <h2 class="mb-6 text-2xl font-bold text-gray-800 dark:text-white">Formulir Riwayat Pendidikan</h2>
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">Menambahkan riwayat pendidikan untuk karyawan: <strong class="text-gray-900 dark:text-white">{{ $karyawan->nama_lengkap }}</strong> (NIP: {{ $karyawan->nip }})</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700 dark:bg-red-900 dark:text-red-300" role="alert">
                <span class="font-bold">Terjadi Kesalahan!</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form action menunjuk ke route store dengan parameter karyawan --}}
        <form action="{{ route('karyawan.pendidikan.store', $karyawan->id) }}" method="POST" class="space-y-6">
            @csrf

            {{-- Jenjang Pendidikan --}}
            <div>
                <label for="jenjang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                <input type="text" name="jenjang" id="jenjang" value="{{ old('jenjang') }}" required placeholder="Contoh: SMA, S1, S2"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                @error('jenjang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nama Institusi --}}
            <div>
                <label for="nama_institusi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Institusi / Sekolah <span class="text-red-500">*</span></label>
                <input type="text" name="nama_institusi" id="nama_institusi" value="{{ old('nama_institusi') }}" required placeholder="Nama lengkap sekolah atau universitas"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                @error('nama_institusi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Jurusan --}}
            <div>
                <label for="jurusan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jurusan / Program Studi</label>
                <input type="text" name="jurusan" id="jurusan" value="{{ old('jurusan') }}" placeholder="Kosongkan jika tidak relevan (misal: SD/SMP/SMA)"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                @error('jurusan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tahun Masuk & Lulus (dalam satu baris) --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="tahun_masuk" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Masuk <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun_masuk" id="tahun_masuk" value="{{ old('tahun_masuk') }}" required placeholder="YYYY" min="1900" max="{{ date('Y') }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    @error('tahun_masuk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tahun_lulus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Lulus <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun_lulus" id="tahun_lulus" value="{{ old('tahun_lulus') }}" required placeholder="YYYY" min="1900" max="{{ date('Y') + 5 }}" {{-- Maksimal 5 tahun ke depan --}}
                           class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    @error('tahun_lulus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- IPK --}}
            <div>
                <label for="ipk" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IPK / Nilai Akhir</label>
                <input type="number" step="0.01" name="ipk" id="ipk" value="{{ old('ipk') }}" placeholder="Contoh: 3.75 (kosongkan jika tidak relevan)" min="0" max="4.00"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                 <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Gunakan titik (.) sebagai pemisah desimal.</p>
                @error('ipk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end space-x-4 pt-4">
                {{-- Tombol Batal mengarah kembali ke detail karyawan --}}
                <a href="{{ route('karyawan.show', $karyawan->id) }}" class="inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-700 shadow transition-colors duration-200 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center rounded-lg bg-green-500 px-6 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700">
                    Simpan Riwayat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

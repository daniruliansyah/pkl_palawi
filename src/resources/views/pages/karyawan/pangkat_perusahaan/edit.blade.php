@extends('layouts.dashboard')

@section('title', 'Edit Riwayat Pangkat - ' . $karyawan->nama_lengkap)

@section('content')
<div class="flex items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow-md dark:bg-gray-800">
        <h2 class="mb-6 text-2xl font-bold text-gray-800 dark:text-white">Formulir Edit Riwayat Pangkat Perusahaan</h2>
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">Mengedit riwayat pangkat/golongan untuk: <strong class="text-gray-900 dark:text-white">{{ $karyawan->nama_lengkap }}</strong></p>

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

        {{-- Route Update Pangkat --}}
        <form action="{{ route('pangkat.update', $pangkat->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Golongan Ruang --}}
            <div>
                <label for="gol_ruang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Golongan / Ruang <span class="text-red-500">*</span></label>
                <input type="text" name="gol_ruang" id="gol_ruang" value="{{ old('gol_ruang', $pangkat->gol_ruang) }}" required placeholder="Contoh: III/a, IV/b"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                @error('gol_ruang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- TMT Golongan --}}
            <div>
                <label for="tmt_gol" class="block text-sm font-medium text-gray-700 dark:text-gray-300">TMT Golongan <span class="text-red-500">*</span></label>
                <input type="date" name="tmt_gol" id="tmt_gol" value="{{ old('tmt_gol', $pangkat->tmt_gol) }}" required
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                @error('tmt_gol') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nomor SK & Tanggal SK --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="no_sk" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor SK <span class="text-red-500">*</span></label>
                    <input type="text" name="no_sk" id="no_sk" value="{{ old('no_sk', $pangkat->no_sk) }}" required placeholder="Nomor Surat Keputusan"
                           class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    @error('no_sk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tgl_sk" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal SK <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_sk" id="tgl_sk" value="{{ old('tgl_sk', $pangkat->tgl_sk) }}" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                    @error('tgl_sk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Link Berkas --}}
            <div>
                <label for="link_berkas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link Berkas (Scan SK)</label>
                <input type="text" name="link_berkas" id="link_berkas" value="{{ old('link_berkas', $pangkat->link_berkas) }}" placeholder="Link file SK Pangkat"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                @error('link_berkas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('karyawan.show', $karyawan->id) }}" class="inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-700 shadow transition-colors duration-200 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center rounded-lg bg-green-500 px-6 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700">
                    Update Riwayat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.dashboard')

@section('title', 'Edit Riwayat Penghargaan - ' . $karyawan->nama_lengkap)

@section('content')
<div class="flex items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow-md dark:bg-gray-800">
        <h2 class="mb-6 text-2xl font-bold text-gray-800 dark:text-white">Formulir Edit Riwayat Penghargaan</h2>
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">Mengedit riwayat penghargaan untuk: <strong class="text-gray-900 dark:text-white">{{ $karyawan->nama_lengkap }}</strong></p>

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

        {{-- Route Update Penghargaan --}}
        <form action="{{ route('penghargaan.update', $penghargaan->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nama Penghargaan --}}
            <div>
                <label for="nama_penghargaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Penghargaan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_penghargaan" id="nama_penghargaan" value="{{ old('nama_penghargaan', $penghargaan->nama_penghargaan) }}" required placeholder="Contoh: Karyawan Teladan Tahun 2024"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                @error('nama_penghargaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal Terima --}}
            <div>
                <label for="tgl_terima" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Diterima <span class="text-red-500">*</span></label>
                <input type="date" name="tgl_terima" id="tgl_terima" value="{{ old('tgl_terima', $penghargaan->tgl_terima) }}" required
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                @error('tgl_terima') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Link Berkas --}}
            <div>
                <label for="link_berkas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link Berkas (Sertifikat/Piagam)</label>
                <input type="text" name="link_berkas" id="link_berkas" value="{{ old('link_berkas', $penghargaan->link_berkas) }}" placeholder="Link bukti penghargaan"
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
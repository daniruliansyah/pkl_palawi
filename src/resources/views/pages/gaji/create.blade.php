@extends('layouts.dashboard')
@section('title', 'Buat Gaji Karyawan')

@section('content')

<div class="flex items-center justify-center">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow">
        
        {{-- Header Formulir --}}
        <h2 class="mb-2 text-2xl font-bold text-gray-800">Formulir Gaji Karyawan</h2>
        <p class="mb-6 text-sm text-gray-500">
            Membuat slip gaji untuk karyawan: <span class="font-semibold">{{ $user->nama_lengkap }}</span>
        </p>
        
        {{-- Menampilkan notifikasi error jika ada --}}
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700" role="alert">
                <span class="font-bold">Terjadi Kesalahan!</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('gaji.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Hidden input untuk user_id, ini PENTING! --}}
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            {{-- Periode Gaji --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                    <input type="number" name="bulan" id="bulan" value="{{ old('bulan', date('m')) }}" min="1" max="12" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 {{ $errors->has('bulan') ? 'border-red-500' : 'border-gray-300' }}" required>
                </div>
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                    <input type="number" name="tahun" id="tahun" value="{{ old('tahun', date('Y')) }}" min="2020" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 {{ $errors->has('tahun') ? 'border-red-500' : 'border-gray-300' }}" required>
                </div>
            </div>

            {{-- Gaji Pokok --}}
            <div>
                <label for="gaji_pokok" class="block text-sm font-medium text-gray-700">Gaji Pokok</label>
                <input type="number" name="gaji_pokok" id="gaji_pokok" value="{{ old('gaji_pokok', 0) }}" min="0" placeholder="Contoh: 5000000" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 {{ $errors->has('gaji_pokok') ? 'border-red-500' : 'border-gray-300' }}" required>
            </div>
            
            <hr class="border-t border-gray-200">

            {{-- Detail Potongan (Dinamis) --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Detail Potongan</h3>
                <p class="mt-1 text-sm text-gray-500">Isi hanya kolom yang berlaku. Biarkan 0 jika tidak ada potongan.</p>
                <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @forelse($masterPotongan as $potongan)
                        <div>
                            <label for="potongan_{{ $potongan->id }}" class="block text-sm font-medium text-gray-700">{{ $potongan->nama_potongan }}</label>
                            <input type="number" name="potongan[{{ $potongan->id }}]" id="potongan_{{ $potongan->id }}" value="{{ old('potongan.' . $potongan->id, 0) }}" min="0" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                    @empty
                        <div class="sm:col-span-2 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-700">
                            Tidak ada data master potongan yang aktif. Silakan tambahkan terlebih dahulu di menu Master Data.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 {{ $errors->has('keterangan') ? 'border-red-500' : 'border-gray-300' }}" placeholder="Catatan tambahan untuk slip gaji ini...">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end pt-2">
                <a href="{{ route('gaji.indexForKaryawan', $user->id) }}" class="mr-4 inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-700 shadow transition-colors duration-200 hover:bg-gray-300">Batal</a>
                <button type="submit" name="simpan" class="inline-flex items-center rounded-lg bg-blue-500 px-6 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.dashboard')

@section('title', 'Input Gaji Karyawan')

@section('content')
<div class="p-6 mx-auto max-w-screen-lg">
    <h1 class="text-2xl font-bold mb-6">Input Gaji Karyawan</h1>

    {{-- Notifikasi Error Validasi --}}
    @if ($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">
            <span class="font-medium">Oops! Terjadi kesalahan:</span>
            <ul class="mt-1.5 ml-4 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gaji.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- BAGIAN DATA UTAMA --}}
        <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Utama</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Pilih Karyawan --}}
                <div>
                    <label for="user_id" class="mb-2 block text-sm font-medium">Nama Karyawan</label>
                    <select name="user_id" id="user_id"
                            class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                            required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bulan --}}
                <div>
                    <label for="bulan" class="mb-2 block text-sm font-medium">Bulan</label>
                    <select name="bulan" id="bulan"
                            class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                            required>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('bulan', date('n')) == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Tahun --}}
                <div>
                    <label for="tahun" class="mb-2 block text-sm font-medium">Tahun</label>
                    <input type="number" name="tahun" id="tahun"
                           class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                           placeholder="Contoh: 2025" value="{{ old('tahun', date('Y')) }}" required>
                </div>

                {{-- Gaji Pokok --}}
                <div class="md:col-span-3">
                    <label for="gaji_pokok" class="mb-2 block text-sm font-medium">Gaji Pokok (Rp)</label>
                    <input type="number" name="gaji_pokok" id="gaji_pokok" step="any"
                           class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                           placeholder="Masukkan nominal gaji pokok" value="{{ old('gaji_pokok') }}" required>
                </div>
            </div>
        </div>


        {{-- BAGIAN POTONGAN --}}
        <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Rincian Potongan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                
                @forelse ($masterPotongan as $potongan)
                    <div>
                        <label for="potongan_{{ $potongan->id }}" class="mb-2 block text-sm font-medium">{{ $potongan->nama_potongan }}</label>
                        <input type="number" name="potongan[{{ $potongan->id }}]" id="potongan_{{ $potongan->id }}" step="any"
                               class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                               placeholder="0" value="{{ old('potongan.' . $potongan->id, 0) }}">
                    </div>
                @empty
                    <div class="md:col-span-2 p-4 text-sm text-gray-500 bg-gray-50 rounded-lg">
                        Tidak ada data master potongan yang aktif. Silakan tambahkan terlebih dahulu di menu master data.
                    </div>
                @endforelse
                
            </div>
        </div>
        
        {{-- Keterangan Tambahan --}}
        <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
             <h2 class="text-lg font-semibold mb-4 border-b pb-2">Lain-lain</h2>
             <div>
                <label for="keterangan" class="mb-2 block text-sm font-medium">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                          placeholder="Masukkan keterangan atau catatan tambahan jika ada">{{ old('keterangan') }}</textarea>
            </div>
        </div>


        {{-- Tombol Aksi BARU --}}
        <div class="flex justify-end gap-4 mt-6">
            <a href="#" class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm text-gray-700 hover:bg-gray-50">Batal</a>
            
            {{-- Tombol untuk Simpan Saja --}}
            <button type="submit" name="action" value="save" class="rounded-lg bg-gray-500 px-6 py-2 text-sm font-medium text-white hover:bg-gray-600">
                Simpan
            </button>

            {{-- Tombol untuk Simpan dan Lanjut Cetak --}}
            <button type="submit" name="action" value="save_and_print" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Simpan & Cetak
            </button>
        </div>
        {{-- Form End --}}
    </form>
</div>
@endsection
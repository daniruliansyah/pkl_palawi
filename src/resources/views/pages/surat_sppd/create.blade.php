@extends('layouts.dashboard')
@section('title', 'Tambah SPPD')
@section('content')

<div class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Ajukan SPPD</h2>

        <form action="{{ route('sppd.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="pemberi_tugas_id" class="block text-sm font-medium text-gray-700">Pemberi Perintah</label>
                <select name="pemberi_tugas_id" id="pemberi_tugas_id" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                    <option value="">-- Pilih Pemberi Perintah --</option>
                    @foreach ($jabatan as $jabatan)
                        <option value="{{ $jabatan->id }}" {{ old('pemberi_tugas_id') == $jabatan->id ? 'selected' : '' }}>
                            {{ $jabatan->nama_jabatan }}
                        </option>
                    @endforeach
                </select>
                @error('pemberi_tugas_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="keterangan_sppd" class="block text-sm font-medium text-gray-700">Maksud Perjalanan Dinas</label>
                <textarea name="keterangan_sppd" id="keterangan_sppd" rows="3" required
                          class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 shadow-sm text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">{{ old('keterangan_sppd') }}</textarea>
                @error('keterangan_sppd') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="alat_angkat" class="block text-sm font-medium text-gray-700">Alat Angkat yang Dipergunakan</label>
                <input type="text" name="alat_angkat" id="alat_angkat" value="{{ old('alat_angkat') }}" required
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 shadow-sm text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                @error('alat_angkat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="lokasi_berangkat" class="block text-sm font-medium text-gray-700">Tempat Berangkat</label>
                <input type="text" name="lokasi_berangkat" id="lokasi_berangkat" value="{{ old('lokasi_berangkat') }}" required
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 shadow-sm text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                @error('lokasi_berangkat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="lokasi_tujuan" class="block text-sm font-medium text-gray-700">Tempat Tujuan</label>
                <input type="text" name="lokasi_tujuan" id="lokasi_tujuan" value="{{ old('lokasi_tujuan') }}" required
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 shadow-sm text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                @error('lokasi_tujuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal Berangkat</label>
                @include('partials.datepicker', [
                    'name' => 'tgl_mulai',
                    'id' => 'tgl_mulai',
                    'placeholder' => 'Pilih tanggal mulai',
                    'value' => old('tgl_mulai')
                ])
                @error('tgl_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Kembali</label>
                @include('partials.datepicker', [
                    'name' => 'tgl_selesai',
                    'id' => 'tgl_selesai',
                    'placeholder' => 'Pilih tanggal selesai',
                    'value' => old('tgl_selesai')
                ])
                @error('tgl_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="no_rekening" class="block text-sm font-medium text-gray-700">Pembebanan Anggaran (Rekening)</label>
                <input type="text" name="no_rekening" id="no_rekening" value="{{ old('no_rekening') }}"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 shadow-sm text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                @error('no_rekening') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nama_rekening" class="block text-sm font-medium text-gray-700">Pembebanan Anggaran (Nama Rekening)</label>
                <input type="text" name="nama_rekening" id="nama_rekening" value="{{ old('nama_rekening') }}"
                       class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 shadow-sm text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                @error('nama_rekening') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="keterangan_lain" class="block text-sm font-medium text-gray-700">Keterangan Lain</label>
                <textarea name="keterangan_lain" id="keterangan_lain" rows="2"
                          class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 shadow-sm text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">{{ old('keterangan_lain') }}</textarea>
                @error('keterangan_lain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-green-500 text-white font-medium rounded-lg shadow hover:bg-green-600 transition">
                    Ajukan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

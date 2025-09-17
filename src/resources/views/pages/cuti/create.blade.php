@extends('layouts.dashboard')
@section('title', 'Tambah Cuti')
@section('content')

<div class="flex items-center justify-center bg-gray-50">
  <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow">
    <h2 class="mb-6 text-2xl font-bold text-gray-800">Formulir Pengajuan Cuti</h2>

    {{-- BAGIAN 1: TAMPILKAN SEMUA ERROR VALIDASI DI SINI --}}
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

    <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label for="jenis_izin" class="block text-sm font-medium text-gray-700">Jenis Izin</label>
            <select id="jenis_izin" name="jenis_izin" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('jenis_izin') ? 'border-red-500' : 'border-gray-300' }}" required>
                <option value="" disabled selected>-- Pilih Jenis Izin --</option>
                {{-- BAGIAN 2: Gunakan old() untuk mempertahankan pilihan --}}
                <option value="Cuti" @if(old('jenis_izin') == 'Cuti') selected @endif>Cuti</option>
                <option value="Sakit" @if(old('jenis_izin') == 'Sakit') selected @endif>Sakit</option>
            </select>
        </div>

        <div>
            <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
            {{-- BAGIAN 4: Disederhanakan menggunakan input type="date" --}}
            <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ old('tgl_mulai') }}" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_mulai') ? 'border-red-500' : 'border-gray-300' }}" required>
        </div>

        <div>
            <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
            <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ old('tgl_selesai') }}" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_selesai') ? 'border-red-500' : 'border-gray-300' }}" required>
        </div>

        <div>
            {{-- BAGIAN 3: Label 'for' diperbaiki --}}
            <label for="jumlah_hari" class="block text-sm font-medium text-gray-700">Jumlah Hari</label>
            <input type="number" name="jumlah_hari" id="jumlah_hari" value="{{ old('jumlah_hari') }}" max="12" min="1" placeholder="Maks. 12 hari" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('jumlah_hari') ? 'border-red-500' : 'border-gray-300' }}" required>
        </div>

        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('keterangan') ? 'border-red-500' : 'border-gray-300' }}" required>{{ old('keterangan') }}</textarea>
        </div>

        <div>
            <label for="file_izin" class="block text-sm font-medium text-gray-700">Upload Surat Izin (Opsional)</label>
            <input type="file" name="file_izin" id="file_izin" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-green-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100">
            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, atau PDF. Maksimal 2MB.</p>
        </div>

        <div class="flex justify-end pt-2">
            {{-- BAGIAN 5: Tombol Batal ditambahkan --}}
            <a href="{{ route('cuti.index') }}" class="mr-4 inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-700 shadow transition-colors duration-200 hover:bg-gray-300">
              Batal
            </a>
            <button type="submit" class="inline-flex items-center rounded-lg bg-green-500 px-6 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-green-600">
              Ajukan
            </button>
        </div>
    </form>
  </div>
</div>
@endsection
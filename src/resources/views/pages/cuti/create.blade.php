@extends('layouts.dashboard')
@section('title', 'Tambah Cuti')
@section('content')

<div class="flex items-center justify-center min-h-screen bg-gray-50">
  <div class="w-full max-w-2xl bg-white rounded-2xl shadow p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Ajukan CUTI</h2>

      <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
          @csrf

          <div>
            <label for="jenis_izin" class="block text-sm font-medium text-gray-700">Jenis Izin</label>
            <select id="jenis_izin" name="jenis_izin" 
                    class="block w-full px-4 py-2.5 mt-1 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg shadow-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 focus:outline-none">
                    <option value="Cuti">Cuti</option>
                    <option value="Sakit">Sakit</option>
            </select>
          </div>

          <!-- Tanggal Mulai -->
          <div>
              <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
              @include('partials.datepicker', [
                  'name' => 'tgl_mulai',
                  'id' => 'tgl_mulai',
                  'placeholder' => 'Pilih tanggal mulai'
              ])
          </div>

          <!-- Tanggal Selesai -->
          <div>
              <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
              @include('partials.datepicker', [
                  'name' => 'tgl_selesai',
                  'id' => 'tgl_selesai',
                  'placeholder' => 'Pilih tanggal selesai'
              ])
          </div>

          <!-- Jumlah Hari -->
          <div>
              <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Jumlah Hari</label>
                <input type="number"
                       name="jumlah_hari" 
                       id="jumlah_hari"
                       max="12"
                       min="1"
                       placeholder="Maks. 12 hari"
                       class="mt-1 block w-full rounded-lg border border-gray-300 bg-white py-2.5 px-4 
                            shadow-sm text-sm font-medium text-gray-700
                            focus:border-green-400 focus:ring-2 focus:ring-green-100 
                            focus:outline-none transition-colors duration-200"
                    >
          </div>

          <!-- Keterangan -->
          <div>
              <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
              <textarea name="keterangan" id="keterangan" rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white py-2.5 px-4
                               shadow-sm text-sm font-medium text-gray-700
                               focus:border-green-400 focus:ring-2 focus:ring-green-100
                               focus:outline-none transition-colors duration-200"></textarea>
          </div>

          <!-- Upload File Izin -->
          <div>
              <label for="file_izin" class="block text-sm font-medium text-gray-700">Upload File Izin</label>
              <input type="file" name="file_izin" id="file_izin" accept=".jpg,.jpeg,.png,.pdf"
                     class="mt-1 block w-full text-sm text-gray-700
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-lg file:border-0
                            file:text-sm file:font-semibold
                            file:bg-green-50 file:text-green-700
                            hover:file:bg-green-100
                            focus:border-green-400 focus:ring-2 focus:ring-green-100
                            focus:outline-none transition-colors duration-200">
              <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, atau PDF</p>
          </div>

          <!-- Tombol -->
          <div class="flex justify-end">
              <button type="submit"
                  class="inline-flex items-center px-6 py-2 bg-green-500 text-white font-medium rounded-lg shadow hover:bg-green-600 transition-colors duration-200">
                  Ajukan
              </button>
          </div>
      </form>
  </div>
</div>

@endsection

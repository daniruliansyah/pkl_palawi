@extends('layouts.dashboard')
@section('title', 'Tambah SPPD')
@section('content')

<div class="flex items-center justify-center min-h-screen bg-gray-50">
  <div class="w-full max-w-2xl bg-white rounded-2xl shadow p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Ajukan SPPD</h2>

      <form action="{{ route('sppd.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
          @csrf

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

          <!-- Keterangan -->
          <div>
              <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
              <textarea name="keterangan" id="keterangan" rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white py-2.5 px-4
                               shadow-sm text-sm font-medium text-gray-700
                               focus:border-green-400 focus:ring-2 focus:ring-green-100
                               focus:outline-none transition-colors duration-200"></textarea>
          </div>

          <!-- Lokasi Tujuan -->
          <div>
              <label for="lokasi_tujuan" class="block text-sm font-medium text-gray-700">Lokasi Tujuan</label>
              <input type="text" name="lokasi_tujuan" id="lokasi_tujuan"
                     class="mt-1 block w-full rounded-lg border border-gray-300 bg-white py-2.5 px-4
                            shadow-sm text-sm font-medium text-gray-700
                            focus:border-green-400 focus:ring-2 focus:ring-green-100
                            focus:outline-none transition-colors duration-200">
          </div>

          <!-- Upload Surat Bukti Dinas -->
          <div>
              <label for="surat_bukti" class="block text-sm font-medium text-gray-700">Upload Surat Bukti Dinas</label>
              <input type="file" name="surat_bukti" id="surat_bukti" accept=".jpg,.jpeg,.png,.pdf"
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

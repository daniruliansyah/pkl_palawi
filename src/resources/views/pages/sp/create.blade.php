@extends('layouts.dashboard')
@section('title', 'Tambah Surat Peringatan')
@section('content')

<div class="flex items-center justify-center bg-gray-50">
  <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow">
    <h2 class="mb-6 text-2xl font-bold text-gray-800">Formulir Surat Peringatan</h2>

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

    <form action="{{ route('sp.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Input Pencarian Karyawan --}}
        <div>
            <label for="nip_user" class="block text-sm font-medium text-gray-700">Ditujukan Kepada Karyawan</label>
            <select id="cari" name="nip_user" class="mt-1 block w-full" required>
                {{-- Opsi akan diisi oleh JavaScript saat Anda mencari --}}
            </select>
            @error('nip_user')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        
        <hr class="border-gray-200">
        
        {{-- Input Form Lainnya --}}
        <div>
            <label for="tgl_sp_terbit" class="block text-sm font-medium text-gray-700">Tanggal SP Terbit</label>
            <input type="date" name="tgl_sp_terbit" id="tgl_sp_terbit" value="{{ old('tgl_sp_terbit') }}" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_sp_terbit') ? 'border-red-500' : 'border-gray-300' }}" required>
        </div>

        <div>
            <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ old('tgl_mulai') }}" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_mulai') ? 'border-red-500' : 'border-gray-300' }}" required>
        </div>

        <div>
            <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
            <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ old('tgl_selesai') }}" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_selesai') ? 'border-red-500' : 'border-gray-300' }}" required>
        </div>

        <div>
            <label for="ket_peringatan" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea name="ket_peringatan" id="ket_peringatan" rows="3" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('ket_peringatan') ? 'border-red-500' : 'border-gray-300' }}" required>{{ old('ket_peringatan') }}</textarea>
        </div>

        <div>
            <label for="file_sp" class="block text-sm font-medium text-gray-700">Upload Surat Peringatan (Opsional)</label>
            <input type="file" name="file_sp" id="file_sp" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-green-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100">
            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, atau PDF. Maksimal 2MB.</p>
        </div>

        <div class="flex justify-end pt-2">
            <a href="{{ route('sp.index') }}" class="mr-4 inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-700 shadow transition-colors duration-200 hover:bg-gray-300">
              Batal
            </a>
            <button type="submit" class="inline-flex items-center rounded-lg bg-green-500 px-6 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-green-600">
              Kirim
            </button>
        </div>
    </form>
  </div>
</div>

{{-- Pastikan @push berada SEBELUM @endsection --}}
@push('scripts')
<script>
    $(document).ready(function() {
        $('#cari').select2({
            placeholder: 'Cari berdasarkan NIP atau Nama Karyawan',
            ajax: {
                url: '{{ route("cari") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term //send 'q' to server
                    };              
                },
                processResults: function (data) {
                    // server mengembalikan { results: [...] } jadi kembalikan data langsung
                    return data;
                },
                cache: true
            },
            minimumInputLength: 1, // memaksa user mengetik minimal 1 karakter
            width: '100%'
        });
    });
</script>
@endpush

@endsection
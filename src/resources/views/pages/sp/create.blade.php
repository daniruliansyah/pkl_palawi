@extends('layouts.dashboard')

@section('title', 'Tambah Surat Peringatan')

@section('content')
<div class="flex items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow-md">
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

        <form action="{{ route('sp.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Input Pencarian Karyawan dengan Select2 --}}
            <div>
                <label for="nip_user" class="block text-sm font-medium text-gray-700">Ditujukan Kepada Karyawan</label>
                <select name="nip_user" id="nip_user" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                    <option value="">-- Cari NIP atau Nama Karyawan --</option>
                </select>
                @error('nip_user') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Input Tanggal SP Terbit --}}
            <div>
                <label for="tgl_sp_terbit" class="block text-sm font-medium text-gray-700">Tanggal SP Terbit</label>
                @include('partials.datepicker', [
                    'name' => 'tgl_sp_terbit',
                    'id' => 'tgl_sp_terbit',
                    'placeholder' => 'Pilih Tanggal',
                    'value' => old('tgl_sp_terbit')
                ])
                @error('tgl_sp_terbit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Input Tanggal Mulai Berlaku --}}
            <div>
                <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai Berlaku</label>
                @include('partials.datepicker', [
                    'name' => 'tgl_mulai',
                    'id' => 'tgl_mulai',
                    'placeholder' => 'Pilih Tanggal Mulai',
                    'value' => old('tgl_mulai')
                ])
                @error('tgl_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Input Tanggal Selesai Berlaku --}}
            <div>
                <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai Berlaku</label>
                @include('partials.datepicker', [
                    'name' => 'tgl_selesai',
                    'id' => 'tgl_selesai',
                    'placeholder' => 'Pilih Tanggal Selesai',
                    'value' => old('tgl_selesai')
                ])
                @error('tgl_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="ket_peringatan" class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
                <textarea name="ket_peringatan" id="ket_peringatan" rows="3"
                    class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('ket_peringatan') ? 'border-red-500' : 'border-gray-300' }}" required>{{ old('ket_peringatan') }}</textarea>
                @error('ket_peringatan')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="file_bukti" class="mb-1 block text-sm font-medium text-gray-700">Upload Bukti Pelanggaran (Opsional)</label>
                <input type="file" name="file_bukti" id="file_bukti" accept=".jpg,.jpeg,.png,.pdf"
                    class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-green-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100">
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, atau PDF. Maksimal 2MB.</p>
                @error('file_bukti')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('sp.index') }}" class="inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-700 shadow transition-colors duration-200 hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center rounded-lg bg-green-500 px-6 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-green-600">
                    Kirim
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#nip_user').select2({
            placeholder: 'Cari berdasarkan NIP atau Nama Karyawan',
            allowClear: true,
            containerCssClass: 'mt-1 block w-full border border-gray-300 rounded-lg py-2.5 px-4 text-sm font-medium text-gray-700 shadow-sm focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100',
            dropdownCssClass: 'rounded-lg border border-gray-300 shadow-md',
            ajax: {
                url: '{{ route("cari-karyawan") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            width: '100%'
        });

        flatpickr("#tgl_sp_terbit", { dateFormat: "Y-m-d" });
        flatpickr("#tgl_mulai", { dateFormat: "Y-m-d" });
        flatpickr("#tgl_selesai", { dateFormat: "Y-m-d" });
    });
</script>
@endpush

@endsection

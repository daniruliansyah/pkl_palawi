@extends('layouts.dashboard')

@section('title', 'Tambah Surat Peringatan')

@section('content')
<div class="flex items-center justify-center p-4">
    <div class="w-full max-w-4xl rounded-2xl bg-white p-8 shadow-md">
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

            {{-- BLOK 1: INFORMASI SURAT --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                {{-- Input Hal Surat --}}
                <div>
                    <label for="hal_surat" class="block text-sm font-medium text-gray-700">Hal Surat</label>
                    <input type="text" name="hal_surat" id="hal_surat" value="{{ old('hal_surat', 'Surat Peringatan') }}" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                    @error('hal_surat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jenis Surat Peringatan --}}
                <div>
                    <label for="jenis_sp" class="block text-sm font-medium text-gray-700">Jenis Surat Peringatan</label>
                    <select name="jenis_sp" id="jenis_sp" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                        <option value="">-- Pilih Jenis SP --</option>
                        <option value="Pertama" {{ old('jenis_sp') == 'Pertama' ? 'selected' : '' }}>Surat Peringatan Pertama</option>
                        <option value="Kedua" {{ old('jenis_sp') == 'Kedua' ? 'selected' : '' }}>Surat Peringatan Kedua</option>
                        <option value="Terakhir" {{ old('jenis_sp') == 'Terakhir' ? 'selected' : '' }}>Surat Peringatan Terakhir</option>
                    </select>
                    @error('jenis_sp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- BLOK 2: Tanggal Berlaku --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                {{-- Input Tanggal SP Terbit (dijadikan tanggal surat) --}}
                <div>
                    <label for="tgl_sp_terbit" class="block text-sm font-medium text-gray-700">Tanggal Surat Dikeluarkan</label>
                    @include('partials.datepicker', [
                        'name' => 'tgl_sp_terbit',
                        'id' => 'tgl_sp_terbit',
                        'placeholder' => 'Pilih Tanggal',
                        'value' => old('tgl_sp_terbit', now()->format('Y-m-d'))
                    ])
                    @error('tgl_sp_terbit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Input Tanggal Mulai Berlaku --}}
                <div>
                    <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai Berlaku SP</label>
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
                    <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai Berlaku SP</label>
                    @include('partials.datepicker', [
                        'name' => 'tgl_selesai',
                        'id' => 'tgl_selesai',
                        'placeholder' => 'Pilih Tanggal Selesai',
                        'value' => old('tgl_selesai')
                    ])
                    @error('tgl_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="my-4">

            {{-- BLOK 3: KARYAWAN & ISI SURAT --}}

            {{-- Input Pencarian Karyawan dengan Select2 --}}
            <div>
                <label for="nip_user" class="block text-sm font-medium text-gray-700">Ditujukan Kepada Karyawan (Penerima SP)</label>
                <select name="nip_user" id="nip_user" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">
                    <option value="">-- Cari NIP atau Nama Karyawan --</option>
                </select>
                @error('nip_user') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Isi Surat (Body/Paragraf Tengah) --}}
            <div>
                <label for="isi_surat" class="mb-1 block text-sm font-medium text-gray-700">Isi Surat (Diketik User)</label>
                <textarea name="isi_surat" id="isi_surat" rows="5"
                    class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('isi_surat') ? 'border-red-500' : 'border-gray-300' }}" required>{{ old('isi_surat') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Masukkan paragraf isi surat yang menjelaskan pelanggaran.</p>
                @error('isi_surat')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>


            {{-- Upload Bukti Pelanggaran --}}
            <div>
                <label for="file_bukti" class="mb-1 block text-sm font-medium text-gray-700">Upload Bukti Pelanggaran (Lampiran Surat)</label>
                <input type="file" name="file_bukti" id="file_bukti" accept=".jpg,.jpeg,.png,.pdf"
                    class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-green-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100">
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, atau PDF. Maksimal 2MB.</p>
                @error('file_bukti')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-4">

            {{-- BLOK 4: TEMBUSAN --}}
            <div>
                <label for="tembusan" class="block text-sm font-medium text-gray-700">Tembusan Kepada Yth. (Bisa Pilih Banyak)</label>
                {{-- Pastikan nama input adalah tembusan[] --}}
                <select name="tembusan[]" id="tembusan" multiple
                    class="mt-1 block w-full rounded-lg border border-gray-300 py-2.5 px-4 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100">

                    {{-- Loop data jabatan yang dikirim dari Controller --}}
                    @foreach ($jabatanTembusan ?? [] as $jabatan)
                        <option value="{{ $jabatan }}"
                            {{ in_array($jabatan, old('tembusan', [])) ? 'selected' : '' }}>
                            {{ $jabatan }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Pilih jabatan yang akan ditembuskan (Ctrl/Cmd + Klik untuk memilih lebih dari satu).</p>
                @error('tembusan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>


            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('sp.index') }}" class="inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-700 shadow transition-colors duration-200 hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center rounded-lg bg-green-500 px-6 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-green-600">
                    Kirim Surat Peringatan
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk Tembusan
        $('#tembusan').select2({
            placeholder: 'Pilih Tembusan (atau biarkan kosong)',
            allowClear: true,
            width: '100%'
        });

        // Inisialisasi Select2 untuk Pencarian Karyawan (Sudah benar)
        $('#nip_user').select2({
            placeholder: 'Cari berdasarkan NIP atau Nama Karyawan',
            allowClear: true,
            ajax: {
                url: '{{ route("sp.cari-karyawan") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { term: params.term };
                },
                processResults: function(data) {
                    return data;
                },
                cache: true
            },
            minimumInputLength: 1,
            width: '100%'
        });

        // Inisialisasi Datepicker (Sudah benar, asumsikan partials.datepicker ada)
        flatpickr("#tgl_sp_terbit", { dateFormat: "Y-m-d" });
        flatpickr("#tgl_mulai", { dateFormat: "Y-m-d" });
        flatpickr("#tgl_selesai", { dateFormat: "Y-m-d" });
    });
</script>
@endpush
@endsection

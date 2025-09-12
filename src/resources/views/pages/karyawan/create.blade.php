@extends('layouts.dashboard')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="p-6 mx-auto max-w-screen-lg">
    <h1 class="text-2xl font-bold mb-6">Tambah Karyawan</h1>

    {{-- Blok untuk menampilkan error validasi --}}
    @if ($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
            <span class="font-medium">Oops! Terjadi kesalahan:</span>
            <ul class="mt-1.5 ml-4 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ======================================================= --}}
    {{-- ▼▼▼ PENTING: Tambahkan enctype untuk upload file ▼▼▼ --}}
    {{-- ======================================================= --}}
    <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

                {{-- Foto Karyawan (Bisa menampilkan foto lama & preview foto baru) --}}
        <div class="md:col-span-2" 
            x-data="{photoName: null, photoPreview: null}">
            
            <label for="foto" class="mb-2 block text-sm font-medium">Foto Karyawan</label>

            <div x-show="photoPreview" class="mt-2 mb-4">
                <span class="block w-32 h-32 rounded-full"
                    :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                </span>
            </div>

            <input type="file" name="foto" id="foto" class="block w-full text-sm text-gray-900 border border-stroke rounded-lg cursor-pointer bg-transparent focus:outline-none file:mr-4 file:py-3 file:px-5 file:rounded-l-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                @change="
                    photoName = $event.target.files[0].name;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        photoPreview = e.target.result;
                    };
                    reader.readAsDataURL($event.target.files[0]);
                ">
            <p class="mt-1 text-xs text-gray-500">PNG, JPG atau WEBP (MAX. 2MB).</p>
            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah foto.</p>
        </div>


        {{-- Nama Lengkap --}}
        <div>
            <label for="nama_lengkap" class="mb-2 block text-sm font-medium">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap') }}" required>
        </div>

        {{-- NIP --}}
        <div>
            <label for="nip" class="mb-2 block text-sm font-medium">NIP</label>
            <input type="text" name="nip" id="nip"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="Nomor Induk Pegawai" value="{{ old('nip') }}">
        </div>

        {{-- NIK --}}
        <div>
            <label for="nik" class="mb-2 block text-sm font-medium">NIK</label>
            <input type="text" name="nik" id="nik"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="Nomor Induk Kependudukan" value="{{ old('nik') }}" required>
        </div>

        {{-- No Telp --}}
        <div>
            <label for="no_telp" class="mb-2 block text-sm font-medium">No. Telepon</label>
            <input type="text" name="no_telp" id="no_telp"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="+62 xxx xxxx xxxx" value="{{ old('no_telp') }}" required>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="mb-2 block text-sm font-medium">Email</label>
            <input type="email" name="email" id="email"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="info@example.com" value="{{ old('email') }}" required>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="mb-2 block text-sm font-medium">Password</label>
            <input type="password" name="password" id="password"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="••••••••" required>
        </div>

        {{-- Jenis Kelamin --}}
        <div>
            <label class="mb-2 block text-sm font-medium">Jenis Kelamin</label>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <input type="radio" name="jenis_kelamin" value="1" class="text-primary focus:ring-primary" {{ old('jenis_kelamin') == '1' ? 'checked' : '' }} required>
                    Laki-laki
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="jenis_kelamin" value="0" class="text-primary focus:ring-primary" {{ old('jenis_kelamin') == '0' ? 'checked' : '' }}>
                    Perempuan
                </label>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="md:col-span-2">
            <label for="alamat" class="mb-2 block text-sm font-medium">Alamat</label>
            <textarea name="alamat" id="alamat" rows="3"
                      class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                      placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
        </div>

        {{-- Tgl Lahir --}}
        <div>
            <label for="tgl_lahir" class="mb-2 block text-sm font-medium">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" id="tgl_lahir"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   value="{{ old('tgl_lahir') }}" required>
        </div>

        {{-- Tempat Lahir --}}
        <div>
            <label for="tempat_lahir" class="mb-2 block text-sm font-medium">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="Kota/Kabupaten" value="{{ old('tempat_lahir') }}" required>
        </div>

        {{-- Agama --}}
        <div>
            <label for="agama" class="mb-2 block text-sm font-medium">Agama</label>
            <input type="text" name="agama" id="agama"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="Agama" value="{{ old('agama') }}" required>
        </div>

        {{-- Status Perkawinan --}}
        <div>
            <label for="status_perkawinan" class="mb-2 block text-sm font-medium">Status Perkawinan</label>
            <input type="text" name="status_perkawinan" id="status_perkawinan"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="Lajang/Menikah" value="{{ old('status_perkawinan') }}" required>
        </div>

        {{-- Area Bekerja --}}
        <div>
            <label for="area_bekerja" class="mb-2 block text-sm font-medium">Area Bekerja</label>
            <input type="text" name="area_bekerja" id="area_bekerja"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="Area kerja" value="{{ old('area_bekerja') }}" required>
        </div>

        {{-- Status Aktif --}}
        <div>
            <label class="mb-2 block text-sm font-medium">Status Aktif</label>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <input type="radio" name="status_aktif" value="1" class="text-primary focus:ring-primary" {{ old('status_aktif', '1') == '1' ? 'checked' : '' }} required>
                    Aktif
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="status_aktif" value="0" class="text-primary focus:ring-primary" {{ old('status_aktif') == '0' ? 'checked' : '' }}>
                    Nonaktif
                </label>
            </div>
        </div>

        {{-- NPK Baru --}}
        <div>
            <label for="npk_baru" class="mb-2 block text-sm font-medium">NPK Baru</label>
            <input type="text" name="npk_baru" id="npk_baru"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   value="{{ old('npk_baru') }}" required>
        </div>

        {{-- NPWP --}}
        <div>
            <label for="npwp" class="mb-2 block text-sm font-medium">NPWP</label>
            <input type="text" name="npwp" id="npwp"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   value="{{ old('npwp') }}" required>
        </div>

        {{-- Join Date --}}
        <div>
            <label for="join_date" class="mb-2 block text-sm font-medium">Tanggal Bergabung</label>
            <input type="date" name="join_date" id="join_date"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   value="{{ old('join_date') }}" required>
        </div>

        {{-- Jatah Cuti --}}
        <div>
            <label for="jatah_cuti" class="mb-2 block text-sm font-medium">Jatah Cuti</label>
            <input type="number" name="jatah_cuti" id="jatah_cuti"
                   class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                   value="{{ old('jatah_cuti', 12) }}" required>
        </div>


        {{-- Tombol --}}
        <div class="md:col-span-2 flex justify-end gap-4 mt-6">
            <a href="{{ route('karyawan.index') }}" class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
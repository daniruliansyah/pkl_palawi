@extends('layouts.dashboard')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="p-6 mx-auto max-w-screen-lg">
    <h1 class="text-2xl font-bold mb-6">Tambah Jabatan</h1>

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

    {{-- Form utama untuk update PI --}}
    <form action="{{ route('karyawan.updatepi', $karyawan->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @csrf
    @method('PUT')

    {{-- Section Personal Information --}}
    <div class="md:col-span-2">
        <div class="mt-7">
            <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                Personal Information
            </h5>
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                {{-- Input Nama Lengkap --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ $karyawan->nama_lengkap ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>

                {{-- Input NIK --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">NIK</label>
                    <input type="text" name="nik" value="{{ $karyawan->nik ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>

                {{-- Input Tanggal Lahir --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" value="{{ $karyawan->tgl_lahir ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>
                
                {{-- Input Tempat Lahir --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ $karyawan->tempat_lahir ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>

                {{-- Input Jenis Kelamin --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option value="">Pilih</option>
                        <option value="0" {{ ($karyawan->jenis_kelamin === 0) ? 'selected' : '' }}>Laki-laki</option>
                        <option value="1" {{ ($karyawan->jenis_kelamin === 1) ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                
                {{-- Input Agama --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Agama</label>
                    <input type="text" name="agama" value="{{ $karyawan->agama ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>

                {{-- Input Status Perkawinan --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status Perkawinan</label>
                    <input type="text" name="status_perkawinan" value="{{ $karyawan->status_perkawinan ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>
                
                {{-- Input Email --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                    <input type="email" name="email" value="{{ $karyawan->email ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>
                
                {{-- Input No. Telepon --}}
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Telepon</label>
                    <input type="tel" name="no_telp" value="{{ $karyawan->no_telp ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Simpan --}}
    <div class="md:col-span-2 flex justify-end gap-4 mt-6">
        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
            Simpan
        </button>
    </div>
</form>
</div>

@endsection
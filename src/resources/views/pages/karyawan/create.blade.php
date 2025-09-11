@extends('layouts.dashboard')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="p-6 mx-auto max-w-screen-lg">
    <h1 class="text-2xl font-bold mb-6">Tambah Karyawan</h1>

    <form action="{{ route('karyawan.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="nama_lengkap" class="mb-2 block text-sm font-medium">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="Masukkan nama lengkap" required>
        </div>

        {{-- NIP --}}
        <div>
            <label for="nip" class="mb-2 block text-sm font-medium">NIP</label>
            <input type="text" name="nip" id="nip"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="Nomor Induk Pegawai">
        </div>

        {{-- NIK --}}
        <div>
            <label for="nik" class="mb-2 block text-sm font-medium">NIK</label>
            <input type="text" name="nik" id="nik"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="Nomor Induk Kependudukan" required>
        </div>

        {{-- No Telp --}}
        <div>
            <label for="no_telp" class="mb-2 block text-sm font-medium">No. Telepon</label>
            <input type="text" name="no_telp" id="no_telp"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="+62 xxx xxxx xxxx" required>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="mb-2 block text-sm font-medium">Email</label>
            <input type="email" name="email" id="email"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="info@gmail.com" required>
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
                    <input type="radio" name="jenis_kelamin" value="1" class="text-primary focus:ring-primary" required>
                    Laki-laki
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="jenis_kelamin" value="0" class="text-primary focus:ring-primary">
                    Perempuan
                </label>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="md:col-span-2">
            <label for="alamat" class="mb-2 block text-sm font-medium">Alamat</label>
            <textarea name="alamat" id="alamat" rows="3"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="Masukkan alamat lengkap" required></textarea>
        </div>

        {{-- Tgl Lahir --}}
        <div>
            <label for="tgl_lahir" class="mb-2 block text-sm font-medium">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" id="tgl_lahir"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                required>
        </div>

        {{-- Tempat Lahir --}}
        <div>
            <label for="tempat_lahir" class="mb-2 block text-sm font-medium">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="Kota/Kabupaten" required>
        </div>

        {{-- Agama --}}
        <div>
            <label for="agama" class="mb-2 block text-sm font-medium">Agama</label>
            <input type="text" name="agama" id="agama"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="Agama" required>
        </div>

        {{-- Status Perkawinan --}}
        <div>
            <label for="status_perkawinan" class="mb-2 block text-sm font-medium">Status Perkawinan</label>
            <input type="text" name="status_perkawinan" id="status_perkawinan"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="Lajang/Menikah" required>
        </div>

        {{-- Area Bekerja --}}
        <div>
            <label for="area_bekerja" class="mb-2 block text-sm font-medium">Area Bekerja</label>
            <input type="text" name="area_bekerja" id="area_bekerja"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                placeholder="Area kerja" required>
        </div>

        {{-- Status Aktif --}}
        <div>
            <label class="mb-2 block text-sm font-medium">Status Aktif</label>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <input type="radio" name="status_aktif" value="1" class="text-primary focus:ring-primary" required>
                    Aktif
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="status_aktif" value="0" class="text-primary focus:ring-primary">
                    Nonaktif
                </label>
            </div>
        </div>

        {{-- NPK Baru --}}
        <div>
            <label for="npk_baru" class="mb-2 block text-sm font-medium">NPK Baru</label>
            <input type="text" name="npk_baru" id="npk_baru"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                required>
        </div>

        {{-- NPWP --}}
        <div>
            <label for="npwp" class="mb-2 block text-sm font-medium">NPWP</label>
            <input type="text" name="npwp" id="npwp"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                required>
        </div>

        {{-- Join Date --}}
        <div>
            <label for="join_date" class="mb-2 block text-sm font-medium">Tanggal Bergabung</label>
            <input type="date" name="join_date" id="join_date"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                required>
        </div>

        {{-- Jatah Cuti --}}
        <div>
            <label for="jatah_cuti" class="mb-2 block text-sm font-medium">Jatah Cuti</label>
            <input type="number" name="jatah_cuti" id="jatah_cuti"
                class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                value="12" required>
        </div>

        {{-- Tombol --}}
        <div class="md:col-span-2 flex justify-end gap-4 mt-6">
            <a href="{{ route('karyawan.index') }}" class="rounded-lg border border-stroke px-6 py-2 text-sm">Batal</a>
          <button type="submit" class="rounded-lg bg-blue-500 px-6 py-2 text-sm font-medium text-white hover:bg-blue-600">
    Simpan
</button>

        </div>
    </form>
</div>
@endsection

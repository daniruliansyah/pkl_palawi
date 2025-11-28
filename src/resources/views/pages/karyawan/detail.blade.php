@extends('layouts.dashboard')

@section('title', 'Karyawan - ' . $karyawan->nama_lengkap)

@section('content')

<div class="p-4 mx-auto max-w-screen-2xl md:p-6 2xl:p-10">
    {{-- Wrapper Utama --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        
        {{-- Judul Halaman --}}
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">
            Profile Karyawan
        </h3>

        {{-- SECTION 1: HEADER PROFILE & TOMBOL AKSI --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                {{-- Foto & Nama --}}
                <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
                    <div class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
                        <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="foto" class="w-full h-full object-cover">
                    </div>
                    <div class="order-3 xl:order-2">
                        <h4 class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left">
                            {{ $karyawan->nama_lengkap }}
                        </h4>
                        <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $karyawan->jabatanTerbaru?->jabatan?->nama_jabatan ?? '-' }}
                            </p>
                            <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $karyawan->nip }}
                            </p>
                        </div>
                    </div>

                    {{-- Area Tombol Aksi --}}
                    <div class="flex items-center order-2 gap-3 grow xl:order-3 xl:justify-end">
                        {{-- Tombol Unduh Profil PDF --}}
                        <a href="{{ route('karyawan.cetakDetail', $karyawan->id) }}"
                           class="flex h-11 items-center justify-center gap-2.5 rounded-lg border border-yellow-200 bg-yellow-100 px-4 text-sm font-medium text-yellow-800 shadow-theme-xs hover:bg-yellow-200 hover:text-yellow-900 dark:border-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200 dark:hover:bg-yellow-900/50 dark:hover:text-yellow-100">
                            <svg class="fill-current text-yellow-800 dark:text-yellow-200" width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                            </svg>
                            <span>Unduh</span>
                        </a>

                        {{-- Tombol Riwayat Gaji --}}
                        <a href="{{ route('gaji.indexForKaryawan', $karyawan->id) }}"
                           class="flex h-11 items-center justify-center gap-2.5 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 2H5C3.89543 2 3 2.89543 3 4V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V4C21 2.89543 20.1046 2 19 2ZM7 8H17V10H7V8ZM7 12H17V14H7V12ZM7 16H13V18H7V16Z"/>
                            </svg>
                            <span>Riwayat Gaji</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: PERSONAL INFORMATION --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Personal Information
                        </h4>
                        <a href="{{ route('karyawan.editpi', $karyawan->id) }}" class="flex items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"></path>
                            </svg>
                            Edit
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Nama Lengkap</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->nama_lengkap }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">NIK</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->nik }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Lahir</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->tgl_lahir }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tempat Lahir</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->tempat_lahir }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Jenis Kelamin</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan' }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Agama</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->agama }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Email</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->email }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Nomor Telepon</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->no_telp }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Alamat</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->alamat }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Status Pernikahan</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->status_perkawinan }}</p></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: KEPEGAWAIAN --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Kepegawaian</h4>
                        <a href="{{ route('karyawan.editkep', $karyawan->id) }}" class="flex items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"></path>
                            </svg>
                            Edit
                        </a>
                    </div>
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">NPK</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->npk_baru }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">NPWP</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->npwp }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Jabatan</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->jabatanTerbaru?->jabatan?->nama_jabatan ?? '-' }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Area Kerja</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->jabatanTerbaru?->area_bekerja ?? '-' }}</p></div>
                        <div><p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Pangkat</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $karyawan->jabatanTerbaru?->jenjang ?? '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: RIWAYAT JABATAN (SUDAH DIPERBAIKI SESUAI FORMAT LAIN) --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-6">
                <div><h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat Jabatan</h4></div>
                <div>
                    <a href="{{ route('karyawan.tambahjabatan', $karyawan->id) }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-green-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 lg:inline-flex lg:w-auto">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.75 9.75H9.75V15.75H8.25V9.75H2.25V8.25H8.25V2.25H9.75V8.25H15.75V9.75Z" fill="currentColor"/></svg>
                        Tambah
                    </a>
                </div>
            </div>
            
            <div class="space-y-6">
                @forelse ($karyawan->riwayatJabatans as $riwayat)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0 last:pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                            <div class="mb-4 sm:mb-0">
                                {{-- Nama Jabatan + Link --}}
                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $riwayat->jabatan->nama_jabatan }}</p>
                                    @if ($riwayat->link_berkas)
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                                        <a href="{{ $riwayat->link_berkas }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 text-xs font-normal text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/></svg>
                                            Lihat Berkas
                                        </a>
                                    @endif
                                </div>
                                {{-- Area --}}
                                <p class="text-xs text-gray-500 dark:text-gray-400">Area: {{ $riwayat->area_bekerja }}</p>
                                {{-- Tanggal --}}
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($riwayat->tgl_mulai)->format('d/m/Y') }} -
                                    {{ $riwayat->tgl_selesai ? \Carbon\Carbon::parse($riwayat->tgl_selesai)->format('d/m/Y') : 'Sekarang' }}
                                </p>
                            </div>
                            
                            {{-- Tombol Aksi --}}
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                {{-- Edit --}}
                                <a href="{{ route('riwayat.edit', ['karyawan' => $karyawan->id, 'riwayat' => $riwayat->id]) }}" class="flex items-center justify-center gap-1 rounded-full border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                    <svg class="fill-current" width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"></path></svg>
                                    Edit
                                </a>
                                {{-- Delete --}}
                                <form action="{{ route('riwayat.destroy', ['karyawan' => $karyawan->id, 'riwayat' => $riwayat->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="flex items-center justify-center gap-1 rounded-full border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-100 dark:border-red-700 dark:bg-red-900/[0.3] dark:text-red-400 dark:hover:bg-red-900/[0.5]">
                                        <svg class="fill-current" width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3V2H6V3H2V4H3V13C3 13.5523 3.44772 14 4 14H12C12.5523 14 13 13.5523 13 13V4H14V3H10ZM12 4H4V13H12V4ZM7 6H8V11H7V6ZM10 6H9V11H10V6Z" fill="currentColor"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat jabatan.</p>
                @endforelse
            </div>
        </div>

        {{-- SECTION 5: RIWAYAT PENDIDIKAN --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-6">
                <div><h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat Pendidikan</h4></div>
                <div>
                    <a href="{{ route('karyawan.pendidikan.create', $karyawan->id) }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-green-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 lg:inline-flex lg:w-auto">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.75 9.75H9.75V15.75H8.25V9.75H2.25V8.25H8.25V2.25H9.75V8.25H15.75V9.75Z" fill="currentColor"/></svg>
                        Tambah
                    </a>
                </div>
            </div>
            <div class="space-y-6">
                @forelse ($karyawan->riwayatPendidikan->sortByDesc('tahun_lulus') as $pendidikan)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0 last:pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                            <div class="mb-4 sm:mb-0">
                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $pendidikan->jenjang }} - {{ $pendidikan->nama_institusi }}</p>
                                    @if ($pendidikan->link_berkas)
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                                        <a href="{{ $pendidikan->link_berkas }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 text-xs font-normal text-blue-600 hover:text-blue-800 hover:underline" title="Klik untuk melihat berkas">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/></svg>
                                            Lihat Berkas
                                        </a>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Jurusan: {{ $pendidikan->jurusan ?: '-' }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lulus Tahun {{ $pendidikan->tahun_lulus }} @if($pendidikan->ipk) - IPK: {{ number_format($pendidikan->ipk, 2) }} @endif</p>
                            </div>
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                <a href="{{ route('pendidikan.edit', $pendidikan->id) }}" class="flex items-center justify-center gap-1 rounded-full border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                    <svg class="fill-current" width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"></path></svg>
                                    Edit
                                </a>
                                <form action="{{ route('pendidikan.destroy', $pendidikan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat pendidikan ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="flex items-center justify-center gap-1 rounded-full border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-100 dark:border-red-700 dark:bg-red-900/[0.3] dark:text-red-400 dark:hover:bg-red-900/[0.5]">
                                        <svg class="fill-current" width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3V2H6V3H2V4H3V13C3 13.5523 3.44772 14 4 14H12C12.5523 14 13 13.5523 13 13V4H14V3H10ZM12 4H4V13H12V4ZM7 6H8V11H7V6ZM10 6H9V11H10V6Z" fill="currentColor"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data riwayat pendidikan.</p>
                @endforelse
            </div>
        </div>

        {{-- SECTION 6: RIWAYAT KPO (Baru) --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-6">
                <div><h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat KPO</h4></div>
                <div>
                    <a href="{{ route('karyawan.kpo.create', $karyawan->id) }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-green-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 lg:inline-flex lg:w-auto">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.75 9.75H9.75V15.75H8.25V9.75H2.25V8.25H8.25V2.25H9.75V8.25H15.75V9.75Z" fill="currentColor"/></svg>
                        Tambah
                    </a>
                </div>
            </div>
            <div class="space-y-6">
                @forelse ($karyawan->riwayatKpo->sortByDesc('tgl_jabat') as $kpo)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0 last:pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                            <div class="mb-4 sm:mb-0">
                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $kpo->nama_jabatan }}</p>
                                    @if ($kpo->link_berkas)
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                                        <a href="{{ $kpo->link_berkas }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 text-xs font-normal text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/></svg>
                                            Lihat Berkas
                                        </a>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Organisasi: {{ $kpo->nama_organisasi }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tanggal Menjabat: {{ \Carbon\Carbon::parse($kpo->tgl_jabat)->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                <a href="{{ route('kpo.edit', $kpo->id) }}" class="flex items-center justify-center gap-1 rounded-full border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"><svg class="fill-current" width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"></path></svg> Edit</a>
                                <form action="{{ route('kpo.destroy', $kpo->id) }}" method="POST" onsubmit="return confirm('Hapus data?');">@csrf @method('DELETE')<button type="submit" class="flex items-center justify-center gap-1 rounded-full border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-100 dark:border-red-700 dark:bg-red-900/[0.3] dark:text-red-400 dark:hover:bg-red-900/[0.5]"><svg class="fill-current" width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3V2H6V3H2V4H3V13C3 13.5523 3.44772 14 4 14H12C12.5523 14 13 13.5523 13 13V4H14V3H10ZM12 4H4V13H12V4ZM7 6H8V11H7V6ZM10 6H9V11H10V6Z" fill="currentColor"/></svg> Hapus</button></form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data riwayat KPO.</p>
                @endforelse
            </div>
        </div>

        {{-- SECTION 7: RIWAYAT LATIHAN JABATAN (Baru) --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-6">
                <div><h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat Latihan Jabatan</h4></div>
                <div>
                    <a href="{{ route('karyawan.latihan-jabatan.create', $karyawan->id) }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-green-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 lg:inline-flex lg:w-auto">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.75 9.75H9.75V15.75H8.25V9.75H2.25V8.25H8.25V2.25H9.75V8.25H15.75V9.75Z" fill="currentColor"/></svg>
                        Tambah
                    </a>
                </div>
            </div>
            <div class="space-y-6">
                @forelse ($karyawan->riwayatLatihanJabatan->sortByDesc('tgl_mulai') as $latihan)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0 last:pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                            <div class="mb-4 sm:mb-0">
                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $latihan->nama_latihan }}</p>
                                    @if ($latihan->link_berkas)
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                                        <a href="{{ $latihan->link_berkas }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 text-xs font-normal text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/></svg>
                                            Lihat Berkas
                                        </a>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($latihan->tgl_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($latihan->tgl_selesai)->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                <a href="{{ route('latihan-jabatan.edit', $latihan->id) }}" class="flex items-center justify-center gap-1 rounded-full border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"><svg class="fill-current" width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"></path></svg> Edit</a>
                                <form action="{{ route('latihan-jabatan.destroy', $latihan->id) }}" method="POST" onsubmit="return confirm('Hapus data?');">@csrf @method('DELETE')<button type="submit" class="flex items-center justify-center gap-1 rounded-full border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-100 dark:border-red-700 dark:bg-red-900/[0.3] dark:text-red-400 dark:hover:bg-red-900/[0.5]"><svg class="fill-current" width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3V2H6V3H2V4H3V13C3 13.5523 3.44772 14 4 14H12C12.5523 14 13 13.5523 13 13V4H14V3H10ZM12 4H4V13H12V4ZM7 6H8V11H7V6ZM10 6H9V11H10V6Z" fill="currentColor"/></svg> Hapus</button></form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data latihan jabatan.</p>
                @endforelse
            </div>
        </div>

        {{-- SECTION 8: RIWAYAT PANGKAT (Baru) --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-6">
                <div><h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat Pangkat Perusahaan</h4></div>
                <div>
                    <a href="{{ route('karyawan.pangkat.create', $karyawan->id) }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-green-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 lg:inline-flex lg:w-auto">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.75 9.75H9.75V15.75H8.25V9.75H2.25V8.25H8.25V2.25H9.75V8.25H15.75V9.75Z" fill="currentColor"/></svg>
                        Tambah
                    </a>
                </div>
            </div>
            <div class="space-y-6">
                @forelse ($karyawan->riwayatPangkatPerusahaan->sortByDesc('tmt_gol') as $pangkat)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0 last:pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                            <div class="mb-4 sm:mb-0">
                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Golongan/Ruang: {{ $pangkat->gol_ruang }}</p>
                                    @if ($pangkat->link_berkas)
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                                        <a href="{{ $pangkat->link_berkas }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 text-xs font-normal text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/></svg>
                                            Lihat Berkas
                                        </a>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">TMT: {{ \Carbon\Carbon::parse($pangkat->tmt_gol)->format('d/m/Y') }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">SK: {{ $pangkat->no_sk }} ({{ \Carbon\Carbon::parse($pangkat->tgl_sk)->format('d/m/Y') }})</p>
                            </div>
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                <a href="{{ route('pangkat.edit', $pangkat->id) }}" class="flex items-center justify-center gap-1 rounded-full border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"><svg class="fill-current" width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"></path></svg> Edit</a>
                                <form action="{{ route('pangkat.destroy', $pangkat->id) }}" method="POST" onsubmit="return confirm('Hapus data?');">@csrf @method('DELETE')<button type="submit" class="flex items-center justify-center gap-1 rounded-full border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-100 dark:border-red-700 dark:bg-red-900/[0.3] dark:text-red-400 dark:hover:bg-red-900/[0.5]"><svg class="fill-current" width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3V2H6V3H2V4H3V13C3 13.5523 3.44772 14 4 14H12C12.5523 14 13 13.5523 13 13V4H14V3H10ZM12 4H4V13H12V4ZM7 6H8V11H7V6ZM10 6H9V11H10V6Z" fill="currentColor"/></svg> Hapus</button></form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data riwayat pangkat.</p>
                @endforelse
            </div>
        </div>

        {{-- SECTION 9: RIWAYAT PENGHARGAAN (Baru) --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-6">
                <div><h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat Penghargaan</h4></div>
                <div>
                    <a href="{{ route('karyawan.penghargaan.create', $karyawan->id) }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-green-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 lg:inline-flex lg:w-auto">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.75 9.75H9.75V15.75H8.25V9.75H2.25V8.25H8.25V2.25H9.75V8.25H15.75V9.75Z" fill="currentColor"/></svg>
                        Tambah
                    </a>
                </div>
            </div>
            <div class="space-y-6">
                @forelse ($karyawan->riwayatPenghargaan->sortByDesc('tgl_terima') as $penghargaan)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0 last:pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                            <div class="mb-4 sm:mb-0">
                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $penghargaan->nama_penghargaan }}</p>
                                    @if ($penghargaan->link_berkas)
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                                        <a href="{{ $penghargaan->link_berkas }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 text-xs font-normal text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/></svg>
                                            Lihat Berkas
                                        </a>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tanggal Diterima: {{ \Carbon\Carbon::parse($penghargaan->tgl_terima)->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                <a href="{{ route('penghargaan.edit', $penghargaan->id) }}" class="flex items-center justify-center gap-1 rounded-full border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"><svg class="fill-current" width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="currentColor"></path></svg> Edit</a>
                                <form action="{{ route('penghargaan.destroy', $penghargaan->id) }}" method="POST" onsubmit="return confirm('Hapus data?');">@csrf @method('DELETE')<button type="submit" class="flex items-center justify-center gap-1 rounded-full border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-100 dark:border-red-700 dark:bg-red-900/[0.3] dark:text-red-400 dark:hover:bg-red-900/[0.5]"><svg class="fill-current" width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3V2H6V3H2V4H3V13C3 13.5523 3.44772 14 4 14H12C12.5523 14 13 13.5523 13 13V4H14V3H10ZM12 4H4V13H12V4ZM7 6H8V11H7V6ZM10 6H9V11H10V6Z" fill="currentColor"/></svg> Hapus</button></form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data riwayat penghargaan.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
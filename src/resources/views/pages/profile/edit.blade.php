@extends('layouts.dashboard')

@section('title', 'Edit Profil')

@section('content')
<div class="p-6 mx-auto max-w-screen-lg">
    <h1 class="text-2xl font-bold mb-6">Edit Profil Saya</h1>

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

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PATCH')

        {{-- Input Alamat --}}
        <div class="col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat</label>
            <input type="text" name="alamat" value="{{ old('alamat', auth()->user()->alamat) }}"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"/>
        </div>

        {{-- Input Nomor Telepon --}}
        <div class="col-span-2 lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Telepon</label>
            <input type="text" name="no_telp" value="{{ old('no_telp', auth()->user()->no_telp) }}"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"/>
        </div>

        {{-- Input Email --}}
        <div class="col-span-2 lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"/>
        </div>

        {{-- Input Password Baru --}}
        <div class="col-span-2 lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password Baru</label>
            <input type="password" name="password" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 dark:text-white/90"/>
        </div>

        {{-- Konfirmasi Password --}}
        <div class="col-span-2 lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 dark:text-white/90"/>
        </div>

        {{-- Upload Foto --}}
        <div class="col-span-2 lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Foto Profil</label>
            <input type="file" name="foto"
                class="dark:bg-dark-900 block w-full text-sm text-gray-800 dark:text-white/90 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-700"/>
            @if (auth()->user()->foto)
                <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto Profil" class="mt-3 w-20 h-20 rounded-full object-cover">
            @endif
        </div>

        {{-- Tombol --}}
        <div class="md:col-span-2 flex justify-end gap-4 mt-6">
            <a href="{{ url()->previous() }}" class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
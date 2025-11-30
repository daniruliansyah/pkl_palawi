@extends('layouts.dashboard') {{-- Pastikan nama layout global sudah benar --}}

@section('title', 'Dashboard Utama')

@section('content')
<div class="mx-auto max-w-screen-2xl">

    @php
        // -----------------------------------------------------
        // 1. LOGIC WAKTU & SAPAAN
        // -----------------------------------------------------
        date_default_timezone_set('Asia/Jakarta');

        $user = Auth::user();
        $hour = date('H');
        $greeting = 'Hai, ';
        $timeOfDay = '';

        if ($hour >= 5 && $hour < 12) {
            $greeting .= 'Selamat Pagi';
            $timeOfDay = 'pagi';
        } elseif ($hour >= 12 && $hour < 18) {
            $greeting .= 'Selamat Siang';
            $timeOfDay = 'siang';
        } else {
            $greeting .= 'Selamat Malam';
            $timeOfDay = 'malam';
        }

        // -----------------------------------------------------
        // 2. LOGIC MOTIVASI
        // -----------------------------------------------------
        $motivations = [
            "Semangat memulai hari dengan rencana terbaik!",
            "Selamat melanjutkan aktivitas dengan penuh semangat!",
            "Jangan lupa istirahat sejenak untuk mengisi energi!",
            "Fokus pada hal yang membawa progres hari ini!",
            "Jadikan hari ini lebih baik dari hari kemarin!",
            "Keberhasilan menanti mereka yang bekerja keras dan cerdas!",
        ];

        $motivation = $motivations[array_rand($motivations)];

        // -----------------------------------------------------
        // 3. LOGIC GAMBAR ACERAK
        // -----------------------------------------------------
        $images = [
            '1.png',
            '2.png',
            '3.png',
        ];

        $randomImageName = $images[array_rand($images)];
        $illustrationPath = asset('images/welcome/' . $randomImageName);
    @endphp

    {{-- -------------------------------------------------------------------------------- --}}
    {{-- --- STRUKTUR HTML (WELCOME CARD) DENGAN UKURAN GAMBAR OPTIMAL --- --}}
    {{-- -------------------------------------------------------------------------------- --}}
    <div class="bg-white p-6 sm:p-10 rounded-2xl shadow-xl flex flex-col md:flex-row items-start mb-8 border border-gray-200 dark:border-gray-700">

        {{-- Area Ilustrasi (Posisi Kiri - Ukuran Tengah: w-60 h-60) --}}
        {{-- Kita menggunakan w-60 h-60 untuk ukuran desktop agar terlihat lebih besar dari 48/56, tapi lebih kecil dari 80 --}}
        <div class="flex-shrink-0 w-48 h-48 sm:w-60 sm:h-60 mb-4 md:mb-0 md:mr-16">
            <img
                src="{{ $illustrationPath }}"
                alt="Ilustrasi Selamat Datang"
                class="w-full h-full object-contain"
            >
        </div>

        {{-- Teks Sapaan (Posisi Kanan - Jarak Dipertahankan Lebar) --}}
        <div class="flex-grow pt-4 md:ml-12">
            <h1 class="text-3xl font-bold text-green-800 dark:text-green-800 mb-2">
                {{ $greeting }}, {{ $user->nama_lengkap ?? 'User' }}
            </h1>

            <p class="text-3xl sm:text-4xl text-gray-800 dark:text-gray-200 font-semibold">
                {{ $motivation }}
            </p>
        </div>
    </div>

    {{-- Hapus bagian Ringkasan Aktivitas yang tidak digunakan --}}
    {{-- <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Aktivitas dan Informasi</h2> --}}

</div>
@endsection

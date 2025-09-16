@extends('layouts.dashboard')

@section('title', 'Daftar Karyawan')

@section('content')
<div class="p-6 max-w-screen-lg mx-auto">
    <div class="mb-6 p-6 bg-gray-100" style="background-color: transparent">
        
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Karyawan</h1>
            
            <a href="{{ route('karyawan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition duration-150 ease-in-out">
                + Tambah Karyawan
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($karyawan as $item)
                @include('partials.profile.card-karyawan', ['karyawan' => $item])
            @endforeach
        </div>
    </div>
</div>
@endsection
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

        <div class="mb-4">
            <form action="{{ route('karyawan.index') }}" method="GET" class="flex items-center space-x-2">
                <input 
                    type="search" 
                    name="search" 
                    placeholder="Cari nama karyawan..." 
                    class="flex-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="{{ request('search') }}"
                >
                <button type="submit" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($karyawan as $item)
                @include('partials.profile.card-karyawan', ['karyawan' => $item])
            @endforeach
        </div>
    </div>
</div>
@endsection
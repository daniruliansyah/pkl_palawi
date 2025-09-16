@extends('layouts.dashboard')

@section('title', 'Daftar Karyawan')

@section('content')
<div class="p-6 max-w-screen-lg mx-auto">
    <!-- Container besar dengan judul -->
    <div class="mb-6 p-6 bg-gray-100 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Karyawan</h1>


    <!-- Grid / List Karyawan (2 card per baris) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        @foreach ($karyawan as $item)
            @include('partials.profile.card-karyawan', ['karyawan' => $item])
        @endforeach
    </div>
    </div>


</div>
@endsection

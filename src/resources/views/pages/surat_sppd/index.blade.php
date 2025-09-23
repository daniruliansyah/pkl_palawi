@extends('layouts.dashboard')

@section('title', 'Daftar SPPD Karyawan')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="fixed top-4 right-4 z-50 rounded-lg bg-red-500 text-white p-4 shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="fixed top-4 right-4 z-50 rounded-lg bg-orange-500 text-white p-4 shadow-lg">
            {{ session('warning') }}
        </div>
    @endif

    {{-- Include partial table --}}
    @include('partials.table.table-sppd-acceptance', ['sppds' => $sppds])
</div>
@endsection

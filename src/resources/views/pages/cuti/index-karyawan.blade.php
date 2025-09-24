@extends('layouts.dashboard')

@section('title', 'Daftar Pengajuan Cuti Saya')

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

    {{-- Arahkan ke partial table yang benar dan kirim variabel '$cutis' --}}
    @include('partials.table.table-cuti-karyawan', ['cutis' => $cutis])
</div>
@endsection
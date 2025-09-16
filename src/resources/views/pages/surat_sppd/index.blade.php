@extends('layouts.dashboard')

@section('title', 'Daftar SPPD')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Include partial table --}}
    @include('partials.table.table-sppd', ['sppds' => $sppds])
</div>
@endsection

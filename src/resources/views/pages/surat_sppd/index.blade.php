@extends('layouts.dashboard')
@section('title', 'Daftar SPPD')
@section('content')
<div class="container">
    <h2>Daftar Pengajuan SPPD</h2>

    @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('sppd.create') }}">+ Ajukan SPPD Baru</a>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Nama Pengaju</th>
            <th>Tanggal</th>
            <th>Lokasi</th>
            <th>Status</th>
        </tr>
        @foreach ($sppds as $i => $sppd)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $sppd->user->nama_lengkap }}</td>
            <td>{{ $sppd->tgl_mulai }} - {{ $sppd->tgl_selesai }}</td>
            <td>{{ $sppd->lokasi_tujuan }}</td>
            <td>{{ $sppd->status_pengajuan }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endsection

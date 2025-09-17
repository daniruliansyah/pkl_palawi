@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Daftar Surat SPPD</h1>

    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">No</th>
                <th class="border px-4 py-2">Nama</th>
                <th class="border px-4 py-2">Tanggal</th>
                <th class="border px-4 py-2">Tujuan</th>
                <th class="border px-4 py-2">Status GM</th>
                <th class="border px-4 py-2">Status SDM</th>
                <th class="border px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sppds as $sppd)
                <tr>
                    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="border px-4 py-2">{{ $sppd->user->name ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $sppd->tgl_mulai }} - {{ $sppd->tgl_selesai }}</td>
                    <td class="border px-4 py-2">{{ $sppd->lokasi_tujuan }}</td>

                    <!-- Status GM -->
                    <td class="border px-4 py-2">
                        @php
                            $statusGm = $sppd->status_gm ?? 'menunggu';
                            $classGm = $statusGm === 'disetujui' ? 'bg-green-100 text-green-700'
                                        : ($statusGm === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600');
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $classGm }}">
                            {{ ucfirst($statusGm) }}
                        </span>
                        @if($statusGm === 'disetujui')
                            <img src="{{ asset('images/barcode_gm.png') }}" width="80" alt="TTD GM" class="mt-1">
                        @endif
                    </td>

                    <!-- Status SDM -->
                    <td class="border px-4 py-2">
                        @php
                            $statusSdm = $sppd->status_sdm ?? 'menunggu';
                            $classSdm = $statusSdm === 'disetujui' ? 'bg-green-100 text-green-700'
                                        : ($statusSdm === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600');
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $classSdm }}">
                            {{ ucfirst($statusSdm) }}
                        </span>
                        @if($statusSdm === 'disetujui')
                            <img src="{{ asset('images/barcode_sdm.png') }}" width="80" alt="TTD SDM" class="mt-1">
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="border px-4 py-2">
                        <!-- Form untuk GM -->
                        <form action="{{ route('sppd.updateStatus', $sppd->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="role" value="gm">
                            <button name="status" value="disetujui" class="bg-green-500 text-white px-2 py-1 rounded">Approve GM</button>
                            <button name="status" value="ditolak" class="bg-red-500 text-white px-2 py-1 rounded">Reject GM</button>
                        </form>

                        <!-- Form untuk SDM -->
                        <form action="{{ route('sppd.updateStatus', $sppd->id) }}" method="POST" class="inline-block ml-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="role" value="sdm">
                            <button name="status" value="disetujui" class="bg-green-500 text-white px-2 py-1 rounded">Approve SDM</button>
                            <button name="status" value="ditolak" class="bg-red-500 text-white px-2 py-1 rounded">Reject SDM</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

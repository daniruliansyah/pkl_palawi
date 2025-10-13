{{-- Ganti dengan layout utama Anda --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Form Pertanggungjawaban SPPD</h1>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <p class="mb-2"><strong>Nomor Surat:</strong> {{ $sppd->no_surat ?? 'Belum terbit' }}</p>
        <p class="mb-4"><strong>Perihal:</strong> {{ $sppd->keterangan_sppd }}</p>

        <form action="{{ route('pertanggungjawaban.store', $sppd->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Daftar Rincian Biaya --}}
                <div>
                    <label for="uang_harian" class="block text-sm font-medium text-gray-700">Uang Harian (Rp)</label>
                    <input type="number" name="uang_harian" id="uang_harian" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                </div>
                <div>
                    <label for="transportasi_lokal" class="block text-sm font-medium text-gray-700">Transportasi Lokal (Rp)</label>
                    <input type="number" name="transportasi_lokal" id="transportasi_lokal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                </div>
                <div>
                    <label for="uang_makan" class="block text-sm font-medium text-gray-700">Uang Makan (Rp)</label>
                    <input type="number" name="uang_makan" id="uang_makan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                </div>
                <div>
                    <label for="akomodasi_mandiri" class="block text-sm font-medium text-gray-700">Akomodasi Mandiri (Rp)</label>
                    <input type="number" name="akomodasi_mandiri" id="akomodasi_mandiri" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                </div>
                <div>
                    <label for="akomodasi_tt" class="block text-sm font-medium text-gray-700">Akomodasi T&T (Rp)</label>
                    <input type="number" name="akomodasi_tt" id="akomodasi_tt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                </div>
                <div>
                    <label for="transportasi_lain" class="block text-sm font-medium text-gray-700">Transportasi Lain (Rp)</label>
                    <input type="number" name="transportasi_lain" id="transportasi_lain" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan Tambahan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('sppd.index') }}" class="py-2 px-4 border rounded-md shadow-sm text-sm font-medium mr-2">Batal</a>
                <button type="submit" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Simpan dan Buat Kuitansi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
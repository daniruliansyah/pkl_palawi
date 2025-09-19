@extends('layouts.dashboard')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="p-6 mx-auto max-w-screen-lg">
    <h1 class="text-2xl font-bold mb-6">Tambah Jabatan</h1>

    @if ($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">
            <span class="font-medium">Oops! Terjadi kesalahan:</span>
            <ul class="mt-1.5 ml-4 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('riwayat.update', ['karyawan' => $karyawan->id, 'riwayat' => $riwayat->id]) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        {{-- Gunakan x-data dengan data yang sudah ada --}}
        <div x-data="{
            riwayatJabatan: [
                {
                    jabatan_id: '{{ old('jabatan_id', $riwayat->id_jabatan) }}',
                    tgl_mulai: '{{ old('tgl_mulai', $riwayat->tgl_mulai) }}',
                    tgl_selesai: '{{ old('tgl_selesai', $riwayat->tgl_selesai) }}'
                }
            ]
        }">

            {{-- Container untuk riwayat jabatan (hanya satu item) --}}
            <div id="jabatan-container">
                <template x-for="(jabatan, index) in riwayatJabatan" :key="index">
                    <div class="jabatan-item mb-4 border p-4 rounded-lg">
                        {{-- Tombol Hapus (tidak diperlukan untuk edit satu item) --}}
                        <div class="flex justify-end">
                            <button type="button" 
                                    @click="riwayatJabatan.splice(index, 1)" 
                                    class="bg-red-500 text-white px-3 py-1 text-xs rounded-lg hover:bg-red-600"
                                    x-show="riwayatJabatan.length > 1">
                                Hapus
                            </button>
                        </div>
                        
                        {{-- Form input jabatan --}}
                        <div>
                            <label for="jabatan_id" class="mb-2 block text-sm font-medium">Jabatan</label>
                            <select x-model="jabatan.jabatan_id" name="jabatan_id" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary">
                                <option value="">Pilih Jabatan</option>
                                @foreach($jabatans as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tgl_mulai" class="mb-2 block text-sm font-medium">Tanggal Mulai</label>
                            <input type="date" x-model="jabatan.tgl_mulai" name="tgl_mulai" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary">
                        </div>
                        <div>
                            <label for="tgl_selesai" class="mb-2 block text-sm font-medium">Tanggal Selesai</label>
                            <input type="date" x-model="jabatan.tgl_selesai" name="tgl_selesai" class="w-full rounded-lg border border-stroke bg-transparent py-3 px-5 text-sm outline-none focus:border-primary">
                        </div>
                    </div>
                </template>
            </div>
            
            {{-- Tombol untuk menambah form baru (tidak diperlukan untuk edit satu item) --}}
            <button type="button" @click="riwayatJabatan.push({})" class="bg-green-500 text-white px-4 py-2 rounded-lg mt-4" style="display: none;">Tambah Jabatan Lain</button>
        </div>

        {{-- Tombol Simpan --}}
        <div class="md:col-span-2 flex justify-end gap-4 mt-6">
            <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Simpan Perubahan
            </button>   
        </div>
    </form>
    {{-- Form End --}}
</div>
@endsection


saya mau tampilan edit riwayat jabatan yang dipilih sama seperti ini, namun untuk value (otomatis terisi) dari data yang ada sebelum di edit.
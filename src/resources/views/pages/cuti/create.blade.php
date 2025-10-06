@extends('layouts.dashboard')
@section('title', 'Tambah Cuti')

@section('content')

    {{-- HILANGKAN SEMUA WRAPPER GANDA. KITA FOKUS PADA FORMULIR DENGAN MAX-WIDTH DI SINI. --}}

    <div class="flex items-center justify-center">
        {{-- Gunakan max-w-2xl untuk membatasi lebar form agar terpusat rapi di dalam padding <main> --}}
        <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">Formulir Pengajuan Cuti</h2>

            {{-- Kotak informasi sisa cuti --}}
            <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700">
                <p><span class="font-semibold">Sisa Jatah Cuti Tahunan Anda:</span> {{ $sisaCuti ?? 'N/A' }} hari.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700" role="alert">
                    <span class="font-bold">Terjadi Kesalahan!</span>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="jenis_izin" class="block text-sm font-medium text-gray-700">Jenis Izin</label>
                    <select id="jenis_izin" name="jenis_izin" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('jenis_izin') ? 'border-red-500' : 'border-gray-300' }}" required>
                        <option value="" disabled selected>-- Pilih Jenis Izin --</option>
                        <option value="Cuti Tahunan" @if(old('jenis_izin') == 'Cuti Tahunan') selected @endif>Cuti Tahunan</option>
                        <option value="Cuti Besar" @if(old('jenis_izin') == 'Cuti Besar') selected @endif>Cuti Besar</option>
                        <option value="Cuti Sakit" @if(old('jenis_izin') == 'Cuti Sakit') selected @endif>Cuti Sakit</option>
                        <option value="Cuti Bersalin" @if(old('jenis_izin') == 'Cuti Bersalin') selected @endif>Cuti Bersalin</option>
                        <option value="Cuti Alasan Penting" @if(old('jenis_izin') == 'Cuti Alasan Penting') selected @endif>Cuti Alasan Penting</option>
                        <option value="Izin" @if(old('jenis_izin') == 'Izin') selected @endif>Izin (1-2 hari)</option>
                    </select>
                </div>

                {{-- Tampilkan dropdown ini JIKA perlu (berdasarkan Auth::user()->isKaryawanBiasa() dari kode Anda yang lama) --}}
                @if(Auth::user()->isKaryawanBiasa())
                    <div>
                        <label for="nip_user_ssdm" class="block text-sm font-medium text-gray-700">Pilih Atasan Langsung (Senior)</label>
                        <select id="nip_user_ssdm" name="nip_user_ssdm" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('nip_user_ssdm') ? 'border-red-500' : 'border-gray-300' }}" required>
                            <option value="" disabled selected>-- Pilih Atasan Anda --</option>
                            @forelse($seniors as $senior)
                                <option value="{{ $senior->nip }}" @if(old('nip_user_ssdm') == $senior->nip) selected @endif>
                                    {{ $senior->nama_lengkap }} - ({{ $senior->jabatanTerbaru->jabatan->nama_jabatan ?? 'Jabatan Tidak Diketahui' }})
                                </option>
                            @empty
                                <option value="" disabled>-- Tidak ada data atasan yang bisa dipilih. Hubungi admin. --</option>
                            @endforelse
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ old('tgl_mulai') }}" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_mulai') ? 'border-red-500' : 'border-gray-300' }}" required>
                    </div>
                    <div>
                        <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ old('tgl_selesai') }}" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_selesai') ? 'border-red-500' : 'border-gray-300' }}" required>
                    </div>
                </div>

                <div>
                    <label for="jumlah_hari" class="block text-sm font-medium text-gray-700">Jumlah Hari</label>
                    <input type="number" name="jumlah_hari" id="jumlah_hari" value="{{ old('jumlah_hari') }}" min="1" placeholder="Contoh: 3" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('jumlah_hari') ? 'border-red-500' : 'border-gray-300' }}" required>
                </div>

                {{-- Tambahan Kolom Alamat dan HP --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="alamat_saat_cuti" class="block text-sm font-medium text-gray-700">Alamat Saat Cuti</label>
                        <input type="text" name="alamat_saat_cuti" id="alamat_saat_cuti" value="{{ old('alamat_saat_cuti') }}" placeholder="Alamat selama cuti" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('alamat_saat_cuti') ? 'border-red-500' : 'border-gray-300' }}" required>
                    </div>
                    <div>
                        <label for="no_hp_saat_cuti" class="block text-sm font-medium text-gray-700">No HP Saat Cuti</label>
                        <input type="text" name="no_hp_saat_cuti" id="no_hp_saat_cuti" value="{{ old('no_hp_saat_cuti') }}" placeholder="Nomor HP aktif" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('no_hp_saat_cuti') ? 'border-red-500' : 'border-gray-300' }}" required>
                    </div>
                </div>
                {{-- Akhir Tambahan Kolom --}}

                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keperluan Cuti / Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('keterangan') ? 'border-red-500' : 'border-gray-300' }}" required>{{ old('keterangan') }}</textarea>
                </div>

                <div>
                    <label for="file_izin" class="block text-sm font-medium text-gray-700">
                        Upload Surat Izin <span id="file-label-info">(Opsional)</span>
                    </label>
                    <input type="file" name="file_izin" id="file_izin" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-green-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, atau PDF. Maksimal 2MB.</p>
                </div>

                <div class="flex justify-end pt-2">
                    <a href="{{ route('cuti.index') }}" class="mr-4 inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-700 shadow transition-colors duration-200 hover:bg-gray-300">Batal</a>
                    <button type="submit" class="inline-flex items-center rounded-lg bg-green-500 px-6 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-green-600">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jenisIzinSelect = document.getElementById('jenis_izin');
        const fileLabelInfo = document.getElementById('file-label-info');

        function toggleFileInputRequirement() {
            // Jika jenis cuti adalah 'Cuti Sakit', file menjadi WAJIB
            if (jenisIzinSelect.value === 'Cuti Sakit') {
                fileLabelInfo.innerHTML = '<span class="text-red-500">(Wajib)</span>';
                // Anda mungkin perlu menambahkan atribut 'required' jika validasinya di sisi client.
                // Namun, validasi wajib harus dilakukan di backend Laravel.
            } else {
                fileLabelInfo.textContent = '(Opsional)';
            }
        }

        toggleFileInputRequirement();
        jenisIzinSelect.addEventListener('change', toggleFileInputRequirement);

        // Opsional: Logika menghitung jumlah hari berdasarkan tanggal
        const tglMulaiInput = document.getElementById('tgl_mulai');
        const tglSelesaiInput = document.getElementById('tgl_selesai');
        const jumlahHariInput = document.getElementById('jumlah_hari');

        function calculateDays() {
            const start = new Date(tglMulaiInput.value);
            const end = new Date(tglSelesaiInput.value);

            // Periksa validitas tanggal
            if (start && end && start <= end) {
                // Hitung selisih hari (termasuk hari terakhir)
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                jumlahHariInput.value = diffDays;
            } else if (start && end && start > end) {
                jumlahHariInput.value = '';
            }
        }

        tglMulaiInput.addEventListener('change', calculateDays);
        tglSelesaiInput.addEventListener('change', calculateDays);
    });
</script>
@endpush

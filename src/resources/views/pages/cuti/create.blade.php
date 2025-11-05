@extends('layouts.dashboard')
@section('title', 'Tambah Cuti')

@section('content')

    <div class="flex items-center justify-center">
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
                    </select>
                </div>

                {{-- Tampilkan dropdown ini HANYA JIKA perlu (berdasarkan Auth::user()->isKaryawanBiasa()) --}}
                @if(Auth::user()->isKaryawanBiasa())
                    <div>
                        <label for="nip_user_ssdm" class="block text-sm font-medium text-gray-700">Pilih Atasan Langsung</label>
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
                        {{-- Atribut 'min' dihapus, akan dikontrol oleh JavaScript --}}
                        <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ old('tgl_mulai') }}"
                            class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_mulai') ? 'border-red-500' : 'border-gray-300' }}" required>
                    </div>
                    <div>
                        <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                        {{-- Atribut 'min' dihapus, akan dikontrol oleh JavaScript --}}
                        <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ old('tgl_selesai') }}"
                            class="mt-1 block w-full rounded-lg border bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('tgl_selesai') ? 'border-red-500' : 'border-gray-300' }}" required>
                    </div>
                </div>

                <div>
                    <label for="jumlah_hari" class="block text-sm font-medium text-gray-700">Jumlah Hari Kerja</label>
                    {{-- Dibuat readonly agar diisi JS & tidak membingungkan user --}}
                    <input type="number" name="jumlah_hari" id="jumlah_hari" value="{{ old('jumlah_hari') }}" min="1" placeholder="Akan terisi otomatis"
                           class="mt-1 block w-full rounded-lg border bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-100 {{ $errors->has('jumlah_hari') ? 'border-red-500' : 'border-gray-300' }}" readonly>
                </div>

                {{-- Tambahan Kolom Alamat dan HP (Sudah benar) --}}
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

{{-- ======================================================================== --}}
{{-- SELURUH JAVASCRIPT DI BAWAH INI TELAH DIPERBARUI --}}
{{-- ======================================================================== --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jenisIzinSelect = document.getElementById('jenis_izin');
        const fileLabelInfo = document.getElementById('file-label-info');
        const tglMulaiInput = document.getElementById('tgl_mulai');
        const tglSelesaiInput = document.getElementById('tgl_selesai');
        const jumlahHariInput = document.getElementById('jumlah_hari');

        // --- Variabel Tanggal untuk Validasi ---

        // 'today' (YYYY-MM-DD) untuk Cuti Sakit
        const today = new Date().toISOString().split('T')[0];

        // 'minDateOther' (H-3) untuk Cuti Lainnya
        // Aturan backend: after:now()->addDays(2) -> berarti hari pertama yang valid adalah addDays(3)
        // Kita gunakan Blade untuk mendapatkan tanggal yang pasti dari server
        const minDateOtherLeave = '{{ Carbon\Carbon::now()->addDays(3)->toDateString() }}';


        /**
         * Mengatur label file input (Wajib/Opsional) berdasarkan Jenis Izin
         */
        function toggleFileInputRequirement() {
            if (jenisIzinSelect.value === 'Cuti Sakit') {
                fileLabelInfo.innerHTML = '<span class="text-red-500">(Wajib)</span>';
            } else {
                fileLabelInfo.textContent = '(Opsional)';
            }
        }

        /**
         * PERBAIKAN 1: Mengatur tanggal 'min' dinamis (H-3 vs Hari H)
         */
        function updateMinDate() {
            let minDate;
            if (jenisIzinSelect.value === 'Cuti Sakit') {
                minDate = today; // Boleh hari H
            } else {
                minDate = minDateOtherLeave; // Wajib H-3
            }

            tglMulaiInput.min = minDate;

            // Set tgl_selesai.min juga, tidak boleh sebelum tgl_mulai
            if (tglMulaiInput.value && tglMulaiInput.value > minDate) {
                 tglSelesaiInput.min = tglMulaiInput.value;
            } else {
                 tglSelesaiInput.min = minDate;
            }
        }

        /**
         * PERBAIKAN 2: Menghitung HARI KERJA (Anti Sabtu-Minggu)
         */
        function calculateWorkDays() {
            const startStr = tglMulaiInput.value;
            const endStr = tglSelesaiInput.value;

            // Periksa validitas tanggal
            if (!startStr || !endStr) {
                jumlahHariInput.value = '';
                return;
            }

            // Tambahkan 'T00:00:00' agar konsisten antar browser (hindari masalah timezone)
            const start = new Date(startStr + 'T00:00:00');
            const end = new Date(endStr + 'T00:00:00');

            if (start > end) {
                jumlahHariInput.value = '';
                return;
            }

            let workDays = 0;
            let currentDate = new Date(start.getTime()); // Buat salinan tanggal

            // Loop dari tgl_mulai s/d tgl_selesai
            while (currentDate <= end) {
                const dayOfWeek = currentDate.getDay(); // 0 = Minggu, 6 = Sabtu

                // Jika BUKAN Minggu (0) DAN BUKAN Sabtu (6)
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    workDays++;
                }

                // Pindah ke hari berikutnya
                currentDate.setDate(currentDate.getDate() + 1);
            }

            // Tampilkan hanya jika hari kerja > 0
            jumlahHariInput.value = workDays > 0 ? workDays : '';
        }

        // --- Event Listeners ---

        // Jalankan saat 'jenis_izin' berubah
        jenisIzinSelect.addEventListener('change', function() {
            toggleFileInputRequirement();
            updateMinDate();
            // Hapus tanggal & jumlah hari jika aturan berubah, agar user memilih ulang
            tglMulaiInput.value = '';
            tglSelesaiInput.value = '';
            jumlahHariInput.value = '';
        });

        // Jalankan saat tanggal berubah
        tglMulaiInput.addEventListener('change', function() {
            // Update min tanggal selesai agar tidak < tanggal mulai
            tglSelesaiInput.min = tglMulaiInput.value;
            calculateWorkDays();
        });
        tglSelesaiInput.addEventListener('change', calculateWorkDays);

        // --- Inisialisasi saat halaman dimuat ---
        toggleFileInputRequirement();
        updateMinDate();

        // Hitung ulang jika ada old('value') dari error validasi
        if (tglMulaiInput.value && tglSelesaiInput.value) {
            calculateWorkDays();
        }
    });
</script>
@endpush

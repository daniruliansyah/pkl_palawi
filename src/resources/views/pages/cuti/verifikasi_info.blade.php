<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Perjalanan Dinas</title>
    {{-- Anda bisa menautkan CSS framework seperti Tailwind atau Bootstrap di sini --}}
    {{-- Contoh: <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        h1 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .info-item {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
        }
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .label {
            font-weight: bold;
            flex: 1;
            color: #555;
        }
        .value {
            flex: 2;
            color: #333;
        }
        .status-badge {
            display: block;
            margin-top: 20px;
            padding: 10px;
            text-align: center;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 4px;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>VERIFIKASI SURAT CUTI</h1>

        {{-- Status Verifikasi --}}
        @if ($cuti->status_gm == 'Disetujui')
            <div class="status-badge status-approved">
                STATUS: Surat Dinyatakan SAH dan VALID
            </div>
        @endif

        <div style="margin-top: 30px;">
            {{-- 1. Judul Surat --}}
            <div class="info-item">
                <span class="label">Jenis Surat</span>
                <span class="value">Surat Cuti</span>
            </div>

            {{-- 2. Nomor Surat --}}
            <div class="info-item">
                <span class="label">Nomor Surat</span>
                <span class="value">{{ $cuti->no_surat ?? 'BELUM DITERBITKAN' }}</span>
            </div>

            {{-- 3. Tanggal Persetujuan --}}
            <div class="info-item">
                <span class="label">Tanggal Persetujuan</span>
                {{-- Menggunakan Carbon untuk memformat tanggal ke format Indonesia --}}
                <span class="value">
                    {{ $cuti->tgl_persetujuan_gm ? \Carbon\Carbon::parse($cuti->tgl_persetujuan_gm)->isoFormat('D MMMM YYYY') : '-' }}
                </span>
            </div>

            {{-- 4. Maksud Perjalanan Dinas --}}
            <div class="info-item">
                <span class="label">Jenis Cuti</span>
                <span class="value">{{ $cuti->jenis_izin}}</span>
            </div>

            {{-- 5. Yang Melakukan Perjalanan --}}
            <div class="info-item">
                <span class="label">Pengaju</span>
                <span class="value">{{ $cuti->user->nama_lengkap ?? 'Data Karyawan Tidak Ditemukan' }}</span>
            </div>

            {{-- 6. Pejabat Penyetuju (NIP Penyetuju) --}}
            <div class="info-item">
                <span class="label">Disetujui Oleh</span>
                {{-- Memuat data penyetuju berdasarkan NIP yang tersimpan --}}
                <span class="value">
                    @php
                        // Coba ambil nama penyetuju dari relasi user berdasarkan nip_penyetuju
                        $penyetuju = \App\Models\User::where('nip', $cuti->nip_user_gm)->first();
                    @endphp
                    {{ $penyetuju->nama_lengkap ?? 'Tidak Diketahui' }}
                </span>
            </div>

            {{-- 7. Detail Tujuan Singkat --}}
            <div class="info-item">
                <span class="label">Jumlah Hari Cuti</span>
                <span class="value">{{ $cuti->jumlah_hari }}</span>
            </div>
        </div>
    </div>

</body>
</html>

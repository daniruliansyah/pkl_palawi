<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Permohonan {{ ucwords(strtolower($cuti->jenis_izin)) }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .text-center { text-align: center; }
        .text-underline { text-decoration: underline; }
        b, strong { font-weight: bold; }

        /* Style untuk bagian atas surat (menggantikan kop surat) */
        .header-section {
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        /* PERBAIKAN CSS UNTUK UKURAN LOGO */
        .logo-container {
            text-align: left;
            height: 60px;
        }
        .logo-image {
            width: 100px; /* Ukuran dikurangi */
            height: auto;
            display: block;
        }
        /* Akhir perbaikan logo */

        .details-table td {
            vertical-align: top;
            padding: 0;
            line-height: 1.3;
        }
        .details-table td:first-child { width: 180px; }
        .details-table td:nth-child(2) { width: 10px; }

        .approval-section {
            margin-top: 15px;
            width: 100%;
            border: none;
        }
        .approval-section td {
            vertical-align: top;
            padding: 4px 8px;
        }
        .approval-section .sdm-notes {
            width: 45%;
        }
        .approval-section .ssdm-notes {
            width: 55%;
            text-align: center;
        }
        .approval-section .sdm-approval,
        .approval-section .gm-approval {
            width: 50%;
            text-align: left;
        }

        .sdm-details-table {
            width: 100%;
            margin-top: 5px;
        }
        .sdm-details-table td {
            padding: 0;
        }
        .sdm-details-table td:first-child { width: 70%; }
        .sdm-details-table td:last-child { text-align: left; }

        .signature-space { height: 50px; }
        .qr-code {
            height: 50px;
            display: block;
            margin-top: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

{{-- BAGIAN ATAS SURAT (Menggantikan Kop Surat) --}}
    <div class="header-section">
        <div class="logo-container">
            @if($embed)
                <img src="{{ $embed }}" alt="Logo Perusahaan" class="logo-image">
            @endif
        </div>

        <table style="margin-top: 10px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 80px;">Perihal</td>
                            <td>: <b class="text-underline">PERMOHONAN {{ strtoupper($cuti->jenis_izin) }}</b></td>
                        </tr>
                        <tr>
                            <td>Nomor</td>
                            <td>: {{ $cuti->no_surat }}</td>
                        </tr>
                    </table>

                    <div style="margin-top: 20px;">
                        Kepada Yth:<br>
                        General Manager ABWWT<br>
                        PT. Perhutani Alam Wisata Risorsis<br>
                        Di<br>
                        <b class="text-underline">SURABAYA</b>
                    </div>
                </td>

                <td style="width: 50%; text-align: right; vertical-align: top;">
                    Surabaya, {{ \Carbon\Carbon::parse($cuti->created_at)->isoFormat('D MMMM Y') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ISI SURAT --}}
    <p style="margin-top: 20px;">Yang bertanda tangan di bawah ini:</p>
    <table class="details-table" style="margin-left: 20px;">
        <tr><td>Nama</td><td>:</td><td>{{ $karyawan->nama_lengkap ?? '...' }}</td></tr>
        <tr><td>NPK</td><td>:</td><td>{{ $karyawan->nik ?? '...' }}</td></tr>
        <tr><td>Pangkat/Gol Ruang</td><td>:</td><td>{{ $karyawan->jabatanTerbaru->jenjang ?? '-' }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>{{ $karyawan->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}</td></tr>
    </table>

    <p style="text-align: justify;">
        Dengan ini mengajukan {{ $cuti->jenis_izin }} untuk tahun {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('Y') }}
        selama {{ $cuti->jumlah_hari }} ({{ ucwords(\Terbilang::make($cuti->jumlah_hari)) }}) Hari kerja,
        terhitung tanggal {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->isoFormat('D MMMM Y') }}
        s/d {{ \Carbon\Carbon::parse($cuti->tgl_selesai)->isoFormat('D MMMM Y') }}
        dengan kesanggupan dan perjanjian sebagai berikut:
    </p>

    <ol type="a" style="padding-left: 40px; text-align: justify; margin: 0;">
        <li>Sebelum menjalankan cuti tersebut, akan menyerahkan pekerjaan yang menjadi tanggung jawab saya kepada atasan saya langsung.</li>
        <li>Setelah selesai menjalankan cuti saya akan segera melaporkan diri kepada atasan saya langsung dan bekerja kembali seperti biasa.</li>
        <li>
            Selama menjalankan cuti, saya dapat dihubungi melalui:
            <table class="details-table" style="margin-top: 5px; width: 85%;">
                <tr><td>Alamat</td><td>:</td><td><b>{{ $karyawan->alamat ?? '...' }}</b></td></tr>
                <tr><td>No HP</td><td>:</td><td><b>{{ $karyawan->no_telp ?? '...' }}</b></td></tr>
                <tr><td>Keperluan Cuti</td><td>:</td><td>{{ $cuti->keterangan }}</td></tr>
            </table>
        </li>
    </ol>

    <p>Demikian surat permintaan ini saya buat untuk dapat dipertimbangkan dan mendapat persetujuan.</p>

    <table style="width: 100%;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;" class="text-center">
                Hormat saya,
                <div class="signature-space"></div>
                <b class="text-underline">{{ $karyawan->nama_lengkap ?? '...' }}</b>
            </td>
        </tr>
    </table>

    {{-- BAGIAN PERSETUJUAN --}}
    <table class="approval-section">
        <tr>
            <td class="sdm-notes">
            <b class="text-underline">CATATAN PEJABAT SDM</b><br>
            Cuti yang telah diambil dalam tahun yang bersangkutan
            <table class="sdm-details-table">
                <tr><td>1. Cuti Tahunan</td><td>: {{ $dataCutiSDM['cuti_tahunan'] ?? 0 }} hari</td></tr>
                <tr><td>2. Cuti Besar</td><td>: {{ $dataCutiSDM['cuti_besar'] ?? 0 }} hari</td></tr>
                <tr><td>3. Cuti Sakit</td><td>: {{ $dataCutiSDM['cuti_sakit'] ?? 0 }} hari</td></tr>
                <tr><td>4. Cuti Bersalin</td><td>: {{ $dataCutiSDM['cuti_bersalin'] ?? 0 }} hari</td></tr>
                <tr><td>5. Cuti Karena Alasan Penting</td><td>: {{ $dataCutiSDM['cuti_alasan_penting'] ?? 0 }} hari</td></tr>
                <tr><td>6. Sisa Cuti Tahunan</td><td>: <b>{{ $sisaCutiDari12 ?? 12 }}</b> hari</td></tr>
            </table>
        </td>
            <td class="ssdm-notes">
                <span class="text-underline">CATATAN/PERTIMBANGAN ATASAN LANGSUNG</span><br>
                {{ $cuti->ssdm->jabatanTerbaru->jabatan->nama_jabatan ?? 'Senior...' }}<br>
                Area Bisnis Wisata Wilayah Timur
                <div class="signature-space"></div>
                <b class="text-underline">{{ $cuti->ssdm->nama_lengkap ?? '...' }}</b><br>
                NPK. {{ $cuti->ssdm->nik ?? '...' }}
            </td>
        </tr>
        <tr>
            <td class="sdm-approval">
                MENGETAHUI<br>
                Senior Analis Keu, SDM & Umum<br>
                Area Bisnis Wisata Wilayah Timur
                <div class="signature-space"></div>
                <b class="text-underline">{{ $cuti->sdm->nama_lengkap ?? '...' }}</b><br>
                NPK. {{ $cuti->sdm->nik ?? '...' }}
            </td>
            <td class="gm-approval">
                PEJABAT YANG BERWENANG<br>
                MENYETUJUI CUTI<br>
                General Manager<br>
                Area Bisnis Wisata Wilayah Timur
                <div class="qr-code">
                    {{-- QR Code untuk Verifikasi --}}
                    <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="width: 50px; height: 50px;">
                </div>
                <b class="text-underline">{{ $gm->nama_lengkap ?? '...' }}</b><br>
                NPK. {{ $gm->nik ?? '...' }}
            </td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Permohonan {{ ucwords(strtolower($cuti->jenis_izin)) }}</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.2;
            margin: 0;
        }
        table { width: 100%; border-collapse: collapse; }
        .text-center { text-align: center; }
        .text-underline { text-decoration: underline; }
        b, strong { font-weight: bold; }

        .header-section { padding-bottom: 5px; margin-bottom: 15px; }
        .logo-container { text-align: left; height: 50px; margin-bottom: 10px; }
        .logo-image { width: 90px; height: auto; display: block; }

        .details-table td {
            vertical-align: top;
            padding: 0;
            line-height: 1.2;
        }
        .details-table td:first-child { width: 180px; }
        .details-table td:nth-child(2) { width: 10px; }

        .signature-space { height: 50px; }

        .approval-section {
            margin-top: 20px;
            width: 100%;
            table-layout: fixed;
            font-size: 11pt;
        }
        .approval-section td {
            vertical-align: top;
            padding: 5px;
        }

        .sdm-notes, .ssdm-notes { width: 50%; vertical-align: top; text-align: center; }

        .sdm-details-table { margin: 3px auto 3px 0; }
        .sdm-details-table td { padding: 0; line-height: 1.2; }
        .sdm-details-table td:first-child { width: 70%; }
        .sdm-details-table td:last-child { text-align: left; }

        .qr-code { height: 60px; display: block; margin: 5px auto; }

        .page-wrapper { padding: 0 15px; box-sizing: border-box; }

        .signature-pemohon-container {
            width: 50%;
            float: right;
            text-align: center;
            margin-top: -15px;
        }
        .signature-right { font-size: 11pt; }
        .signature-right .signature-space { height: 50px; }

        .signature-box {
            margin-top: 10px;
            page-break-inside: avoid;
            line-height: 1.2;
        }

        ol { margin: 5px 0 10px 0; padding-left: 30px; }
        li { margin-bottom: 3px; }
    </style>
</head>

<body>
<div class="page-wrapper">

    {{-- HEADER --}}
    <div class="header-section">
        <div class="logo-container">
            @if($embed)
                <img src="{{ $embed }}" alt="Logo Perusahaan" class="logo-image">
            @endif
        </div>

        <table style="margin-top: 5px;">
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

                    <div style="margin-top: 15px;">
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
    <p style="margin-top: 10px;">Yang bertanda tangan di bawah ini:</p>

    <table class="details-table" style="margin-left: 20px;">
        <tr><td>Nama</td><td>:</td><td>{{ $karyawan->nama_lengkap ?? '...' }}</td></tr>
        <tr><td>NPK</td><td>:</td><td>{{ $karyawan->nik ?? '...' }}</td></tr>
        <tr><td>Pangkat/Gol Ruang</td><td>:</td><td>{{ $karyawan->jabatanTerbaru->jenjang ?? '-' }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>{{ $karyawan->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}</td></tr>
    </table>

    <p style="text-align: justify; margin-bottom: 0;">
        Dengan ini mengajukan {{ $cuti->jenis_izin }} untuk tahun {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('Y') }}
        selama <b>{{ $cuti->jumlah_hari }} ({{ ucwords((new NumberFormatter('id', NumberFormatter::SPELLOUT))->format($cuti->jumlah_hari)) }})</b> hari kerja, terhitung tanggal
        {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->isoFormat('D MMMM Y') }}
        s/d {{ \Carbon\Carbon::parse($cuti->tgl_selesai)->isoFormat('D MMMM Y') }}
        dengan kesanggupan dan perjanjian sebagai berikut:
    </p>

    <ol type="a" style="padding-left: 30px; text-align: justify;">
        <li>Sebelum menjalankan cuti tersebut, akan menyerahkan pekerjaan yang menjadi tanggung jawab saya kepada atasan saya langsung.</li>
        <li>Setelah selesai menjalankan cuti saya akan segera melaporkan diri kepada atasan saya langsung dan bekerja kembali seperti biasa.</li>
        <li>
            <table class="details-table" style="margin-top: 3px; width: 85%;">
                <tr><td>Keperluan Cuti</td><td>:</td><td>{{ $cuti->keterangan }}</td></tr>
                <tr><td>Alamat</td><td>:</td><td><b>{{ $karyawan->alamat ?? '...' }}</b></td></tr>
                <tr><td>No HP</td><td>:</td><td><b>{{ $karyawan->no_telp ?? '...' }}</b></td></tr>
            </table>
        </li>
    </ol>

    <p style="margin-top: 5px; margin-bottom: 20px;">
        Demikian surat permintaan ini saya buat untuk dapat dipertimbangkan dan mendapat persetujuan.
    </p>

    {{-- HORMAT SAYA --}}
    <div class="signature-pemohon-container">
        <div class="signature-right">
            Hormat saya,
            <div class="signature-space"></div>
            <b class="text-underline">{{ $karyawan->nama_lengkap ?? '...' }}</b><br>
            NPK. {{ $karyawan->nik ?? '...' }}
        </div>
    </div>
    <div style="clear: both;"></div>

    {{-- FOOTER / CATATAN --}}
    <table class="approval-section">
        <tr>
            {{-- KIRI: CATATAN SDM --}}
            <td class="sdm-notes">
                <b class="text-underline">CATATAN PEJABAT SDM</b><br>
                <div style="text-align: left; padding-left: 20px;">
                    Cuti yang telah diambil dalam tahun yang bersangkutan
                    <table class="sdm-details-table" style="margin-top: 3px;">
                        <tr><td>1. Cuti Tahunan</td><td>:</td><td>{{ $dataCutiSDM['cuti_tahunan'] ?? '0' }} hari</td></tr>
                        <tr><td>2. Cuti Besar</td><td>:</td><td>{{ $dataCutiSDM['cuti_besar'] ?? '0' }} hari</td></tr>
                        <tr><td>3. Cuti Sakit</td><td>:</td><td>{{ $dataCutiSDM['cuti_sakit'] ?? '0' }} hari</td></tr>
                        <tr><td>4. Cuti Bersalin</td><td>:</td><td>{{ $dataCutiSDM['cuti_bersalin'] ?? '0' }} hari</td></tr>
                        <tr><td>5. Cuti Karena Alasan Penting</td><td>:</td><td>{{ $dataCutiSDM['cuti_alasan_penting'] ?? '0' }} hari</td></tr>
                        <tr><td>6. Sisa Cuti Tahunan</td><td>:</td><td><b>{{ $dataCutiSDM['sisa_cuti_tahunan'] ?? '0' }} hari</b></td></tr>
                    </table>
                </div>

                @if($sdm)
                    <b class="text-underline">MENGETAHUI</b><br>
                       {{ $sdm->jabatanTerbaru?->jabatan?->nama_jabatan ?? 'Pejabat SDM' }}<br>
                        Area Bisnis Wisata Wilayah Timur
                    <div class="signature-space"></div>
                    <b class="text-underline">{{ $sdm->nama_lengkap ?? 'ATASAN LANGSUNG' }}</b><br>
                    NPK. {{ $sdm->nik ?? '...' }}
                @endif
            </td>

            {{-- KANAN: ATASAN LANGSUNG & GM --}}
            <td class="ssdm-notes">
                @if($atasan_langsung)
                    <b class="text-underline">CATATAN/PERTIMBANGAN ATASAN LANGSUNG</b><br>
                    {{ $atasan_langsung->jabatanTerbaru?->jabatan?->nama_jabatan ?? 'Atasan Langsung' }}<br>
                    Area Bisnis Wisata Wilayah Timur
                    <div class="signature-space"></div>
                    <b class="text-underline">{{ $atasan_langsung->nama_lengkap ?? 'ATASAN LANGSUNG' }}</b><br>
                    NPK. {{ $atasan_langsung->nik ?? '...' }}
                @endif

                <div class="signature-box">
                    <b class="text-underline">PEJABAT YANG BERWENANG MENYETUJUI CUTI</b><br>
                    {{ $gm?->jabatanTerbaru?->jabatan?->nama_jabatan ?? 'General Manager' }}<br>
                    Area Bisnis Wisata Wilayah Timur
                    @if($cuti->status_gm == 'Disetujui')
                        <div class="qr-code">
                            <img src="{{ $qrCodeBase64 }}" alt="QR GM" style="width: 60px; height: 60px;">
                        </div>
                    @else
                        <div class="signature-space"></div>
                    @endif
                    <b class="text-underline">{{ $gm?->nama_lengkap ?? 'GENERAL MANAGER' }}</b><br>
                    NPK. {{ $gm?->nik ?? '...' }}
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>

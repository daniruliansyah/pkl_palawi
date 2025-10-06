<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Permohonan {{ ucwords(strtolower($cuti->jenis_izin)) }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 40px; }
        .header-table, .main-table, .signature-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; }
        .content { margin-top: 20px; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table td { vertical-align: top; padding: 1px 0; }
        .details-table td:first-child { width: 180px; }
        .details-table td:nth-child(2) { width: 15px; }
        .signature-box { border: 1px solid #ccc; background-color: #f2f2f2; padding: 10px; text-align: center; }
        .signature-space { height: 60px; }
        .catatan-sdm-table td { padding-right: 15px; }
        .text-center { text-align: center; }
        .text-underline { text-decoration: underline; }
        b, strong { font-weight: bold; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 10%;"><img src="{{ $embed }}" style="width: 80px;"></td>
            <td style="width: 50%;">
                <h4 style="margin:0;">PT. PERHUTANI ALAM WISATA</h4>
                <p style="margin:0; font-size: 10pt;">Anak Perusahaan</p>
            </td>
            <td style="width: 40%; text-align: right;">
                Surabaya, {{ \Carbon\Carbon::parse($cuti->created_at)->isoFormat('D MMMM Y') }}
            </td>
        </tr>
    </table>

    <table class="main-table" style="margin-top: 20px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <table class="details-table">
                    <tr>
                        <td>Perihal</td>
                        <td>:</td>
                        <td><b>PERMOHONAN {{ strtoupper($cuti->jenis_izin) }}</b></td>
                    </tr>
                    <tr>
                        <td>Nomor</td>
                        <td>:</td>
                        <td>{{ $cuti->no_surat }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%; vertical-align: top; padding-left: 20px;">
                Kepada Yth:<br> General Manager ABWWT<br> PT. Perhutani Alam Wisata Risorsis<br><br> Di<br><b>SURABAYA</b>
            </td>
        </tr>
    </table>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table class="details-table" style="margin-left: 20px;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $karyawan->nama_lengkap ?? '...' }}</td>
            </tr>
            <tr>
                <td>NPK</td>
                <td>:</td>
                <td>{{ $karyawan->nik ?? '...' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}</td>
            </tr>
        </table>

        <p style="text-align: justify;">
            Dengan ini mengajukan {{ $cuti->jenis_izin }} selama {{ $cuti->jumlah_hari }} Hari kerja, terhitung mulai tanggal {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($cuti->tgl_selesai)->isoFormat('D MMMM Y') }} dengan keperluan: {{ $cuti->keterangan }}.
        </p>
        <p>Demikian surat permintaan ini saya buat untuk dapat dipertimbangkan dan mendapat persetujuan.</p>
    </div>

    <table class="signature-table" style="margin-top: 30px;">
        <tr>
            <td style="width: 50%; vertical-align: top;" class="text-center">
                Hormat saya,
                <div class="signature-space"></div>
                <b class="text-underline">{{ $karyawan->nama_lengkap ?? '...' }}</b><br>
                NPK. {{ $karyawan->nik ?? '...' }}
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 20px;" class="text-center">
                Pejabat yang Menyetujui,
                <div class="signature-space">
                    <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="width: 60px; height: 60px; margin: auto;">
                </div>
                <b class="text-underline">{{ $gm->nama_lengkap ?? '...' }}</b><br>
                NPK. {{ $gm->nik ?? '...' }}
            </td>
        </tr>
    </table>

</body>
</html>
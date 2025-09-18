<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat SPPD - {{ $sppd->no_surat }}</title>
    <style>
        /* Gaya CSS Anda di sini */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        .header, .footer {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .no-border th, .no-border td {
            border: none;
            padding: 2px 0;
        }
        .signature-image {
            max-height: 80px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>SURAT PERINTAH PERJALANAN DINAS</h3>
            <p>No. Surat: {{ $sppd->no_surat }}</p>
        </div>
        <p>Yang bertanda tangan di bawah ini:</p>
        <table class="no-border">
            <tr><td>Nama</td><td>: General Manager</td></tr>
            <tr><td>Jabatan</td><td>: General Manager</td></tr>
        </table>
        <p>Memberikan perintah perjalanan dinas kepada:</p>
        <table class="no-border">
            <tr><td>Nama</td><td>: {{ $sppd->user->nama_lengkap }}</td></tr>
            <tr><td>NIP</td><td>: {{ $sppd->user->nip }}</td></tr>
            <tr><td>Jabatan</td><td>: {{ $sppd->user->jabatanTerbaru->jabatan->nama_jabatan }}</td></tr>
            <tr><td>Unit Kerja</td><td>: -</td></tr>
        </table>
        <p>Untuk melaksanakan perjalanan dinas dengan tujuan:</p>
        <table class="no-border">
            <tr><td>Lokasi Tujuan</td><td>: {{ $sppd->lokasi_tujuan }}</td></tr>
            <tr><td>Tanggal Berangkat</td><td>: {{ \Carbon\Carbon::parse($sppd->tgl_mulai)->format('d F Y') }}</td></tr>
            <tr><td>Tanggal Selesai</td><td>: {{ \Carbon\Carbon::parse($sppd->tgl_selesai)->format('d F Y') }}</td></tr>
            <tr><td>Keterangan</td><td>: {{ $sppd->keterangan }}</td></tr>
        </table>
        <p>Demikian surat ini dibuat untuk dilaksanakan dengan sebaik-baiknya.</p>

                <table class="no-border" style="margin-top: 50px;">
            <tr>
                <td style="text-align: center; width: 45%;">
                    <p>Mengetahui,</p>
                    <p>General Manager</p>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/barcode_gm.png'))) }}" alt="Tanda Tangan GM" class="signature-image">
                    <p>(Nama Lengkap GM)</p>
                </td>
                <td style="width: 10%;"></td>
                <td style="text-align: center; width: 45%;">
                    <p>Dibuat di Jakarta,</p>
                    <p>{{ \Carbon\Carbon::parse($sppd->created_at)->format('d F Y') }}</p>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/barcode_sdm.png'))) }}" alt="Tanda Tangan SDM" class="signature-image">
                    <p>(Nama Lengkap SDM)</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

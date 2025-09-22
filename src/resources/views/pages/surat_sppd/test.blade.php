<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat SPPD - {{ $sppd->no_surat }}</title>
    <style>
        @page {
        margin-top: 20px;
        margin-bottom: 20px;
        margin-left: 25px;
        margin-right: 25px;
        } 
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
            vertical-align: top;
        }
        hr {
            border-top: 2px solid #000;
            margin: 1rem 0;
        }
        .signature-area {
            margin-top: 50px;
        }
        .signature-area table {
            width: 100%;
        }
        .signature-area td {
            width: 45%;
            text-align: center;
        }
        .signature-image {
            max-height: 80px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table style="width: 100%; margin-bottom: 2rem;">
            <tr>
                <td style="width: 20%; text-align: left;">
                    {{-- Logo kiri --}}
                   {{-- <img src="{{ public_path('images/econique.png') }}" alt="Logo" style="max-height: 80px;"> --}}
                   <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/econique.jpg'))) }}" style="max-height:80px;">
                </td>
                <td style="width: 60%; text-align: center;">
                    <h1 style="font-size: 1rem; font-weight: bold; text-transform: uppercase;">PT. PERHUTANI ALAM WISATA RISORSIS</h1>
                    <h1 style="font-size: 1rem; font-weight: bold; text-transform: uppercase;">Area Bisnis Wisata Wilayah Timur</h1>
                    <h1 style="font-size: 1rem; font-weight: bold; text-transform: uppercase;">GRAHA PERHUTANI SURABAYA</h1>
                    <p style="font-size: 0.75rem;">JL. GENTENGKALI NO. 49 SURABAYA</p>
                    <h1 style="font-size: 1rem; font-weight: bold;">SURAT PERINTAH PERJALANAN DINAS</h1>
                </td>
                <td style="width: 20%;"></td>
            </tr>
        </table>

        <hr>

        <div class="header">
            <p style="margin-top: 0.5rem;">NOMOR : {{ $sppd->no_surat }}</p>
        </div>

        <p style="margin-top: 2rem; margin-bottom: 1rem;">Yang bertanda tangan di bawah ini:</p>
        <table class="no-border">
            <tr><td>Nama</td><td>: General Manager</td></tr>
            <tr><td>Jabatan</td><td>: General Manager</td></tr>
        </table>
        <p style="margin-top: 2rem; margin-bottom: 1rem;">Memberikan perintah perjalanan dinas kepada:</p>
        <table class="no-border">
            <tr><td>Nama</td><td>: {{ $sppd->user->nama_lengkap }}</td></tr>
            <tr><td>NIP</td><td>: {{ $sppd->user->nip }}</td></tr>
            <tr><td>Jabatan</td><td>: {{ $sppd->user->jabatanTerbaru->jabatan->nama_jabatan }}</td></tr>
            <tr><td>Unit Kerja</td><td>: -</td></tr>
        </table>
        <p style="margin-top: 2rem; margin-bottom: 1rem;">Untuk melaksanakan perjalanan dinas dengan tujuan:</p>
        <table class="no-border">
            <tr><td>Lokasi Tujuan</td><td>: {{ $sppd->lokasi_tujuan }}</td></tr>
            <tr><td>Tanggal Berangkat</td><td>: {{ \Carbon\Carbon::parse($sppd->tgl_mulai)->format('d F Y') }}</td></tr>
            <tr><td>Tanggal Selesai</td><td>: {{ \Carbon\Carbon::parse($sppd->tgl_selesai)->format('d F Y') }}</td></tr>
            <tr><td>Keterangan</td><td>: {{ $sppd->keterangan }}</td></tr>
        </table>
        <p style="margin-top: 2rem;">Demikian surat ini dibuat untuk dilaksanakan dengan sebaik-baiknya.</p>
        <div class="signature-area">
            <table>
                <tr>
                    <td>
                        <p>Mengetahui,</p>
                        <p style="font-weight: bold;">General Manager</p>
                        {{-- <img src="{{ public_path('images/barcode_gm.jpg') }}" alt="Tanda Tangan GM" class="signature-image"> --}}
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/barcode_gm.jpg'))) }}" style="max-height:80px;">
                        <p style="font-weight: bold;">(Nama Lengkap GM)</p>
                    </td>
                    <td style="width: 10%;"></td>
                    <td>
                        <p>Dibuat di Jakarta,</p>
                        <p>{{ \Carbon\Carbon::parse($sppd->created_at)->format('d F Y') }}</p>
                       {{-- <img src="{{ public_path('images/barcode_sdm.jpg') }}" alt="Tanda Tangan SDM" class="signature-image"> --}}
                       <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/barcode_sdm.jpg'))) }}" style="max-height:80px;">
                        <p style="font-weight: bold;">(Nama Lengkap SDM)</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>

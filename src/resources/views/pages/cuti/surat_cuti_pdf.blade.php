<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Permohonan Cuti Tahunan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; }
        .container { width: 90%; margin: 0 auto; }
        .header { text-align: left; margin-bottom: 20px; }
        .header .logo { width: 100px; float: left; margin-right: 20px; }
        .header .company-info { float: left; }
        .header h4, .header p { margin: 0; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .content { margin-top: 20px; }
        .content .section { margin-bottom: 15px; }
        .content .details-table { width: 100%; border-collapse: collapse; }
        .content .details-table td { vertical-align: top; padding: 2px 0; }
        .content .details-table td:first-child { width: 150px; }
        .content .details-table td:nth-child(2) { width: 10px; }
        .signatures { margin-top: 50px; width: 100%; }
        .signatures .signature-block { width: 33%; float: left; text-align: center; }
        .signatures .qr-code { position: relative; }
        .signatures .qr-code img { width: 80px; height: 80px; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            {{-- Anda bisa menambahkan logo perusahaan di sini --}}
            {{-- <img src="{{ public_path('images/logo.png') }}" alt="logo" class="logo"> --}}
            <div class="company-info">
                <h4>PT. PERHUTANI ALAM WISATA</h4>
                <p>Anak Perusahaan</p>
            </div>
        </div>

        <h3 style="text-align:center; text-decoration:underline;">PERMOHONAN TAHUNAN</h3>
        <p style="text-align:center; margin-top:-10px;">Nomor : {{ $cuti->no_surat }}</p>

        <div class="content">
            <div class="section">
                Kepada Yth:<br>
                General Manager ABWWT<br>
                PT. Perhutani Alam Wisata Risorsis<br>
                Di<br>
                SURABAYA
            </div>

            <div class="section">
                Yang bertanda tangan di bawah ini:
                <table class="details-table">
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td>{{ $cuti->user->nama_lengkap ?? '...' }}</td>
                    </tr>
                    <tr>
                        <td>NPK</td>
                        <td>:</td>
                        <td>{{ $cuti->user->nik ?? '...' }}</td>
                    </tr>
                    <tr>
                        <td>Pangkat/Gol Ruang</td>
                        <td>:</td>
                        <td>{{ $cuti->user->jabatanTerbaru->jabatan->jenjang ?? '...' }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>{{ $cuti->user->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <p>
                    Dengan ini mengajukan Cuti Tahunan untuk tahun {{ date('Y') }} selama {{ $cuti->jumlah_hari }} ({{ ucwords(\Terbilang::make($cuti->jumlah_hari)) }}) Hari kerja, terhitung tanggal {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->isoFormat('D MMMM') }} & {{ \Carbon\Carbon::parse($cuti->tgl_selesai)->isoFormat('D MMMM Y') }} dengan kesanggupan dan perjanjian sebagai berikut:
                </p>
                <ol type="a">
                    <li>Sebelum menjalankan cuti tersebut, akan menyerahkan pekerjaan yang menjadi tanggung jawab saya kepada atasan saya langsung.</li>
                    <li>Setelah selesai menjalankan cuti saya akan segera melaporkan diri kepada atasan saya langsung dan bekerja kembali seperti biasa.</li>
                    <li>Keperluan cuti : {{ $cuti->keterangan }}</li>
                </ol>
            </div>

            <p>Demikian surat permintaan ini saya buat untuk dapat dipertimbangkan dan mendapat persetujuan.</p>
        </div>

        <div class="signatures clearfix">
            <div class="signature-block">
                Mengetahui<br>
                Senior Analis Keu, SDM & Umum<br>
                Area Bisnis Wisata Wilayah Timur
                <div class="signature-space"></div>
                <b><u>{{ $cuti->sdm->nama_lengkap ?? '...' }}</u></b><br>
                NPK. {{ $cuti->sdm->nik ?? '...' }}
            </div>
            <div class="signature-block">
                CATATAN/PERTIMBANGAN ATASAN LANGSUNG<br>
                {{ $cuti->ssdm->jabatanTerbaru->jabatan->nama_jabatan ?? 'Senior...' }}<br>
                 Area Bisnis Wisata Wilayah Timur
                <div class="qr-code">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                </div>
                <b><u>{{ $cuti->ssdm->nama_lengkap ?? '...' }}</u></b><br>
                NPK. {{ $cuti->ssdm->nik ?? '...' }}
            </div>
            <div class="signature-block">
                PEJABAT YANG BERWENANG MENYETUJUI CUTI<br>
                General Manager<br>
                Area Bisnis Wisata Wilayah Timur
                <div class="signature-space"></div>
                <b><u>{{ $cuti->gm->nama_lengkap ?? '...' }}</u></b><br>
                NPK. {{ $cuti->gm->nik ?? '...' }}
            </div>
        </div>
    </div>
</body>
</html>

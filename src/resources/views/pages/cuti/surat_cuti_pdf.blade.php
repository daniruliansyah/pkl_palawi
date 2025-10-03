<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Permohonan {{ ucwords(strtolower($cuti->jenis_izin)) }}</title>
    <style>
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
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
            <td style="width: 10%;">
                {{-- Ganti path gambar ini jika Anda punya logo --}}
                <img src="{{ public_path('images/logo/b.png') }}" style="width: 80px;">
            </td>
            <td style="width: 50%;">
                <h4 style="margin:0;">PT. PERHUTANI ALAM WISATA</h4>
                <p style="margin:0; font-size: 10pt;">Anak Perusahaan</p>
            </td>
            <td style="width: 40%; text-align: right;">
                Surabaya, {{ \Carbon\Carbon::parse($cuti->created_at)->isoFormat('D MMMM Y') }}
            </td>
        </tr>
    </table>
=======
        /* Gaya dasar dokumen */
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .container {
            width: 85%; /* Dikecilkan agar ada margin lebih di kertas */
            margin: 0 auto;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* HEADER */
        .header-top-info {
            width: 100%;
            margin-bottom: 5px;
        }
        .header-top-info .logo-container {
            width: 50%;
            float: left;
        }
        .header-top-info .date-location {
            width: 50%;
            float: left;
            text-align: right;
        }
        .logo-container img {
            max-width: 120px;
            height: auto;
        }

        /* ISI SURAT */
        .content {
            margin-top: 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .details-table td {
            vertical-align: top;
            padding: 0px 0; /* Padding dihilangkan */
        }
        .details-table td:first-child {
            width: 140px; /* Lebar kolom kiri */
        }
        .details-table td:nth-child(2) {
            width: 10px; /* Lebar kolom titik dua */
        }
        .indent-list {
            list-style-type: lower-alpha;
            margin-left: -5px; /* Menggeser ke kiri */
            padding-left: 20px;
        }
        .indent-list li {
            padding-left: 5px;
            margin-bottom: 5px;
        }

        /* FOOTER / TANDA TANGAN */
        .signatures {
            margin-top: 50px;
            width: 100%;
            border: 1px dashed transparent; /* Placeholder untuk batas utama */
        }
        .signatures-table {
            width: 100%;
            table-layout: fixed;
        }
        .signatures-table td {
            width: 33.33%;
            padding: 0 5px;
            vertical-align: top;
            text-align: center;
        }
        .notes-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .notes-table td {
            vertical-align: top;
            padding: 0;
            border: 1px solid black;
        }
        .notes-table td:first-child {
            width: 45%;
        }
        .notes-table td:last-child {
            width: 55%;
        }
        .notes-block {
            padding: 5px 10px;
            text-align: left;
            font-size: 10pt; /* Dikecilkan */
            line-height: 1.3;
        }
        .note-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .signature-space {
            height: 50px; /* Jarak untuk tanda tangan */
        }
        .qr-code {
            height: 60px; /* Ruang untuk QR code */
            padding-top: 10px;
            padding-bottom: 5px;
        }
        .qr-code img {
            width: 60px;
            height: 60px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
=======
        /* Gaya dasar dokumen */
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .container {
            width: 85%; /* Dikecilkan agar ada margin lebih di kertas */
            margin: 0 auto;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* HEADER */
        .header-top-info {
            width: 100%;
            margin-bottom: 5px;
        }
        .header-top-info .logo-container {
            width: 50%;
            float: left;
        }
        .header-top-info .date-location {
            width: 50%;
            float: left;
            text-align: right;
        }
        .logo-container img {
            max-width: 120px;
            height: auto;
        }

        /* ISI SURAT */
        .content {
            margin-top: 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .details-table td {
            vertical-align: top;
            padding: 0px 0; /* Padding dihilangkan */
        }
        .details-table td:first-child {
            width: 140px; /* Lebar kolom kiri */
        }
        .details-table td:nth-child(2) {
            width: 10px; /* Lebar kolom titik dua */
        }
        .indent-list {
            list-style-type: lower-alpha;
            margin-left: -5px; /* Menggeser ke kiri */
            padding-left: 20px;
        }
        .indent-list li {
            padding-left: 5px;
            margin-bottom: 5px;
        }

        /* FOOTER / TANDA TANGAN */
        .signatures {
            margin-top: 50px;
            width: 100%;
            border: 1px dashed transparent; /* Placeholder untuk batas utama */
        }
        .signatures-table {
            width: 100%;
            table-layout: fixed;
        }
        .signatures-table td {
            width: 33.33%;
            padding: 0 5px;
            vertical-align: top;
            text-align: center;
        }
        .notes-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .notes-table td {
            vertical-align: top;
            padding: 0;
            border: 1px solid black;
        }
        .notes-table td:first-child {
            width: 45%;
        }
        .notes-table td:last-child {
            width: 55%;
        }
        .notes-block {
            padding: 5px 10px;
            text-align: left;
            font-size: 10pt; /* Dikecilkan */
            line-height: 1.3;
        }
        .note-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .signature-space {
            height: 50px; /* Jarak untuk tanda tangan */
        }
        .qr-code {
            height: 60px; /* Ruang untuk QR code */
            padding-top: 10px;
            padding-bottom: 5px;
        }
        .qr-code img {
            width: 60px;
            height: 60px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
>>>>>>> Stashed changes
=======
        /* Gaya dasar dokumen */
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .container {
            width: 85%; /* Dikecilkan agar ada margin lebih di kertas */
            margin: 0 auto;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* HEADER */
        .header-top-info {
            width: 100%;
            margin-bottom: 5px;
        }
        .header-top-info .logo-container {
            width: 50%;
            float: left;
        }
        .header-top-info .date-location {
            width: 50%;
            float: left;
            text-align: right;
        }
        .logo-container img {
            max-width: 120px;
            height: auto;
        }

        /* ISI SURAT */
        .content {
            margin-top: 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .details-table td {
            vertical-align: top;
            padding: 0px 0; /* Padding dihilangkan */
        }
        .details-table td:first-child {
            width: 140px; /* Lebar kolom kiri */
        }
        .details-table td:nth-child(2) {
            width: 10px; /* Lebar kolom titik dua */
        }
        .indent-list {
            list-style-type: lower-alpha;
            margin-left: -5px; /* Menggeser ke kiri */
            padding-left: 20px;
        }
        .indent-list li {
            padding-left: 5px;
            margin-bottom: 5px;
        }

        /* FOOTER / TANDA TANGAN */
        .signatures {
            margin-top: 50px;
            width: 100%;
            border: 1px dashed transparent; /* Placeholder untuk batas utama */
        }
        .signatures-table {
            width: 100%;
            table-layout: fixed;
        }
        .signatures-table td {
            width: 33.33%;
            padding: 0 5px;
            vertical-align: top;
            text-align: center;
        }
        .notes-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .notes-table td {
            vertical-align: top;
            padding: 0;
            border: 1px solid black;
        }
        .notes-table td:first-child {
            width: 45%;
        }
        .notes-table td:last-child {
            width: 55%;
        }
        .notes-block {
            padding: 5px 10px;
            text-align: left;
            font-size: 10pt; /* Dikecilkan */
            line-height: 1.3;
        }
        .note-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .signature-space {
            height: 50px; /* Jarak untuk tanda tangan */
        }
        .qr-code {
            height: 60px; /* Ruang untuk QR code */
            padding-top: 10px;
            padding-bottom: 5px;
        }
        .qr-code img {
            width: 60px;
            height: 60px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
>>>>>>> Stashed changes
=======
        /* Gaya dasar dokumen */
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .container {
            width: 85%; /* Dikecilkan agar ada margin lebih di kertas */
            margin: 0 auto;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* HEADER */
        .header-top-info {
            width: 100%;
            margin-bottom: 5px;
        }
        .header-top-info .logo-container {
            width: 50%;
            float: left;
        }
        .header-top-info .date-location {
            width: 50%;
            float: left;
            text-align: right;
        }
        .logo-container img {
            max-width: 120px;
            height: auto;
        }

        /* ISI SURAT */
        .content {
            margin-top: 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .details-table td {
            vertical-align: top;
            padding: 0px 0; /* Padding dihilangkan */
        }
        .details-table td:first-child {
            width: 140px; /* Lebar kolom kiri */
        }
        .details-table td:nth-child(2) {
            width: 10px; /* Lebar kolom titik dua */
        }
        .indent-list {
            list-style-type: lower-alpha;
            margin-left: -5px; /* Menggeser ke kiri */
            padding-left: 20px;
        }
        .indent-list li {
            padding-left: 5px;
            margin-bottom: 5px;
        }

        /* FOOTER / TANDA TANGAN */
        .signatures {
            margin-top: 50px;
            width: 100%;
            border: 1px dashed transparent; /* Placeholder untuk batas utama */
        }
        .signatures-table {
            width: 100%;
            table-layout: fixed;
        }
        .signatures-table td {
            width: 33.33%;
            padding: 0 5px;
            vertical-align: top;
            text-align: center;
        }
        .notes-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .notes-table td {
            vertical-align: top;
            padding: 0;
            border: 1px solid black;
        }
        .notes-table td:first-child {
            width: 45%;
        }
        .notes-table td:last-child {
            width: 55%;
        }
        .notes-block {
            padding: 5px 10px;
            text-align: left;
            font-size: 10pt; /* Dikecilkan */
            line-height: 1.3;
        }
        .note-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .signature-space {
            height: 50px; /* Jarak untuk tanda tangan */
        }
        .qr-code {
            height: 60px; /* Ruang untuk QR code */
            padding-top: 10px;
            padding-bottom: 5px;
        }
        .qr-code img {
            width: 60px;
            height: 60px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
>>>>>>> Stashed changes
=======
        /* Gaya dasar dokumen */
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .container {
            width: 85%; /* Dikecilkan agar ada margin lebih di kertas */
            margin: 0 auto;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* HEADER */
        .header-top-info {
            width: 100%;
            margin-bottom: 5px;
        }
        .header-top-info .logo-container {
            width: 50%;
            float: left;
        }
        .header-top-info .date-location {
            width: 50%;
            float: left;
            text-align: right;
        }
        .logo-container img {
            max-width: 120px;
            height: auto;
        }

        /* ISI SURAT */
        .content {
            margin-top: 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .details-table td {
            vertical-align: top;
            padding: 0px 0; /* Padding dihilangkan */
        }
        .details-table td:first-child {
            width: 140px; /* Lebar kolom kiri */
        }
        .details-table td:nth-child(2) {
            width: 10px; /* Lebar kolom titik dua */
        }
        .indent-list {
            list-style-type: lower-alpha;
            margin-left: -5px; /* Menggeser ke kiri */
            padding-left: 20px;
        }
        .indent-list li {
            padding-left: 5px;
            margin-bottom: 5px;
        }

        /* FOOTER / TANDA TANGAN */
        .signatures {
            margin-top: 50px;
            width: 100%;
            border: 1px dashed transparent; /* Placeholder untuk batas utama */
        }
        .signatures-table {
            width: 100%;
            table-layout: fixed;
        }
        .signatures-table td {
            width: 33.33%;
            padding: 0 5px;
            vertical-align: top;
            text-align: center;
        }
        .notes-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .notes-table td {
            vertical-align: top;
            padding: 0;
            border: 1px solid black;
        }
        .notes-table td:first-child {
            width: 45%;
        }
        .notes-table td:last-child {
            width: 55%;
        }
        .notes-block {
            padding: 5px 10px;
            text-align: left;
            font-size: 10pt; /* Dikecilkan */
            line-height: 1.3;
        }
        .note-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .signature-space {
            height: 50px; /* Jarak untuk tanda tangan */
        }
        .qr-code {
            height: 60px; /* Ruang untuk QR code */
            padding-top: 10px;
            padding-bottom: 5px;
        }
        .qr-code img {
            width: 60px;
            height: 60px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
>>>>>>> Stashed changes
    {{-- Variabel Placeholder (Gantilah ini dengan data dari Controller/PHP Anda) --}}
    @php
        $no_urutan = $cuti->no_urutan_surat ?? '000'; // Angka 000, 001, 002, dst.
        $no_surat = $no_urutan . '/014.1/SDM/ABWWT/2025';
        $logo_path = 'images/logo_perhutani.png'; // Ganti dengan path logo yang benar
        $tgl_surat = \Carbon\Carbon::parse('2025-09-15')->isoFormat('D MMMM Y'); // Contoh tanggal
        $nama_pemohon = $cuti->user->nama_lengkap ?? 'Nama Karyawan';
        $npk_pemohon = $cuti->user->nik ?? '1111111111';
        $jenjang = $cuti->user->jabatanTerbaru->jabatan->jenjang ?? 'Jenjang Jabatan IV';
        $jabatan = $cuti->user->jabatanTerbaru->jabatan->nama_jabatan ?? 'Analis Sosling';
        $jml_hari = $cuti->jumlah_hari ?? 2;
        $tgl_mulai = \Carbon\Carbon::parse('2025-08-26')->isoFormat('D MMMM');
        $tgl_selesai = \Carbon\Carbon::parse('2025-09-02')->isoFormat('D MMMM Y');
        $keperluan_cuti = $cuti->keterangan ?? 'Sakit';
        $alamat_cuti = $cuti->alamat_saat_cuti ?? 'Perumahan A2 No. 10';
        $no_hp = $cuti->no_hp_saat_cuti ?? '0812XXXXXXXX';

        // Data Cuti Tahun Lalu (Placeholder)
        $cuti_telah_diambil = 9;
        $cuti_besar = 0;
        $cuti_sakit = 4;
        $cuti_bersalin = 0;
        $cuti_alasan_penting = 0;
        $sisa_cuti = 3;

    @endphp

    <div class="container">

        <div class="header-top-info clearfix">
            <div class="logo-container">
                {{-- Logo perusahaan --}}
                <img src="{{ $embed($logo_path) }}" alt="Logo">
                <p style="margin-top: -5px; font-size: 9pt;">PF. Perhutani Alam Wisata</p>
            </div>
            <div class="date-location">
                Surabaya, {{ $tgl_surat }}
            </div>
        </div>

        <p style="margin-bottom: 5px; font-weight: bold;">Perihal : PERMOHONAN TAHUNAN</p>
        <p style="margin-top: 0px;">Nomor : <u>{{ $no_surat }}</u></p>
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes

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
                Kepada Yth:<br>
                General Manager ABWWT<br>
                PT. Perhutani Alam Wisata Risorsis<br>
                <br>
                Di<br>
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
                <b>SURABAYA</b>
            </td>
        </tr>
    </table>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table class="details-table" style="margin-left: 20px;">
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
                <td>{{ $cuti->user->jabatanTerbaru->jabatan->jenjang ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $cuti->user->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}</td>
            </tr>
        </table>

        <p style="text-align: justify;">
            Dengan ini mengajukan {{ $cuti->jenis_izin }} untuk tahun {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('Y') }} selama {{ $cuti->jumlah_hari }} Hari kerja, terhitung tanggal {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($cuti->tgl_selesai)->isoFormat('D MMMM Y') }} dengan kesanggupan dan perjanjian sebagai berikut:
        </p>
        <ol type="a" style="padding-left: 40px; text-align: justify;">
            <li>Sebelum menjalankan cuti tersebut, akan menyerahkan pekerjaan yang menjadi tanggung jawab saya kepada atasan saya langsung.</li>
            <li>Setelah selesai menjalankan cuti saya akan segera melaporkan diri kepada atasan saya langsung dan bekerja kembali seperti biasa.</li>
            <li>Keperluan cuti: {{ $cuti->keterangan }}</li>
            <li>Selama menjalankan cuti tersebut, alamat saya: {{ $cuti->user->alamat ?? '...' }}</li>
            <li>No HP: {{ $cuti->user->no_telp ?? '...' }}</li>
        </ol>
        <p>Demikian surat permintaan ini saya buat untuk dapat dipertimbangkan dan mendapat persetujuan.</p>
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
                <b style="text-decoration: underline;">SURABAYA</b>
            </div>

            <div class="section" style="margin-top: 20px;">
                Yang bertanda tangan di bawah ini:
                <table class="details-table">
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td>{{ $nama_pemohon }}</td>
                    </tr>
                    <tr>
                        <td>NPK</td>
                        <td>:</td>
                        <td>{{ $npk_pemohon }}</td>
                    </tr>
                    <tr>
                        <td>Pangkat/Gol Ruang</td>
                        <td>:</td>
                        <td>{{ $jenjang }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>{{ $jabatan }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <p>
                    Dengan ini mengajukan Cuti Tahunan untuk tahun {{ date('Y') }} selama **{{ $jml_hari }}** ({{ ucwords(\Terbilang::make($jml_hari)) }}) Hari kerja, terhitung tanggal **{{ $tgl_mulai }} & {{ $tgl_selesai }}** dengan kesanggupan dan perjanjian sebagai berikut:
                </p>
                <ol class="indent-list">
                    <li>Sebelum menjalankan cuti tersebut, akan menyerahkan pekerjaan yang menjadi tanggung jawab saya kepada atasan saya langsung.</li>
                    <li>Setelah selesai menjalankan cuti saya akan segera melaporkan diri kepada atasan saya langsung dan bekerja kembali seperti biasa.</li>
                    <li>Keperluan Cuti : **{{ $keperluan_cuti }}**</li>
                    <li>Selama menjalankan cuti tersebut, alamat saya: **{{ $alamat_cuti }}**</li>
                    <li>No HP: **{{ $no_hp }}**</li>
                </ol>
            </div>

            <p style="margin-top: 15px;">Demikian surat permintaan ini saya buat untuk dapat dipertimbangkan dan mendapat persetujuan.</p>

            <p style="text-align: right; margin-top: 20px;">Hormat saya,</p>
        </div>

        <div class="signatures clearfix">

            <table class="notes-table">
                <tr>
                    {{-- CATATAN PEJABAT SDM (Kiri) --}}
                    <td style="border-right: none;">
                        <div class="notes-block">
                            <p style="font-weight: bold; text-decoration: underline;">CATATAN PEJABAT SDM</p>
                            <p style="margin-top: 5px; margin-bottom: 5px;">Cuti yang telah diambil dalam tahun yang bersangkutan</p>
                            <div class="note-row"><span>1. Cuti Tahunan</span><span>: **{{ $cuti_telah_diambil }}**</span></div>
                            <div class="note-row"><span>2. Cuti Besar</span><span>: **{{ $cuti_besar }}**</span></div>
                            <div class="note-row"><span>3. Cuti Sakit</span><span>: **{{ $cuti_sakit }}**</span></div>
                            <div class="note-row"><span>4. Cuti Bersalin</span><span>: **{{ $cuti_bersalin }}**</span></div>
                            <div class="note-row"><span>5. Cuti Karena Alasan Penting</span><span>: **{{ $cuti_alasan_penting }}**</span></div>
                            <div class="note-row"><span>6. Sisa Cuti Tahunan</span><span>: **{{ $sisa_cuti }}**</span></div>
                        </div>
                    </td>
                    {{-- CATATAN/PERTIMBANGAN ATASAN LANGSUNG (Kanan Atas) --}}
                    <td>
                        <div class="notes-block">
                            <p style="font-weight: bold; text-decoration: underline;">CATATAN/PERTIMBANGAN ATASAN LANGSUNG</p>
                            <p>Senior Analis Pengelolaan Destinasi</p>
                            <p>Area Bisnis Wisata Wilayah Timur</p>
                            <div class="qr-code">
                                <img src="data:image/svg+xml;base64,{{ $qrCode ?? '' }}" alt="QR Code">
                            </div>
                            <b style="text-decoration: underline;">{{ $cuti->ssdm->nama_lengkap ?? '...' }}</b><br>
                            NPK. {{ $cuti->ssdm->nik ?? '1111111111' }}
                        </div>
                    </td>
                </tr>
            </table>

            <table class="signatures-table">
                <tr>
                    {{-- MENGETAHUI (Kiri Bawah) --}}
                    <td>
                        <p>MENGETAHUI</p>
                        <p>Senior Analis Keu, SDM & Umum</p>
                        <p>Area Bisnis Wisata Wilayah Timur</p>
                        <div class="signature-space"></div>
                        <b style="text-decoration: underline;">{{ $cuti->sdm->nama_lengkap ?? '...' }}</b><br>
                        NPK. {{ $cuti->sdm->nik ?? '1111111111' }}
                    </td>
                    {{-- KOSONG (Tengah Bawah, sesuai gambar) --}}
                    <td>
                        <div class="signature-space"></div>
                    </td>
                    {{-- PEJABAT YANG BERWENANG MENYETUJUI (Kanan Bawah) --}}
                    <td>
                        <p>PEJABAT YANG BERWENANG</p>
                        <p>MENYETUJUI CUTI</p>
                        <p>General Manager</p>
                        <p>Area Bisnis Wisata Wilayah Timur</p>
                        <div class="signature-space"></div>
                        <b style="text-decoration: underline;">{{ $cuti->gm->nama_lengkap ?? '...' }}</b><br>
                        NPK. {{ $cuti->gm->nik ?? '1111111111' }}
                    </td>
                </tr>
            </table>
        </div>
>>>>>>> Stashed changes
    </div>

    <table class="signature-table" style="margin-top: 30px;">
        <tr>
            {{-- KOLOM KIRI --}}
            <td style="width: 50%; vertical-align: top;">
                <b class="text-underline">CATATAN PEJABAT SDM</b><br>
                Cuti yang telah diambil dalam tahun yang bersangkutan
                <table class="catatan-sdm-table">
                    <tr><td>1. Cuti Tahunan</td><td>: {{ $riwayatCuti['Cuti Tahunan'] ?? 0 }}</td></tr>
                    <tr><td>2. Cuti Besar</td><td>: {{ $riwayatCuti['Cuti Besar'] ?? 0 }}</td></tr>
                    <tr><td>3. Cuti Sakit</td><td>: {{ $riwayatCuti['Cuti Sakit'] ?? 0 }}</td></tr>
                    <tr><td>4. Cuti Bersalin</td><td>: {{ $riwayatCuti['Cuti Bersalin'] ?? 0 }}</td></tr>
                    <tr><td>5. Cuti Karena Alasan Penting</td><td>: {{ $riwayatCuti['Cuti Alasan Penting'] ?? 0 }}</td></tr>
                    <tr><td>6. Sisa Cuti Tahunan</td><td>: {{ $cuti->user->jatah_cuti ?? '...' }}</td></tr>
                </table>
                <br><br>
                <div class="text-center">
                    Mengetahui<br>
                    Senior Analis Keu, SDM & Umum<br>
                    Area Bisnis Wisata Wilayah Timur
                    <div class="signature-space"></div>
                    <b class="text-underline">{{ $cuti->sdm->nama_lengkap ?? '...' }}</b><br>
                    NPK. {{ $cuti->sdm->nik ?? '...' }}
                </div>
            </td>

            {{-- KOLOM KANAN --}}
            <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                <div class="text-center">
                    Hormat saya,
                    <div class="signature-space"></div>
                    <b class="text-underline">{{ $cuti->user->nama_lengkap ?? '...' }}</b><br>
                    NPK. {{ $cuti->user->nik ?? '...' }}
                </div>
                <br>
                <div class="signature-box">
                    <b class="text-underline">CATATAN/PERTIMBANGAN ATASAN LANGSUNG</b><br>
                    {{ $cuti->ssdm->jabatanTerbaru->jabatan->nama_jabatan ?? 'Senior...' }}<br>
                    Area Bisnis Wisata Wilayah Timur
                    <div style="height: 80px; margin-top:5px; margin-bottom:5px;">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 80px; height: 80px; margin: auto;">
                    </div>
                    <b class="text-underline">{{ $cuti->ssdm->nama_lengkap ?? '...' }}</b><br>
                    NPK. {{ $cuti->ssdm->nik ?? '...' }}
                </div>
                <br>
                <div class="signature-box">
                    <b class="text-underline">PEJABAT YANG BERWENANG MENYETUJUI CUTI</b><br>
                    General Manager<br>
                    Area Bisnis Wisata Wilayah Timur
                    <div class="signature-space"></div>
                    <b class="text-underline">{{ $cuti->gm->nama_lengkap ?? '...' }}</b><br>
                    NPK. {{ $cuti->gm->nik ?? '...' }}
                </div>
            </td>
        </tr>
    </table>

</body>
</html>


<!DOCTYPE html>
<html>
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
                Di<br>
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


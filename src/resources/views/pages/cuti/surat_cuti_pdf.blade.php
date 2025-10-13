<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Permohonan {{ ucwords(strtolower($cuti->jenis_izin)) }}</title>
    <style>
        /* ... (CSS Anda yang lain tetap sama) ... */
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

        .details-table td {
            vertical-align: top;
            padding: 0;
            line-height: 1.3;
        }
        .details-table td:first-child { width: 180px; }
        .details-table td:nth-child(2) { width: 10px; }

        /* --- PERUBAHAN DI SINI --- */
        .approval-section {
            margin-top: 15px;
            width: 100%;
            border: none; /* Menghilangkan border luar */
        }
        .approval-section td {
            vertical-align: top;
            padding: 4px 8px;
        }
        .approval-section .sdm-notes {
            width: 45%;
            border-right: none; /* Menghilangkan border kanan */
            border-bottom: none; /* Menghilangkan border bawah */
        }
        .approval-section .ssdm-notes {
            width: 55%;
            border-bottom: none; /* Menghilangkan border bawah */
            text-align: center;
        }
        .approval-section .sdm-approval {
            width: 50%;
            border-right: none; /* Menghilangkan border kanan */
            text-align: center;
        }
        /* --- AKHIR PERUBAHAN --- */

        .approval-section .gm-approval {
            width: 50%;
            text-align: center;
        }

        .sdm-details-table { width: 100%; }
        .sdm-details-table td { padding: 0; }
        .sdm-details-table td:first-child { width: 70%; }
        .sdm-details-table td:last-child { text-align: left; }

        .signature-space { height: 50px; }
        .qr-code {
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div>
        <table>
            <tr>
                {{-- KOLOM KIRI: Berisi Perihal, Nomor, dan Kepada Yth --}}
                <td style="width: 50%; vertical-align: top;">

                    {{-- Bagian Perihal & Nomor --}}
                    <table>
                        <tr>
                            <td style="width: 80px;">Perihal</td>
                            <td>: <b>PERMOHONAN {{ strtoupper($cuti->jenis_izin) }}</b></td>
                        </tr>
                        <tr>
                            <td>Nomor</td>
                            <td>: {{ $cuti->no_surat }}</td>
                        </tr>
                    </table>

                    {{-- Bagian "Kepada Yth:" yang dipindahkan ke sini --}}
                    <div style="margin-top: 20px;">
                        Kepada Yth:<br>
                        General Manager ABWWT<br>
                        PT. Perhutani Alam Wisata Risorsis<br>
                        Di<br>
                        <b>SURABAYA</b>
                    </div>

                </td>

                {{-- KOLOM KANAN: Hanya berisi tanggal --}}
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    Surabaya, {{ \Carbon\Carbon::parse($cuti->created_at)->isoFormat('D MMMM Y') }}
                </td>
            </tr>
        </table>

        <p style="margin-top: 20px;">Yang bertanda tangan di bawah ini:</p>
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
                <td>Pangkat/Gol Ruang</td>
                <td>:</td>
                <td>{{ $karyawan->jabatanTerbaru->jabatan->jenjang ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}</td>
            </tr>
        </table>

        <p style="text-align: justify;">
            Dengan ini mengajukan {{ $cuti->jenis_izin }} untuk tahun {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('Y') }} selama {{ $cuti->jumlah_hari }} ({{ ucwords(\Terbilang::make($cuti->jumlah_hari)) }}) Hari kerja, terhitung tanggal {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($cuti->tgl_selesai)->isoFormat('D MMMM Y') }} dengan kesanggupan dan perjanjian sebagai berikut:
        </p>
        <ol type="a" style="padding-left: 40px; text-align: justify; margin: 0;">
            <li>Sebelum menjalankan cuti tersebut, akan menyerahkan pekerjaan yang menjadi tanggung jawab saya kepada atasan saya langsung.</li>
            <li>Setelah selesai menjalankan cuti saya akan segera melaporkan diri kepada atasan saya langsung dan bekerja kembali seperti biasa.</li>
        </ol>

        <table class="details-table" style="margin-top: 15px;">
             <tr>
                <td>Keperluan Cuti</td>
                <td>:</td>
                <td>{{ $cuti->keterangan }}</td>
            </tr>
            <tr>
                <td>Selama menjalankan cuti tersebut, alamat saya</td>
                <td>:</td>
                <td>{{ $karyawan->alamat ?? '...' }}</td>
            </tr>
            <tr>
                <td>No HP</td>
                <td>:</td>
                <td>{{ $karyawan->no_telp ?? '...' }}</td>
            </tr>
        </table>

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

        <table class="approval-section">
            <tr>
                <td class="sdm-notes">
                    <b class="text-underline">CATATAN PEJABAT SDM</b><br>
                    Cuti yang telah diambil dalam tahun yang bersangkutan
                    <table class="sdm-details-table">
                        <tr><td>1. Cuti Tahunan</td><td>: ... hari</td></tr>
                        <tr><td>2. Cuti Besar</td><td>: ... hari</td></tr>
                        <tr><td>3. Cuti Sakit</td><td>: ... hari</td></tr>
                        <tr><td>4. Cuti Bersalin</td><td>: ... hari</td></tr>
                        <tr><td>5. Cuti Karena Alasan Penting</td><td>: ... hari</td></tr>
                        <tr><td>6. Sisa Cuti Tahunan</td><td>: {{ $karyawan->jatah_cuti ?? '...' }} hari</td></tr>
                    </table>
                </td>
                <td class="ssdm-notes">
                    <b class="text-underline">CATATAN/ PERTIMBANGAN ATASAN LANGSUNG</b><br>
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
                        <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="width: 50px; height: 50px;">
                    </div>
                    <b class="text-underline">{{ $gm->nama_lengkap ?? '...' }}</b><br>
                    NPK. {{ $gm->nik ?? '...' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

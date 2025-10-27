<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat SPPD - {{ $sppd->no_surat ?? '-' }}</title>
    <style>
        /* Gaya umum */
        @page { margin: 18mm 12mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.25; color:#333; }
        .container { width:100%; }
        table { width:100%; border-collapse: collapse; }
        hr { border-top: 1px solid #000; margin: 8px 0; }
        .text-center { text-align:center; }

        /* Gaya Tabel Utama (Bordered Table) */
        .bordered-table { margin-top: 20px; table-layout: fixed; }
        /* Menggunakan padding lebih kecil agar mirip Excel */
        .bordered-table th, .bordered-table td {
            border: 1px solid #000;
            padding: 3px 5px; /* Disesuaikan */
            vertical-align: top;
        }
        .bordered-table th { background-color: #f2f2f2; }
        .bordered-table td { height: auto; } /* Hapus tinggi tetap */
        .sub-item-cell { padding-left: 25px; } /* Untuk indentasi a, b, c */

        /* Gaya TTD */
        .ttd-box { width: 45%; text-align: center; }
        .ttd-spacer { width: 10%; }
        .qr-code-container { margin: 5px 0 10px 0; }
        .qr-code-image { width: 80px; height: 80px; display: block; margin: 0 auto; } /* Disesuaikan agar tidak terlalu besar */

        /* Mengatur posisi dan ukuran logo agar lebih mirip Excel */
        .logo-cell { width: 20%; vertical-align:middle; text-align: left; }
        .logo-image { max-height: 50px; }

        /* Mengatur font header */
        .header-text { line-height: 1.1; }

    </style>
</head>
<body>
    {{-- helper embed image --}}
    @php
    $embed = function($relativePath) {
        $full = public_path($relativePath);
        if (!file_exists($full)) {
            return '';
        }
        $mime = @mime_content_type($full) ?: 'image/png';
        $data = base64_encode(file_get_contents($full));
        return "data:{$mime};base64,{$data}";
    };
    @endphp

    <div class="container">
        <table>
            <tr>
                <td class="logo-cell">
                    {{-- Logo perusahaan --}}
                    <img src="{{ $embed('images/logo2.jpg') }}" alt="Logo" class="logo-image">
                </td>
                <td style="width:60%;" class="text-center header-text">
                    <div style="font-weight:bold; text-transform:uppercase; font-size:12px;">PT. PERHUTANI ALAM WISATA RISORSIS</div>
                    <div style="font-weight:bold; text-transform:uppercase; font-size:12px;">Area Bisnis Wisata Wilayah Timur</div>
                    <div style="font-weight:bold; text-transform:uppercase; font-size:12px;">GRAHA PERHUTANI SURABAYA</div>
                    <div style="font-size:10px;">JL. GENTENGKALI NO. 49 SURABAYA</div>
                    <div style="font-weight:bold; font-size:12px; margin-top:6px;">SURAT PERINTAH PERJALANAN DINAS</div>
                </td>
                <td style="width:20%;"></td>
            </tr>
        </table>

        <hr>

        <div class="text-center" style="margin-bottom:8px;">
            <strong style="font-size: 11px;">NOMOR : {{ $sppd->no_surat ?? '000/ABW/WIL.TIMUR/VIII/2025' }}</strong>
        </div>

        <table class="bordered-table">
    {{-- BARIS 1 - Pemberi Perintah --}}
    <tr>
        <td style="width:5%;">1.</td>
        <td style="width:35%;">Pemberi Perintah</td>
        <td style="width:60%;">{{ $sppd->pemberi_tugas ?? 'General Manager' }}</td>
    </tr>
    {{-- BARIS 2 - Pejabat yang diperintah --}}
    <tr>
        <td>2.</td>
        <td>Pejabat yang diperintah</td>
        <td>{{ $sppd->user->nama_lengkap ?? 'Indra Aqurtiana' }}</td>
    </tr>
    {{-- BARIS 3 - Jabatan Golongan Kategori SPPD --}}
    <tr>
        <td>3.</td>
        <td>Jabatan Golongan Kategori SPPD</td>
        <td>{{ $sppd->user->jabatanTerbaru->jabatan->nama_jabatan ?? 'Manager Rentstand' }}</td>
    </tr>
    {{-- BARIS 4 - Maksud Perjalanan Dinas --}}
    <tr>
        <td>4.</td>
        <td>Maksud Perjalanan Dinas</td>
        <td>{{ $sppd->keterangan_sppd ?? 'Mendampingi Kunjungan Kejaksaan Tinggi Jawa Timur di Site Papuma' }}</td>
    </tr>
    {{-- BARIS 5 - Alat Angkutan --}}
    <tr>
        <td>5.</td>
        <td>Alat Angkutan yang dipergunakan</td>
        <td>{{ $sppd->alat_angkat ?? 'Dinas' }}</td>
    </tr>
    {{-- BARIS 6 - Tempat Berangkat --}}
    <tr>
        <td>6.</td>
        <td>Tempat Berangkat</td>
        <td>{{ $sppd->lokasi_berangkat ?? 'Kantor ABWWT Surabaya' }}</td>
    </tr>
    {{-- BARIS 7 - Tempat Tujuan --}}
    <tr>
        <td>7.</td>
        <td>Tempat Tujuan</td>
        <td>{{ $sppd->lokasi_tujuan ?? 'Papuma' }}</td>
    </tr>
    {{-- BARIS 8 - Lama Perjalanan Dinas dan Sub-Poin --}}
    <tr>
        {{-- Item 8 di Excel adalah "Lama Perjalanan Dinas" dengan sub-poin --}}
        <td>8.</td>
        <td>Lama Perjalanan Dinas</td>
        <td>{{ $sppd->jumlah_hari ?? '1' }} Hari</td>
    </tr>
    <tr>
        <td></td>
        <td class="sub-item-cell">a. Tanggal Berangkat</td>
        <td>{{ \Carbon\Carbon::parse($sppd->tgl_mulai ?? '2025-07-04')->format('d F Y') }}</td>
    </tr>
    <tr>
        <td></td>
        <td class="sub-item-cell">b. Tanggal Kembali</td>
        <td>{{ \Carbon\Carbon::parse($sppd->tgl_selesai ?? '2025-07-04')->format('d F Y') }}</td>
    </tr>
    {{-- BARIS 9 - Pembebanan Anggaran (Dibuat dua kolom agar mirip Excel) --}}
    <tr>
        <td>9.</td>
        <td>
            Pembebanan Anggaran
        </td>
        <td style="border: none; padding: 0;">
            {{-- Menggunakan tabel kecil di dalam cell agar sejajar --}}
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 30%; border: 1px solid #000; border-right: none; padding: 3px 5px;"><strong>REKENING</strong></td>
                    <td style="width: 70%; border: 1px solid #000; padding: 3px 5px;">{{ $sppd->rekening ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="width: 30%; border: 1px solid #000; border-top: none; border-right: none; padding: 3px 5px;"><strong>NAMA REKENING</strong></td>
                    <td style="width: 70%; border: 1px solid #000; border-top: none; padding: 3px 5px;">{{ $sppd->nama_rekening ?? '-' }}</td>
                </tr>
            </table>
        </td>
    </tr>
    {{-- BARIS 10 - Keterangan Lain (Ini adalah item terakhir, tanpa nomor di Excel, tapi dibuat terpisah) --}}
    <tr>
        <td></td>
        <td>Keterangan Lain</td>
        <td>{{ $sppd->keterangan_lain ?? '-' }}</td>
    </tr>
</table>

        <div style="margin-top:30px;">
            <table style="width:100%;">
                <tr>
                    {{-- BLOK KIRI: Yang diberi Perintah (Sesuai Gambar Excel) --}}
                    <td class="ttd-box">
                        <p>Yang diberi Perintah</p>
                        <br><br><br>
                        {{-- Jarak untuk tanda tangan manual --}}
                        <p style="font-weight:bold; margin-top: 50px;">{{ $sppd->user->nama_lengkap ?? 'Indra Aqurtiana' }}</p>
                    </td>

                    <td class="ttd-spacer"></td>

                    {{-- BLOK KANAN: Pemberi Perintah/Pejabat yang Menyetujui (Sesuai Gambar Excel) --}}
                    <td class="ttd-box">
                        <p>Dikeluarkan di Surabaya</p>
                        <p>Pada Tanggal {{ \Carbon\Carbon::parse($sppd->tgl_persetujuan ?? '2025-07-04')->format('d F Y') }}</p>
                        <p>Yang memberi perintah,</p>
                        <p style="font-weight:bold; margin-bottom: 5px;">General Manager</p>

                        <div class="qr-code-container">
                            @if(isset($qrCodeBase64) && $qrCodeBase64)
                                <img src="{{ $qrCodeBase64 }}" alt="QR Code Verifikasi" class="qr-code-image">
                            @else
                                {{-- Placeholder jika QR code gagal dimuat --}}
                                <div style="width: 80px; height: 80px; margin: 0 auto; border: 1px solid #000; text-align: center; font-size: 8px; line-height: 80px;">TTD Digital</div>
                            @endif
                        </div>

                        <p style="font-weight:bold; margin-top: 5px;">
                            {{ $sppd->penyetuju->nama_lengkap ?? 'Andy Irvindarta' }}
                        </p>
                        <p style="margin-top: -5px; font-size: 10px;">(Tanda Tangan Elektronik)</p>

                    </td>
                </tr>
            </table>
        </div>

        {{-- Tabel Pelaporan Perjalanan Dinas --}}
        <div class="container bordered-table" style="page-break-before: always;">
            <table style="width: 100%;">
                <tr style="height: 25px;">
                    <td colspan="5" style="border: none; padding: 0;">
                        <div style="font-weight:bold; font-size:12px; margin-bottom: 5px;">LAPORAN PERJALANAN DINAS</div>
                    </td>
                </tr>
                <thead>
                    <tr>
                        <th style="width:5%;">No.</th>
                        <th style="width:15%;">Datang</th>
                        <th style="width:15%;">Pulang</th>
                        <th style="width:30%;">Tempat Tujuan</th>
                        <th style="width:35%;">Pejabat (TTD) & Stempel</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 5; $i++)
                    <tr>
                        <td>{{ $i }}.</td>
                        <td style="height: 35px;"></td>
                        <td></td>
                        <td></td>
                        <td style="height: 35px;"></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
            <p style="margin-top:10px; font-size: 9px;">Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

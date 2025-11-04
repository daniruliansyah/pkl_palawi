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
        .text-center { text-align:center; }

        /* Gaya Tabel Utama (Bordered Table) */
        .bordered-table { margin-top: 20px; table-layout: fixed; }

        /* * =================================================================
         * PERBAIKAN CSS: (Dibagi 2 untuk THEAD dan TBODY)
         * Ini memperbaiki Halaman 2 yang blank.
         * =================================================================
         */

        /* Target Header (TH) di dalam THEAD */
        .bordered-table > thead > tr > th {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Target Data (TD) di dalam TBODY */
        .bordered-table > tbody > tr > td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .bordered-table th { background-color: #f2f2f2; }
        .bordered-table td { height: auto; }
        .sub-item-cell { padding-left: 25px; }

        /* Gaya TTD */
        .ttd-box { width: 45%; text-align: center; }
        .ttd-spacer { width: 10%; }
        .qr-code-container { margin: 5px 0 10px 0; }

        /* * =================================================================
         * PERBAIKAN CSS: (Koma diubah menjadi Titik Koma)
         * Ini memperbaiki QR Code yang membesar.
         * =================================================================
         */
        .qr-code-image { width: 80px; height: 80px; display: block; margin: 0 auto; }

        /* Mengatur posisi dan ukuran logo agar lebih mirip Excel */
        .logo-cell { width: 20%; vertical-align:middle; text-align: left; }
        .logo-image { max-height: 70px; }

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

        <div style="width:60%; margin: 8px auto; border-top: 1px solid #000;"></div>

        <div class="text-center" style="margin-bottom:8px;">
            <strong style="font-size: 11px;">NOMOR : {{ $sppd->no_surat ?? '000/ABW/WIL.TIMUR/VIII/2025' }}</strong>
        </div>

        <table class="bordered-table">
            <tbody>

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
                <td>{{ $sppd->user->nama_lengkap ?? '-' }}</td>
            </tr>
            {{-- BARIS 3 - Jabatan Golongan Kategori SPPD --}}
            <tr>
                <td>3.</td>
                <td>Jabatan Golongan Kategori SPPD</td>
                <td>{{ $sppd->user->jabatanTerbaru->jabatan->nama_jabatan ?? '-' }}</td>
            </tr>
            {{-- BARIS 4 - Maksud Perjalanan Dinas --}}
            <tr>
                <td>4.</td>
                <td>Maksud Perjalanan Dinas</td>
                <td>{{ $sppd->keterangan_sppd ?? '-' }}</td>
            </tr>
            {{-- BARIS 5 - Alat Angkutan --}}
            <tr>
                <td>5.</td>
                <td>Alat Angkutan yang dipergunakan</td>
                <td>{{ $sppd->alat_angkat ?? '-' }}</td>
            </tr>

            {{-- BARIS 6: Merge (rowspan) untuk No. 6 --}}
            <tr>
                <td style="width:5%;" rowspan="2">6.</td>
                <td>Tempat Berangkat</td>
                <td>{{ $sppd->lokasi_berangkat ?? '-' }}</td>
            </tr>
            <tr>
                <td class="sub-item-cell">Tempat Tujuan</td>
                <td>{{ $sppd->lokasi_tujuan ?? '-' }}</td>
            </tr>

            {{-- BARIS 7: Merge (rowspan) untuk No. 7 --}}
            <tr>
                <td style="width:5%;" rowspan="3">7.</td>
                <td>a. Lama Perjalanan Dinas</td>
                <td>{{ $sppd->jumlah_hari ?? '1' }} Hari</td>
            </tr>
            <tr>
                <td class="sub-item-cell">b. Tanggal Berangkat</td>
                <td>{{ \Carbon\Carbon::parse($sppd->tgl_mulai ?? '2025-07-04')->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="sub-item-cell">c. Tanggal Kembali</td>
                <td>{{ \Carbon\Carbon::parse($sppd->tgl_selesai ?? '2025-07-04')->format('d F Y') }}</td>
            </tr>


            {{-- BARIS 8: Pembebanan Anggaran (FIXED VISUALS) --}}
            <tr>
                <td style="width:5%;">8.</td>

                {{-- Kolom 2: Label --}}
                <td style="width:35%; padding:0; vertical-align: top;">
                    <table style="width:100%; border-collapse: collapse;">
                        <tr>
                            <td style="width:50%; border-right:1px solid #000; vertical-align: top; padding:3px 5px; height: 36px;">
                                Pembebanan Anggaran
                            </td>
                            <td style="width:50%; padding:0; vertical-align: top;">
                                <table style="width:100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding:3px 5px; border-bottom:1px solid #000; height: 18px;">No. Rekening</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 5px; height: 18px;">Nama Rekening</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>

                {{-- Kolom 3: Data --}}
                <td style="width:60%; padding:0; vertical-align: top;">
                    <table style="width:100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding:3px 5px; border-bottom:1px solid #000; height: 18px;">{{ $sppd->no_rekening ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 5px; height: 18px;">{{ $sppd->nama_rekening ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            {{-- BARIS 9 - Keterangan Lain --}}
            <tr>
                <td>9.</td>
                <td>Keterangan Lain</td>
                <td>{{ $sppd->keterangan_lain ?? '-'}}</td>
            </tr>

            </tbody> {{-- <-- PENUTUP TBODY --}}
        </table>

        <div style="margin-top:30px;">
            <table style="width:100%;">
                <tr>
                    {{-- BLOK KIRI: Yang diberi Perintah --}}
                    <td class="ttd-box">
                        <p>Yang diberi Perintah</p>
                        <br><br><br>
                        <p style="font-weight:bold; margin-top: 50px;">{{ $sppd->user->nama_lengkap ?? '-' }}</p>
                    </td>

                    <td class="ttd-spacer"></td>

                    {{-- BLOK KANAN: Pemberi Perintah/Pejabat --}}
                    <td class="ttd-box">
                        <p>Dikeluarkan di Surabaya</p>
                        <p>Pada Tanggal {{ \Carbon\Carbon::parse($sppd->tgl_persetujuan ?? '2025-07-04')->format('d F Y') }}</p>
                        <p>Yang memberi perintah,</p>

                        <p style="font-weight:bold; margin-bottom: 5px;">
                            @if($sppd->penyetuju && $sppd->penyetuju->jabatanTerbaru)
                                {{ $sppd->penyetuju->jabatanTerbaru->jabatan->nama_jabatan }}
                            @else
                                {{ $sppd->pemberi_tugas }}
                            @endif
                        </p>

                        <div class="qr-code-container">
                            @if(isset($qrCodeBase64) && $qrCodeBase64)
                                <img src="{{ $qrCodeBase64 }}" alt="QR Code Verifikasi" class="qr-code-image">
                            @else
                                <div style="width: 80px; height: 80px; margin: 0 auto; border: 1px solid #000; text-align: center; font-size: 8px; line-height: 80px;">TTD Digital</div>
                            @endif
                        </div>

                        <p style="font-weight:bold; margin-top: 5px;">
                            {{ $sppd->penyetuju->nama_lengkap ?? '-' }}
                        </p>
                        <p style="margin-top: -5px; font-size: 10px;">(Tanda Tangan Elektronik)</p>

                    </td>
                </tr>
            </table>
        </div>


        {{--
         * =================================================================
         * PERBAIKAN HTML: (Memindahkan class bordered-table)
         * Ini memperbaiki Halaman 2 yang blank.
         * =================================================================
         --}}

        {{-- Tabel Pelaporan Perjalanan Dinas --}}
        <div class="container" style="page-break-before: always;">
            <table class="bordered-table" style="width: 100%;"> {{-- <-- CLASS DIPINDAH KE SINI --}}
                <tr style="height: 25px;">
                    {{-- Hapus border dan padding dari sel judul --}}
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
            <p style="margin-top:10px; font-size: 9px;">Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        </div>
    </div>
</body>
</html>

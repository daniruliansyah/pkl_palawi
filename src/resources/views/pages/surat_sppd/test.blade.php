<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat SPPD - {{ $sppd->no_surat ?? '-' }}</title>
    <style>
        @page { margin: 18mm 12mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.25; color:#333; }
        .container { width:100%; }
        table { width:100%; border-collapse: collapse; }
        .no-border td { border:none; padding:2px 0; vertical-align: top; }
        hr { border-top: 1px solid #000; margin: 8px 0; }
        .signature-image { max-height:80px; margin-top:8px; }
        .text-center { text-align:center; }

        /* CSS untuk tabel tambahan */
        .bordered-table { margin-top: 20px; table-layout: fixed; }
        .bordered-table th, .bordered-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top; /* Mengatur konten ke atas sel */
        }
        .bordered-table th { background-color: #f2f2f2; }
        .bordered-table td { height: 35px; } /* Menambahkan tinggi minimal untuk setiap sel data */

        /* Menentukan lebar kolom */
        .col-no { width: 5%; }
        .col-datang-pulang { width: 20%; }
        .col-tujuan { width: 35%; }
        .col-pejabat { width: 20%; } /* Atur lebar sesuai kebutuhan, total harus 100% */
    </style>
</head>
<body>
    {{-- helper embed image --}}
    @php
        $embed = function($relativePath) {
            $full = public_path($relativePath);
            if (!file_exists($full)) return '';
            $mime = @mime_content_type($full) ?: 'image/png';
            $data = base64_encode(file_get_contents($full));
            return "data:{$mime};base64,{$data}";
        };
    @endphp

    <div class="container">
        <table style="margin-bottom:10px;">
            <tr>
                <td style="width:20%; vertical-align:middle;">
                    <img src="{{ $embed('images/econique.jpg') }}" alt="Logo" style="max-height:80px;">
                </td>
                <td style="width:60%;" class="text-center">
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
            <strong>NOMOR : {{ $sppd->no_surat ?? '-' }}</strong>
        </div>

        <p>Yang bertanda tangan di bawah ini:</p>
        <table class="no-border">
            <tr><td>Nama</td><td>: {{ $sppd->pemberi_tugas }}</td></tr>
            <tr><td>Jabatan</td><td>: {{ $sppd->pemberi_tugas }}</td></tr>
        </table>

        <p style="margin-top:12px;">Memberikan perintah perjalanan dinas kepada:</p>
        <table class="no-border">
            <tr><td>Nama</td><td>: {{ $sppd->user->nama_lengkap ?? '-' }}</td></tr>
            <tr><td>Jabatan</td><td>: {{ $sppd->user->jabatanTerbaru->jabatan->nama_jabatan ?? '-' }}</td></tr>
        </table>

        <p style="margin-top:12px;">Untuk melaksanakan perjalanan dinas dengan tujuan:</p>
        <table class="no-border">
            <tr><td>Lokasi Berangkat</td><td>: {{ $sppd->lokasi_berangkat }}</td></tr>
            <tr><td>Lokasi Tujuan</td><td>: {{ $sppd->lokasi_tujuan }}</td></tr>
            <tr><td>Lama</td><td>: {{ $sppd->jumlah_hari }} hari</td></tr>
            <tr><td>Tanggal Berangkat</td><td>: {{ \Carbon\Carbon::parse($sppd->tgl_mulai)->format('d F Y') }}</td></tr>
            <tr><td>Tanggal Kembali</td><td>: {{ \Carbon\Carbon::parse($sppd->tgl_selesai)->format('d F Y') }}</td></tr>
            <tr><td>Maksud</td><td>: {{ $sppd->keterangan_sppd }}</td></tr>
            <tr><td>Alat Angkut</td><td>: {{ $sppd->alat_angkat }}</td></tr>
        </table>

        <p style="margin-top:12px;">Demikian surat ini dibuat untuk dilaksanakan dengan sebaik-baiknya.</p>

        <div style="margin-top:30px;">
            <table style="width:100%;">
                <tr>
                    <td style="text-align:center; width:45%;">
                        <p>Dikeluarkan di Surabaya,</p>
                        <p> Pada Tanggal, {{ \Carbon\Carbon::parse($sppd->created_at)->format('d F Y') }}</p>
                        <p>Yang memberi perintah,</p>
                        <p style="font-weight:bold;">{{ $sppd->pemberi_tugas }}</p>
                        @if($sppd->pemberi_tugas === 'General Manager')
                            <img src="{{ $embed('images/barcode_gm.jpg') }}" alt="ttd GM" class="signature-image">
                        @elseif($sppd->pemberi_tugas === 'Senior Analis Keuangan, SDM & Umum')
                            <img src="{{ $embed('images/barcode_sdm.jpg') }}" alt="ttd SDM" class="signature-image">
                        @endif
                        <p style="font-weight:bold;">
                            @if($sppd->penyetuju)
                                {{ $sppd->penyetuju->nama_lengkap }}
                            @else
                                (Nama Lengkap)
                            @endif
                        </p>
                    </td>

                    <td style="width:10%;"></td>

                    <td style="text-align:center; width:45%;">
                        <p>Yang diberi Perintah</p>
                        <br><br><br>
                        <p style="font-weight:bold;">{{ $sppd->user->nama_lengkap ?? '-' }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="container bordered-table" style="page-break-before: always;">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-datang-pulang">Datang</th>
                        <th class="col-datang-pulang">Pulang</th>
                        <th class="col-tujuan">Tempat Tujuan</th>
                        <th class="col-pejabat">Pejabat (TTD) & Stempel</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 5; $i++)
                    <tr>
                        <td>{{ $i }}.</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="height: 60px;"></td> </tr>
                    @endfor
                </tbody>
            </table>
            <p style="margin-top:10px;">Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

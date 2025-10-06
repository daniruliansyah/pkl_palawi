<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan {{ $sp->jenis_sp ?? '...' }}</title>
    <style>
        @page {
            /* Margin A4: 25mm 25mm 30mm 25mm */
            margin: 25mm 25mm 30mm 25mm;
        }

        body {
            position: relative;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color:#000;
        }

        .container {
            width:100%;
            position: relative;
            z-index: 10;
        }

        table { width:100%; border-collapse: collapse; }

        /* HEADER */
        .header-section { margin-bottom: 25px; }
        .logo-container { width: 100%; text-align: left; vertical-align: top; }
        .logo-image {
            max-height: 100px; /* Ukuran logo HEADER: DITINGKATKAN dari 50px */
            width: auto; /* Memastikan rasio aspek terjaga */
        }
        .date-right {
            text-align: right;
            font-size: 11pt;
            vertical-align: top;
            white-space: nowrap;
        }

        /* WATERMARK (Fix untuk DOMPDF: menggunakan transform translate) */
        .watermark {
            position: fixed;
            top: 50%;
            left: 40%;
            width: 350px; /* Ukuran watermark: DITINGKATKAN dari 250px */
            height: auto; /* Memastikan rasio aspek terjaga */
            opacity: 0.08; /* Opacity watermark: DITINGKATKAN untuk lebih transparan */
            transform: translate(-50%, -50%); /* Tetap gunakan ini untuk centering */
            z-index: -1; /* Pastikan selalu di belakang konten */
        }

        /* PAGE BREAK */
        .page-break {
            page-break-before: always;
        }

        /* DETAIL SURAT */
        .detail-surat-table td { padding: 2px 0; vertical-align: top; }
        .detail-surat-table .label { width: 100px; }
        .detail-surat-table .separator { width: 10px; }

        /* ISI SURAT */
        .isi-surat { margin: 20px 0; text-align: justify; }
        .pernyataan-rekomendasi { margin: 30px 0; }
        .pernyataan-rekomendasi p { margin: 5px 0; }
        .nama-karyawan { font-weight: bold; text-decoration: underline; }
        .salam { margin-top: 20px; }

        /* TANDA TANGAN */
        .ttd-section { margin-top: 50px; }
        .ttd-section table { table-layout: fixed; }
        .ttd-section td { width: 50%; text-align: center; vertical-align: top; }
        .qr-code-image { width: 80px; height: 80px; display: block; margin: 5px auto; }

        /* TEMBUSAN */
        .tembusan { font-size: 10pt; }

        /* FOOTER (selalu muncul di semua halaman PDF) */
        .footer {
            position: fixed;
            bottom: 10mm;
            left: 25mm;
            right: 25mm;
            font-size: 8pt;
            text-align: justify;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            color: #000;
        }
    </style>
</head>
<body>

@php
    $embed = fn($relativePath) => file_exists(public_path($relativePath))
        ? 'data:' . (@mime_content_type(public_path($relativePath)) ?: 'image/png') . ';base64,' . base64_encode(file_get_contents(public_path($relativePath)))
        : '';

    $karyawan_sp = $sp->user ?? null;
    $logo_path = 'images/logo2.jpg'; // Path untuk logo header dan watermark (pastikan ini path yang benar ke logo Anda)
    $gm_name = 'GM Area Bisnis Wisata Wil.Timur';
    $gm_fullname = $gm->nama_lengkap ?? 'Nama Lengkap GM';
    $tembusan_list = $sp->tembusan ? json_decode($sp->tembusan) : [];
    $company_name = 'PT. Perhutani Alam Wisata Risorsis';
    $footer_surat_no = 'xxxxxx';
@endphp

<img src="{{ $embed($logo_path) }}" class="watermark" alt="Watermark">

<div class="container">

    <div class="header-section">
        <table>
            <tr>
                <td class="logo-container">
                    <img src="{{ $embed($logo_path) }}" alt="Logo" class="logo-image">
                </td>
                <td class="date-right">
                    Surabaya, {{ \Carbon\Carbon::parse($sp->tgl_sp_terbit ?? now())->isoFormat('D MMMM Y') }}
                </td>
            </tr>
        </table>
    </div>

    <table class="detail-surat-table">
        <tr>
            <td class="label">Nomor</td>
            <td class="separator">:</td>
            <td>{{ $sp->no_surat }}</td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td class="separator">:</td>
            <td>{{ $sp->file_bukti ? '1 (satu) Berkas Bukti Pelanggaran' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hal</td>
            <td class="separator">:</td>
            <td style="font-weight: bold;">{{ $sp->hal_surat ?? 'Surat Peringatan Pertama' }} {{ $karyawan_sp->nama_lengkap ?? '-' }}</td>
        </tr>
    </table>

    <div style="margin-top: 25px;">
        Kepada Yth.<br>
        <span style="font-weight: bold;">{{ $karyawan_sp->nama_lengkap ?? '-' }}</span><br>
        {{ $karyawan_sp->jabatanTerbaru->jabatan->nama_jabatan ?? '-' }}<br>
    </div>

    <div class="salam">
        Salam Sinergi,
    </div>

    <div class="isi-surat">
        {!! nl2br(e($sp->isi_surat ?? 'Paragraf isi surat ini diisi oleh user.')) !!}
    </div>

    <div class="pernyataan-rekomendasi">
        <p>
            Berdasarkan klasifikasi tersebut di atas, Tim Pertimbangan Kepegawaian dan General Manager Area Bisnis Wilayah Timur PT.Perhutani Alam Wisata Risorsis merekomendasikan
            <strong>Surat Peringatan {{ $sp->jenis_sp ?? 'Pertama' }}</strong> kepada Sdr.
            <span class="nama-karyawan">{{ $karyawan_sp->nama_lengkap ?? '-' }}</span>
            <em>{{ $karyawan_sp->jabatanTerbaru->jabatan->nama_jabatan ?? '-' }}</em> pada Kantor Area Bisnis Wisata Wilayah Timur PT. Perhutani Alam Wisata Risorsis.
        </p>
    </div>

    <div class="isi-surat" style="margin-top: 30px;">
        Demikian untuk menjadi perhatian dan lebih disiplin dalam melaksanakan tugas.
    </div>

    <div class="ttd-section">
        <table>
            <tr>
                <td></td>
                <td style="text-align: center;">
                    <p>{{ $gm_name }}</p>
                    <p>{{ $company_name }}</p>
                    <div class="qr-code">
                        @if(isset($qrCodeBase64) && $qrCodeBase64)
                            <img src="{!! $qrCodeBase64 !!}" alt="QR Code Verifikasi" class="qr-code-image">
                        @else
                            <div style="width: 80px; height: 80px; margin: 5px auto; border: 1px solid #000; text-align: center; font-size: 8px; line-height: 80px;">
                                TTD Digital
                            </div>
                        @endif
                    </div>
                    <p style="font-weight: bold; margin-top: 5px; text-decoration: underline;">{{ $gm_fullname }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="tembusan">
        <p style="font-weight: bold; margin-bottom: 5px;">Tembusan Kepada Yth:</p>
        <ol style="margin: 0; padding-left: 15px;">
            @foreach($tembusan_list as $jabatan)
                <li>{{ $jabatan }}</li>
            @endforeach

            @if (count($tembusan_list) == 0)
                <li>Tidak ada tembusan.</li>
            @endif
        </ol>
    </div>
</div>

<div class="footer">
    Dokumen ini telah ditandatangani secara elektronik sesuai surat Direktur Utama PT.Perhutani Wisata Alam Risorsis No.{{ $footer_surat_no }}
    perihal implementasi QR Code pada hasil keluaran surat menyurat Elektronik Lingkup PT.Perhutani Wisata Alam Risorsis (Group).
</div>

</body>
</html>

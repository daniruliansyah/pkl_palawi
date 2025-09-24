<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan - {{ $sp->no_surat ?? '-' }}</title>
    <style>
        @page { margin: 18mm 12mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.25; color:#333; }
        .container { width:100%; }
        table { width:100%; border-collapse: collapse; }
        .no-border td { border:none; padding:2px 0; vertical-align: top; }
        hr { border-top: 1px solid #000; margin: 8px 0; }
        .signature-image { max-height:80px; margin-top:8px; }
        .text-center { text-align:center; }
        .text-justify { text-align: justify; }
        .header-content { text-align: center; }
        .header-content .title { font-weight: bold; text-transform: uppercase; font-size: 14px; margin-bottom: 2px; }
        .header-content .subtitle { font-weight: bold; text-transform: uppercase; font-size: 12px; margin-top: 0; }
        .header-content .address { font-size: 10px; }
        .bordered-box { border: 1px solid #000; padding: 8px; margin-top: 10px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    @php
        $embed = function($relativePath) {
            $fullPath = Str::startsWith($relativePath, 'storage/')
                ? storage_path('app/public/' . Str::after($relativePath, 'storage/'))
                : public_path($relativePath);
            if (!file_exists($fullPath)) return '';
            $mime = @mime_content_type($fullPath) ?: 'image/png';
            $data = base64_encode(file_get_contents($fullPath));
            return "data:{$mime};base64,{$data}";
        };
    @endphp

    <div class="container">
        <!-- Header -->
        <table>
            <tr>
                <td style="width:20%; vertical-align:middle;">
                    <img src="{{ $embed('images/econique.jpg') }}" alt="Logo" style="max-height:80px;">
                </td>
                <td style="width:60%;" class="header-content">
                    <div class="title">PT. PERHUTANI ALAM WISATA RISORSIS</div>
                    <div class="subtitle">Area Bisnis Wisata Wilayah Timur</div>
                    <div class="subtitle">GRAHA PERHUTANI SURABAYA</div>
                    <div class="address">JL. GENTENGKALI NO. 49 SURABAYA</div>
                    <div class="subtitle" style="margin-top:6px;">SURAT PERINGATAN</div>
                </td>
                <td style="width:20%;"></td>
            </tr>
        </table>
        <hr>

        <!-- Nomor Surat -->
        <div class="text-center" style="margin-bottom:8px;">
            <strong>NOMOR : {{ $sp->no_surat ?? '-' }}</strong>
        </div>

        <!-- Isi Surat -->
        <p class="text-justify" style="margin-top:20px;">
            Berdasarkan hasil evaluasi kinerja terhadap sdr. / sdri. <strong>{{ $sp->user->nama_lengkap ?? '-' }}</strong>
            dengan NIP <strong>{{ $sp->user->nip ?? '-' }}</strong>, terhitung mulai tanggal
            <strong>{{ \Carbon\Carbon::parse($sp->tgl_mulai)->format('d F Y') }}</strong>
            sampai dengan <strong>{{ \Carbon\Carbon::parse($sp->tgl_selesai)->format('d F Y') }}</strong>,
            maka dengan ini perusahaan memberikan surat peringatan dengan ketentuan sebagai berikut:
        </p>

        <p class="text-justify">1. Perilaku yang melanggar: <strong>{{ $sp->ket_peringatan }}</strong></p>
        <p class="text-justify">2. Dengan dikeluarkannya surat peringatan ini, perusahaan mengharapkan yang bersangkutan dapat memperbaiki perilaku dan kinerja agar tidak terjadi pelanggaran yang sama di kemudian hari.</p>
        <p class="text-justify">3. Jika yang bersangkutan tidak menunjukkan perbaikan dalam jangka waktu yang telah ditentukan, perusahaan akan mengambil tindakan lebih lanjut sesuai dengan peraturan perusahaan yang berlaku.</p>

        <p class="text-justify" style="margin-top:20px;">
            Demikian surat peringatan ini dibuat untuk dipergunakan sebagaimana mestinya.
        </p>

        <!-- Tanda Tangan -->
        <div style="margin-top:40px;">
            <table style="width:100%;">
                <tr>
                    <!-- Karyawan -->
                    <td style="text-align:left; width:50%;">
                        <p>Diterima dan dibaca oleh,</p>
                        <br><br><br>
                        <p style="font-weight:bold; border-bottom: 1px solid #000; display:inline-block; padding-bottom:2px;">
                            {{ $sp->user->nama_lengkap ?? '-' }}
                        </p>
                        <p>NIP. {{ $sp->user->nip ?? '-' }}</p>
                    </td>

                    <!-- GM & SDM -->
                    <td style="text-align:right; width:50%;">
                        <p>Surabaya, {{ \Carbon\Carbon::parse($sp->tgl_sp_terbit)->format('d F Y') }}</p>
                        <table style="width:100%; text-align:right;">
                            <tr>
                                <td style="text-align:center; width:50%; padding-right:10px;">
                                    <p>{{ $ttd_gm->jabatan }}</p>
                                    <img src="{{ $embed($ttd_gm->ttd_path) }}" alt="Tanda Tangan GM" class="signature-image">
                                    <p style="font-weight:bold; border-bottom: 1px solid #000; display:inline-block; padding-bottom:2px;">
                                        {{ $ttd_gm->nama_lengkap }}
                                    </p>
                                    <p>NIP. {{ $ttd_gm->nip }}</p>
                                </td>
                                <td style="text-align:center; width:50%; padding-left:10px;">
                                    <p>{{ $ttd_sdm->jabatan }}</p>
                                    <img src="{{ $embed($ttd_sdm->ttd_path) }}" alt="Tanda Tangan SDM" class="signature-image">
                                    <p style="font-weight:bold; border-bottom: 1px solid #000; display:inline-block; padding-bottom:2px;">
                                        {{ $ttd_sdm->nama_lengkap }}
                                    </p>
                                    <p>NIP. {{ $ttd_sdm->nip }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Lampiran Bukti -->
        @if($sp->file_bukti)
        <div class="page-break">
            <div class="text-center" style="margin-top:20px;">
                <h2 style="font-size:16px; margin:0;">LAMPIRAN BUKTI PELANGGARAN</h2>
            </div>
            @php
                $fileExtension = pathinfo($sp->file_bukti, PATHINFO_EXTENSION);
            @endphp
            <div style="margin-top:20px; text-align:center;">
                @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ $embed('storage/' . $sp->file_bukti) }}" alt="Bukti Pelanggaran" style="max-width:100%; height:auto;">
                @else
                    <p style="color:#666;">
                        *File bukti yang diunggah tidak dapat ditampilkan secara langsung karena bukan format gambar.
                        <br>Silakan lihat file asli ({{ strtoupper($fileExtension) }}) untuk bukti lengkap.
                    </p>
                @endif
            </div>
        </div>
        @endif
    </div>
</body>
</html>

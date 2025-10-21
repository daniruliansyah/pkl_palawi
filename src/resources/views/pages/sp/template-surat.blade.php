<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan {{ $sp->jenis_sp ?? '...' }}</title>
    <style>
        @page { margin: 25mm 25mm 30mm 25mm; }
        body { position: relative; font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color:#000; }
        .container { width:100%; position: relative; z-index: 10; }
        table { width:100%; border-collapse: collapse; }
        .header-section { margin-bottom: 25px; }
        .logo-container { width: 100%; text-align: left; vertical-align: top; }
        
        /* PERBAIKAN 3: Logo kop surat diperbesar */
        .logo-image { max-height: 85px; width: auto; } /* Ukuran diubah dari 70px menjadi 85px */
        
        .date-right { text-align: right; font-size: 11pt; vertical-align: top; white-space: nowrap; }
        
        /* PERBAIKAN 1: CSS untuk watermark sudah benar (center). Jika masih kurang pas, kemungkinan ada spasi kosong di file gambar logo itu sendiri. */
        .watermark { position: fixed; top: 50%; left: 50%; width: 350px; height: auto; opacity: 0.08; transform: translate(-50%, -50%); z-index: -1; }
        
        .page-break { page-break-before: always; }
        .detail-surat-table td { padding: 2px 0; vertical-align: top; }
        .detail-surat-table .label { width: 100px; }
        .detail-surat-table .separator { width: 10px; }
        .isi-surat { margin: 20px 0; text-align: justify; }
        
        /* PERBAIKAN 2: Mencegah TTD dan Tembusan terpotong di akhir halaman */
        .ttd-section { margin-top: 50px; page-break-inside: avoid; }
        .tembusan { font-size: 10pt; margin-top: 50px; page-break-inside: avoid; }

        .ttd-section table { table-layout: fixed; }
        .ttd-section td { width: 50%; text-align: center; vertical-align: top; }
        .qr-code-image { width: 80px; height: 80px; display: block; margin: 5px auto; }
        .footer { position: fixed; bottom: 10mm; left: 25mm; right: 25mm; font-size: 8pt; text-align: justify; border-top: 1px solid #ccc; padding-top: 5px; color: #000; }
    </style>
</head>
<body>

<!-- PERBAIKAN 4: Nama file logo diubah di Controller, bukan di sini. Template ini hanya menerima variabel $embed. -->
@if($embed)
    <img src="{{ $embed }}" class="watermark" alt="Watermark">
@endif

<div class="container">

    <div class="header-section">
        <table>
            <tr>
                <td class="logo-container">
                    @if($embed)
                        <img src="{{ $embed }}" alt="Logo Perusahaan" class="logo-image">
                    @endif
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
            <td style="font-weight: bold;">{{ $sp->hal_surat ?? 'Surat Peringatan' }} a.n. {{ $karyawan->nama_lengkap ?? '-' }}</td>
        </tr>
    </table>

    <div style="margin-top: 25px;">
        Kepada Yth.<br>
        <span style="font-weight: bold;">{{ $karyawan->nama_lengkap ?? '-' }}</span><br>
        {{ $karyawan->jabatanTerbaru->jabatan->nama_jabatan ?? '-' }}<br>
        Di tempat
    </div>

    <div class="isi-surat" style="margin-top: 20px;">
        Dengan hormat,
    </div>

    <div class="isi-surat">
        {!! nl2br(e($sp->isi_surat ?? 'Paragraf isi surat.')) !!}
    </div>

    <div class="isi-surat" style="margin-top: 30px;">
        Demikian Surat Peringatan ini dibuat untuk menjadi perhatian dan dilaksanakan sebagaimana mestinya.
    </div>

    <div class="ttd-section">
        <table>
            <tr>
                <td></td>
                <td style="text-align: center;">
                    <p style="margin-bottom: 5px;">Hormat kami,</p>
                    <p style="margin-top: 0;">General Manager</p>
                    <div class="qr-code">
                        @if(isset($qrCodeDataUri) && $qrCodeDataUri)
                            <img src="{{ $qrCodeDataUri }}" alt="QR Code Verifikasi" class="qr-code-image">
                        @else
                            <div style="width: 80px; height: 80px; margin: 5px auto; border: 1px solid #000; display:flex; align-items:center; justify-content:center; text-align: center; font-size: 8px;">
                                TTD Digital
                            </div>
                        @endif
                    </div>
                    <p style="font-weight: bold; margin-top: 5px; text-decoration: underline;">{{ $gm->nama_lengkap ?? 'Nama Lengkap GM' }}</p>
                </td>
            </tr>
        </table>
    </div>

    @if(!empty($tembusanArray))
        <div class="tembusan">
            <p style="font-weight: bold; margin-bottom: 5px; text-decoration: underline;">Tembusan:</p>
            <ol style="margin: 0; padding-left: 18px;">
                @foreach($tembusanArray as $jabatan)
                    <li>{{ $jabatan }}</li>
                @endforeach
            </ol>
        </div>
    @endif
</div>

<div class="footer">
    Dokumen ini sah dan telah ditandatangani secara elektronik melalui Sistem Informasi Karyawan. Keaslian dapat diverifikasi dengan memindai QR Code yang tersedia.
</div>

</body>
</html>
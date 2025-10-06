<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Peringatan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        h1 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verifikasi Dokumen Resmi</h1>

        @if (isset($sp))
            <div class="success">
                <h3 style="margin-top:0;">Dokumen ASLI dan TERDAFTAR</h3>
                <p>Surat Peringatan dengan Nomor **{{ $sp->no_surat }}** ditemukan dan terdaftar resmi di sistem {{ config('app.name') }}.</p>
            </div>

            <h2>Detail Surat Peringatan</h2>
            <table>
                <tr>
                    <th>Nomor Surat</th>
                    <td>{{ $sp->no_surat }}</td>
                </tr>
                <tr>
                    <th>Hal Surat</th>
                    <td>{{ $sp->hal_surat }} ({{ $sp->jenis_sp }})</td>
                </tr>
                <tr>
                    <th>Ditujukan Kepada</th>
                    <td>{{ $sp->user->nama_lengkap ?? 'N/A' }} (NIP: {{ $sp->nip_user }})</td>
                </tr>
                <tr>
                    <th>Masa Berlaku</th>
                    <td>{{ \Carbon\Carbon::parse($sp->tgl_mulai)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($sp->tgl_selesai)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Terbit</th>
                    <td>{{ \Carbon\Carbon::parse($sp->tgl_sp_terbit)->format('d F Y') }}</td>
                </tr>
            </table>

        @else
            <div class="danger">
                <h3 style="margin-top:0;">Verifikasi GAGAL</h3>
                <p>{{ $message ?? 'Surat Peringatan tidak ditemukan dalam database. Dokumen ini kemungkinan tidak valid atau telah dibatalkan.' }}</p>
            </div>
        @endif

        <p style="margin-top: 30px; font-size: 10pt;">Halaman ini dibuat otomatis untuk memverifikasi keaslian dokumen melalui QR Code.</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Pembayaran SPPD - {{ $sppd->no_surat }}</title>
    <style>
        /* CSS untuk styling surat */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
        }
        .header-table .logo {
            width: 15%;
            vertical-align: top;
        }
        .header-table .company-details {
            width: 85%;
            text-align: center;
            vertical-align: middle;
        }
        .company-details h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .company-details p {
            margin: 0;
            font-size: 14px;
        }
        .title-box {
            background-color: black;
            color: white;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
        }
        .info-section {
            width: 100%;
            margin-top: 15px;
        }
        .info-section .left-info, .info-section .right-info {
            width: 48%;
            vertical-align: top;
            padding: 5px;
        }
        .info-section .right-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section .right-info td, .info-section .right-info th {
            border: 1px solid black;
            padding: 4px;
        }
        .amount-section {
            width: 100%;
            border: 1px solid #000;
            padding: 5px;
            margin-top: 10px;
            font-style: italic;
        }
        table.main-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.main-details th, table.main-details td {
            border: 1px solid black;
            padding: 5px;
            vertical-align: top;
        }
        table.main-details th {
            background-color: #f2f2f2;
            text-align: center;
        }
        table.main-details .text-right {
            text-align: right;
        }
        table.main-details tfoot th {
            font-weight: bold;
        }
        .signature-section {
            width: 100%;
            margin-top: 20px;
        }
        .signature-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-section th, .signature-section td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            font-size: 9px;
        }
        .signature-section .signature-space {
            height: 50px;
        }
        .footer-note {
            margin-top: 5px;
            font-size: 8px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td class="logo">
                    {{-- Ganti dengan tag <img> jika Anda punya file logo --}}
                    <img src="{{ public_path('images/logo_palawi.png') }}" alt="Logo" style="width: 100px;">
                </td>
                <td class="company-details">
                    <h1>PT. PERHUTANI ALAM WISATA</h1>
                    <p>(PALAWI RISORSIS)</p>
                </td>
            </tr>
        </table>

        <div class="title-box">
            KWITANSI PEMBAYARAN
        </div>

        <table class="info-section">
            <tr>
                <td class="left-info">
                    <table>
                        <tr>
                            <td style="width: 120px;"><strong>Telah Terima dari</strong></td>
                            <td>: Bendahara Umum PT.Palawi Risorsis</td>
                        </tr>
                        <tr>
                            <td><strong>Nama</strong></td>
                            <td>: Area Bisnis Wisata Wilayah Timur</td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td>: Jl. Genteng Kali 49 Surabaya</td>
                        </tr>
                    </table>
                </td>
                <td class="right-info">
                    <table>
                        <tr>
                            <th style="width: 50%;">Nomor Bukti :</th>
                            <td style="text-align: right;">{{ $laporan->id }}/PJ/{{ date('m/Y', strtotime($laporan->tanggal_laporan)) }}</td>
                        </tr>
                        <tr>
                            <th colspan="2">Kode Rekening dan Rupiah :</th>
                        </tr>
                        <tr>
                            <td colspan="2" style="height: 60px; text-align: right; vertical-align: top; padding-right: 20px;">
                                Rp. {{ number_format($total, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Rekening Lawan :</th>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <div class="amount-section">
            <strong>Banyaknya Uang :</strong>
            {{-- Untuk fitur ini, Anda perlu helper/package. Contoh: terbilang/terbilang --}}
            {{-- {{ Terbilang::make($total, ' rupiah') }} --}}
            *Tiga Juta Rupiah*
        </div>
        <div class="amount-section" style="border-top: none; font-style: normal;">
            <strong>Untuk Penerimaan :</strong> Biaya Perjalanan Dinas an. {{ $user->nama_lengkap }} ({{ $sppd->keterangan_sppd }})
        </div>

        <table class="main-details">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Uraian Pembayaran</th>
                    <th rowspan="2">Volume</th>
                    <th rowspan="2">Tarif (Rp)</th>
                    <th colspan="3">Perhitungan</th>
                    <th rowspan="2">PPh (Rp)</th>
                    <th rowspan="2">Pembayaran Bersih (Rp)</th>
                </tr>
                <tr>
                    <th>Pembayaran Pokok (Rp)</th>
                    <th>PPN (Rp)</th>
                    <th>Pembayaran Kotor (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @if($laporan->uang_harian > 0)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>Uang Harian</td><td></td><td></td>
                    <td class="text-right">{{ number_format($laporan->uang_harian, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->uang_harian, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->uang_harian, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($laporan->transportasi_lokal > 0)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>Transportasi Lokal</td><td></td><td></td>
                    <td class="text-right">{{ number_format($laporan->transportasi_lokal, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->transportasi_lokal, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->transportasi_lokal, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($laporan->uang_makan > 0)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>Uang Makan</td><td></td><td></td>
                    <td class="text-right">{{ number_format($laporan->uang_makan, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->uang_makan, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->uang_makan, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($laporan->akomodasi_mandiri > 0)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>Akomodasi Mandiri</td><td></td><td></td>
                    <td class="text-right">{{ number_format($laporan->akomodasi_mandiri, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->akomodasi_mandiri, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->akomodasi_mandiri, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($laporan->akomodasi_tt > 0)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>Akomodasi T&T</td><td></td><td></td>
                    <td class="text-right">{{ number_format($laporan->akomodasi_tt, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->akomodasi_tt, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->akomodasi_tt, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($laporan->transportasi_lain > 0)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>Transportasi</td><td></td><td></td>
                    <td class="text-right">{{ number_format($laporan->transportasi_lain, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->transportasi_lain, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($laporan->transportasi_lain, 2, ',', '.') }}</td>
                </tr>
                @endif
                {{-- Baris kosong untuk mengisi sisa tabel --}}
                @for ($i = $no; $i <= 6; $i++)
                <tr><td style="text-align: center;">{{ $i }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: center;">Jumlah Rp.</th>
                    <th class="text-right">{{ number_format($total, 2, ',', '.') }}</th>
                    <th>-</th>
                    <th class="text-right">{{ number_format($total, 2, ',', '.') }}</th>
                    <th>-</th>
                    <th class="text-right">{{ number_format($total, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="signature-section">
            <table>
                <tr>
                    <td style="width: 25%;"><strong>Keterangan :</strong><br>Barang/Jasa/Pekerjaan telah diterima/dikerjakan dengan baik</td>
                    <td style="width: 25%;"><strong>Mengetahui / Setuju :</strong></td>
                    <td style="width: 25%;"><strong>Boleh Dibayar :</strong></td>
                    <td style="width: 25%;"><strong>Yang Menerima/ Membayar*)</strong></td>
                </tr>
                <tr>
                    <td><strong>Tempat, Tanggal :</strong> Surabaya, {{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                    <td>Pengguna Anggaran</td>
                    <td>Bendahara Umum</td>
                    <td>{{ $user->jabatanTerbaru->jabatan->nama_jabatan ?? 'Karyawan' }}</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td><strong>ERRY SUBAGUO</strong></td>
                    <td><strong>ANDY ISWINDARTO</strong></td>
                    <td><strong>ERNA JURIANTI K.</strong></td>
                    <td><strong>{{ strtoupper($user->nama_lengkap) }}</strong></td>
                </tr>
            </table>
        </div>
        <div class="footer-note">
            *) Coret yang tidak perlu
        </div>
    </div>
</body>
</html>
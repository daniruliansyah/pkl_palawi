<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        /* CSS Sederhana untuk tampilan slip gaji */
        body { font-family: sans-serif; margin: 0; padding: 20px; }
        .container { border: 1px solid #ddd; padding: 20px; max-width: 800px; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1, .header p { margin: 0; }
        .details, .earnings, .deductions, .summary { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
        .text-right { text-align: right; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SLIP GAJI KARYAWAN</h1>
            <p>PT Palawi Risorsis</p>
            <p>Periode: {{ \Carbon\Carbon::create()->month($gaji->bulan)->format('F') }} {{ $gaji->tahun }}</p>
        </div>

        <div class="details">
            <table>
                <tr>
                    <td><strong>Nama Karyawan</strong></td>
                    <td>: {{ $gaji->user->nama_lengkap }}</td>
                    <td><strong>NIP</strong></td>
                    <td>: {{ $gaji->user->nip }}</td>
                </tr>
            </table>
        </div>

        <div class="earnings">
            <h4>PENGHASILAN</h4>
            <table>
                <tr>
                    <td>Gaji Pokok</td>
                    <td class="text-right">Rp {{ number_format($gaji->gaji_pokok, 2, ',', '.') }}</td>
                </tr>
                {{-- Anda bisa menambahkan baris untuk tunjangan di sini --}}
            </table>
        </div>

        <div class="deductions">
            <h4>POTONGAN</h4>
            <table>
                @forelse($gaji->detailPotongan as $potongan)
                <tr>
                    <td>{{ $potongan->masterPotongan->nama_potongan }}</td>
                    <td class="text-right">Rp {{ number_format($potongan->jumlah, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td>Tidak ada potongan</td>
                    <td class="text-right">Rp 0,00</td>
                </tr>
                @endforelse
                <tr class="total">
                    <td>Total Potongan</td>
                    <td class="text-right">Rp {{ number_format($gaji->total_potongan, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="summary">
            <h4>RINGKASAN</h4>
            <table>
                <tr class="total">
                    <td>GAJI DITERIMA (TAKE HOME PAY)</td>
                    <td class="text-right">Rp {{ number_format($gaji->gaji_diterima, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
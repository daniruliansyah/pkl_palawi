<!DOCTYPE html>

<html lang="id">
<head>
<meta charset="utf-8">
<title>Surat Permohonan {{ ucwords(strtolower($cuti->jenis_izin)) }}</title>
{{-- Copy CSS dari template asli (surat_cuti_umum_pdf) --}}
<style>
body {
font-family: 'Times New Roman', Times, serif;
font-size: 11pt;
line-height: 1.4;
margin: 0;
/* Tambahkan margin halaman jika diperlukan oleh dompdf */
@page { margin: 1.5cm 2cm; }
}
table {
width: 100%;
border-collapse: collapse;
}
.text-center { text-align: center; }
.text-underline { text-decoration: underline; }
b, strong { font-weight: bold; }

    /* Style untuk bagian atas surat (menggantikan kop surat) */
    .header-section {
        padding-bottom: 10px;
        margin-bottom: 15px;
        /* Hapus padding-top jika @page margin sudah cukup */
        /* padding-top: 1.5cm; */
    }

    .logo-container {
        text-align: left;
        height: 60px;
    }
    .logo-image {
        width: 100px; /* Ukuran dikurangi */
        height: auto;
        display: block;
    }

    .details-table td {
        vertical-align: top;
        padding: 0;
        line-height: 1.3;
    }
    .details-table td:first-child { width: 180px; }
    .details-table td:nth-child(2) { width: 10px; }

    .approval-section {
        margin-top: 15px;
        width: 100%;
        border: none;
        /* Hapus padding jika @page margin sudah cukup */
        /* padding: 0 2cm; */
    }
    .approval-section td {
        vertical-align: top;
        padding: 4px 8px;
    }
    .approval-section .sdm-notes {
        width: 45%;
         font-size: 10pt; /* Samakan font size catatan */
    }
    .approval-section .ssdm-notes { /* Digunakan untuk Approver 1 (Manager/SDM) */
        width: 55%;
        text-align: center;
    }
    .approval-section .sdm-approval { /* Digunakan untuk placeholder kosong */
        width: 50%;
        text-align: left;
    }
    .approval-section .gm-approval { /* Digunakan untuk Approver Final (GM/Manager) */
        width: 50%;
        text-align: left;
    }

    .sdm-details-table {
        width: 100%;
        margin-top: 5px;
         font-size: 10pt; /* Samakan font size catatan */
    }
    .sdm-details-table td {
        padding: 1px 0; /* Sedikit padding */
    }
    .sdm-details-table td:first-child { width: 70%; }
    .sdm-details-table td:last-child { text-align: left; }

    .signature-space { height: 50px; }
    .qr-code {
        height: 50px;
        width: 50px;
        display: block;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    /* Hapus .content-body jika tidak diperlukan lagi */

</style>


</head>

<body>

{{-- BAGIAN ATAS SURAT (Sama seperti template asli) --}}
<div class="header-section">
<div class="logo-container">
@if($embed)
<img src="{{ $embed }}" alt="Logo Perusahaan" class="logo-image">
@endif
</div>
<table style="margin-top: 10px;">
<tr>
<td style="width: 50%; vertical-align: top;">
<table style="width: 100%;">
<tr>
<td style="width: 80px;">Perihal</td>
<td>: <b class="text-underline">PERMOHONAN {{ strtoupper($cuti->jenis_izin) }}</b></td>
</tr>
<tr>
<td>Nomor</td>
<td>: {{ $cuti->no_surat }}</td>
</tr>
</table>
<div style="margin-top: 20px;">
Kepada Yth:




@php
// Untuk template ini, penerima hampir selalu GM, kecuali Alur 5 (GM cuti)
$penerimaSurat = $gm ?? $manager; // Ambil GM jika ada, jika tidak (Alur 5) ambil Manager
$jabatanPenerima = 'General Manager ABWWT';
if ($penerimaSurat && $penerimaSurat->isManager()) {
$jabatanPenerima = $penerimaSurat->jabatanTerbaru->jabatan->nama_jabatan ?? 'Manager ABWWT';
} elseif (!$penerimaSurat && isset($manager)) { // Handle Alur 5 jika $gm null
$jabatanPenerima = $manager->jabatanTerbaru->jabatan->nama_jabatan ?? 'Manager ABWWT';
}
@endphp
{{ $jabatanPenerima }}




PT. Perhutani Alam Wisata Risorsis




Di




SURABAYA

{{-- ISI SURAT (Sama seperti template asli) --}}
<p style="margin-top: 20px;">Yang bertanda tangan di bawah ini:</p>
<table class="details-table" style="margin-left: 20px;">
<tr><td>Nama</td><td>:</td><td>{{ $karyawan->nama_lengkap ?? '...' }}</td></tr>
<tr><td>NPK</td><td>:</td><td>{{ $karyawan->nik ?? '...' }}</td></tr>
{{-- <tr><td>Pangkat/Gol Ruang</td><td>:</td><td>{{ $karyawan->jabatanTerbaru->jabatan->jenjang ?? '-' }}</td></tr> --}}
<tr><td>Jabatan</td><td>:</td><td>{{ $karyawan->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}</td></tr>
</table>
<p style="text-align: justify;">
Dengan ini mengajukan {{ $cuti->jenis_izin }} untuk tahun {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('Y') }}
selama {{ $cuti->jumlah_hari }} ({{ ucwords(\Terbilang::make($cuti->jumlah_hari)) }}) Hari kerja,
terhitung tanggal {{ \Carbon\Carbon::parse($cuti->tgl_mulai)->isoFormat('D MMMM Y') }}
s/d {{ \Carbon\Carbon::parse($cuti->tgl_selesai)->isoFormat('D MMMM Y') }}
dengan kesanggupan dan perjanjian sebagai berikut:
</p>
<ol type="a" style="padding-left: 40px; text-align: justify; margin: 0;">
<li>Sebelum menjalankan cuti tersebut, akan menyerahkan pekerjaan yang menjadi tanggung jawab saya kepada atasan saya langsung.</li>
<li>Setelah selesai menjalankan cuti saya akan segera melaporkan diri kepada atasan saya langsung dan bekerja kembali seperti biasa.</li>
<li>
Selama menjalankan cuti, saya dapat dihubungi melalui:
<table class="details-table" style="margin-top: 5px; width: 85%;">
<tr><td>Alamat</td><td>:</td><td><b>{{ $karyawan->alamat ?? '...' }}</b></td></tr>
<tr><td>No HP</td><td>:</td><td><b>{{ $karyawan->no_telp ?? '...' }}</b></td></tr>
<tr><td>Keperluan Cuti</td><td>:</td><td>{{ $cuti->keterangan }}</td></tr>
</table>
</li>
</ol>
<p>Demikian surat permintaan ini saya buat untuk dapat dipertimbangkan dan mendapat persetujuan.</p>

{{-- Tanda Tangan Pemohon (Sama seperti template asli) --}}
<table style="width: 100%;">
<tr>
<td style="width: 60%;"></td>
<td style="width: 40%;" class="text-center">
Hormat saya,
<div class="signature-space"></div>
<b class="text-underline">{{ $karyawan->nama_lengkap ?? '...' }}</b> 



 {{-- Tambah line break --}}
NPK. {{ $karyawan->nik ?? '...' }} {{-- Samakan format NPK --}}
</td>
</tr>
</table>

{{-- ==================================================================== --}}
{{-- BAGIAN PERSETUJUAN (Struktur HTML & CSS ikut template asli, konten beda) --}}
{{-- ==================================================================== --}}
<table class="approval-section">
{{-- Baris Pertama: Catatan SDM (kiri) & TTD Approver 1 (kanan) --}}
<tr>
<td class="sdm-notes">
<b class="text-underline">CATATAN PEJABAT SDM</b>




Cuti yang telah diambil dalam tahun yang bersangkutan
<table class="sdm-details-table">
<tr><td>1. Cuti Tahunan</td><td>: {{ $dataCutiSDM['cuti_tahunan'] ?? 0 }} hari</td></tr>
<tr><td>2. Cuti Besar</td><td>: {{ $dataCutiSDM['cuti_besar'] ?? 0 }} hari</td></tr>
<tr><td>3. Cuti Sakit</td><td>: {{ $dataCutiSDM['cuti_sakit'] ?? 0 }} hari</td></tr>
<tr><td>4. Cuti Bersalin</td><td>: {{ $dataCutiSDM['cuti_bersalin'] ?? 0 }} hari</td></tr>
<tr><td>5. Cuti Karena Alasan Penting</td><td>: {{ $dataCutiSDM['cuti_alasan_penting'] ?? 0 }} hari</td></tr>
<tr><td>6. Sisa Cuti Tahunan</td><td>: <b>{{ $sisaCutiDari12 ?? 12 }}</b> hari</td></tr>
</table>
</td>
<td class="ssdm-notes"> {{-- Gunakan class sddm-notes untuk approver pertama --}}
{{-- Logika untuk menentukan approver pertama --}}
@php
$approver1 = null;
$role1 = 'MENGETAHUI'; // Default
if ($karyawan->isSdm()) { // Alur 3 -> Manager
$approver1 = $manager;
$role1 = 'MENGETAHUI';
} elseif ($karyawan->isGm()) { // Alur 5 -> SDM
$approver1 = $sdm;
$role1 = 'MENGETAHUI';
} elseif ($karyawan->isManager() || $karyawan->isSenior()) { // Alur 2 & 4 -> SDM
$approver1 = $sdm;
$role1 = 'MENGETAHUI';
}
@endphp

             @if($approver1)
                {{-- Ganti judul jika perlu --}}
                <span class="text-underline">{{ $role1 }}</span><br>
                {{ $approver1->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}<br>
                Area Bisnis Wisata Wilayah Timur {{-- Sesuaikan jika perlu --}}
                <div class="signature-space"></div>
                <b class="text-underline">{{ $approver1->nama_lengkap ?? '...' }}</b><br>
                NPK. {{ $approver1->nik ?? '...' }}
             @else
                 {{-- Kosongkan jika approver 1 tidak relevan (tidak boleh terjadi di alur ini) --}}
                  <div style="height: 110px;"></div> {{-- Placeholder tinggi --}}
             @endif
        </td>
    </tr>
    {{-- Baris Kedua: Placeholder Kosong (kiri bawah) & TTD Approver Final (kanan bawah) --}}
    <tr>
         <td class="sdm-approval">
             {{-- Kosongkan kolom kiri bawah karena hanya 2 TTD --}}
              <div style="height: 110px;"></div> {{-- Placeholder tinggi --}}
        </td>
        <td class="gm-approval"> {{-- Gunakan class gm-approval untuk approver final --}}
            {{-- Logika untuk menentukan approver kedua/final --}}
             @php
                $approverFinal = null;
                $roleFinal = 'PEJABAT YANG BERWENANG'; // Judul Default
                if ($karyawan->isSdm() || $karyawan->isManager() || $karyawan->isSenior()) { // Alur 2, 3, 4 -> Final GM
                    $approverFinal = $gm;
                    $roleFinal = 'PEJABAT YANG BERWENANG MENYETUJUI CUTI';
                } elseif ($karyawan->isGm()) { // Alur 5 -> Final Manager
                    $approverFinal = $manager;
                     $roleFinal = 'PEJABAT YANG BERWENANG MENYETUJUI CUTI'; // Atau judul lain jika perlu
                }
            @endphp

            @if($approverFinal)
                {{ $roleFinal }}<br>
                MENYETUJUI CUTI<br> {{-- Tambah baris ini jika perlu --}}
                {{ $approverFinal->jabatanTerbaru->jabatan->nama_jabatan ?? '...' }}<br>
                Area Bisnis Wisata Wilayah Timur {{-- Sesuaikan jika perlu --}}
                <div class="qr-code">
                     <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="width: 50px; height: 50px;">
                </div>
                {{-- <div class="signature-space" style="height: 5px;"></div> --}} {{-- Hapus jika QR cukup --}}
                <b class="text-underline">{{ $approverFinal->nama_lengkap ?? '...' }}</b><br>
                NPK. {{ $approverFinal->nik ?? '...' }}
            @else
                {{-- Kosong jika tidak ada approver final (seharusnya tidak terjadi) --}}
                 <div style="height: 110px;"></div> {{-- Placeholder --}}
            @endif
        </td>
    </tr>
</table>


</body>
</html>
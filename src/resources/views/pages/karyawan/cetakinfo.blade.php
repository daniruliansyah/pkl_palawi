<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Data Karyawan - {{ $karyawan->nama_lengkap }}</title>
    <style>
        /* CSS Dasar */
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #333; line-height: 1.3; margin-top: 0; }
        
        /* Header Laporan (Format Kop Surat) */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 3px double #333; padding-bottom: 10px; }
        .header-logo { width: 15%; vertical-align: middle; text-align: left; }
        .header-text { width: 85%; vertical-align: middle; text-align: center; padding-right: 15%; /* Padding kanan agar teks benar-benar di tengah halaman, mengimbangi lebar logo */ }
        
        .logo-image { width: 90px; height: auto; display: block; }
        
        .header h1 { margin: 0; font-size: 18pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 11pt; }

        /* Judul Per Bagian */
        .section-title { 
            background-color: #eeeeee; 
            padding: 6px 10px; 
            font-weight: bold; 
            font-size: 12pt; 
            margin-top: 20px; 
            margin-bottom: 10px;
            border-left: 5px solid #333;
            text-transform: uppercase;
        }

        /* Container Info Personal */
        .profile-container { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .photo-col { width: 130px; vertical-align: top; }
        .info-col { vertical-align: top; padding-left: 10px; }
        
        .photo-img { 
            width: 120px; 
            height: 150px; 
            object-fit: cover; 
            border: 1px solid #999; 
            padding: 3px;
            background: #fff;
        }

        /* Tabel Data Baris */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .label { font-weight: bold; width: 140px; color: #555; }

        /* Tabel List Data */
        .data-table { width: 100%; border-collapse: collapse; font-size: 10pt; margin-top: 5px; }
        .data-table th, .data-table td { 
            border: 1px solid #999; 
            padding: 6px 8px; 
            text-align: left; 
            vertical-align: top; 
        }
        .data-table th { 
            background-color: #e0e0e0; 
            font-weight: bold; 
            text-align: center;
        }
        
        .text-center { text-align: center; }
        .text-muted { color: #666; font-size: 9pt; font-style: italic; }
        .mb-1 { margin-bottom: 4px; display: block; }
    </style>
</head>
<body>

    {{-- HEADER (Format Kop Surat dengan Logo) --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(isset($logo) && $logo)
                    <img src="{{ $logo }}" alt="Logo" class="logo-image">
                @else
                    {{-- Placeholder jika logo tidak ditemukan --}}
                    <b>LOGO</b>
                @endif
            </td>
            <td class="header-text">
                <div class="header">
                    <h1>PT PALAWI RISORSIS</h1>
                    <p style="font-weight: bold;">Area Bisnis Wisata Wilayah Timur</p>
                    <p>Laporan Detail Data Karyawan</p>
                </div>
            </td>
        </tr>
    </table>

    {{-- 1. INFORMASI PERSONAL --}}
    <div class="section-title">Informasi Personal</div>
    
    <table class="profile-container">
        <tr>
            {{-- Kolom Foto --}}
            <td class="photo-col">
                @php
                    $path = $karyawan->foto ? public_path('storage/' . $karyawan->foto) : public_path('images/default.png');
                    $fotoSrc = file_exists($path) ? $path : public_path('images/default.png'); 
                @endphp
                <img src="{{ $fotoSrc }}" class="photo-img" alt="Foto">
            </td>

            {{-- Kolom Biodata --}}
            <td class="info-col">
                <table class="info-table">
                    <tr><td class="label">Nama Lengkap</td><td>: <strong>{{ $karyawan->nama_lengkap }}</strong></td></tr>
                    <tr><td class="label">NIP</td><td>: {{ $karyawan->nip ?? '-' }}</td></tr>
                    <tr><td class="label">NIK</td><td>: {{ $karyawan->nik }}</td></tr>
                    <tr><td class="label">NPK Baru</td><td>: {{ $karyawan->npk_baru ?? '-' }}</td></tr>
                    <tr><td class="label">Jabatan Saat Ini</td><td>: {{ $karyawan->jabatanTerbaru->jabatan->nama_jabatan ?? 'Belum Ada Jabatan' }}</td></tr>
                    <tr><td class="label">Email</td><td>: {{ $karyawan->email }}</td></tr>
                    <tr><td class="label">No. Telepon</td><td>: {{ $karyawan->no_telp }}</td></tr>
                    <tr><td class="label">Tempat, Tgl Lahir</td><td>: {{ $karyawan->tempat_lahir }}, {{ \Carbon\Carbon::parse($karyawan->tgl_lahir)->translatedFormat('d F Y') }}</td></tr>
                    <tr><td class="label">Jenis Kelamin</td><td>: {{ $karyawan->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                    <tr><td class="label">Agama</td><td>: {{ $karyawan->agama }}</td></tr>
                    <tr><td class="label">Status Perkawinan</td><td>: {{ $karyawan->status_perkawinan }}</td></tr>
                    <tr><td class="label">Alamat</td><td>: {{ $karyawan->alamat }}</td></tr>
                    <tr><td class="label">Status Kepegawaian</td><td>: {{ $karyawan->status_aktif ? 'Aktif' : 'Nonaktif' }}</td></tr>
                    <tr><td class="label">Bergabung Sejak</td><td>: {{ \Carbon\Carbon::parse($karyawan->join_date)->translatedFormat('d F Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- 2. RIWAYAT JABATAN --}}
    <div class="section-title">Riwayat Jabatan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Jabatan</th>
                <th style="width: 30%">Area / Lokasi</th>
                <th style="width: 30%">Periode Menjabat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawan->riwayatJabatans as $index => $riwayat)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $riwayat->jabatan->nama_jabatan ?? '-' }}</strong>
                </td>
                <td>{{ $riwayat->area_bekerja }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($riwayat->tgl_mulai)->format('d/m/Y') }} - 
                    @if ($riwayat->tgl_selesai)
                        {{ \Carbon\Carbon::parse($riwayat->tgl_selesai)->format('d/m/Y') }}
                    @else
                        <span style="font-weight:bold; color:#008000;">Sekarang</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="padding: 15px;">Belum ada riwayat jabatan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 3. RIWAYAT PENDIDIKAN --}}
    <div class="section-title">Riwayat Pendidikan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 40%">Institusi & Jenjang</th>
                <th style="width: 35%">Jurusan</th>
                <th style="width: 20%">Tahun Lulus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawan->riwayatPendidikan->sortByDesc('tahun_lulus') as $index => $pendidikan)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>
                    <span class="mb-1"><strong>{{ $pendidikan->jenjang }}</strong></span>
                    {{ $pendidikan->nama_institusi }}
                </td>
                <td>{{ $pendidikan->jurusan ?: '-' }}</td>
                <td class="text-center">
                    {{ $pendidikan->tahun_lulus }}
                    @if($pendidikan->ipk)
                        <br><span class="text-muted">IPK: {{ number_format($pendidikan->ipk, 2) }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="padding: 15px;">Belum ada riwayat pendidikan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 4. RIWAYAT SURAT PERINGATAN (SP) --}}
    <div class="section-title">Riwayat Surat Peringatan (SP)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Nomor SP</th>
                <th style="width: 20%">Jenis SP</th>
                <th style="width: 20%">Tanggal</th>
                <th style="width: 35%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawan->riwayatSP as $index => $sp)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $sp->no_surat }}</td>
                <td>{{ $sp->jenis_sp }}</td>
                <td>{{ \Carbon\Carbon::parse($sp->tanggal_sp)->format('d/m/Y') }}</td>
                <td>{{ $sp->isi_surat }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 15px;">Tidak ada riwayat surat peringatan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer Tanggal Cetak --}}
    <div style="margin-top: 50px; text-align: right; font-size: 10pt;">
        <p>Dicetak pada: {{ now()->locale('id')->translatedFormat('l, d F Y H:i') }} WIB</p>
    </div>

</body>
</html>
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikasi saat seeding
        DB::table('pengajuan_cuti')->truncate();

        // Contoh 1: Pengajuan yang sudah disetujui semua level
        DB::table('pengajuan_cuti')->insert([
            'nip_user'              => '123456789', // NIP Karyawan (Pastikan ada di tabel users)
            'jenis_izin'            => 'Cuti Tahunan',
            'tgl_mulai'             => '2025-10-01',
            'tgl_selesai'           => '2025-10-03',
            'jumlah_hari'           => 3,
            'keterangan'            => 'Cuti tahunan untuk liburan keluarga.',
            'tgl_upload'            => Carbon::now()->subDays(10),
            'no_surat'              => 'CUTI/2025/09/001',
            'file_izin'             => null,
            'nip_user_ssdm'         => '22334455', // NIP Senior (Pastikan ada di tabel users)
            'tgl_persetujuan_ssdm'  => Carbon::now()->subDays(9),
            'nip_user_sdm'          => '33445566', // NIP SDM (Pastikan ada di tabel users)
            'tgl_persetujuan_sdm'   => Carbon::now()->subDays(8),
            'nip_user_gm'           => '44556677', // NIP GM (Pastikan ada di tabel users)
            'tgl_persetujuan_gm'    => Carbon::now()->subDays(7),
            // Kolom status yang baru
            'status_ssdm'           => 'Disetujui',
            'status_sdm'            => 'Disetujui',
            'status_gm'             => 'Disetujui',
            'alasan_penolakan'      => null,
            'created_at'            => Carbon::now()->subDays(10),
            'updated_at'            => Carbon::now()->subDays(7),
        ]);

        // Contoh 2: Pengajuan yang menunggu persetujuan SDM
        DB::table('pengajuan_cuti')->insert([
            'nip_user'              => '123456789',
            'jenis_izin'            => 'Cuti Sakit',
            'tgl_mulai'             => '2025-11-05',
            'tgl_selesai'           => '2025-11-05',
            'jumlah_hari'           => 1,
            'keterangan'            => 'Izin sakit karena demam.',
            'tgl_upload'            => Carbon::now()->subDays(2),
            'no_surat'              => 'CUTI/2025/09/002',
            'file_izin'             => 'file_izin/contoh_surat_sakit.pdf', // Contoh path file
            'nip_user_ssdm'         => '22334455',
            'tgl_persetujuan_ssdm'  => Carbon::now()->subDay(),
            'nip_user_sdm'          => null,
            'tgl_persetujuan_sdm'   => null,
            'nip_user_gm'           => null,
            'tgl_persetujuan_gm'    => null,
            'status_ssdm'           => 'Disetujui',
            'status_sdm'            => 'Menunggu Persetujuan',
            'status_gm'             => 'Menunggu',
            'alasan_penolakan'      => null,
            'created_at'            => Carbon::now()->subDays(2),
            'updated_at'            => Carbon::now()->subDay(),
        ]);

        // Contoh 3: Pengajuan yang baru dibuat, menunggu persetujuan Senior (SSDM)
        DB::table('pengajuan_cuti')->insert([
            // === BAGIAN YANG DIPERBAIKI ===
            'nip_user'              => '434221016', // Menggunakan NIP 'Nadea Yiyian' yang ada di UserSeeder
            'jenis_izin'            => 'Cuti Alasan Penting',
            'tgl_mulai'             => '2025-12-20',
            'tgl_selesai'           => '2025-12-20',
            'jumlah_hari'           => 1,
            'keterangan'            => 'Acara keluarga mendadak.',
            'tgl_upload'            => Carbon::now(),
            'no_surat'              => 'CUTI/2025/09/003',
            'file_izin'             => null,
            'nip_user_ssdm'         => '22334455', // Diajukan ke Senior yang sama
            'tgl_persetujuan_ssdm'  => null,
            'nip_user_sdm'          => null,
            'tgl_persetujuan_sdm'   => null,
            'nip_user_gm'           => null,
            'tgl_persetujuan_gm'    => null,
            'status_ssdm'           => 'Menunggu Persetujuan',
            'status_sdm'            => 'Menunggu',
            'status_gm'             => 'Menunggu',
            'alasan_penolakan'      => null,
            'created_at'            => Carbon::now(),
            'updated_at'            => Carbon::now(),
        ]);
    }
}


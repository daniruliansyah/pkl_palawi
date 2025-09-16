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
        // Contoh 1: Pengajuan yang sudah disetujui semua
        DB::table('pengajuan_cuti')->insert([
            'nip_user'              => '123456789', // NIP karyawan yang mengajukan
            'jenis_izin'            => 'Cuti',
            'tgl_mulai'             => '2025-10-01',
            'tgl_selesai'           => '2025-10-03',
            'jumlah_hari'           => 3,
            'keterangan'            => 'Cuti tahunan untuk liburan keluarga.',
            'tgl_upload'            => Carbon::now()->subDays(10), // Diunggah 10 hari lalu
            'nip_user_ssdm'         => '22334455',
            'tgl_persetujuan_gm'    => Carbon::now()->subDays(8), // Disetujui 8 hari lalu
            'nip_user_gm'           => '33445566',
            'tgl_persetujuan_sdm'   => Carbon::now()->subDays(9), // Disetujui 9 hari lalu
            'nip_user_sdm'          => '44556677',
            'status_pengajuan'      => 'Selesai',
            'no_surat'              => 'CUTI/2025/09/001',
            'file_izin'             => null, // Tidak ada file izin untuk cuti ini
            'created_at'            => Carbon::now()->subDays(10),
            'updated_at'            => Carbon::now()->subDays(8),
        ]);
    }
}
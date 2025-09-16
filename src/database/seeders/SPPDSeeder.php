<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SppdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sppd')->insert([
            'nip_user'            => '123456789', // nip pengaju
            'tgl_mulai'           => '2025-09-20',
            'tgl_selesai'         => '2025-09-25',
            'keterangan'          => 'Perjalanan dinas ke Surabaya untuk rapat kerja',
            'lokasi_tujuan'       => 'Surabaya',
            'nip_user_sdm'        => '33445566',
            'tgl_persetujuan_sdm' => Carbon::now(),
            'nip_user_gm'         => '44556677',
            'tgl_persetujuan_gm'  => Carbon::now(),
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now(),
        ]);

        DB::table('sppd')->insert([
            'nip_user'            => '123456789', // nip pengaju
            'tgl_mulai'           => '2025-09-21',
            'tgl_selesai'         => '2025-09-25',
            'keterangan'          => 'ayam dinas ke Surabaya untuk rapat kerja',
            'lokasi_tujuan'       => 'Surabaya',
            'nip_user_sdm'        => '33445566',
            'tgl_persetujuan_sdm' => Carbon::now(),
            'nip_user_gm'         => '44556677',
            'tgl_persetujuan_gm'  => Carbon::now(),
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now(),
            'status_gm'           => 'Ditolak',
        ]);
    }
}

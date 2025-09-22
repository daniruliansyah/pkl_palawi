<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sppd;
use App\Models\User;
use Carbon\Carbon;

class SppdSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user berdasarkan email atau kriteria lain
        $userKaryawan = User::where('email', 'dummy@example.com')->first();
        $userSDM     = User::where('email', 'sdm@example.com')->first();
        $userGM      = User::where('email', 'gm@example.com')->first();

        if (!$userKaryawan || !$userSDM || !$userGM) {
            $this->command->info("Seeder SPPD dibatalkan: User belum ada.");
            return;
        }

        // Buat beberapa data SPPD
        $sppds = [
            [
                'nip_user'      => $userKaryawan->nip,
                'tgl_mulai'     => Carbon::now()->addDays(3)->format('Y-m-d'),
                'tgl_selesai'   => Carbon::now()->addDays(5)->format('Y-m-d'),
                'keterangan'    => 'Perjalanan dinas ke Surabaya untuk rapat kerja',
                'lokasi_tujuan' => 'Banyuwangi',
                'lokasi_berangkat' => 'Surabaya',
                'status'    => 'menunggu',
                'pemberi_tugas' => 'SDM',
                'nip_penyetuju'  => $userSDM->nip,
                'tgl_persetujuan' => Carbon::now(),
                'alat_angkat' => 'bis kencang',
                'jumlah_hari' => 12,
            ],
            // Bisa tambah SPPD lain disini
        ];

        foreach ($sppds as $data) {
            Sppd::create($data);
        }
    }
}

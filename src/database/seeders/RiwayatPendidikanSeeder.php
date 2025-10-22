<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RiwayatPendidikan; // Pastikan Model di-import
use Carbon\Carbon; // Untuk timestamps

class RiwayatPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data riwayat pendidikan untuk User ID 7 (Dani Ruliansyah)
        
        RiwayatPendidikan::create([
            'user_id' => 7,
            'jenjang' => 'SMP',
            'nama_institusi' => 'SMP Negeri 26 Surabaya',
            'jurusan' => '-',
            'tahun_masuk' => '2016',
            'tahun_lulus' => '2019',
            'ipk' => null, // SMA/SMK mungkin tidak pakai IPK
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RiwayatPendidikan::create([
            'user_id' => 7,
            'jenjang' => 'SMA',
            'nama_institusi' => 'SMA Negeri 12 Surabaya',
            'jurusan' => 'IPA',
            'tahun_masuk' => '2019',
            'tahun_lulus' => '2022',
            'ipk' => null, // SMA/SMK mungkin tidak pakai IPK
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RiwayatPendidikan::create([
            'user_id' => 7,
            'jenjang' => 'Sarjana',
            'nama_institusi' => 'Universitas Airlangga',
            'jurusan' => 'D4 Teknik Informatika',
            'tahun_masuk' => '2022',
            'tahun_lulus' => '2026',
            'ipk' => 3.42,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // Anda bisa tambahkan data untuk user_id lain di sini jika perlu
    }
}

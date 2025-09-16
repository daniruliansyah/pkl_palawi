<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    $users = [
        [
            'nama_lengkap' => 'Dummy User',
            'nip' => '123456789',
            'nik' => '9876543210123456',
            'email' => 'dummy@example.com',
            'password' => bcrypt('password'),
            'no_telp'   => '081234567890',
            'jenis_kelamin' => '1',
            'alamat' => 'Jl. Contoh Alamat No. 123',
            'tgl_lahir' => '1990-01-01',
            'tempat_lahir' => 'Contoh Kota',
            'agama' => 'Islam',
            'status_perkawinan' => 'Belum Kawin',
            'area_bekerja' => 'IT',
            'status_aktif' => '1',
            'npk_baru' => 'NPK123456',
            'npwp' => '12.345.678.9-012.345',
            'join_date' => '2020-01-01',
            'jatah_cuti' => 12,
        ],
        [
            'nama_lengkap' => 'User SSDM',
            'nip' => '22334455',
            'nik' => '1111222233334444',
            'email' => 'ssdm@example.com',
            'password' => bcrypt('password'),
            'no_telp'   => '081111111111',
            'jenis_kelamin' => '1',
            'alamat' => 'Jl. SSDM',
            'tgl_lahir' => '1991-01-01',
            'tempat_lahir' => 'Kota SSDM',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'area_bekerja' => 'SSDM',
            'status_aktif' => '1',
            'npk_baru' => 'NPK22334455',
            'npwp' => '22.333.444.5-678.910',
            'join_date' => '2021-01-01',
            'jatah_cuti' => 12,
        ],
        [
            'nama_lengkap' => 'User SDM',
            'nip' => '33445566',
            'nik' => '2222333344445555',
            'email' => 'sdm@example.com',
            'password' => bcrypt('password'),
            'no_telp'   => '082222222222',
            'jenis_kelamin' => '1',
            'alamat' => 'Jl. SDM',
            'tgl_lahir' => '1992-01-01',
            'tempat_lahir' => 'Kota SDM',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'area_bekerja' => 'SDM',
            'status_aktif' => '1',
            'npk_baru' => 'NPK33445566',
            'npwp' => '33.444.555.6-789.101',
            'join_date' => '2022-01-01',
            'jatah_cuti' => 12,
        ],
        [
            'nama_lengkap' => 'User GM',
            'nip' => '44556677',
            'nik' => '3333444455556666',
            'email' => 'gm@example.com',
            'password' => bcrypt('password'),
            'no_telp'   => '083333333333',
            'jenis_kelamin' => '1',
            'alamat' => 'Jl. GM',
            'tgl_lahir' => '1993-01-01',
            'tempat_lahir' => 'Kota GM',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'area_bekerja' => 'GM',
            'status_aktif' => '1',
            'npk_baru' => 'NPK44556677',
            'npwp' => '44.555.666.7-890.112',
            'join_date' => '2023-01-01',
            'jatah_cuti' => 12,
        ],
    ];

    foreach ($users as $user) {
        \App\Models\User::create($user);
    }
}


}


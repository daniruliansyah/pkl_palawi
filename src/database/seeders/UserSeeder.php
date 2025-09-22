<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\RiwayatJabatan;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil jabatan dari JabatanSeeder
        $jabatanGM = Jabatan::where('nama_jabatan', 'General Manager')->first();
        $jabatanSDM = Jabatan::where('nama_jabatan', 'Senior Analis Keuangan, SDM & Umum')->first();
        $jabatanKaryawan = Jabatan::where('nama_jabatan', 'Senior Analis Pengelolaan Destinasi')->first();

        $users = [
            [
                'nama_lengkap' => 'User Karyawan',
                'nip' => '123456789',
                'nik' => '98765432101234560',
                'nama_lengkap' => 'Dummy User',
                'nip' => '123456789',
                'nik' => '9876543210123456',
                'email' => 'dummy@example.com',
                'password' => bcrypt('password'),
                'no_telp' => '081234567890',
                'jenis_kelamin' => '1',
                'alamat' => 'Jl. Contoh Alamat No. 123',
                'tgl_lahir' => '1990-01-01',
                'tempat_lahir' => 'Contoh Kota',
                'agama' => 'Islam',
                'status_perkawinan' => 'Belum Kawin',
                'status_aktif' => '1',
                'npk_baru' => 'NPK123456',
                'npwp' => '12.345.678.9-012.345',
                'join_date' => '2020-01-01',
                'jatah_cuti' => 12,
                'jabatan' => $jabatanKaryawan,
            ],
            [
                'nama_lengkap' => 'User SSDM',
                'nip' => '22334455',
                'nik' => '1111222233334444',
                'email' => 'ssdm@example.com',
                'password' => bcrypt('password'),
                'no_telp' => '081111111111',
                'jenis_kelamin' => '1',
                'alamat' => 'Jl. SSDM',
                'tgl_lahir' => '1991-01-01',
                'tempat_lahir' => 'Kota SSDM',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'status_aktif' => '1',
                'npk_baru' => 'NPK22334455',
                'npwp' => '22.333.444.5-678.910',
                'join_date' => '2021-01-01',
                'jatah_cuti' => 12,
                'jabatan' => $jabatanSDM,
            ],
            [
                'nama_lengkap' => 'User SDM',
                'nip' => '33445566',
                'nik' => '2222333344445555',
                'email' => 'sdm@example.com',
                'password' => bcrypt('password'),
                'no_telp' => '082222222222',
                'jenis_kelamin' => '1',
                'alamat' => 'Jl. SDM',
                'tgl_lahir' => '1992-01-01',
                'tempat_lahir' => 'Kota SDM',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'status_aktif' => '1',
                'npk_baru' => 'NPK33445566',
                'npwp' => '33.444.555.6-789.101',
                'join_date' => '2022-01-01',
                'jatah_cuti' => 12,
                'jabatan' => $jabatanSDM,
            ],
            [
                'nama_lengkap' => 'User GM',
                'nip' => '44556677',
                'nik' => '3333444455556666',
                'email' => 'gm@example.com',
                'password' => bcrypt('password'),
                'no_telp' => '083333333333',
                'jenis_kelamin' => '1',
                'alamat' => 'Jl. GM',
                'tgl_lahir' => '1993-01-01',
                'tempat_lahir' => 'Kota GM',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'status_aktif' => '1',
                'npk_baru' => 'NPK44556677',
                'npwp' => '44.555.666.7-890.112',
                'join_date' => '2023-01-01',
                'jatah_cuti' => 12,
                'jabatan' => $jabatanGM,
                'role' => 'GM',
            ],
            [
                'nama_lengkap' => 'Nadea Yiyian',
                'nip' => '434221016',
                'nik' => '36520182939730002',
                'email' => 'nadea@example.com',
                'password' => bcrypt('12345678'),
                'no_telp' => '081111111111',
                'jenis_kelamin' => '1',
                'alamat' => 'Jl. SSDM',
                'tgl_lahir' => '1991-01-01',
                'tempat_lahir' => 'Kota SSDM',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'status_aktif' => '1',
                'npk_baru' => 'NPK22334455',
                'npwp' => '22.333.444.5-678.910',
                'join_date' => '2021-01-01',
                'jatah_cuti' => 12,
                'jabatan' => $jabatanKaryawan,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'nama_lengkap' => $userData['nama_lengkap'],
                'nip' => $userData['nip'],
                'nik' => $userData['nik'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'no_telp' => $userData['no_telp'],
                'jenis_kelamin' => $userData['jenis_kelamin'],
                'alamat' => $userData['alamat'],
                'tgl_lahir' => $userData['tgl_lahir'],
                'tempat_lahir' => $userData['tempat_lahir'],
                'agama' => $userData['agama'],
                'status_perkawinan' => $userData['status_perkawinan'],
                'status_aktif' => $userData['status_aktif'],
                'npk_baru' => $userData['npk_baru'],
                'npwp' => $userData['npwp'],
                'join_date' => $userData['join_date'],
                'jatah_cuti' => $userData['jatah_cuti'],
            ]);

            RiwayatJabatan::create([
                'nip_user' => $user->nip,
                'id_jabatan' => $userData['jabatan']->id,
                'tgl_mulai' => Carbon::now(),
                'tgl_selesai' => Carbon::now()->addYears(2),
            ]);
        }
    }
}

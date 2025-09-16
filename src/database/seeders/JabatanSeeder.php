<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jabatan::create([
            'nama_jabatan' => 'General Manager',
        ]);

        Jabatan::create([
            'nama_jabatan' => 'Manager Perencanaan dan Standarisasi',
        ]);

        Jabatan::create([
            'nama_jabatan' => 'Senior Analis Pengelolaan Destinasi',
        ]);

        Jabatan::create([
            'nama_jabatan' => 'Senior Analis Keuangan, SDM & Umum',
        ]);

        Jabatan::create([
            'nama_jabatan' => ' Analis Sosial dan Lingkungan',
        ]);
    }
}

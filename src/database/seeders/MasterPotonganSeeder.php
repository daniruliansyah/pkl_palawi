<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPotongan;
use Illuminate\Support\Facades\Schema; // Pastikan ini ada

class MasterPotonganSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key SEBELUM truncate
        Schema::disableForeignKeyConstraints();

        MasterPotongan::truncate();

        // Aktifkan kembali SETELAH truncate
        Schema::enableForeignKeyConstraints();

        $daftarPotongan = [
            'Asperan',
            'Bank BJB',
            'Bank BKK/BPR',
            'Bank BNI',
            'Bank BPD Jateng',
            'Bank BRI',
            'Bank BSI',
            'Bank BTN',
            'Bank Jatim',
            'Bank Mandiri',
            'Bank Lainnya',
            'Bapor',
            'Dansos',
            'IIK',
            'HPK',
            'Infaq',
            'Kamar Mesh',
            'Kendaraan',
            'Kontrak Rumah',
            'Koperasi',
            'Madu',
            'Majalah Bina',
            'Perumahan',
            'Piutang TGR',
            'PKBL',
            'PMI',
            'Potongan Lain-Lain',
            'Reklokal',
            'Sekar',
            'YJS & YKP',
            'Zakat',
        ];

        foreach ($daftarPotongan as $namaPotongan) {
            MasterPotongan::create([
                'nama_potongan' => $namaPotongan,
                'is_active' => true,
            ]);
        }
    }
}
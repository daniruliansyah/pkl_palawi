<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalendarSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        DB::table('calendar')->insert([
            [
                'nip_user' => '434221041',
                'note_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'notes' => 'Rapat koordinasi tim HR besok pagi',
                'urgency' => 'high',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip_user' => '434221041',
                'note_date' => Carbon::now()->format('Y-m-d'),
                'notes' => 'Follow up laporan bulanan',
                'urgency' => 'medium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip_user' => '434221041',
                'note_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'notes' => 'Pengumpulan dokumen cuti tahunan',
                'urgency' => 'low',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

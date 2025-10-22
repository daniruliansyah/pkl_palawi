<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menggunakan Schema::table untuk mengubah tabel yang sudah ada
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            /**
             * Menambahkan kolom 'jenjang'.
             * ->string() untuk tipe data (sesuaikan jika perlu)
             * ->nullable() agar kolom ini boleh kosong (opsional)
             * ->after('area_bekerja') agar posisinya rapi setelah kolom area_bekerja
             */
            $table->string('jenjang')->nullable()->after('area_bekerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Logika untuk rollback (menghapus kolom)
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->dropColumn('jenjang');
        });
    }
};

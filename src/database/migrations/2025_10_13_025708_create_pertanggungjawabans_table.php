<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanggungjawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained('sppd')->onDelete('cascade'); // Terhubung ke tabel sppd
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Terhubung ke tabel users
            $table->date('tanggal_laporan');
            $table->text('keterangan')->nullable();
            $table->string('file_kuitansi')->nullable(); // Menyimpan path/lokasi file PDF kuitansi
            $table->decimal('total_biaya_bersih', 15, 2);

            // Kolom untuk setiap rincian biaya (sesuai gambar kuitansi)
            $table->decimal('uang_harian', 15, 2)->default(0);
            $table->decimal('transportasi_lokal', 15, 2)->default(0);
            $table->decimal('uang_makan', 15, 2)->default(0);
            $table->decimal('akomodasi_mandiri', 15, 2)->default(0);
            $table->decimal('akomodasi_tt', 15, 2)->default(0); // T&T = Tiket & Transportasi
            $table->decimal('transportasi_lain', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanggungjawaban');
    }
};

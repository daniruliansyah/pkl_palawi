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
        Schema::create('riwayat_kpo', function (Blueprint $table) {
            $table->id();
            // ==========================================================
            // PERBAIKAN: Menggunakan foreignId('user_id')
            // ==========================================================
            // Kolom untuk menghubungkan ke ID user
            $table->foreignId('user_id')
                  ->constrained('users') // Otomatis merujuk ke 'id' di tabel 'users'
                  ->onDelete('cascade'); // Ikut menghapus riwayat jika user dihapus
            // ==========================================================
            
            $table->string('nama_jabatan', 100)->nullable(); 
            $table->string('nama_organisasi', 100)->nullable(); 
            $table->date('tgl_jabat')->nullable();
            $table->string('link_berkas')->nullable();

            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kpo');
    }
};

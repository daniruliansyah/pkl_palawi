<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();

            // KOLOM PENTING: Foreign Key ke tabel users
            // Menggunakan 'user_id' sebagai Foreign Key.
            // 'constrained('users')' memastikan ia merujuk ke kolom 'id' di tabel 'users'.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Kolom Data Notifikasi
            $table->string('jenis_surat');
            $table->string('nama_pengirim', 100); // Batasi panjang, sesuai kolom 'nama_lengkap' di user
            $table->text('isi_pesan');
            $table->string('foto_pengirim')->nullable(); // Path foto pengirim
            $table->string('status_persetujuan')->default('Menunggu Persetujuan');
            $table->boolean('sudah_dibaca')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};

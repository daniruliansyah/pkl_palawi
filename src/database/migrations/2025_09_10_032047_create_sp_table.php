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
        Schema::create('sp', function (Blueprint $table) {
            $table->id();

            // Kolom baru dari form
            $table->string('hal_surat')->nullable(); // Ditambahkan
            // $table->string('kepada_yth')->nullable(); // Ditambahkan
            $table->enum('jenis_sp', ['Pertama', 'Kedua', 'Terakhir']); // Ditambahkan
            $table->text('isi_surat'); // Ditambahkan
            $table->json('tembusan')->nullable(); // Ditambahkan untuk menyimpan array jabatan

            // Kolom yang sudah ada
            $table->string('nip_user', 20); // Tambahkan kolom relasi ke User
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->date('tgl_sp_terbit');
            $table->string('no_surat', 100)->unique(); // Jadikan unique
            $table->string('file_sp', 100)->nullable(); // Jadikan nullable
            $table->string('file_bukti', 100)->nullable();

            $table->timestamps();

            // Tambahkan foreign key constraint
            $table->foreign('nip_user')->references('nip')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp');
    }
};

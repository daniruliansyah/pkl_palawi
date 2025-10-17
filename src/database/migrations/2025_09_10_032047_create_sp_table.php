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

        // Kolom dari form
        $table->string('hal_surat')->nullable();
        $table->enum('jenis_sp', ['Pertama', 'Kedua', 'Terakhir']);
        $table->text('isi_surat');
        $table->json('tembusan')->nullable();

        // Kolom Utama
        $table->string('nip_user', 20);
        $table->string('nip_pembuat', 20);
        $table->date('tgl_mulai');
        $table->date('tgl_selesai');
        $table->date('tgl_sp_terbit');
        $table->string('no_surat', 100)->unique();
        $table->string('file_sp', 100)->nullable();
        $table->string('file_bukti', 100)->nullable();

        // Kolom Approval (MENGGUNAKAN STRING (VARCHAR) DENGAN PANJANG 25)
        $table->string('status_sdm', 25)->default('Menunggu Persetujuan');
        $table->string('nip_user_sdm', 25)->nullable();
        $table->timestamp('tgl_persetujuan_sdm')->nullable();

        $table->string('status_gm', 25)->default('Menunggu');
        $table->string('nip_user_gm', 25)->nullable();
        $table->timestamp('tgl_persetujuan_gm')->nullable();

        $table->text('alasan_penolakan')->nullable();
 
        $table->timestamps();

        // Tambahkan foreign key constraint
        $table->foreign('nip_user')->references('nip')->on('users')->onDelete('cascade');
        $table->foreign('nip_pembuat')->references('nip')->on('users')->onDelete('cascade');
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

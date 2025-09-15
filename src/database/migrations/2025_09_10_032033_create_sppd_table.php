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
         Schema::create('sppd', function (Blueprint $table) {
            $table->id();
            $table->string('nip_user', 20); // pengaju
            $table->foreign('nip_user')->references('nip')->on('users')->onDelete('cascade');

            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('keterangan');
            $table->string('lokasi_tujuan', 100);

            $table->enum('status_pengajuan', ['pending','approved','rejected'])->default('pending');
            $table->string('no_surat', 100)->nullable();
            $table->string('file_sppd', 100)->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sppd');
    }
};

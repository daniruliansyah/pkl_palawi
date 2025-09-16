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
        Schema::create('pengajuan_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_izin', 20);
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('jumlah_hari');
            $table->string('keterangan', 100);
            $table->datetime('tgl_upload');
            $table->datetime('tgl_persetujuan_ssdm')->nullable();
            $table->datetime('tgl_persetujuan_sdm')->nullable();
            $table->datetime('tgl_persetujuan_gm')->nullable();
            $table->string('status_pengajuan', 100)->default('pending Staff Ahli SDM');
            $table->string('no_surat', 100)->nullable();
            $table->string('file_izin', 100)->nullable(); // <-- Diperbaiki di sini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cuti');
    }
};

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
            $table->string('pemberi_tugas');
            $table->integer('jumlah_hari');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('keterangan');
            $table->string('lokasi_berangkat', 100);
            $table->string('lokasi_tujuan', 100);
            $table->string('alat_angkat');
            $table->datetime('tgl_persetujuan')->nullable();
            $table->string('status')->default('menunggu');
            $table->string('no_surat', 100)->nullable();
            $table->string('file_sppd', 100)->nullable();
            $table->string('surat_bukti', 255)->nullable();
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

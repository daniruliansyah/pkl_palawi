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
            // Kolom Data Utama
            $table->id();
            $table->string('jenis_izin', 50);
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('jumlah_hari');
            $table->string('keterangan', 255);
            $table->datetime('tgl_upload');
            $table->string('no_surat', 100)->nullable();
            $table->string('file_izin', 255)->nullable();

            // Kolom Foreign Key untuk User dan Approvers
            $table->string('nip_user', 20); // NIP Karyawan yang mengajukan
            $table->string('nip_user_ssdm', 20)->nullable(); // NIP Senior yang approve
            $table->string('nip_user_sdm', 20)->nullable(); // NIP SDM yang approve
            $table->string('nip_user_gm', 20)->nullable(); // NIP GM yang approve

            // Kolom Tanggal Persetujuan per Level
            $table->datetime('tgl_persetujuan_ssdm')->nullable();
            $table->datetime('tgl_persetujuan_sdm')->nullable();
            $table->datetime('tgl_persetujuan_gm')->nullable();

            // === BAGIAN YANG DIPERBAIKI ===
            // Menggunakan kolom status yang terpisah untuk setiap level
            $table->string('status_ssdm', 30)->default('Menunggu Persetujuan');
            $table->string('status_sdm', 30)->default('Menunggu');
            $table->string('status_gm', 30)->default('Menunggu');
            $table->text('alasan_penolakan')->nullable();
            // Kolom 'status_pengajuan' yang lama sudah dihapus

            $table->timestamps();

            // Optional: Menambahkan foreign key constraint untuk integritas data
            $table->foreign('nip_user')->references('nip')->on('users')->onDelete('cascade');
            $table->foreign('nip_user_ssdm')->references('nip')->on('users')->onDelete('set null');
            $table->foreign('nip_user_sdm')->references('nip')->on('users')->onDelete('set null');
            $table->foreign('nip_user_gm')->references('nip')->on('users')->onDelete('set null');
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
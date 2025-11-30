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
            $table->string('file_surat', 255)->nullable(); // Menambahkan kolom file_surat (ada di model Anda)
            $table->string('alamat_saat_cuti', 255)->nullable();
            $table->string('no_hp_saat_cuti', 13)->nullable();

            // Kolom Foreign Key untuk User dan Approvers
            $table->string('nip_user', 20); // NIP Karyawan yang mengajukan
            $table->string('nip_user_ssdm', 20)->nullable(); // NIP Senior yang approve (Alur 1)
            $table->string('nip_user_sdm', 20)->nullable(); // NIP SDM yang approve (Alur 1, 2, 5)
            
            // === PERUBAHAN 1: Menambahkan Kolom Manager ===
            $table->string('nip_user_manager', 20)->nullable(); // NIP Manager yang approve (Alur 3, 5)
            // ==============================================

            $table->string('nip_user_gm', 20)->nullable(); // NIP GM yang approve (Alur 1, 2, 3, 4)

            // Kolom Tanggal Persetujuan per Level
            $table->datetime('tgl_persetujuan_ssdm')->nullable();
            $table->datetime('tgl_persetujuan_sdm')->nullable();
            
            // === PERUBAHAN 2: Menambahkan Tgl Persetujuan Manager ===
            $table->datetime('tgl_persetujuan_manager')->nullable();
            // ==================================================

            $table->datetime('tgl_persetujuan_gm')->nullable();

            // Kolom status yang terpisah untuk setiap level
            $table->string('status_ssdm', 30)->default('Menunggu');
            $table->string('status_sdm', 30)->default('Menunggu');

            // === PERUBAHAN 3: Menambahkan Status Manager ===
            $table->string('status_manager', 30)->default('Menunggu');
            // =============================================

            $table->string('status_gm', 30)->default('Menunggu');
            $table->text('alasan_penolakan')->nullable();
            
            $table->timestamps();

            // Optional: Menambahkan foreign key constraint untuk integritas data
            $table->foreign('nip_user')->references('nip')->on('users')->onDelete('cascade');
            $table->foreign('nip_user_ssdm')->references('nip')->on('users')->onDelete('set null');
            $table->foreign('nip_user_sdm')->references('nip')->on('users')->onDelete('set null');
            
            // === PERUBAHAN 4: Menambahkan Foreign Key Manager ===
            $table->foreign('nip_user_manager')->references('nip')->on('users')->onDelete('set null');
            // ==================================================

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

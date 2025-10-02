<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('calendar', function (Blueprint $table) {
            // Kolom Identifikasi
            $table->id(); // Primary Key (id)
            
            // Foreign Key ke tabel users menggunakan NIP (sesuai format cuti)
            // Asumsi: tabel 'users' memiliki kolom 'nip'
            $table->string('nip_user', 20)->comment('NIP Karyawan yang memiliki catatan');

            // Kolom Data Notes
            $table->date('note_date')->comment('Tanggal spesifik catatan (YYYY-MM-DD)');
            $table->text('notes')->comment('Isi detail catatan atau event');
            
            // Kolom Urgensi
            // Menggunakan ENUM untuk memastikan nilai hanya 'low', 'medium', atau 'high'
            $table->enum('urgency', ['low', 'medium', 'high'])->default('low')->comment('Tingkat urgensi catatan');

            // Timestamp Standar
            $table->timestamps(); // created_at dan updated_at

            // Indeks Unik Gabungan (Composite Unique Index)
            // Ini memastikan satu user (berdasarkan NIP) hanya bisa memiliki satu catatan untuk satu tanggal.
            $table->unique(['nip_user', 'note_date'], 'unique_user_date_note');
            
            // Optional: Menambahkan foreign key constraint untuk integritas data (mengacu pada tabel users)
            $table->foreign('nip_user')->references('nip')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Batalkan (rollback) migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_notes');
    }
};

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
        // Membuat tabel baru bernama 'riwayat_pendidikan'
        Schema::create('riwayat_pendidikan', function (Blueprint $table) {
            $table->id(); // Kunci utama (Primary Key)
            
            // ==========================================================
            // PERBAIKAN: Menggunakan foreignId('user_id')
            // ==========================================================
            // Kolom untuk menghubungkan ke ID user
            $table->foreignId('user_id')
                  ->constrained('users') // Otomatis merujuk ke 'id' di tabel 'users'
                  ->onDelete('cascade'); // Ikut menghapus riwayat jika user dihapus
            // ==========================================================
            
            $table->string('jenjang', 50); // Misal: SMA/SMK, S1, S2
            $table->string('nama_institusi'); // Misal: Universitas Gadjah Mada
            $table->string('jurusan')->nullable(); // Misal: Teknik Informatika (bisa null untuk SD/SMP)
            $table->string('tahun_masuk', 4)->nullable();
            $table->string('tahun_lulus', 4);
            $table->decimal('ipk', 3, 2)->nullable(); // Opsional, untuk universitas

            $table->timestamps(); // created_at dan updated_at

            // Baris foreign key sebelumnya yang menggunakan 'user_nip' sudah dihapus
            // karena 'constrained()' sudah menanganinya.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pendidikan');
    }
};
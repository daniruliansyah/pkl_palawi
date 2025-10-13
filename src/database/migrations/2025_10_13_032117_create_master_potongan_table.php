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
        Schema::create('master_potongan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_potongan')->unique(); // 'Bank BRI', 'Koperasi', dll.
            $table->boolean('is_active')->default(true); // Untuk menonaktifkan jika sudah tidak dipakai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_potongan');
    }
};

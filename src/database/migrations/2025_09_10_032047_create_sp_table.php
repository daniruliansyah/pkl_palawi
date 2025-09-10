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
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('ket_peringatan');
            $table->date('tgl_sp_terbit');
            $table->string('no_surat', 100);
            $table->string('file_sp', 100)->nullable;
            $table->timestamps();
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

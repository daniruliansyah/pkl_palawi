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
        Schema::create('gaji', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->integer('bulan');
        $table->year('tahun');
        $table->decimal('gaji_pokok', 15, 2)->default(0);
        $table->decimal('total_potongan', 15, 2)->default(0);
        $table->decimal('gaji_diterima', 15, 2)->default(0);
        $table->string('file_slip')->nullable();
        $table->text('keterangan')->nullable();
        $table->timestamps();
        $table->unique(['user_id', 'bulan', 'tahun']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji');
    }
};

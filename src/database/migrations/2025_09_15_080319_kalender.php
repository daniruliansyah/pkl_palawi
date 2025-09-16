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
                Schema::create('kalenders', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event'); // Nama event
            $table->date('tanggal_mulai'); // Start date
            $table->date('tanggal_selesai'); // End date
            $table->enum('warna', ['Danger', 'Success', 'Primary', 'Warning']); // Kategori warna
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalenders');
    }
};

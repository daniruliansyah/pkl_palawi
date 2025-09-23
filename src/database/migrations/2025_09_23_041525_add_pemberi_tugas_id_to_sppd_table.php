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
    Schema::table('sppd', function (Blueprint $table) {
        $table->unsignedBigInteger('pemberi_tugas_id')->nullable()->after('pemberi_tugas');
        $table->foreign('pemberi_tugas_id')->references('id')->on('jabatan')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('sppd', function (Blueprint $table) {
        $table->dropForeign(['pemberi_tugas_id']);
        $table->dropColumn('pemberi_tugas_id');
    });
}

};

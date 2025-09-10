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
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->string('nip_user_gm', 20)->after('tgl_persetujuan_sdm');
            $table->foreign('nip_user_gm')->references('nip')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->dropForeign(['nip_user_gm']);
            $table->dropColumn('nip_user_gm');
        });
    }
};

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
            $table->string('nip_user_sdm', 20)->after('tgl_persetujuan_ssdm');
            $table->foreign('nip_user_sdm')->references('nip')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropForeign(['nip_user_sdm']);
            $table->dropColumn('nip_user_sdm');
        });
    }
};

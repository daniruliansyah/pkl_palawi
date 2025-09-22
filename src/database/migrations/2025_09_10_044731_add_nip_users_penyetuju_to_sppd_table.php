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
           $table->string('nip_penyetuju', 20)->nullable()->after('status');
        $table->foreign('nip_penyetuju')->references('nip')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropForeign(['nip_penyetuju']);
            $table->dropColumn('nip_penyetuju');
        });
    }
};

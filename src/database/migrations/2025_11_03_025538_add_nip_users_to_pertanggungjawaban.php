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
        Schema::table('pertanggungjawaban', function (Blueprint $table) {
            $table->string('nip_user', 20)->after('id');
            $table->foreign('nip_user')->references('nip')->on('users');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertanggungjawaban', function (Blueprint $table) {
            $table->dropForeign(['nip_user']);
            $table->dropColumn('nip_user');
        });
    }
};

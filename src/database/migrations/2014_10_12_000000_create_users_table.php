<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 100);
            $table->string('nip', 20)->nullable()->unique();
            $table->string('nik', 20)->unique();
            $table->string('no_telp', 15);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('jenis_kelamin');
            $table->string('alamat');
            $table->date('tgl_lahir');
            $table->string('tempat_lahir', 30);
            $table->string('agama', 10);
            $table->string('status_perkawinan', 20);
            $table->string('area_bekerja');
            $table->boolean('status_aktif');
            $table->string('npk_baru');
            $table->string('npwp');
            $table->date('join_date');
            $table->integer('jatah_cuti')->default(12);
            $table->rememberToken();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

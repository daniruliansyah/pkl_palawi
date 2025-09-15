<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation ke berbagai jenis surat (SPPD, SuratCuti, dll.)
            $table->unsignedBigInteger('approvable_id');
            $table->string('approvable_type');

            // ✅ Role khusus penyetuju
            $table->enum('role', ['senior_divisi','SDM','GM']);

            $table->string('approver_nip', 20);
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->datetime('tgl_approval')->nullable();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->foreign('approver_nip')->references('nip')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};

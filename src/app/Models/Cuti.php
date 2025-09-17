<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Cuti extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     */
    protected $table = 'pengajuan_cuti';

    /**
     * Kolom yang diizinkan untuk diisi secara massal (mass assignment).
     * Disesuaikan dengan data dari form dan controller.
     */
    protected $fillable = [
        'nip_user',
        'jenis_izin',
        'tgl_mulai',
        'tgl_selesai',
        'jumlah_hari',      // Ditambahkan
        'keterangan',
        'file_izin',   // Ditambahkan (untuk menyimpan path file)
        'status_pengajuan',           // Ditambahkan (untuk status: Diajukan, Disetujui, dll)
        'tgl_upload',
        'nip_user_ssdm',
        'nip_user_sdm',
        'nip_user_gm',
        'no_surat',
    ];

    /**
     * Relasi ke user (pengaju).
     * Satu pengajuan cuti dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        // Foreign key di tabel ini adalah 'nip_user'
        // Owner key (primary key) di tabel users adalah 'nip'
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }

    /**
     * Relasi polymorphic ke model Approval.
     */
    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }
}
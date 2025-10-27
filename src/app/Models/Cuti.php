<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_cuti';

    /**
     * Kolom yang diizinkan untuk diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'nip_user',
        'jenis_izin',
        'tgl_mulai',
        'tgl_selesai',
        'jumlah_hari',
        'keterangan',
        'file_izin',
        'tgl_upload',
        'no_surat',
        'nip_user_ssdm',
        'nip_user_sdm',
        'nip_user_manager', // <-- TAMBAHKAN
        'nip_user_gm',
        'status_ssdm',
        'status_sdm',
        'status_manager', // <-- TAMBAHKAN
        'status_gm',
        'alasan_penolakan',
        'file_surat',
    ];

    /**
     * Relasi ke user (pengaju).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }

    /**
     * Relasi ke user approver level 1 (Senior/SSDM).
     */
    public function ssdm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_user_ssdm', 'nip');
    }

    /**
     * Relasi ke user approver level 2 (SDM).
     */
    public function sdm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_user_sdm', 'nip');
    }

    /**
     * === PERUBAHAN 5: Menambahkan Relasi Manager ===
     * Relasi ke user approver (Manager).
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_user_manager', 'nip');
    }
    // =============================================

    /**
     * Relasi ke user approver level 3 (GM).
     */
    public function gm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_user_gm', 'nip');
    }
}

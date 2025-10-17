<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SP extends Model
{
    use HasFactory;

    protected $table = 'sp';

    protected $fillable = [
        'nip_user',
        'no_surat',
        'hal_surat',
        'jenis_sp',
        'isi_surat',
        'tembusan',
        'tgl_mulai',
        'tgl_selesai',
        'tgl_sp_terbit',
        'file_sp',
        'file_bukti',

        // --- Tambahan untuk Approval (PENTING) ---
        'nip_pembuat',
        'status_sdm',
        'nip_user_sdm',
        'tgl_persetujuan_sdm',
        'status_gm',
        'nip_user_gm',
        'tgl_persetujuan_gm',
        'alasan_penolakan',
        // -----------------------------------------
    ];

    protected $casts = [
        'tembusan' => 'array',
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_sp_terbit' => 'date',
        'tgl_persetujuan_sdm' => 'datetime', // Asumsi tanggal persetujuan disimpan dengan waktu
        'tgl_persetujuan_gm' => 'datetime',
    ];

    /**
     * Relasi ke karyawan penerima SP.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }

    /**
     * Relasi ke user pembuat/penginput SP.
     */
    public function pembuat(): BelongsTo
    {
        // Asumsi ada kolom 'nip_pembuat' di tabel 'sp'
        return $this->belongsTo(User::class, 'nip_pembuat', 'nip');
    }

    /**
     * Relasi ke user approver level 1 (SDM).
     */
    public function sdm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_user_sdm', 'nip');
    }

    /**
     * Relasi ke user approver level 2 (GM).
     */
    public function gm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_user_gm', 'nip');
    }

    // Jika Anda masih menggunakan tabel 'approvals' terpisah untuk log
    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }
}

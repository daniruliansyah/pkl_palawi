<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SP extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     */
    protected $table = 'sp';

    /**
     * Kolom yang diizinkan untuk diisi secara massal (mass assignment).
     * Disesuaikan dengan data dari form dan controller.
     */
    protected $fillable = [
        'nip_user',
        'tgl_mulai',
        'tgl_selesai',
        'ket_peringatan',
        'tgl_sp_terbit',
        'file_sp',   // Ditambahkan (untuk menyimpan path file)
        'no_surat',
    ];

    public function user(): BelongsTo
    {
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
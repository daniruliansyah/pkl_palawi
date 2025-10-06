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
        'no_surat', // Sudah ada
        'hal_surat',    // <<-- Baru
        // 'kepada_yth',   // <<-- Baru
        'jenis_sp',     // <<-- Baru
        'isi_surat',    // <<-- Baru
        'tembusan',     // <<-- Baru
        'tgl_mulai',
        'tgl_selesai',
        'tgl_sp_terbit',
        'file_sp',
        'file_bukti',
    ];

    // Cast kolom 'tembusan' agar otomatis diubah menjadi array saat diambil dari database
    protected $casts = [
        'tembusan' => 'array',
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_sp_terbit' => 'date',
    ];

    public function user(): BelongsTo
    {
        // Pastikan 'nip_user' ada di tabel 'sp' dan terhubung ke 'nip' di tabel 'users'
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKpo extends Model
{
    use HasFactory;

    protected $table = 'riwayat_kpo';

    protected $fillable = [
        'user_id', // <-- Disesuaikan untuk foreign key 'id'
        'nama_jabatan',
        'nama_organisasi',
        'tgl_jabat',
        'link_berkas',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
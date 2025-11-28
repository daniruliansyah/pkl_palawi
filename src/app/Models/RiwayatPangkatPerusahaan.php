<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPangkatPerusahaan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pangkat_perusahaan';

    protected $fillable = [
        'user_id', // <-- Disesuaikan untuk foreign key 'id'
        'gol_ruang',
        'tmt_gol',
        'no_sk',
        'tgl_sk',
        'link_berkas',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
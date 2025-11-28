<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatLatihanJabatan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_latihan_jabatan';

    protected $fillable = [
        'user_id', // <-- Disesuaikan untuk foreign key 'id'
        'nama_latihan',
        'tgl_mulai',
        'tgl_selesai',
        'link_berkas',
    ];

    /* Relasi "belongsTo" (kebalikan dari hasMany).
     * Setiap riwayat pendidikan dimiliki oleh satu User.*/

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
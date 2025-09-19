<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sppd extends Model
{
    use HasFactory;

    protected $table = 'sppd';

    protected $fillable = [
        'nip_user',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'lokasi_tujuan',
        'surat_bukti',
        'status_sdm',
        'status_gm',
        'nip_user_sdm',
        'nip_user_gm',
        'no_surat',
        'file_sppd'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }
}

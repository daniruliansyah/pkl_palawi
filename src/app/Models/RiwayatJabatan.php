<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatJabatan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_jabatan';

    protected $fillable = [
        'nip_user',
        'id_jabatan',
        'tgl_mulai',
        'tgl_selesai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id');
    }
}

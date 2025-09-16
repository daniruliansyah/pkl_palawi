<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan'; // optional kalau nama tabelnya sesuai konvensi
    protected $fillable = [
        'nama_jabatan',
        'deskripsi',
    ];

    /**
     * Relasi ke User melalui tabel pivot riwayat_jabatan
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'riwayat_jabatan', 'id_jabatan', 'nip_user')
                    ->withPivot('tgl_mulai', 'tgl_selesai')
                    ->withTimestamps();
    }

    /**
     * Relasi ke RiwayatJabatan (kalau butuh langsung hasMany)
     */
    public function riwayat()
    {
        return $this->hasMany(RiwayatJabatan::class, 'id_jabatan');
    }
}

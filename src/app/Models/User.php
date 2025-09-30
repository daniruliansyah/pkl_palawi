<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Notifikasi;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'nip',
        'nik',
        'no_telp',
        'email',
        'password',
        'jenis_kelamin',
        'alamat',
        'tgl_lahir',
        'tempat_lahir',
        'agama',
        'foto',
        'status_perkawinan',
        'area_bekerja',
        'status_aktif',
        'npk_baru',
        'npwp',
        'join_date',
        'jatah_cuti',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function jabatans()
    {
        return $this->belongsToMany(Jabatan::class, 'riwayat_jabatan', 'nip_user', 'id_jabatan')
                    ->withPivot('tgl_mulai', 'tgl_selesai', 'area_bekerja')
                    ->withTimestamps();
    }

    public function riwayatJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class, 'nip_user', 'nip');
    }

    public function jabatanTerbaru()
    {
        return $this->hasOne(RiwayatJabatan::class, 'nip_user', 'nip')
                    ->latestOfMany('tgl_mulai');
    }

    public function riwayatSP()
    {
        return $this->hasMany(SP::class, 'nip_user', 'nip');
    }
    public function notifikasi()
    {
        // 'user_id' adalah Foreign Key di tabel 'pemberitahuans'
        return $this->hasMany(Notifikasi::class, 'user_id');
    }
}

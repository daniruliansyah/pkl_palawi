<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
}

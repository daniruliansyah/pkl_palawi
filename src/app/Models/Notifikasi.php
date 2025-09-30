<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model // Menggunakan Notifikasi
{
    use HasFactory;

    // Kolom-kolom yang boleh diisi secara massal
    protected $fillable = [
        'user_id', // Foreign Key
        'jenis_surat',
        'nama_pengirim',
        'isi_pesan',
        'status_persetujuan',
        'sudah_dibaca',
        'foto_pengirim'
    ];

    /**
     * Dapatkan pengguna (User) yang merupakan pemilik notifikasi ini.
     */
    public function user()
    {
        // PERBAIKAN: Foreign Key yang benar adalah 'user_id'
        // Kolom 'user_id' di tabel 'notifikasis' menunjuk ke User.id
        return $this->belongsTo(User::class, 'user_id');
    }
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     */
    protected $table = 'riwayat_latihan_jabatan';

    /**
     * Kolom-kolom yang boleh diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'user_id', // <-- Disesuaikan untuk foreign key 'id'
        'nama_latihan',
        'tgl_mulai',
        'tgl_selesai',
        'link_berkas',
    ];

    /**
     * Relasi "belongsTo" (kebalikan dari hasMany).
     * Setiap riwayat pendidikan dimiliki oleh satu User.
     */
    public function user(): BelongsTo
    {
        // Karena kita mengikuti konvensi Laravel (foreign key 'user_id'
        // merujuk ke 'id' di tabel 'users'), kita tidak perlu
        // menentukan nama kolomnya secara manual.
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kalender extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terasosiasi dengan model ini.
     * Secara default Laravel akan mencari 'calendar_notes'.
     */
    protected $table = 'calendar';

    /**
     * Kolom yang dapat diisi melalui mass assignment.
     */
    protected $fillable = [
        'nip_user',
        'note_date',
        'notes',
        'urgency',
    ];

    /**
     * Mendefinisikan relasi ke model User.
     * Menggunakan nip_user pada model ini untuk merujuk ke kolom 'nip' pada tabel 'users'.
     */
    public function user()
    {
        // Asumsi: Model User memiliki kolom 'nip'
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }

    /**
     * Kolom yang harus dikonversi ke tipe data Date.
     */
    protected $casts = [
        'note_date' => 'date',
    ];
}
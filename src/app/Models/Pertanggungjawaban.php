<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertanggungjawaban extends Model
{
    use HasFactory;

    protected $table = 'pertanggungjawaban';

    protected $fillable = [
        'sppd_id',
        'nip_user',
        'tanggal_laporan',
        'keterangan',
        'file_kuitansi',
        'total_biaya_bersih',
        'uang_harian',
        'transportasi_lokal',
        'uang_makan',
        'akomodasi_mandiri',
        'akomodasi_tt',
        'transportasi_lain',
    ];

    /**
     * Relasi ke SPPD induknya.
     */
    public function sppd()
    {
        return $this->belongsTo(Sppd::class, 'sppd_id');
    }

    /**
     * Relasi ke user yang membuat laporan.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }
}

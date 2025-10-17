<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gaji extends Model
{
    use HasFactory;
    
    protected $table = 'gaji';

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'gaji_pokok',
        'total_potongan',
        'gaji_diterima',
        'file_slip',
        'keterangan',
    ];

    /**
     * Mendefinisikan relasi "satu Gaji dimiliki oleh satu User".
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Mendefinisikan relasi "satu Gaji memiliki banyak DetailPotongan".
     */
    public function detailPotongan(): HasMany
    {
        return $this->hasMany(DetailPotongan::class, 'gaji_id', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPotongan extends Model
{
    use HasFactory;

    protected $table = 'detail_potongan';

    protected $fillable = [
        'gaji_id',
        'master_potongan_id',
        'jumlah',
    ];
    
    // Menonaktifkan timestamps (created_at, updated_at) jika tidak dibutuhkan
    // public $timestamps = false;

    /**
     * Mendefinisikan relasi "satu DetailPotongan dimiliki oleh satu Gaji".
     */
    public function gaji(): BelongsTo
    {
        return $this->belongsTo(Gaji::class);
    }

    /**
     * Mendefinisikan relasi "satu DetailPotongan mengacu pada satu MasterPotongan".
     */
    public function masterPotongan(): BelongsTo
    {
        return $this->belongsTo(MasterPotongan::class);
    }
}
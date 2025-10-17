<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Jangan lupa tambahkan ini
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterPotongan extends Model
{
    use HasFactory;
    
    protected $table = 'master_potongan';

    protected $fillable = [
        'nama_potongan',
        'is_active',
    ];

    /**
     * Mendefinisikan relasi "satu MasterPotongan bisa ada di banyak DetailPotongan".
     */
    public function detailPotongan(): HasMany
    {
        return $this->hasMany(DetailPotongan::class);
    }
}
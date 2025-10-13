<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPotongan extends Model
{
    use HasFactory;
    
    // Mendefinisikan nama tabel secara eksplisit
    protected $table = 'master_potongan';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'nama_potongan',
        'is_active',
    ];
}
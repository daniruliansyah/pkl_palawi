<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sppd extends Model
{
    use HasFactory;

    protected $table = 'sppd';

    protected $fillable = [
        'nip_user', 'tgl_mulai', 'tgl_selesai', 'keterangan',
        'lokasi_tujuan', 'status_pengajuan', 'no_surat', 'file_sppd'
    ];

    // Relasi ke user (pengaju)
    public function user()
    {
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }

    // Relasi ke approval
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }
}

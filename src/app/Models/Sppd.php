<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sppd extends Model
{
    use HasFactory;

    protected $table = 'sppd';

    protected $fillable = [
        'nip_user',
        'pemberi_tugas',
        'jumlah_hari',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan_sppd',
        'lokasi_berangkat',
        'lokasi_tujuan',
        'alat_angkat',
        'tgl_persetujuan',
        'status',
        'no_surat',
        'file_sppd',
        'nip_penyetuju',
        'no_rekening',
        'nama_rekening',
        'keterangan_lain',
        'pemberi_tugas_id',
        'alasan_penolakan', // Pastikan baris ini ada
    ];

    protected $dates = [
        'tgl_mulai',
        'tgl_selesai',
        'tgl_persetujuan',
        'created_at',
        'updated_at'
    ];

    /**
     * Relasi ke user pembuat SPPD
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nip_user', 'nip');
    }

    // Relasi baru untuk mengambil data user penyetuju
    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'nip_penyetuju', 'nip');
    }

    public function pertanggungjawaban()
    {
        // Asumsi nama modelnya adalah Pertanggungjawaban
        return $this->hasOne(Pertanggungjawaban::class, 'sppd_id');
    }
}

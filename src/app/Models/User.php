<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'nip',
        'nik',
        'no_telp',
        'email',
        'password',
        'jenis_kelamin',
        'alamat',
        'tgl_lahir',
        'tempat_lahir',
        'agama',
        'foto',
        'status_perkawinan',
        'area_bekerja',
        'status_aktif',
        'npk_baru',
        'npwp',
        'join_date',
        'jatah_cuti',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi untuk Jabatan (Bawaan)
    public function jabatans()
    {
        return $this->belongsToMany(Jabatan::class, 'riwayat_jabatan', 'nip_user', 'id_jabatan')
                    ->withPivot('tgl_mulai', 'tgl_selesai', 'area_bekerja')
                    ->withTimestamps();
    }

    /**
     * Relasi ke semua Riwayat Jabatan.
     */
    public function riwayatJabatans(): HasMany
    {
        return $this->hasMany(RiwayatJabatan::class, 'nip_user', 'nip');
    }

    /**
     * Relasi Jabatan yang Paling Baru (Terbaru berdasarkan tgl_mulai).
     */
    public function jabatanTerbaru(): HasOne
    {
        return $this->hasOne(RiwayatJabatan::class, 'nip_user', 'nip')
                    ->latestOfMany('tgl_mulai');
    }

    /**
     * Relasi ke Surat Peringatan (SP).
     */
    public function riwayatSP(): HasMany
    {
        return $this->hasMany(SP::class, 'nip_user', 'nip');
    }

    // --- LOGIKA PENGECEKAN PERAN ---

    /**
     * Mengambil nama jabatan terbaru.
     */
    private function getNamaJabatan(): ?string
    {
        return $this->jabatanTerbaru?->jabatan?->nama_jabatan;
    }

    /**
     * Memeriksa apakah user adalah General Manager.
     */
    public function isGm(): bool
    {
        $namaJabatan = $this->getNamaJabatan();
        return $namaJabatan && stripos($namaJabatan, 'General Manager') !== false;
    }

    /**
     * Memeriksa apakah user adalah Senior Analis Keuangan, SDM & Umum.
     */
    public function isSdm(): bool
    {
        $namaJabatan = $this->getNamaJabatan();
        return $namaJabatan && stripos($namaJabatan, 'Senior Analis Keuangan, SDM & Umum') !== false;
    }

    /**
     * Memeriksa apakah user adalah Senior/Manager, TAPI bukan SDM atau GM.
     */
    public function isSenior(): bool
    {
        $namaJabatan = $this->getNamaJabatan();
        if (!$namaJabatan || $this->isGm() || $this->isSdm()) {
            return false;
        }
        return (stripos($namaJabatan, 'Senior') !== false || stripos($namaJabatan, 'Manager') !== false);
    }

    /**
     * Memeriksa apakah user adalah karyawan biasa (Bukan GM, SDM, atau Senior/Manager).
     */
    public function isKaryawanBiasa(): bool
    {
        return !$this->isGm() && !$this->isSdm() && !$this->isSenior();
    }

    /**
     * Memeriksa apakah user berwenang membuat Surat Peringatan (SP).
     */
    public function canCreatePeringatan(): bool
    {
        // Wewenang membuat SP umumnya dimiliki oleh peran SDM
        return $this->isSdm();
    }

    /**
     * Memeriksa apakah user berwenang mengelola data karyawan (misalnya CRUD data pribadi/jabatan).
     */
    public function canManageKaryawan(): bool
    {
        return $this->isGm() || $this->isSdm();
    }

    public function gaji(): HasMany
    {
        return $this->hasMany(Gaji::class);
    }
}

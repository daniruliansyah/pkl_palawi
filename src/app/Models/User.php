<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
<<<<<<< Updated upstream
<<<<<<< Updated upstream
=======
use Illuminate\Notifications\Notifiable; // <-- INI YANG UTAMA
>>>>>>> Stashed changes
=======
use Illuminate\Notifications\Notifiable; // <-- INI YANG UTAMA
>>>>>>> Stashed changes

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <-- PASTIKAN Notifiable ADA DI SINI

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

<<<<<<< Updated upstream
<<<<<<< Updated upstream
    public function riwayatJabatans(): HasMany
=======
=======
>>>>>>> Stashed changes
    // Relasi ke RiwayatJabatan
    public function riwayatJabatans()
>>>>>>> Stashed changes
    {
        return $this->hasMany(RiwayatJabatan::class, 'nip_user', 'nip');
    }

<<<<<<< Updated upstream
<<<<<<< Updated upstream
    public function jabatanTerbaru(): HasOne
=======
=======
>>>>>>> Stashed changes
    // Relasi Jabatan yang Paling Baru
    public function jabatanTerbaru()
>>>>>>> Stashed changes
    {
        return $this->hasOne(RiwayatJabatan::class, 'nip_user', 'nip')
                    ->latestOfMany('tgl_mulai');
    }

<<<<<<< Updated upstream
<<<<<<< Updated upstream
    public function riwayatSP(): HasMany
=======
=======
>>>>>>> Stashed changes
    // Relasi ke Surat Peringatan (SP)
    public function riwayatSP()
>>>>>>> Stashed changes
    {
        return $this->hasMany(SP::class, 'nip_user', 'nip');
    }

<<<<<<< Updated upstream
<<<<<<< Updated upstream
    // --- LOGIKA PENGECEKAN PERAN ---

    private function getNamaJabatan(): ?string
    {
        return $this->jabatanTerbaru?->jabatan?->nama_jabatan;
    }

    public function isGm(): bool
    {
        $namaJabatan = $this->getNamaJabatan();
        return $namaJabatan && stripos($namaJabatan, 'General Manager') !== false;
    }

    /**
     * Memeriksa apakah user adalah SDM (berdasarkan nama jabatan spesifik).
     */
    public function isSdm(): bool
    {
        $namaJabatan = $this->getNamaJabatan();
        return $namaJabatan && stripos($namaJabatan, 'Senior Analis Keuangan, SDM & Umum') !== false;
    }

    /**
     * Memeriksa apakah user adalah Senior, TAPI bukan SDM atau GM.
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
     * Method baru: Memeriksa apakah user adalah karyawan biasa.
     */
    public function isKaryawanBiasa(): bool
    {
        // Karyawan biasa adalah mereka yang BUKAN GM, BUKAN SDM, dan BUKAN Senior.
        return !$this->isGm() && !$this->isSdm() && !$this->isSenior();
    }

    public function canCreatePeringatan(): bool
    {
        // Wewenang membuat SP dimiliki oleh peran SDM
        return $this->isSdm();
    }

    public function canManageKaryawan(): bool
    {
        return $this->isGm() || $this->isSdm();
    }
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
}


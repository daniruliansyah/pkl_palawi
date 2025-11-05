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

    // === PERUBAHAN DI SINI: Menambahkan Primary Key NIP ===
    // Ini penting karena semua relasi dan controller Anda menggunakan NIP
    // protected $primaryKey = 'nip';
    // public $incrementing = false;
    // protected $keyType = 'string';
    // ===================================================


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

    // --- LOGIKA PENGECEKAN PERAN (DISESUAIKAN UNTUK ALUR CUTI) ---

    /**
     * Mengambil nama jabatan terbaru.
     */
    private function getNamaJabatan(): ?string
    {
        // Menggunakan relasi 'jabatanTerbaru' lalu 'jabatan'
        return $this->jabatanTerbaru?->jabatan?->nama_jabatan;
    }

    /**
     * Memeriksa apakah user adalah General Manager.
     * (Menggunakan stripos agar lebih fleksibel)
     */
    public function isGm(): bool
    {
        $namaJabatan = $this->getNamaJabatan();
        return $namaJabatan && stripos($namaJabatan, 'General Manager') !== false;
    }

    /**
     * Memeriksa apakah user adalah Senior Analis Keuangan, SDM & Umum.
     * (Sesuai seeder)
     */
    public function isSdm(): bool
    {
        $namaJabatan = $this->getNamaJabatan();
        return $namaJabatan && stripos($namaJabatan, 'Senior Analis Keuangan, SDM & Umum') !== false;
    }

    /**
     * === FUNGSI DIPERBAIKI ===
     * Memeriksa apakah user adalah Manager.
     * (Logika diubah untuk memastikan 'General Manager' tidak terhitung sebagai 'Manager')
     */
    public function isManager(): bool
    {
        $namaJabatan = $this->getNamaJabatan();

        // 1. Pastikan dia BUKAN General Manager
        if ($this->isGm()) {
            return false;
        }

        // 2. Jika bukan GM, periksa apakah dia 'Manager'
        return $namaJabatan &&
            (
                // Cek spesifik dari seeder
                stripos($namaJabatan, 'Manager Perencanaan dan Standarisasi') !== false ||
                // Cek 'Manager' lain (jika ada)
                stripos($namaJabatan, 'Manager') !== false
            );
    }


    /**
     * === FUNGSI DIPERBARUI ===
     * Memeriksa apakah user adalah Senior, TAPI bukan SDM, GM, atau Manager.
     * (LOGIKA DIPERBARUI: TIDAK LAGI MENCAKUP MANAGER)
     */
    public function isSenior(): bool
    {
        $namaJabatan = $this->getNamaJabatan();
        // Cek dulu apakah dia peran lain
        if (!$namaJabatan || $this->isGm() || $this->isSdm() || $this->isManager()) {
            return false;
        }
        // Cek "Senior Analis Pengelolaan" (dari seeder) ATAU "Senior" lain
        return $namaJabatan &&
            (stripos($namaJabatan, 'Senior Analis Pengelolaan Destinasi') !== false ||
                (stripos($namaJabatan, 'Senior') !== false && !$this->isSdm()));
    }

    /**
     * === FUNGSI DIPERBARUI ===
     * Memeriksa apakah user adalah karyawan biasa (Bukan GM, SDM, Senior, atau Manager).
     */
    public function isKaryawanBiasa(): bool
    {
        // Pengecekan Karyawan Biasa dari seeder
        $namaJabatan = $this->getNamaJabatan();
        if ($namaJabatan && stripos($namaJabatan, 'Karyawan Biasa') !== false) {
            return true;
        }

        // Fallback: jika bukan salah satu peran di atas
        return !$this->isGm() && !$this->isSdm() && !$this->isManager() && !$this->isSenior();
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

    public function riwayatPendidikan(): HasMany
    {
        // Karena kita mengikuti konvensi Laravel (user_id),
        // kita tidak perlu menentukan nama kolom foreign key.
        return $this->hasMany(RiwayatPendidikan::class);
    }
}


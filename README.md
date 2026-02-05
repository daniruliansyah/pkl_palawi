# 🏢 PALAWI HRIS (Human Resource Information System)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vite](https://img.shields.io/badge/vite-%23646CFF.svg?style=for-the-badge&logo=vite&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)

## 📖 Tentang Project

**PALAWI HRIS** adalah sebuah platform berbasis web yang dikembangkan untuk mendigitalkan proses administrasi dan manajemen Sumber Daya Manusia (SDM) di lingkungan **PT. PALAWI RISORSIS**.

Tujuan utama dari sistem ini adalah untuk meningkatkan efisiensi operasional divisi SDM dengan mengotomatisasi berbagai alur kerja manual, memastikan integrasi data yang akurat, dan mempercepat proses persetujuan internal.

---

## 🤝 Tim Pengembang

Proyek ini dikembangkan oleh kelompok yang terdiri dari 3 anggota dengan pembagian tanggung jawab sebagai berikut:

| Nama Anggota | Peran & Tanggung Jawab Fitur |
| :--- | :--- |
| **Dani Ruliansyah** | Manajemen Data Karyawan & Manajemen Gaji (Payroll) |
| **Shofie Ciliah Hermawan** | Pengajuan Surat Cuti (Multi-Level Approval) |
| **Nadea Yiyian Salsabila** | Pengajuan Surat Perjalanan Dinas (SPD) |

---

## 🚀 Fitur Utama

Sistem ini dilengkapi dengan berbagai modul untuk mendukung aktivitas HR sehari-hari:

### 👥 1. Manajemen Data Karyawan (Dani Ruliansyah)
Pusat database karyawan yang terintegrasi, memungkinkan pengelolaan data pribadi, riwayat pekerjaan, dan informasi administratif lainnya secara terpusat.

### 💰 2. Manajemen Gaji / Payroll (Dani Ruliansyah)
Sistem penggajian otomatis yang mengakomodasi perhitungan kompleks, termasuk **13 jenis potongan gaji** yang telah disesuaikan dengan regulasi dan data dari pimpinan SDM Head Office (HO).

### 📅 3. Pengajuan Surat Cuti (Shofie Ciliah Hermawan)
Sistem pengajuan cuti digital dengan mekanisme persetujuan berjenjang (Multi-level approval):
1. **Senior Divisi** (Approval Tahap 1)
2. **SDM / HRD** (Approval Tahap 2)
3. **General Manager** (Final Approval)

### ✈️ 4. Pengajuan Surat Perjalanan Dinas / SPD (Nadea Yiyian Salsabila)
Fasilitas bagi karyawan untuk mengajukan permohonan dinas luar kota. Sistem secara otomatis mengarahkan permintaan persetujuan kepada atasan yang memberikan perintah dinas.

### ⚠️ 5. Pembuatan Surat Peringatan (SP)
Modul untuk pembuatan dan pencatatan Surat Peringatan secara digital sebagai bagian dari manajemen kedisiplinan karyawan.

---

## 💻 Instalasi dan Cara Menjalankan

Ikuti langkah-langkah berikut untuk menjalankan project ini di lingkungan lokal Anda (Localhost).

### Prasyarat
Pastikan Anda telah menginstal:
* PHP & Composer
* Node.js & NPM
* Database Server (MySQL/MariaDB)

### Langkah-langkah

1. **Clone Repository**
   ```bash
   git clone [https://github.com/username/palawi-hris.git](https://github.com/username/palawi-hris.git)
   cd palawi-hris
   ```

2. **Install Dependencies (Backend)**
   ```bash
   composer update
   ```

3. **Install Dependencies (Frontend)**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**
   Salin file contoh .env dan sesuaikan konfigurasi database Anda:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Jangan lupa buat database baru di MySQL dan sesuaikan `DB_DATABASE` di file `.env`.*

5. **Migrasi Database**
   ```bash
   php artisan migrate
   ```

6. **Jalankan Project**
   Buka 2 terminal berbeda
   **terminal 1**
   ```bash
   npm run dev
   ```
   **terminal 2**
   ```bash
   php artisan serve
   ```
Akses aplikasi melalui browser di [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔒 Lisensi

Project ini adalah properti internal **PT. PALAWI RISORSIS**. Penggunaan, penyalinan, atau distribusi kode sumber tanpa izin dilarang.

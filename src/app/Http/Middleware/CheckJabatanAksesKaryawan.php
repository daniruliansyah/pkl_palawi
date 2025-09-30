<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckJabatanAksesKaryawan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('login'); // Ganti dengan route login Anda
        }

        $user = Auth::user();

        // Asumsi struktur relasi: User -> jabatanTerbaru -> jabatan -> nama_jabatan
        $jabatan = $user->jabatanTerbaru->jabatan->nama_jabatan ?? '';

        // Gunakan strtolower untuk pencarian case-insensitive
        $jabatanLower = strtolower($jabatan);

        // Logic baru: Cek apakah user memiliki peran yang diizinkan berdasarkan KATA KUNCI
        $isAllowed = false;

        // General Manager (Peran spesifik)
        $isGM = str_contains($jabatanLower, 'general manager');

        // Senior SDM (Mengandung 'senior' DAN 'sdm')
        $isSeniorSDM = str_contains($jabatanLower, 'senior') && str_contains($jabatanLower, 'sdm');

        // Staff SDM (Mengandung 'staff' DAN 'sdm')
        $isStaffSDM = str_contains($jabatanLower, 'staff') && str_contains($jabatanLower, 'sdm');

        // Jika salah satu kondisi terpenuhi, user diizinkan
        if ($isGM || $isSeniorSDM || $isStaffSDM) {
            $isAllowed = true;
        }

        if ($isAllowed) {
            // User diizinkan, lanjutkan ke Controller
            return $next($request);
        }

        // User tidak diizinkan, arahkan kembali atau tampilkan pesan error
        // Ini akan mencegah user biasa mengakses URL daftar karyawan
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}

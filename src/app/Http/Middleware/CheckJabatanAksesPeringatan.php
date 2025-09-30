<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckJabatanAksesPeringatan
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

        // Kriteria: Harus mengandung 'senior' DAN 'sdm'
        $hasSenior = str_contains($jabatanLower, 'senior');
        $hasSDM = str_contains($jabatanLower, 'sdm');

        $isSeniorSDM = $hasSenior && $hasSDM;

        if ($isSeniorSDM) {
            // User Senior SDM diizinkan, lanjutkan ke Controller
            return $next($request);
        }

        // Jika user bukan Senior SDM, arahkan kembali ke dashboard
        return redirect('/dashboard')->with('error', 'Akses dibatasi. Hanya Senior SDM yang dapat melihat halaman ini.');
    }
}
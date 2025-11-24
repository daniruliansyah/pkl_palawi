<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPendidikan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiwayatPendidikanController extends Controller
{
    /**
     * Menampilkan form untuk menambah riwayat pendidikan baru.
     * Kita memerlukan $karyawan (User) agar tahu riwayat ini milik siapa.
     */
    public function create(User $karyawan)
    {
        // Anda perlu membuat view 'pages.karyawan.pendidikan.create'
        return view('pages.pendidikan.create', compact('karyawan'));
    }

    /**
     * Menyimpan riwayat pendidikan baru ke database.
     */
    public function store(Request $request, User $karyawan)
    {
        $validatedData = $request->validate([
            'jenjang' => 'required|string|max:50',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:100',
            'tahun_masuk' => 'required|numeric|digits:4',
            'tahun_lulus' => 'required|numeric|digits:4|after_or_equal:tahun_masuk',
            'ipk' => 'nullable|numeric|min:0|max:4.00',
            'link_berkas' => 'required|string|max:255',
        ]);

        try {
            // Langsung buat relasi dari $karyawan
            $karyawan->riwayatPendidikan()->create($validatedData);

            // Redirect kembali ke halaman detail karyawan
            return redirect()->route('karyawan.show', $karyawan->id)
                             ->with('success', 'Riwayat pendidikan berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error("Gagal menyimpan riwayat pendidikan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Menampilkan form untuk mengedit riwayat pendidikan.
     * Kita menggunakan $pendidikan (RiwayatPendidikan) yang sudah ada.
     */
    public function edit(RiwayatPendidikan $pendidikan)
    {
        // Mengambil data karyawan (user) dari relasi
        $karyawan = $pendidikan->user;
        
        // Anda perlu membuat view 'pages.karyawan.pendidikan.edit'
        return view('pages.pendidikan.edit', compact('pendidikan', 'karyawan'));
    }

    /**
     * Update data riwayat pendidikan yang sudah ada.
     */
    public function update(Request $request, RiwayatPendidikan $pendidikan)
    {
        $validatedData = $request->validate([
            'jenjang' => 'required|string|max:50',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:100',
            'tahun_masuk' => 'required|numeric|digits:4',
            'tahun_lulus' => 'required|numeric|digits:4|after_or_equal:tahun_masuk',
            'ipk' => 'nullable|numeric|min:0|max:4.00',
            'link_berkas' => 'required|string|max:255',
        ]);

        try {
            $pendidikan->update($validatedData);

            // Redirect kembali ke halaman detail karyawan (pemilik riwayat ini)
            return redirect()->route('karyawan.show', $pendidikan->user_id)
                             ->with('success', 'Riwayat pendidikan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Gagal update riwayat pendidikan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    /**
     * Menghapus data riwayat pendidikan.
     */
    public function destroy(RiwayatPendidikan $pendidikan)
    {
        try {
            $userId = $pendidikan->user_id;
            $pendidikan->delete();

            return redirect()->route('karyawan.show', $userId)
                             ->with('success', 'Riwayat pendidikan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Gagal hapus riwayat pendidikan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}

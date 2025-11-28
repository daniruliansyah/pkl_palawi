<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPangkatPerusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiwayatPangkatPerusahaanController extends Controller
{
    public function create(User $karyawan)
    {
        // View: resources/views/pages/pangkat/create.blade.php
        return view('pages.karyawan.pangkat_perusahaan.create', compact('karyawan'));
    }

    public function store(Request $request, User $karyawan)
    {
        $validatedData = $request->validate([
            'gol_ruang'   => 'required|string|max:50',
            'tmt_gol'     => 'required|date',
            'no_sk'       => 'required|string|max:100',
            'tgl_sk'      => 'required|date',
            'link_berkas' => 'nullable|string|max:255',
        ]);

        try {
            $karyawan->riwayatPangkatPerusahaan()->create($validatedData);

            return redirect()->route('karyawan.show', $karyawan->id)
                             ->with('success', 'Riwayat pangkat berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error("Gagal menyimpan riwayat pangkat: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(RiwayatPangkatPerusahaan $pangkat)
    {
        $karyawan = $pangkat->user;
        // View: resources/views/pages/pangkat/edit.blade.php
        return view('pages.karyawan.pangkat_perusahaan.edit', compact('pangkat', 'karyawan'));
    }

    public function update(Request $request, RiwayatPangkatPerusahaan $pangkat)
    {
        $validatedData = $request->validate([
            'gol_ruang'   => 'required|string|max:50',
            'tmt_gol'     => 'required|date',
            'no_sk'       => 'required|string|max:100',
            'tgl_sk'      => 'required|date',
            'link_berkas' => 'nullable|string|max:255',
        ]);

        try {
            $pangkat->update($validatedData);

            return redirect()->route('karyawan.show', $pangkat->user_id)
                             ->with('success', 'Riwayat pangkat berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Gagal update riwayat pangkat: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(RiwayatPangkatPerusahaan $pangkat)
    {
        try {
            $userId = $pangkat->user_id;
            $pangkat->delete();

            return redirect()->route('karyawan.show', $userId)
                             ->with('success', 'Riwayat pangkat berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Gagal hapus riwayat pangkat: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
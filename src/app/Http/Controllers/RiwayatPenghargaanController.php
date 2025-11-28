<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPenghargaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiwayatPenghargaanController extends Controller
{
    public function create(User $karyawan)
    {
        // View: resources/views/pages/penghargaan/create.blade.php
        return view('pages.karyawan.penghargaan.create', compact('karyawan'));
    }

    public function store(Request $request, User $karyawan)
    {
        $validatedData = $request->validate([
            'nama_penghargaan' => 'required|string|max:255',
            'tgl_terima'       => 'required|date',
            'link_berkas'      => 'nullable|string|max:255',
        ]);

        try {
            $karyawan->riwayatPenghargaan()->create($validatedData);

            return redirect()->route('karyawan.show', $karyawan->id)
                             ->with('success', 'Riwayat penghargaan berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error("Gagal menyimpan riwayat penghargaan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(RiwayatPenghargaan $penghargaan)
    {
        $karyawan = $penghargaan->user;
        // View: resources/views/pages/penghargaan/edit.blade.php
        return view('pages.karyawan.penghargaan.edit', compact('penghargaan', 'karyawan'));
    }

    public function update(Request $request, RiwayatPenghargaan $penghargaan)
    {
        $validatedData = $request->validate([
            'nama_penghargaan' => 'required|string|max:255',
            'tgl_terima'       => 'required|date',
            'link_berkas'      => 'nullable|string|max:255',
        ]);

        try {
            $penghargaan->update($validatedData);

            return redirect()->route('karyawan.show', $penghargaan->user_id)
                             ->with('success', 'Riwayat penghargaan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Gagal update riwayat penghargaan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(RiwayatPenghargaan $penghargaan)
    {
        try {
            $userId = $penghargaan->user_id;
            $penghargaan->delete();

            return redirect()->route('karyawan.show', $userId)
                             ->with('success', 'Riwayat penghargaan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Gagal hapus riwayat penghargaan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
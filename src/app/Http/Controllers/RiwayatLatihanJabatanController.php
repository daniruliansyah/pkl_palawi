<?php

namespace App\Http\Controllers;

use App\Models\RiwayatLatihanJabatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiwayatLatihanJabatanController extends Controller
{
    public function create(User $karyawan)
    {
        // View: resources/views/pages/latihan_jabatan/create.blade.php
        return view('pages.karyawan.latihan_jabatan.create', compact('karyawan'));
    }

    public function store(Request $request, User $karyawan)
    {
        $validatedData = $request->validate([
            'nama_latihan' => 'required|string|max:255',
            'tgl_mulai'    => 'required|date',
            'tgl_selesai'  => 'required|date|after_or_equal:tgl_mulai',
            'link_berkas'  => 'nullable|string|max:255',
        ]);

        try {
            $karyawan->riwayatLatihanJabatan()->create($validatedData);

            return redirect()->route('karyawan.show', $karyawan->id)
                             ->with('success', 'Riwayat latihan jabatan berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error("Gagal menyimpan riwayat latihan jabatan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(RiwayatLatihanJabatan $latihan)
    {
        $karyawan = $latihan->user;
        // View: resources/views/pages/latihan_jabatan/edit.blade.php
        return view('pages.karyawan.latihan_jabatan.edit', compact('latihan', 'karyawan'));
    }

    public function update(Request $request, RiwayatLatihanJabatan $latihan)
    {
        $validatedData = $request->validate([
            'nama_latihan' => 'required|string|max:255',
            'tgl_mulai'    => 'required|date',
            'tgl_selesai'  => 'required|date|after_or_equal:tgl_mulai',
            'link_berkas'  => 'nullable|string|max:255',
        ]);

        try {
            $latihan->update($validatedData);

            return redirect()->route('karyawan.show', $latihan->user_id)
                             ->with('success', 'Riwayat latihan jabatan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Gagal update riwayat latihan jabatan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(RiwayatLatihanJabatan $latihan)
    {
        try {
            $userId = $latihan->user_id;
            $latihan->delete();

            return redirect()->route('karyawan.show', $userId)
                             ->with('success', 'Riwayat latihan jabatan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Gagal hapus riwayat latihan jabatan: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
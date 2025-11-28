<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKpo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiwayatKpoController extends Controller
{
    public function create(User $karyawan)
    {
        // Pastikan Anda membuat view: resources/views/pages/kpo/create.blade.php
        return view('pages.karyawan.kpo.create', compact('karyawan'));
    }

    public function store(Request $request, User $karyawan)
    {
        $validatedData = $request->validate([
            'nama_jabatan'    => 'required|string|max:255',
            'nama_organisasi' => 'required|string|max:255',
            'tgl_jabat'       => 'required|date',
            'link_berkas'     => 'nullable|string|max:255', // Nullable jika opsional
        ]);

        try {
            $karyawan->riwayatKpo()->create($validatedData);

            return redirect()->route('karyawan.show', $karyawan->id)
                             ->with('success', 'Riwayat KPO berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error("Gagal menyimpan riwayat KPO: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(RiwayatKpo $kpo)
    {
        $karyawan = $kpo->user;
        // Pastikan Anda membuat view: resources/views/pages/kpo/edit.blade.php
        return view('pages.karyawan.kpo.edit', compact('kpo', 'karyawan'));
    }

    public function update(Request $request, RiwayatKpo $kpo)
    {
        $validatedData = $request->validate([
            'nama_jabatan'    => 'required|string|max:255',
            'nama_organisasi' => 'required|string|max:255',
            'tgl_jabat'       => 'required|date',
            'link_berkas'     => 'nullable|string|max:255',
        ]);

        try {
            $kpo->update($validatedData);

            return redirect()->route('karyawan.show', $kpo->user_id)
                             ->with('success', 'Riwayat KPO berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Gagal update riwayat KPO: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(RiwayatKpo $kpo)
    {
        try {
            $userId = $kpo->user_id;
            $kpo->delete();

            return redirect()->route('karyawan.show', $userId)
                             ->with('success', 'Riwayat KPO berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Gagal hapus riwayat KPO: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
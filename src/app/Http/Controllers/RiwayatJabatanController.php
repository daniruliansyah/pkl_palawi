<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatJabatan;
use App\Models\User;
use App\Models\Jabatan;

class RiwayatJabatanController extends Controller
{
    // Menggunakan Route Model Binding untuk mendapatkan instance model
    public function edit(User $karyawan, RiwayatJabatan $riwayat)
    {
        // Pastikan riwayat jabatan yang akan diedit memang milik karyawan tersebut
        if ($riwayat->nip_user !== $karyawan->nip) {
            abort(403); // Unauthorized action
        }
        
        $jabatans = Jabatan::all();
        
        return view('pages.karyawan.editriwayat', compact('karyawan', 'riwayat', 'jabatans'));
    }

    public function update(Request $request, User $karyawan, RiwayatJabatan $riwayat)
    {
        // Validasi data input
        $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'area_bekerja' => 'required|string',
            
            // --- TAMBAHAN ---
            'jenjang' => 'nullable|string|max:10', // Sesuaikan max length
            // --- END TAMBAHAN ---
        ]);
        
        // Pastikan riwayat jabatan yang akan diupdate memang milik karyawan tersebut
        if ($riwayat->nip_user !== $karyawan->nip) {
            abort(403); // Unauthorized action
        }

        $riwayat->update([
            'id_jabatan' => $request->jabatan_id,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'area_bekerja' => $request->area_bekerja,

            // --- TAMBAHAN ---
            'jenjang' => $request->jenjang,
            // --- END TAMBAHAN ---
        ]);

        return redirect()->route('karyawan.show', $karyawan->id)
                        ->with('success', 'Riwayat jabatan berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;

class CutiController extends Controller
{
    public function index()
    {
        $cutis = Cuti::with('user')->latest()->get();
        return view('pages.cuti.index', compact('cutis'));
    }

    public function create()
    {
        return view('pages.cuti.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi semua input dari form
        $request->validate([
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'jumlah_hari' => 'required|integer|min:1|max:12',
            'keterangan' => 'required|string',
            'file_izin' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Opsional, maks 2MB
        ]);

        $pathFileIzin = null;

        // 2. Proses unggahan berkas jika ada
        if ($request->hasFile('file_izin')) {
            // Simpan berkas di dalam folder 'public/file_izin'
            // dan dapatkan path-nya untuk disimpan ke database.
            $pathFileIzin = $request->file('file_izin')->store('file_izin', 'public');
        }

        // 3. Simpan data ke database
        Cuti::create([
            'nip_user' => auth()->user()->nip,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'jumlah_hari' => $request->jumlah_hari,
            'keterangan' => $request->keterangan,
            'file_izin' => $pathFileIzin,
            'tgl_upload' => now(), // Menambahkan tanggal upload secara otomatis
            // 'lokasi_tujuan' sepertinya sudah tidak ada di form baru Anda
        ]);

        // 4. Arahkan kembali dengan pesan sukses
        return redirect()->route('cuti.index')
                        ->with('success', 'Pengajuan Cuti berhasil dibuat.');
    }

    public function show($id)
{
    $cuti = Cuti::with('user')->findOrFail($id);
    return view('cuti.show', compact('cuti'));
}

}

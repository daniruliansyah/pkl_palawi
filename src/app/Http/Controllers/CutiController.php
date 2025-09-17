<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan cuti.
     */
    public function index()
    {
        // Mengambil data cuti beserta relasi user, diurutkan dari yang terbaru
        $cutis = Cuti::with('user')->latest()->get();
        return view('pages.cuti.index', compact('cutis'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan cuti baru.
     */
    public function create()
    {
        return view('pages.cuti.create');
    }

    /**
     * Menyimpan pengajuan cuti baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi semua input dari form dan simpan hasilnya
        $validatedData = $request->validate([
            'jenis_izin'    => 'required|string',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'jumlah_hari'   => 'required|integer|min:1|max:12',
            'keterangan'    => 'required|string',
            'file_izin'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Opsional, maks 2MB
        ]);

        $pathFileIzin = null;

        // 2. Proses unggahan berkas jika ada
        if ($request->hasFile('file_izin')) {
            $pathFileIzin = $request->file('file_izin')->store('file_izin', 'public');
        }

        // --- BAGIAN YANG HILANG: LOGIKA PEMBUATAN NOMOR SURAT ---
        $tahun = date('Y');
        $bulan = date('m');
        $lastCuti = Cuti::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->latest('id')->first();
        $nomorUrut = $lastCuti ? (int)substr($lastCuti->no_surat, -3) + 1 : 1;
        $noSurat = sprintf("CUTI/%s/%s/%03d", $tahun, $bulan, $nomorUrut);
        // --- SELESAI ---

        // 3. Simpan data ke database menggunakan data yang sudah divalidasi
        Cuti::create(array_merge(
            $validatedData, // Semua data dari form ada di sini, termasuk 'jenis_izin'
            [
                'nip_user'       => Auth::user()->nip,
                'no_surat'         => $noSurat,
                'file_izin' => $pathFileIzin, // Pastikan ini nama kolom di database Anda
                'status_pengajuan' => 'Diajukan',     // Memberi status default
                'tgl_upload'       => now(),
                'nip_user_ssdm'    => Auth::user()->nip,
                'nip_user_sdm'    => Auth::user()->nip,
                'nip_user_gm'    => Auth::user()->nip,
            ]
        ));

        // 4. Arahkan kembali ke halaman index dengan pesan sukses
        return redirect()->route('cuti.index')
                         ->with('success', 'Pengajuan Cuti berhasil dibuat.');
    }

    /**
     * Menampilkan detail dari satu pengajuan cuti.
     */
    public function show($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);
        // Pastikan path view sudah benar (sesuai dengan index & create)
        return view('pages.cuti.show', compact('cuti'));
    }
}
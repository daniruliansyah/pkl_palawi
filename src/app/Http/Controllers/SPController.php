<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SP;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Baris ini yang harus ditambahkan

class SPController extends Controller
{
    /**
     * Menampilkan daftar semua surat peringatan.
     */
    public function index()
    {
        $sp = SP::with('user')->latest()->get();
        return view('pages.sp.index', compact('sp'));
    }

    /**
     * Menampilkan form untuk membuat surat peringatan.
     */
    public function create()
    {
        return view('pages.sp.create');
    }

    /**
     * Menyimpan surat peringatan baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip_user'      => 'required|exists:users,nip',
            'tgl_sp_terbit' => 'required|date',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'ket_peringatan'=> 'required|string|max:500',
            'file_bukti'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Generate nomor surat
        $tahun      = date('Y');
        $bulan      = date('m');
        $lastSP     = SP::whereYear('created_at', $tahun)
                     ->whereMonth('created_at', $bulan)
                     ->latest('id')
                     ->first();
        $nomorUrut = $lastSP ? (int)substr($lastSP->no_surat, -3) + 1 : 1;
        $noSurat    = sprintf("SP/%s/%s/%03d", $tahun, $bulan, $nomorUrut);

        // Upload file bukti (jika ada)
        $pathFileBukti = null;
        if ($request->hasFile('file_bukti')) {
            $pathFileBukti = $request->file('file_bukti')->store('file_bukti', 'public');
        }

        // Data untuk template PDF
        $spData = array_merge($validatedData, ['no_surat' => $noSurat]);
        $spData['user']       = User::where('nip', $validatedData['nip_user'])->first();
        $spData['file_bukti'] = $pathFileBukti;

        // Data tanda tangan GM & SDM
        $ttd_gm = (object)[
            'jabatan'      => 'General Manager',
            'nama_lengkap' => 'Nama General Manager',
            'nip'          => 'NIP GM',
            'ttd_path'     => 'images/barcode_gm.jpg'
        ];

        $ttd_sdm = (object)[
            'jabatan'      => 'Senior Analis Keuangan, SDM & Umum',
            'nama_lengkap' => 'Nama Senior Analis',
            'nip'          => 'NIP SDM',
            'ttd_path'     => 'images/barcode_sdm.jpg'
        ];

        // Buat PDF surat peringatan
        $pdf = Pdf::loadView('pages.sp.template-surat', [
            'sp'       => (object)$spData,
            'ttd_gm'   => $ttd_gm,
            'ttd_sdm'  => $ttd_sdm
        ]);

        // Simpan PDF ke storage
        $pathFileSP = 'file_sp/' . Str::slug($noSurat) . '.pdf';
        Storage::disk('public')->put($pathFileSP, $pdf->output());

        // Simpan data ke database
        SP::create([
            'nip_user'      => $validatedData['nip_user'],
            'no_surat'      => $noSurat,
            'tgl_sp_terbit' => $validatedData['tgl_sp_terbit'],
            'tgl_mulai'     => $validatedData['tgl_mulai'],
            'tgl_selesai'   => $validatedData['tgl_selesai'],
            'ket_peringatan'=> $validatedData['ket_peringatan'],
            'file_bukti'    => $pathFileBukti,
            'file_sp'       => $pathFileSP,
        ]);

        return redirect()->route('sp.index')->with('success', 'Surat Peringatan berhasil dibuat.');
    }

    /**
     * Pencarian karyawan berdasarkan nama atau NIP.
     */
   public function cariKaryawan(Request $request)
    {
        $search = $request->input('q');

        // Cari user berdasarkan NIP atau nama lengkap
        $users = User::where('nip', 'like', "%{$search}%")
                     ->orWhere('nama_lengkap', 'like', "%{$search}%")
                     ->limit(10)
                     ->get(['nip', 'nama_lengkap']);

        // Format data agar sesuai dengan Select2 (id dan text)
        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->nip,
                'text' => "{$user->nama_lengkap} ({$user->nip})",
            ];
        });

        // Kirim hasil dalam format yang benar untuk Select2
        return response()->json([
            'results' => $formattedUsers,
        ]);
    }

    /**
     * Mengunduh file surat peringatan (file_sp).
     */
    public function download($id)
    {
        $sp = SP::findOrFail($id);

        if ($sp->file_sp && Storage::disk('public')->exists($sp->file_sp)) {
            $filePath        = storage_path('app/public/' . $sp->file_sp);
            $safeFileName    = Str::slug($sp->no_surat);
            $downloadFileName= 'Surat-Peringatan-' . $safeFileName . '.pdf';

            return response()->download($filePath, $downloadFileName);
        }

        return redirect()->back()->with('error', 'File surat tidak ditemukan.');
    }

    /**
     * Mengunduh file bukti yang diunggah (file_bukti).
     */
    public function downloadBukti($id)
    {
        $sp = SP::findOrFail($id);

        if ($sp->file_bukti && Storage::disk('public')->exists($sp->file_bukti)) {
            $filePath        = storage_path('app/public/' . $sp->file_bukti);
            $safeFileName    = Str::slug($sp->no_surat);
            $originalName    = pathinfo($filePath, PATHINFO_BASENAME);
            $extension       = pathinfo($originalName, PATHINFO_EXTENSION);
            $downloadFileName= 'Bukti-Pelanggaran-' . $safeFileName . '.' . $extension;

            return response()->download($filePath, $downloadFileName);
        }

        return redirect()->back()->with('error', 'File bukti tidak ditemukan.');
    }
}

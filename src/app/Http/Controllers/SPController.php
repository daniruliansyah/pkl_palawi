<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SP;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SPController extends Controller
{
    /**
     * Menampilkan daftar semua surat peringatan.
     */
    public function index()
    {
        // Mengambil data surat peringatan beserta relasi user, diurutkan dari yang terbaru
        $sps = SP::with('user')->latest()->get();
        return view('pages.sp.index', compact('sps'));
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
    // 1. Perbarui blok validasi ini
    $validatedData = $request->validate([
        'nip_user'       => 'required|exists:users,nip', // <-- WAJIB: Memeriksa NIP karyawan
        'tgl_sp_terbit'  => 'required|date',
        'tgl_mulai'      => 'required|date',
        'tgl_selesai'    => 'required|date|after_or_equal:tgl_mulai',
        'ket_peringatan' => 'required|string|max:500',
        'file_sp'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    // 2. Proses unggahan berkas jika ada
    $pathFileSP = null;
    if ($request->hasFile('file_sp')) {
        $pathFileSP = $request->file('file_sp')->store('file_sp', 'public');
    }

    // 3. Logika pembuatan nomor surat
    $tahun = date('Y');
    $bulan = date('m');
    $lastSP = SP::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->latest('id')->first();
    $nomorUrut = $lastSP ? (int)substr($lastSP->no_surat, -3) + 1 : 1;
    $noSurat = sprintf("SP/%s/%s/%03d", $tahun, $bulan, $nomorUrut);
    
    // 4. Simpan data ke database
    // Perhatikan: 'nip_user' sekarang diambil dari $validatedData, bukan Auth::user()
    SP::create(array_merge(
        $validatedData, // Semua data dari form sudah ada di sini, termasuk 'nip_user'
        [
            'no_surat'      => $noSurat,
            // 'file_sp'       => $pathFileSP,
            // Anda mungkin tidak lagi perlu 'tgl_sp_terbit' di sini karena sudah ada di $validatedData
        ]
    ));

    // 5. Arahkan kembali
    return redirect()->route('sp.index')
        ->with('success', 'Surat Peringatan berhasil dibuat.');
    }

    public function cariKaryawan(Request $request)
    {
        // ambil parameter pencarian (mendukung 'q' dan 'term')
        $search = $request->input('q') ?? $request->input('term') ?? '';

        // jika kosong, kembalikan array kosong untuk mencegah pengembalian seluruh data
        if (trim($search) === '') {
            return response()->json(['results' => []]);
        }

        $users = User::select('nip as id', 'nama_lengkap as text')
            ->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                ->orWhere('nip', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json(['results' => $users]);
    }
    /**
     * Menampilkan detail dari satu 
     */
    public function show($id)
    {
        $sp = SP::with('user')->findOrFail($id);
        // Pastikan path view sudah benar (sesuai dengan index & create)
        return view('pages.sp.show', compact('sp'));
    }
}
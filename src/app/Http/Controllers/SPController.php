<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SP;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Notifications\StatusSuratDiperbarui;

class SPController extends Controller
{
    /**
     * FUNGSI INTI: Menampilkan daftar SEMUA SP (untuk admin/pembuat).
     */
    public function index()
    {
        $sp = SP::with('user', 'sdm', 'gm')->latest()->get();
        return view('pages.sp.index', compact('sp'));
    }

    /**
     * FUNGSI INTI: Menampilkan form pembuatan SP baru.
     */
    public function create()
    {
        $jabatanTembusan = Jabatan::pluck('nama_jabatan')->toArray();
        return view('pages.sp.create', compact('jabatanTembusan'));
    }

    /**
     * FUNGSI INTI: Menyimpan data SP baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip_user' => 'required|exists:users,nip',
            'hal_surat' => 'required|string|max:100',
            'jenis_sp' => 'required|in:Pertama,Kedua,Terakhir',
            'isi_surat' => 'required|string',
            'tembusan' => 'nullable|array',
            'tgl_sp_terbit' => 'required|date',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $pathFileBukti = $request->hasFile('file_bukti') ? $request->file('file_bukti')->store('file_bukti_sp', 'public') : null;

        $sp = SP::create(array_merge($validatedData, [
            'nip_pembuat' => Auth::user()->nip,
            'file_bukti' => $pathFileBukti,
            'status_sdm' => 'Menunggu Persetujuan',
            'status_gm' => 'Menunggu',
            'tembusan' => is_array($validatedData['tembusan'] ?? null) ? json_encode($validatedData['tembusan']) : null,
        ]));

        // Kirim notifikasi ke SDM
        $sdmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))->first();
        if ($sdmUser) {
            try {
                $karyawan = User::where('nip', $validatedData['nip_user'])->first();
                $sdmUser->notify(new StatusSuratDiperbarui(
                    Auth::user(), 'Surat Peringatan', 'Menunggu Persetujuan',
                    'Ada SP baru untuk ' . $karyawan->nama_lengkap . ' yang menunggu persetujuan Anda.',
                    route('sp.approvals.index')
                ));
            } catch (\Exception $e) {
                Log::error("Notif gagal (Store SP): " . $e->getMessage());
            }
        }
        return redirect()->route('sp.index')->with('success','Surat Peringatan berhasil dibuat dan diajukan untuk disetujui.');
    }

    /**
     * Menampilkan detail SP (bisa diakses oleh semua yang terlibat).
     */
    public function show($id)
    {
        $sp = SP::with('user','sdm','gm')->findOrFail($id);
        // Anda bisa menambahkan validasi otorisasi di sini jika perlu
        return view('pages.sp.detail', compact('sp'));
    }

    /**
     * Mengunduh file surat SP yang SUDAH JADI.
     */
    public function download($id)
    {
        $sp = SP::findOrFail($id);
        if ($sp->file_sp && Storage::disk('public')->exists($sp->file_sp)) {
            return Storage::disk('public')->download($sp->file_sp, 'SP_' . $sp->jenis_sp . '_' . $sp->user->nama_lengkap . '.pdf');
        }
        return back()->with('error', 'File surat tidak ditemukan atau belum disetujui penuh.');
    }

    /**
     * Menampilkan halaman verifikasi QR Code.
     */
    public function verifikasi($id)
    {
        $sp = SP::with('user','sdm','gm')->find($id);
        if (!$sp || $sp->status_gm !== 'Disetujui') {
            return view('pages.sp.notfound', ['message' => 'Surat Peringatan tidak ditemukan atau belum valid.']);
        }
        return view('pages.sp.verifikasi_info', compact('sp'));
    }

    /**
     * Helper untuk pencarian karyawan di form create.
     */
    public function cariKaryawan(Request $request)
    {
        $search = $request->input('q');
        $users = User::where('nip', 'like', "%{$search}%")->orWhere('nama_lengkap', 'like', "%{$search}%")->limit(10)->get();
        return response()->json(['results' => $users->map(fn($user) => ['id' => $user->nip, 'text' => "{$user->nama_lengkap} ({$user->nip})"])]);
    }
}

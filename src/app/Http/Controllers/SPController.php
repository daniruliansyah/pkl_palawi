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
use Illuminate\Support\Carbon;

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
        $pembuat = Auth::user();
        $karyawan = User::where('nip', $validatedData['nip_user'])->first();
        
        $dataSp = array_merge($validatedData, [
            'no_surat' => $this->generateNoSurat(),
            'nip_pembuat' => $pembuat->nip,
            'file_bukti' => $pathFileBukti,
            'tembusan' => is_array($validatedData['tembusan'] ?? null) ? json_encode($validatedData['tembusan']) : null,
        ]);

        // LOGIKA BARU: Cek apakah pembuat adalah SDM
        if ($pembuat->isSdm()) {
            // JIKA PEMBUAT ADALAH SDM
            // Langsung setujui level SDM dan teruskan ke GM
            $dataSp['status_sdm'] = 'Disetujui SDM';
            $dataSp['nip_user_sdm'] = $pembuat->nip; // Dicatat sebagai disetujui oleh diri sendiri
            $dataSp['tgl_persetujuan_sdm'] = Carbon::now();
            $dataSp['status_gm'] = 'Menunggu Persetujuan';

            // Kirim notifikasi langsung ke GM
            $gmUser = User::whereHas('jabatanTerbaru', fn($q) => $q->where('id_jabatan', 1))->first(); // Asumsi ID 1 adalah GM
            if($gmUser){
                 try {
                    $gmUser->notify(new StatusSuratDiperbarui(
                        $pembuat, 'Surat Peringatan', 'Menunggu Persetujuan',
                        'Ada SP baru untuk ' . $karyawan->nama_lengkap . ' yang menunggu persetujuan Anda.',
                        route('sp.approvals.index')
                    ));
                } catch (\Exception $e) {
                    Log::error("Notif ke GM gagal (Auto-approve): " . $e->getMessage());
                }
            }

        } else {
            // JIKA PEMBUAT BUKAN SDM (alur normal)
            $dataSp['status_sdm'] = 'Menunggu Persetujuan';
            $dataSp['status_gm'] = 'Menunggu';
            
            // Kirim notifikasi ke SDM
            $sdmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))->first();
            if ($sdmUser) {
                try {
                    $sdmUser->notify(new StatusSuratDiperbarui(
                        $pembuat, 'Surat Peringatan', 'Menunggu Persetujuan',
                        'Ada SP baru untuk ' . $karyawan->nama_lengkap . ' yang menunggu persetujuan Anda.',
                        route('sp.approvals.index')
                    ));
                } catch (\Exception $e) {
                    Log::error("Notif ke SDM gagal (Store SP): " . $e->getMessage());
                }
            }
        }
        
        // Buat SP dengan data yang sudah disiapkan
        $sp = SP::create($dataSp);
        
        return redirect()->route('sp.index')->with('success','Surat Peringatan berhasil dibuat dan diajukan untuk disetujui.');
    }

    /**
     * Menampilkan detail SP (bisa diakses oleh semua yang terlibat).
     */
    public function show($id)
    {
        $sp = SP::with('user','sdm','gm')->findOrFail($id);
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
        return view('pages.sp.info', compact('sp'));
    }

    /**
     * Helper untuk pencarian karyawan di form create.
     */
    public function cariKaryawan(Request $request)
    {
        $search = $request->input('term'); 
        if(empty($search)){
            $search = $request->input('q');
        }
        
        $users = User::where('nip', 'like', "%{$search}%")->orWhere('nama_lengkap', 'like', "%{$search}%")->limit(10)->get();
        return response()->json(['results' => $users->map(fn($user) => ['id' => $user->nip, 'text' => "{$user->nama_lengkap} ({$user->nip})"])]);
    }


    // =============================================================
    // HELPER FUNCTIONS
    // =============================================================

    /**
     * Membuat nomor surat SP secara otomatis.
     */
    private function generateNoSurat(): string
    {
        $year = date('Y');

        $lastSp = SP::whereYear('created_at', $year)
            ->whereNotNull('no_surat')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastSp) {
            $parts = explode('/', $lastSp->no_surat);
            $lastNumber = isset($parts[0]) ? (int) $parts[0] : 0;
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return "{$newNumber}/D.1/PAL-ABWWT/{$year}";
    }
}


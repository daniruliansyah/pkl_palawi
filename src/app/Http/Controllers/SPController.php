<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SP;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\StatusSuratDiperbarui;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class SPController extends Controller
{
    /**
     * Menampilkan daftar SP yang dibuat (Hanya untuk yang membuat SP).
     */
    public function index()
    {
        // Asumsi hanya pembuat (biasanya SDM/Admin) yang melihat semua SP
        $sp = SP::with('user', 'sdm', 'gm')->latest()->get();
        return view('pages.sp.index', compact('sp'));
    }

// ---------------------------------------------------------------------

    /**
     * FUNGSI: Untuk menampilkan halaman PERSETUJUAN SP.
     * Halaman ini HANYA diakses oleh atasan (GM, SDM).
     */
    public function indexApproval()
    {
        $user = Auth::user();

        // Approval HANYA SDM dan GM.

        if ($user->isGm()) {
            $spsForApproval = SP::where('status_sdm', 'Disetujui')
                                ->where('status_gm', 'Menunggu Persetujuan')
                                ->latest()->get();
            $spsHistory = SP::where(function ($query) { $query->where('status_gm', 'Disetujui')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'); })->latest()->get();
            // Mengembalikan view dari folder 'approval'
            return view('pages.approval.index-sp-gm', compact('spsForApproval', 'spsHistory'));
        }

        if ($user->isSdm()) {
            $spsForApproval = SP::where('status_sdm', 'Menunggu Persetujuan')
                                ->latest()->get();
            $spsHistory = SP::where(function ($query) { $query->where('status_gm', 'Disetujui')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'); })->latest()->get();
            // Mengembalikan view dari folder 'approval'
            return view('pages.approval.index-sp-sdm', compact('spsForApproval', 'spsHistory'));
        }

        // Jika peran lain mencoba akses, kembalikan ke home atau halaman lain.
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman persetujuan SP.');
    }

// ---------------------------------------------------------------------

    public function create()
    {
        $jabatanTembusan = Jabatan::pluck('nama_jabatan')->toArray();
        // Ambil semua karyawan untuk dipilih yang menerima SP
        $karyawans = User::all();
        return view('pages.sp.create', compact('jabatanTembusan', 'karyawans'));
    }

    public function store(Request $request)
    {
        // Asumsi yang membuat SP adalah user yang berhak (misal: SDM/Admin HRD)
        $userPembuat = Auth::user();

        $validatedData = $request->validate([
            'nip_user'      => 'required|exists:users,nip',
            'hal_surat'     => 'required|string|max:100',
            'jenis_sp'      => 'required|in:Pertama,Kedua,Terakhir',
            'isi_surat'     => 'required|string',
            'tembusan'      => 'nullable|array',
            'tgl_sp_terbit' => 'required|date',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'file_bukti'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $karyawan = User::where('nip', $validatedData['nip_user'])
                         ->with('jabatanTerbaru.jabatan')
                         ->firstOrFail();

        // Cari NIP SDM dan GM
        $sdmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))->first();
        $gmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%General Manager%'))->first();

        // Tentukan Alur Persetujuan Awal
        $statusSdm = 'Menunggu Persetujuan'; $statusGm = 'Menunggu';
        $nipUserSdm = $sdmUser?->nip;
        $nipUserGm = $gmUser?->nip;
        $penerimaNotifikasi = $sdmUser; // Notifikasi pertama ke SDM

        // Override jika yang membuat SP adalah SDM/GM
        if ($userPembuat->isSdm()) {
            $statusSdm = 'Disetujui'; // SDM tandatangan/setuju sendiri
            $statusGm = 'Menunggu Persetujuan';
            $nipUserSdm = $userPembuat->nip;
            $penerimaNotifikasi = $gmUser; // Lanjut ke GM
        } elseif ($userPembuat->isGm()) {
            $statusSdm = 'Disetujui'; // Bypass SDM
            $statusGm = 'Disetujui'; // GM tandatangan/setuju sendiri (Final)
            $nipUserSdm = $userPembuat->nip;
            $nipUserGm = $userPembuat->nip;
            $penerimaNotifikasi = null; // Tidak perlu notifikasi ke orang lain
        }


        // Upload file bukti
        $pathFileBukti = $request->hasFile('file_bukti') ? $request->file('file_bukti')->store('file_bukti_sp', 'public') : null;

        $sp = SP::create(array_merge($validatedData, [
            'no_surat'      => $this->generateNoSurat(),
            'nip_pembuat'   => $userPembuat->nip,
            'file_bukti'    => $pathFileBukti,
            'status_sdm'    => $statusSdm,
            'status_gm'     => $statusGm,
            'nip_user_sdm'  => $nipUserSdm,
            'nip_user_gm'   => $nipUserGm,
            'tembusan'      => is_array($validatedData['tembusan'] ?? null) ? json_encode($validatedData['tembusan']) : null, // Simpan sebagai JSON
        ]));

        // Kirim Notifikasi ke Atasan Pertama yang statusnya 'Menunggu Persetujuan'
        if ($penerimaNotifikasi && ($statusSdm === 'Menunggu Persetujuan' || $statusGm === 'Menunggu Persetujuan')) {
            try {
                $penerimaNotifikasi->notify(new StatusSuratDiperbarui(
                    aktor: auth()->user(),
                    jenisSurat: 'Surat Peringatan',
                    statusBaru: 'Menunggu Persetujuan',
                    keterangan: 'Terdapat SP baru yang menunggu persetujuan Anda untuk karyawan ' . $karyawan->nama_lengkap . '.',
                    url: route('sp.show', $sp->id)
                ));
            } catch (\Exception $e) {
                Log::error("Notif gagal (Store SP): " . $e->getMessage());
            }
        }

        // Jika sudah final (GM yang buat/disetujui langsung), jalankan finalisasi
        if ($sp->status_gm === 'Disetujui') {
            $this->finalizeSp($sp);
        }

        return redirect()->route('sp.index')->with('success','Surat Peringatan berhasil dibuat dan diajukan untuk disetujui.');
    }

// ---------------------------------------------------------------------

    /**
     * Menampilkan detail SP (untuk rute notifikasi dan verifikasi).
     */
    public function show($id)
    {
        $sp = SP::with('user','sdm','gm')->findOrFail($id);
        $user = Auth::user();

        // Cek jika user yang login adalah penerima SP, pembuat, atau salah satu verifier
        $isAuthorized = (
            $user->nip === $sp->nip_user || // Penerima SP
            $user->nip === $sp->nip_pembuat || // Pembuat SP
            $user->nip === $sp->nip_user_sdm || // SDM verifier
            $user->nip === $sp->nip_user_gm // GM verifier
        );

        if (!$isAuthorized) {
             return back()->with('error', 'Anda tidak berhak melihat detail pengajuan ini.');
        }

        return view('pages.sp.detail', compact('sp'));
    }

// ---------------------------------------------------------------------

    /**
     * Mengunduh file surat SP yang sudah disetujui penuh.
     */
    public function download($id)
    {
        $sp = SP::findOrFail($id);

        if ($sp->file_surat && Storage::disk('public')->exists($sp->file_surat)) {
            $filePath = storage_path('app/public/' . $sp->file_surat);
            $safeName = Str::slug(Str::replace('/', '-', $sp->no_surat)) . "_SP_{$sp->jenis_sp}";
            return response()->download($filePath, "{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan atau belum disetujui penuh.');
    }

// ---------------------------------------------------------------------

    /**
     * Menampilkan informasi verifikasi SP dari pemindaian QR Code.
     */
    public function verifikasi($id)
    {
        $sp = SP::with('user','sdm','gm')->find($id);

        if (!$sp) {
            return view('pages.sp.notfound', ['message' => 'Surat Peringatan tidak ditemukan.']);
        }
        return view('pages.sp.verifikasi_info', compact('sp'));
    }

// ---------------------------------------------------------------------

    /**
     * FUNGSI: Untuk memproses SP yang sudah disetujui penuh (PDF Generation & Notification).
     * Fungsi ini dipanggil oleh ApprovalController.
     */
    public function finalizeSp(SP $sp)
    {
        try {
            // 1. Generate PDF dan dapatkan path-nya
            $path = $this->generateSuratPdf($sp);

            if ($path) {
                // 2. Simpan path file surat ke database
                $sp->file_surat = $path;
                $sp->save();

                // 3. Kirim notifikasi final ke penerima SP (Karyawan)
                 $sp->user->notify(new StatusSuratDiperbarui(
                    aktor: Auth::user(),
                    jenisSurat: 'Surat Peringatan',
                    statusBaru: 'Disetujui Penuh',
                    keterangan: "Surat Peringatan ({$sp->jenis_sp}) Anda sudah final. Silakan unduh surat Anda.",
                    url: route('sp.download', $sp->id) // Arahkan ke download
                ));
            }
        } catch (\Exception $e) {
            Log::error("Gagal memfinalisasi SP ID {$sp->id}: " . $e->getMessage());
        }
    }

// ---------------------------------------------------------------------

    // --- HELPER FUNCTIONS ---

    /**
     * Menghasilkan nomor surat SP otomatis.
     */
    private function generateNoSurat(): string
    {
        $tahun = date('Y');
        // Format: [Nomor Urut]/015/SP/SK/ABWWT/[Bulan Romawi]/[Tahun]
        $bulanRomawi = $this->numberToRoman((int)date('m'));
        $lastSPThisYear = SP::whereYear('created_at', $tahun)
                             ->orderBy('id', 'desc')->first();

        $nomorUrut = $lastSPThisYear ? ((int)explode('/', $lastSPThisYear->no_surat)[0] + 1) : 1;

        // Asumsi format '015/SP/SK' adalah kode tetap untuk SP
        return sprintf("%03d/015/SP/SK/ABWWT/%s/%s", $nomorUrut, $bulanRomawi, $tahun);
    }

    /**
     * Mengubah angka menjadi format Romawi (untuk nomor surat).
     */
    protected function numberToRoman($number)
    {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $roman = '';
        foreach ($map as $rom => $val) {
            while ($number >= $val) {
                $number -= $val;
                $roman .= $rom;
            }
        }
        return $roman;
    }

    /**
     * Menghasilkan URL verifikasi untuk QR Code.
     */
    protected function generateQrCodeUrl(SP $sp)
    {
        return route('sp.verifikasi', ['id' => $sp->id]);
    }

    /**
     * Menghasilkan file PDF Surat Peringatan dan menyimpannya.
     */
    protected function generateSuratPdf(SP $sp)
    {
        try {
            $fileName = Str::slug(Str::replace('/', '-', $sp->no_surat)) . "_SP_{$sp->jenis_sp}_{$sp->id}.pdf";
            $pathFileSP = 'file_sp/' . $fileName; // Path di dalam storage/app/public
            $storagePath = storage_path('app/public/' . dirname($pathFileSP));

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // 1. Generate QR Code
            $qrCodeUrl = $this->generateQrCodeUrl($sp);
            $options = new QROptions([
                'outputType'    => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64'   => true,
                'scale'         => 5,
                'eccLevel'      => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl);

            // 2. Siapkan Data View
            $karyawan = $sp->user;
            $gm = $sp->gm;
            $tembusanArray = json_decode($sp->tembusan, true) ?? [];

            // Siapkan variabel logo untuk di-embed
            $pathToLogo = public_path('images/logo/a.png');
            $type = pathinfo($pathToLogo, PATHINFO_EXTENSION);
            $data = file_get_contents($pathToLogo);
            $embed = 'data:image/' . $type . ';base64,' . base64_encode($data);

            // 3. Load View dan Generate PDF
            $pdf = Pdf::loadView(
                'pages.sp.surat_sp_pdf', // Pastikan view ini ada
                compact('sp', 'qrCodeBase64', 'karyawan', 'gm', 'embed', 'tembusanArray')
            )
            ->setOptions([
                'isRemoteEnabled'      => true,
                'isHtml5ParserEnabled' => true,
            ])
            ->setPaper('A4', 'portrait');

            // 4. Simpan PDF ke storage/app/public/file_sp/
            Storage::disk('public')->put($pathFileSP, $pdf->output());

            return $pathFileSP; // Return path relatif untuk disimpan di DB

        } catch (\Exception $e) {
            Log::error("PDF Generation Error [SP ID {$sp->id}]: " . $e->getMessage());
            return null;
        }
    }
}

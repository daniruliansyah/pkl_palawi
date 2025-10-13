<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\StatusSuratDiperbarui;
use Illuminate\Support\Str;

// Import chillerlan/php-qrcode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar cuti berdasarkan peran pengguna.
     */
    // CutiController.php

public function index()
    {
    $user = Auth::user();
    $sisaCuti = $user->jatah_cuti;

    // PERBAIKAN: Ganti nama variabelnya menjadi $cutis agar sesuai dengan view
    $cutis = Cuti::where('nip_user', $user->nip)
                        ->with('user', 'ssdm', 'sdm', 'gm')
                        ->latest()->get();

    // Sekarang, yang dikirim ke view adalah variabel 'cutis' yang sudah benar
    return view('pages.cuti.index-karyawan', compact('cutis', 'sisaCuti'));
}

    /**
     * FUNGSI 2: Untuk menampilkan halaman PERSETUJUAN CUTI.
     * Halaman ini HANYA diakses oleh atasan (GM, SDM, SSDM).
     */
    public function indexApproval()
    {
        $user = Auth::user();

        // Logika untuk mengambil data yang perlu disetujui, berdasarkan peran.
        // Logika ini sama seperti yang kita diskusikan sebelumnya.

        if ($user->isGm()) {
            $cutisForApproval = Cuti::where('status_sdm', 'Disetujui')
                                  ->where('status_gm', 'Menunggu Persetujuan')
                                  ->latest()->get();
            // Mengembalikan view dari folder 'approval'
            return view('pages.approval.index-gm', compact('cutisForApproval'));
        }

        if ($user->isSdm()) {
            $cutisForApproval = Cuti::where(function ($query) {
                                    $query->where('status_ssdm', 'Disetujui')
                                          ->where('status_sdm', 'Menunggu Persetujuan');
                                })->orWhere(function($q) {
                                    $q->whereHas('user', fn($sub) => $sub->whereHas('jabatanTerbaru.jabatan', fn($j) => $j->where('nama_jabatan', 'LIKE', '%General Manager%')))
                                      ->where('status_sdm', 'Menunggu Persetujuan');
                                })->latest()->get();
            // Mengembalikan view dari folder 'approval'
            return view('pages.approval.index-sdm', compact('cutisForApproval'));
        }

        if ($user->isSenior()) {
            $cutisForApproval = Cuti::where('status_ssdm', 'Menunggu Persetujuan')
                                  ->where('nip_user_ssdm', $user->nip)
                                  ->latest()->get();
            // Mengembalikan view dari folder 'approval'
            return view('pages.approval.index-ssdm', compact('cutisForApproval'));
        }

        // Jika karyawan biasa mencoba akses, kembalikan ke home atau halaman lain.
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman persetujuan.');
    }

    /**
     * Menampilkan form untuk membuat pengajuan cuti baru.
     */
    public function create()
    {
        $sisaCuti = Auth::user()->jatah_cuti;
        $seniors = User::whereHas('jabatanTerbaru.jabatan', function($q) {
            $q->where('nama_jabatan', 'LIKE', '%Senior%')->orWhere('nama_jabatan', 'LIKE', '%Manager%');
        })->get();

        return view('pages.cuti.create', compact('seniors', 'sisaCuti'));
    }

    /**
     * Menyimpan pengajuan cuti baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'jenis_izin'    => 'required|string|in:Cuti Tahunan,Cuti Besar,Cuti Sakit,Cuti Bersalin,Cuti Alasan Penting',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'jumlah_hari'   => 'required|integer|min:1',
            'keterangan'    => 'required|string',
            'file_izin'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048|required_if:jenis_izin,Cuti Sakit',
        ];

        // Hanya user non-senior ke bawah yang perlu memilih atasan SSDM
        if ($user->isKaryawanBiasa()) {
            $rules['nip_user_ssdm'] = 'required|string|exists:users,nip';
        }

        $validatedData = $request->validate($rules, [
            'file_izin.required_if' => 'File izin wajib diunggah untuk Cuti Sakit.',
            'nip_user_ssdm.required' => 'Anda harus memilih atasan langsung/SSDM.',
        ]);

        // Cek sisa cuti HANYA untuk cuti yang mengurangi jatah
        if ($this->isCutiMengurangiJatah($validatedData['jenis_izin'], $request->hasFile('file_izin'))) {
            if ($user->jatah_cuti < (int)$validatedData['jumlah_hari']) {
                return redirect()->back()->withErrors(['jumlah_hari' => 'Sisa jatah cuti Anda (' . $user->jatah_cuti . ' hari) tidak mencukupi.'])->withInput();
            }
        }

        // --- PENENTUAN ATASAN DAN ALUR APPROVAL ---

        $sdmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))->first();
        $gmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%General Manager%'))->first();

        // Default untuk Karyawan Biasa
        $statusSsdm = 'Menunggu Persetujuan'; $statusSdm = 'Menunggu'; $statusGm = 'Menunggu';
        $nipUserSsdm = $request->input('nip_user_ssdm');
        $nipUserSdm = $sdmUser?->nip;
        $nipUserGm = $gmUser?->nip;
        $penerimaNotifikasi = User::where('nip', $nipUserSsdm)->first(); // Default ke SSDM pilihan

        // GM
        if ($user->isGm()) {
            $statusSsdm = 'Disetujui'; // Bypass SSDM
            $statusSdm = 'Menunggu Persetujuan';
            $nipUserSsdm = $user->nip; // Assign diri sendiri
            $penerimaNotifikasi = $sdmUser;
        }
        // SDM
        elseif ($user->isSdm()) {
            $statusSsdm = 'Disetujui'; // Bypass SSDM
            $statusSdm = 'Disetujui'; // Bypass SDM
            $statusGm = 'Menunggu Persetujuan';
            $nipUserSsdm = $user->nip; // Assign diri sendiri
            $nipUserSdm = $user->nip; // Assign diri sendiri
            $penerimaNotifikasi = $gmUser;
        }
        // Senior/Manager (SSDM)
        elseif ($user->isSenior()) {
            $statusSsdm = 'Disetujui'; // Disetujui oleh diri sendiri
            $statusSdm = 'Menunggu Persetujuan';
            $nipUserSsdm = $user->nip; // Atasan langsung adalah dirinya sendiri
            $penerimaNotifikasi = $sdmUser;
        }

        // Pastikan NIP SDM dan GM diisi untuk mencegah error relasi, meskipun statusnya 'Menunggu'
        $nipUserSdm = $nipUserSdm ?? $sdmUser?->nip;
        $nipUserGm = $nipUserGm ?? $gmUser?->nip;

        $pathFileIzin = $request->hasFile('file_izin') ? $request->file('file_izin')->store('file_izin', 'public') : null;

        $cuti = Cuti::create(array_merge($validatedData, [
            'nip_user'          => $user->nip,
            'no_surat'          => $this->generateNomorSurat(),
            'file_izin'         => $pathFileIzin,
            'tgl_upload'        => now(),
            'status_ssdm'       => $statusSsdm,
            'status_sdm'        => $statusSdm,
            'status_gm'         => $statusGm,
            'nip_user_ssdm'     => $nipUserSsdm,
            'nip_user_sdm'      => $nipUserSdm,
            'nip_user_gm'       => $nipUserGm,
        ]));

        // Kirim Notifikasi ke Atasan Pertama yang statusnya 'Menunggu Persetujuan'
        if ($penerimaNotifikasi && ($statusSsdm === 'Menunggu Persetujuan' || $statusSdm === 'Menunggu Persetujuan' || $statusGm === 'Menunggu Persetujuan')) {
            try {
                $penerimaNotifikasi->notify(new StatusSuratDiperbarui(
                    aktor: auth()->user(),
                    jenisSurat: 'Cuti',
                    statusBaru: 'Menunggu Persetujuan',
                    keterangan: 'Terdapat pengajuan cuti baru yang menunggu persetujuan Anda.',
                    url: route('cuti.show', $cuti->id)
                ));
            } catch (\Exception $e) {
                Log::error("Notif gagal (Store Cuti): " . $e->getMessage());
            }
        }

        return redirect()->route('cuti.index')->with('success','Pengajuan cuti berhasil dibuat.');
    }
  /**
 * Mengubah status cuti berdasarkan jabatan (SSDM -> SDM -> GM).
 */
public function updateStatus(Request $request, Cuti $cuti)
{
    $request->validate([
        'status' => 'required|in:Disetujui,Ditolak',
        'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak',
    ]);

    $user = auth()->user();
    $pembuatCuti = $cuti->user;

    $penerimaNotifikasiBerikutnya = null;
    $currentStatus = '';
    $keteranganNotif = '';
    $urlDetail = route('cuti.show', $cuti->id);
    $status = $request->status;

    DB::beginTransaction();
    try {
        // Cek otorisasi dan antrian (Logika ini tetap dipertahankan karena sudah benar)
        if ($user->isGm() && $cuti->status_gm !== 'Menunggu Persetujuan') {
            return back()->with('error','Bukan antrian/wewenang Anda untuk GM.');
        } elseif ($user->isSdm() && $cuti->status_sdm !== 'Menunggu Persetujuan') {
            return back()->with('error','Bukan antrian/wewenang Anda untuk SDM.');
        } elseif ($user->isSenior() && $cuti->status_ssdm !== 'Menunggu Persetujuan') {
            return back()->with('error','Bukan antrian/wewenang Anda untuk Atasan Langsung.');
        } elseif (!$user->isGm() && !$user->isSdm() && !$user->isSenior()) {
            return back()->with('error','Anda tidak berwenang memproses pengajuan cuti ini.');
        }

        // General Manager (GM)
        if ($user->isGm()) {
            $cuti->status_gm = $status;
            $cuti->nip_user_gm = $user->nip;
            $cuti->tgl_persetujuan_gm = now();

            if ($status == 'Ditolak') {
                $cuti->alasan_penolakan = $request->alasan_penolakan;
                $currentStatus = 'Ditolak';
                $keteranganNotif = "Cuti Anda ditolak GM. Alasan: " . $cuti->alasan_penolakan;
            } else {
                $currentStatus = 'Disetujui Penuh';
                $keteranganNotif = "Cuti Anda sudah disetujui penuh. Silakan unduh surat cuti Anda.";
                $urlDetail = route('cuti.download', $cuti->id);

                // Logika Pengurangan Jatah Cuti dan Generate PDF
                $path = $this->generateSuratPdf($cuti);
                if ($path) {
                    // ==========================================================
                    // PERBAIKAN: Menggunakan kolom 'file_surat' sesuai permintaan Anda
                    $cuti->file_surat = $path;
                    // ==========================================================

                    if ($this->isCutiMengurangiJatah($cuti->jenis_izin, !is_null($cuti->file_izin))) {
                        $pembuatCuti->decrement('jatah_cuti', $cuti->jumlah_hari);
                        Log::info("Jatah Cuti {$pembuatCuti->nip} dikurangi {$cuti->jumlah_hari} hari.");
                    }
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Cuti disetujui, tapi gagal membuat file surat. Cek logs.');
                }
            }
        }
        // Senior Analis Keuangan, SDM & Umum (SDM)
        elseif ($user->isSdm()) {
            $cuti->status_sdm = $status;
            $cuti->nip_user_sdm = $user->nip;
            $cuti->tgl_persetujuan_sdm = now();

            if ($status == 'Disetujui') {
                $cuti->status_gm = 'Menunggu Persetujuan';
                $penerimaNotifikasiBerikutnya = User::where('nip', $cuti->nip_user_gm)->first();
                $currentStatus = 'Menunggu Persetujuan GM';
                $keteranganNotif = "Disetujui SDM, diteruskan ke GM.";
            } else {
                $cuti->alasan_penolakan = $request->alasan_penolakan;
                $cuti->status_gm = 'Ditolak';
                $currentStatus = 'Ditolak';
                $keteranganNotif = "Cuti Anda ditolak SDM. Alasan: " . $cuti->alasan_penolakan;
            }
        }
        // Senior / Manager (SSDM/Atasan Langsung)
        elseif ($user->isSenior()) {
            $cuti->status_ssdm = $status;
            $cuti->tgl_persetujuan_ssdm = now();

            if ($status == 'Disetujui') {
                $cuti->status_sdm = 'Menunggu Persetujuan';
                $penerimaNotifikasiBerikutnya = User::where('nip', $cuti->nip_user_sdm)->first();
                $currentStatus = 'Menunggu Persetujuan SDM';
                $keteranganNotif = "Disetujui Atasan Langsung, diteruskan ke SDM.";
            } else {
                $cuti->alasan_penolakan = $request->alasan_penolakan;
                $cuti->status_sdm = 'Ditolak'; $cuti->status_gm = 'Ditolak';
                $currentStatus = 'Ditolak';
                $keteranganNotif = "Cuti Anda ditolak Atasan Langsung. Alasan: " . $cuti->alasan_penolakan;
            }
        }

        $cuti->save();

        // Notifikasi ke Pembuat Cuti
        if ($pembuatCuti) {
            $pembuatCuti->notify(new StatusSuratDiperbarui(
                $user, 'Cuti', $currentStatus, $keteranganNotif, $urlDetail
            ));
        }

        // Notifikasi ke Atasan Berikutnya (Jika Disetujui dan belum final)
        if ($penerimaNotifikasiBerikutnya && $status == 'Disetujui' && $currentStatus !== 'Disetujui Penuh') {
            $penerimaNotifikasiBerikutnya->notify(new StatusSuratDiperbarui(
                $user, 'Cuti', 'Menunggu Persetujuan', 'Ada cuti yang menunggu persetujuan Anda.', route('cuti.show',$cuti->id)
            ));
        }

        DB::commit();
        return redirect()->route('cuti.index')->with('success','Status pengajuan cuti berhasil diperbarui.');

    } catch(\Exception $e) {
        DB::rollBack();
        Log::error("Update Cuti Error: ".$e->getMessage());
        return back()->with('error','Terjadi kesalahan: ' . $e->getMessage());
    }
}

    /**
     * Menampilkan detail cuti (untuk rute notifikasi dan verifikasi).
     */
    public function show($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->findOrFail($id);

        // Cek jika user yang login adalah pembuat cuti atau salah satu verifier
        $user = Auth::user();
        if ($user->nip !== $cuti->nip_user && $user->nip !== $cuti->nip_user_ssdm && $user->nip !== $cuti->nip_user_sdm && $user->nip !== $cuti->nip_user_gm) {
             return back()->with('error', 'Anda tidak berhak melihat detail pengajuan ini.');
        }

        // Asumsi ada view 'pages.cuti.detail' untuk menampilkan info dan tombol persetujuan
        return view('pages.cuti.detail', compact('cuti'));
    }

    /**
     * Membatalkan pengajuan cuti yang masih 'Menunggu Persetujuan' SSDM.
     */
    public function cancel(Cuti $cuti)
    {
        if (Auth::user()->nip !== $cuti->nip_user) {
            return redirect()->route('cuti.index')->with('error', 'Anda tidak berhak membatalkan pengajuan ini.');
        }
        // Hanya bisa dibatalkan jika belum disetujui SSDM/Atasan Langsung
        if ($cuti->status_ssdm !== 'Menunggu Persetujuan' || $cuti->status_sdm !== 'Menunggu' || $cuti->status_gm !== 'Menunggu') {
            return redirect()->route('cuti.index')->with('error', 'Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // Hapus file izin jika ada
            if ($cuti->file_izin) {
                Storage::disk('public')->delete($cuti->file_izin);
            }
            $cuti->delete();
            DB::commit();
            return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Cancel Cuti Error: ".$e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membatalkan cuti.');
        }
    }

    /**
     * Mengunduh file surat cuti yang sudah disetujui penuh.
     */
    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->file_cuti && Storage::disk('public')->exists($cuti->file_cuti)) {
            $filePath = storage_path('app/public/' . $cuti->file_cuti);
            $safeName = Str::slug(Str::replace('/', '-', $cuti->no_surat)) . "_Cuti";
            return response()->download($filePath, "{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan.');
    }

    /**
     * Menampilkan informasi verifikasi Cuti dari pemindaian QR Code.
     */
    public function verifikasi($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->find($id);

        if (!$cuti) {
            // Ganti 'pages.cuti.notfound' dengan view not found Anda
            return view('pages.cuti.notfound', ['message' => 'Surat Cuti tidak ditemukan.']);
        }
        // Ganti 'pages.cuti.verifikasi_info' dengan view info verifikasi Anda
        return view('pages.cuti.verifikasi_info', compact('cuti'));
    }

    // --- HELPER FUNCTIONS ---

    /**
     * Menentukan apakah jenis cuti mengurangi jatah tahunan.
     */
    private function isCutiMengurangiJatah(string $jenisIzin, bool $adaFile): bool
    {
        // Cuti Sakit dan Cuti Bersalin TIDAK mengurangi jatah, ASALKAN ada file pendukung.
        $cutiKhususNonJatah = ['Cuti Sakit', 'Cuti Bersalin'];
        if (in_array($jenisIzin, $cutiKhususNonJatah) && $adaFile) {
            return false;
        }
        // Cuti Tahunan, Cuti Besar, Cuti Alasan Penting, atau Cuti Sakit/Bersalin tanpa file akan mengurangi jatah.
        return true;
    }

    /**
     * Menghasilkan nomor surat cuti otomatis.
     */
    private function generateNomorSurat(): string
    {
        $tahun = date('Y');
        $lastCutiThisYear = Cuti::whereYear('created_at', $tahun)->orderBy('id', 'desc')->first();
        $nomorUrut = $lastCutiThisYear ? ((int)explode('/', $lastCutiThisYear->no_surat)[0] + 1) : 1;
        return sprintf("%03d/014.1/SDM/ABWWT/%s", $nomorUrut, $tahun);
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
    protected function generateQrCodeUrl(Cuti $cuti)
    {
        return route('cuti.verifikasi', ['id' => $cuti->id]);
    }

    /**
 * Menghasilkan file PDF Surat Cuti dan menyimpannya.
 */
protected function generateSuratPdf(Cuti $cuti)
{
    try {
        $fileName = Str::slug(Str::replace('/', '-', $cuti->no_surat)) . "_{$cuti->id}.pdf";
        $pathFileCuti = 'file_cuti/' . $fileName; // Path di dalam storage/app/public
        $storagePath = storage_path('app/public/' . dirname($pathFileCuti));

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // 1. Generate QR Code
        $qrCodeUrl = $this->generateQrCodeUrl($cuti);
        $options = new QROptions([
            'outputType'    => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64'   => true,
            'scale'         => 5,
            'eccLevel'      => QRCode::ECC_H,
        ]);
        $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl);

        // 2. Siapkan Data View
        $karyawan = $cuti->user;
        $gm = $cuti->gm;

        // Siapkan variabel logo untuk di-embed
        // Ganti 'images/logo/a.png' dengan path logo Anda di folder public
        $pathToLogo = public_path('images/logo/a.png');
        $type = pathinfo($pathToLogo, PATHINFO_EXTENSION);
        $data = file_get_contents($pathToLogo);
        $embed = 'data:image/' . $type . ';base64,' . base64_encode($data);

        // 3. Load View dan Generate PDF
        $pdf = Pdf::loadView(
            'pages.cuti.surat_cuti_pdf',
            // ===== PERBAIKAN DI SINI =====
            compact('cuti', 'qrCodeBase64', 'karyawan', 'gm', 'embed')
        )
        ->setOptions([
            'isRemoteEnabled'      => true,
            'isHtml5ParserEnabled' => true,
        ])
        ->setPaper('A4', 'portrait');

        // 4. Simpan PDF ke storage/app/public/file_cuti/
        Storage::disk('public')->put($pathFileCuti, $pdf->output());

        return $pathFileCuti; // Return path relatif untuk disimpan di DB

    } catch (\Exception $e) {
        Log::error("PDF Generation Error [Cuti ID {$cuti->id}]: " . $e->getMessage());
        return null;
    }
}

       public function detail($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        // Asumsi ada view 'pages.surat_sp.detail'
        return view('pages.cuti.index-karyawan', compact('cuti'));
    }

    // app/Http/Controllers/CutiController.php

// ... (kode fungsi index, create, store, dll. Anda)


// --- TAMBAHKAN FUNGSI BARU DI BAWAH INI ---

/**
 * FUNGSI BARU: Untuk memproses Cuti yang sudah disetujui penuh.
 * Fungsi ini akan dipanggil oleh ApprovalController.
 * Pastikan fungsi ini berstatus PUBLIC.
 */
public function finalizeCuti(Cuti $cuti)
{
    try {
        // 1. Generate PDF dan dapatkan path-nya
        // Pastikan Anda sudah memiliki method generateSuratPdf di controller ini
        $path = $this->generateSuratPdf($cuti);

        if ($path) {
            // 2. Simpan path file surat ke database
            $cuti->file_surat = $path;
            $cuti->save();

            // 3. Kurangi jatah cuti jika perlu
            // Pastikan Anda juga memiliki method isCutiMengurangiJatah
            if ($this->isCutiMengurangiJatah($cuti->jenis_izin, !is_null($cuti->file_izin))) {
                $cuti->user->decrement('jatah_cuti', $cuti->jumlah_hari);
                Log::info("Jatah Cuti {$cuti->user->nip} dikurangi {$cuti->jumlah_hari} hari.");
            }
        }
    } catch (\Exception $e) {
        Log::error("Gagal memfinalisasi Cuti ID {$cuti->id}: " . $e->getMessage());
        // Anda bisa menangani error di sini, misalnya dengan notifikasi ke admin
    }
    }
}

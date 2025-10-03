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
use Carbon\Carbon; // Ditambahkan karena diperlukan untuk logika cuti
use Illuminate\Support\Str; // Ditambahkan untuk generate nama file unik

// Import chillerlan/php-qrcode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar cuti berdasarkan peran pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        $sisaCuti = $user->jatah_cuti;
        $jabatanInfo = $user->jabatanTerbaru()->with('jabatan')->first();

        // Default: Karyawan Biasa
        if (!$jabatanInfo || !$jabatanInfo->jabatan || (!$user->isSenior() && !$user->isSdm() && !$user->isGm())) {
            $cutis = Cuti::where('nip_user', $user->nip)
                         ->with('user', 'ssdm', 'sdm', 'gm')
                         ->latest()->get();
            return view('pages.cuti.index-karyawan', compact('cutis', 'sisaCuti'));
        }

        $namaJabatan = $jabatanInfo->jabatan->nama_jabatan;

        // General Manager (GM)
        if (Str::contains($namaJabatan, 'General Manager')) {
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('status_sdm', 'Disetujui')
                ->where('status_gm', 'Menunggu Persetujuan')
                ->latest()->get();
            $cutisHistory = Cuti::where('nip_user_gm', $user->nip)->latest()->get();
            return view('pages.cuti.index-gm', compact('cutisForApproval', 'cutisHistory', 'sisaCuti'));
        }

        // Senior Analis Keuangan, SDM & Umum (SDM)
        if (Str::contains($namaJabatan, 'Senior Analis Keuangan, SDM & Umum')) {
            $cutisForApproval = Cuti::where('status_ssdm', 'Disetujui')
                ->where('status_sdm', 'Menunggu Persetujuan')
                ->latest()->get();
            $cutisHistory = Cuti::where('nip_user_sdm', $user->nip)->latest()->get();
            return view('pages.cuti.index-sdm', compact('cutisForApproval', 'cutisHistory', 'sisaCuti'));
        }

        // Senior / Manager (SSDM/Atasan Langsung)
        if (Str::contains($namaJabatan, 'Senior') || Str::contains($namaJabatan, 'Manager')) {
            $cutisForApproval = Cuti::where('status_ssdm', 'Menunggu Persetujuan')
                ->where('nip_user_ssdm', $user->nip)
                ->latest()->get();
            $cutisHistory = Cuti::where('nip_user_ssdm', $user->nip)
                ->where('status_ssdm', '!=', 'Menunggu Persetujuan')
                ->latest()->get();
            return view('pages.cuti.index-ssdm', compact('cutisForApproval', 'cutisHistory', 'sisaCuti'));
        }
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
        if (!$user->isSenior() && !$user->isSdm() && !$user->isGm()) {
            $rules['nip_user_ssdm'] = 'required|string|exists:users,nip';
        }

        $validatedData = $request->validate($rules, [
            'file_izin.required_if' => 'File izin wajib diunggah untuk Cuti Sakit.',
            'nip_user_ssdm.required' => 'Anda harus memilih atasan langsung/SSDM.',
        ]);

        // Cek sisa cuti HANYA untuk cuti yang mengurangi jatah
        if ($this->isCutiMengurangiJatah($validatedData['jenis_izin'], $request->hasFile('file_izin'))) {
            if ($user->jatah_cuti < (int)$validatedData['jumlah_hari']) {
                return redirect()->back()->withErrors(['jumlah_hari' => 'Sisa jatah cuti Anda ('.$user->jatah_cuti.' hari) tidak mencukupi.'])->withInput();
            }
        }

        // Tentukan alur approval berdasarkan jabatan pemohon
        $statusSsdm = 'Menunggu Persetujuan'; $statusSdm = 'Menunggu'; $statusGm = 'Menunggu';
        $nipUserSsdm = $request->input('nip_user_ssdm'); $nipUserSdm = null; $nipUserGm = null;

        $sdmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))->first();
        $gmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%General Manager%'))->first();

        // GM langsung ke SDM (Melewati SSDM)
        if ($user->isGm()) {
            $statusSsdm = 'Disetujui'; // Bypass SSDM
            $statusSdm = 'Menunggu Persetujuan';
            $nipUserSdm = $sdmUser?->nip;
            $nipUserSsdm = $sdmUser?->nip; // Dibuat sama dengan SDM agar relasi tetap ada
        }
        // SDM langsung ke GM
        elseif ($user->isSdm()) {
            $statusSsdm = 'Disetujui'; // Bypass SSDM
            $statusSdm = 'Disetujui'; // Bypass SDM ke dirinya sendiri
            $statusGm = 'Menunggu Persetujuan';
            $nipUserGm = $gmUser?->nip;
            $nipUserSsdm = $sdmUser?->nip; // Dibuat nip sendiri
            $nipUserSdm = $sdmUser?->nip; // Dibuat nip sendiri
        }
        // Senior/Manager langsung ke SDM
        elseif ($user->isSenior()) {
            $statusSsdm = 'Disetujui'; // Disetujui oleh diri sendiri
            $statusSdm = 'Menunggu Persetujuan';
            $nipUserSdm = $sdmUser?->nip;
            $nipUserSsdm = $user->nip; // Atasan langsung adalah dirinya sendiri
        }

        $pathFileIzin = $request->hasFile('file_izin') ? $request->file('file_izin')->store('file_izin', 'public') : null;

        $cuti = Cuti::create(array_merge($validatedData, [
            'nip_user'        => $user->nip,
            'no_surat'        => $this->generateNomorSurat(),
            'file_izin'       => $pathFileIzin,
            'tgl_upload'      => now(),
            'status_ssdm'     => $statusSsdm,
            'status_sdm'      => $statusSdm,
            'status_gm'       => $statusGm,
            'nip_user_ssdm'   => $nipUserSsdm,
            'nip_user_sdm'    => $nipUserSdm,
            'nip_user_gm'     => $nipUserGm,
        ]));

        // Tentukan penerima notifikasi pertama (Atasan Langsung/SDM/GM)
        $penerimaNotifikasi = null;
        if ($cuti->status_ssdm === 'Menunggu Persetujuan') {
             $penerimaNotifikasi = User::where('nip', $cuti->nip_user_ssdm)->first();
        } elseif ($cuti->status_sdm === 'Menunggu Persetujuan') {
             $penerimaNotifikasi = User::where('nip', $cuti->nip_user_sdm)->first();
        } elseif ($cuti->status_gm === 'Menunggu Persetujuan') {
             $penerimaNotifikasi = User::where('nip', $cuti->nip_user_gm)->first();
        }

        // Kirim Notifikasi ke Atasan Pertama
        if ($penerimaNotifikasi) {
            try {
                $penerimaNotifikasi->notify(new StatusSuratDiperbarui(
                    aktor: auth()->user(),
                    jenisSurat: 'Cuti',
                    statusBaru: 'Menunggu Persetujuan',
                    keterangan: 'Terdapat pengajuan cuti baru yang menunggu persetujuan Anda.',
                    url: route('cuti.show', $cuti->id) // Asumsi ada rute cuti.show untuk detail/verifikasi
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
        $jabatanInfo = $user->jabatanTerbaru()->with('jabatan')->first();
        if (!$jabatanInfo) return back()->with('error','Tidak ada jabatan.');

        $namaJabatan = $jabatanInfo->jabatan->nama_jabatan;
        $status = $request->status;

        $pembuatCuti = $cuti->user; // Karyawan yang mengajukan cuti
        $penerimaNotifikasiBerikutnya = null; // Atasan selanjutnya
        $currentStatus = '';
        $keteranganNotif = '';
        $urlDetail = route('cuti.show', $cuti->id); // Default ke rute show

        DB::beginTransaction();
        try {
            if (Str::contains($namaJabatan, 'General Manager')) {
                if ($cuti->status_gm !== 'Menunggu Persetujuan')
                    return back()->with('error','Bukan antrian Anda.');

                $cuti->status_gm = $status;
                $cuti->nip_user_gm = $user->nip;
                $cuti->tgl_persetujuan_gm = now();

                if ($status == 'Ditolak') {
                    $cuti->alasan_penolakan = $request->alasan_penolakan;
                    $currentStatus = 'Ditolak';
                    $keteranganNotif = "Cuti Anda ditolak GM. Alasan: " . $cuti->alasan_penolakan;
                } else {
                    $currentStatus = 'Disetujui Penuh';
                    $keteranganNotif = "Cuti Anda sudah disetujui penuh.";
                    $urlDetail = route('cuti.download', $cuti->id);

                    // Logika Pengurangan Jatah Cuti dan Generate PDF
                    $path = $this->generateSuratPdf($cuti);
                    if ($path) {
                        $cuti->file_cuti = $path;

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
            elseif (Str::contains($namaJabatan, 'Senior Analis Keuangan, SDM & Umum')) {
                if ($cuti->status_sdm !== 'Menunggu Persetujuan')
                    return back()->with('error','Bukan antrian Anda.');

                $cuti->status_sdm = $status;
                $cuti->nip_user_sdm = $user->nip;
                $cuti->tgl_persetujuan_sdm = now();

                if ($status == 'Disetujui') {
                    $cuti->status_gm = 'Menunggu Persetujuan';
                    $penerimaNotifikasiBerikutnya = User::whereHas('jabatanTerbaru.jabatan', fn($q)=>$q->where('nama_jabatan','LIKE','%General Manager%'))->first();
                    $currentStatus = 'Menunggu Persetujuan GM';
                    $keteranganNotif = "Disetujui SDM, diteruskan ke GM.";
                } else {
                    $cuti->alasan_penolakan = $request->alasan_penolakan;
                    $cuti->status_gm = 'Ditolak';
                    $currentStatus = 'Ditolak';
                    $keteranganNotif = "Cuti Anda ditolak SDM. Alasan: " . $cuti->alasan_penolakan;
                }
            }
            elseif (Str::contains($namaJabatan, 'Senior') || Str::contains($namaJabatan, 'Manager')) {
                if ($cuti->status_ssdm !== 'Menunggu Persetujuan')
                    return back()->with('error','Bukan antrian Anda.');

                $cuti->status_ssdm = $status;
                $cuti->tgl_persetujuan_ssdm = now();

                if ($status == 'Disetujui') {
                    $cuti->status_sdm = 'Menunggu Persetujuan';
                    $penerimaNotifikasiBerikutnya = User::whereHas('jabatanTerbaru.jabatan', fn($q)=>$q->where('nama_jabatan','LIKE','%Senior Analis Keuangan, SDM & Umum%'))->first();
                    $currentStatus = 'Menunggu Persetujuan SDM';
                    $keteranganNotif = "Disetujui Atasan Langsung, diteruskan ke SDM.";
                } else {
                    $cuti->alasan_penolakan = $request->alasan_penolakan;
                    $cuti->status_sdm = 'Ditolak'; $cuti->status_gm = 'Ditolak';
                    $currentStatus = 'Ditolak';
                    $keteranganNotif = "Cuti Anda ditolak Atasan Langsung. Alasan: " . $cuti->alasan_penolakan;
                }
            } else {
                return back()->with('error','Anda tidak berwenang memproses pengajuan cuti ini.');
            }

            $cuti->save();

            // Notifikasi ke Pembuat Cuti
            if ($pembuatCuti) {
                $pembuatCuti->notify(new StatusSuratDiperbarui(
                    aktor: $user,
                    jenisSurat:'Cuti',
                    statusBaru:$currentStatus,
                    keterangan:$keteranganNotif,
                    url:$urlDetail
                ));
            }

            // Notifikasi ke Atasan Berikutnya jika Disetujui dan belum final
            if ($penerimaNotifikasiBerikutnya && $status == 'Disetujui' && $currentStatus !== 'Disetujui Penuh') {
                $penerimaNotifikasiBerikutnya->notify(new StatusSuratDiperbarui(
                    aktor: $user,
                    jenisSurat:'Cuti',
                    statusBaru:'Menunggu Persetujuan',
                    keterangan:'Ada cuti yang menunggu persetujuan Anda.',
                    url:route('cuti.show',$cuti->id)
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
        if ($cuti->status_ssdm !== 'Menunggu Persetujuan' || $cuti->status_sdm !== 'Menunggu' || $cuti->status_gm !== 'Menunggu') {
            return redirect()->route('cuti.index')->with('error', 'Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }

        $cuti->delete();
        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }

    /**
     * Mengunduh file surat cuti yang sudah disetujui penuh.
     */
    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->file_cuti && Storage::disk('public')->exists($cuti->file_cuti)) {
            $filePath = storage_path('app/public/' . $cuti->file_cuti);
            $safeName = str_replace('/', '-', $cuti->no_surat);
            return response()->download($filePath, "Surat-Cuti-{$safeName}.pdf");
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
        // Cuti Sakit dan Cuti Bersalin TIDAK mengurangi jatah, ASALKAN ada file pendukung (surat dokter/akte).
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
        $bulanRomawi = $this->numberToRoman(date('n')); // Asumsi ada helper numberToRoman

        $lastCuti = Cuti::whereYear('tgl_upload', $tahun)
            ->whereNotNull('no_surat')
            ->orderBy('tgl_upload', 'desc')
            ->first();

        // Cari nomor urut terakhir, ambil dari bagian pertama sebelum '/'
        $lastNumber = 0;
        if ($lastCuti && preg_match('/^(\d+)\//', $lastCuti->no_surat, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Format surat yang umum: 001/ABWWT/I/2025
        return "{$newNumber}/ABWWT/{$bulanRomawi}/{$tahun}";
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
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl);

            // 2. Siapkan Data View
            $karyawan = $cuti->user;
            $gm = $cuti->gm;

            // 3. Load View dan Generate PDF
            $pdf = Pdf::loadView('pages.cuti.surat', compact('cuti', 'qrCodeBase64', 'karyawan', 'gm'))
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
}

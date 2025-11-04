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
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

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
        // Jatah cuti 12 hari/tahun, asumsikan kolom 'jatah_cuti' di model User menyimpan sisa cuti.
        $sisaCuti = $user->jatah_cuti;

        $cutis = Cuti::where('nip_user', $user->nip)
            ->with('user', 'ssdm', 'sdm', 'gm', 'manager') // Tambahkan relasi manager
            ->latest()->get();

        return view('pages.cuti.index-karyawan', compact('cutis', 'sisaCuti'));
    }

    /**
     * Menampilkan halaman PERSETUJUAN CUTI (indexApproval)
     * NOTE: Logika ini sekarang dipindahkan ke ApprovalController,
     * tapi kita biarkan di sini jika ada rute lama yang masih menggunakannya.
     */
    public function indexApproval()
    {
        $user = Auth::user();
        $userNip = $user->nip;

        // 1. General Manager (GM)
        if ($user->isGm()) {
            $cutisForApproval = Cuti::where(function ($query) {
                // Alur 1, 2, 4 (Karyawan/Senior/Manager -> SDM -> GM)
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'NOT LIKE', '%SDM%')->where('nama_jabatan', 'NOT LIKE', '%General Manager%'))
                    ->where('status_sdm', 'Disetujui')
                    ->where('status_gm', 'Menunggu Persetujuan');
            })->orWhere(function ($query) {
                // Alur 3: SDM -> Manager -> GM
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))
                    ->where('status_manager', 'Disetujui')
                    ->where('status_gm', 'Menunggu Persetujuan');
            })->latest()->get();
            return view('pages.approval.index-gm', compact('cutisForApproval'));
        }

        // 2. Manager
        if ($user->isManager()) {
            $cutisForApproval = Cuti::where(function ($query) use ($userNip) {
                // Alur 3: Menunggu persetujuan Manager dari SDM
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))
                    ->where('status_manager', 'Menunggu Persetujuan')
                    ->where('nip_user_manager', $userNip);
            })->orWhere(function ($query) use ($userNip) {
                // Alur 1 (Jika Manager juga bertindak sebagai SSDM/Atasan Langsung untuk Karyawan Biasa)
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'NOT LIKE', '%General Manager%')->where('nama_jabatan', 'NOT LIKE', '%Manager%')->where('nama_jabatan', 'NOT LIKE', '%Senior%'))
                    ->where('status_ssdm', 'Menunggu Persetujuan')
                    ->where('nip_user_ssdm', $userNip);
            })->latest()->get();
            return view('pages.approval.index-manager', compact('cutisForApproval'));
        }

        // 3. SDM (Senior Analis Keuangan, SDM & Umum)
        if ($user->isSdm()) {
            $cutisForApproval = Cuti::where(function ($query) {
                // Alur 1, 2, 4: Karyawan/Senior/Manager -> SSDM/SSDM/Manager -> SDM
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'NOT LIKE', '%SDM%')->where('nama_jabatan', 'NOT LIKE', '%General Manager%'))
                    ->where(function ($q2) {
                        $q2->where('status_ssdm', 'Disetujui') // Alur 1 (setelah SSDM/Manager)
                            ->orWhere('status_manager', 'Disetujui'); // Alur 4 (setelah Manager Self-Approve)
                    })
                    ->where('status_sdm', 'Menunggu Persetujuan');
            })->latest()->get();
            return view('pages.approval.index-sdm', compact('cutisForApproval'));
        }

        // 4. Senior (SSDM) - Atasan Langsung (Bukan Manager)
        if ($user->isSenior()) {
            $cutisForApproval = Cuti::where(function ($query) use ($userNip) {
                // Alur 1: Karyawan Biasa -> SSDM
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'NOT LIKE', '%General Manager%')->where('nama_jabatan', 'NOT LIKE', '%Manager%')->where('nama_jabatan', 'NOT LIKE', '%Senior%'))
                    ->where('status_ssdm', 'Menunggu Persetujuan')
                    ->where('nip_user_ssdm', $userNip);
            })->latest()->get();
            return view('pages.approval.index-ssdm', compact('cutisForApproval'));
        }

        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman persetujuan.');
    }

    /**
     * Menampilkan form untuk membuat pengajuan cuti baru.
     */
    public function create()
    {
        $sisaCuti = Auth::user()->jatah_cuti;
        // Ambil semua yang jabatannya Senior ATAU Manager
        $seniors = User::whereHas('jabatanTerbaru.jabatan', function ($q) {
            $q->where('nama_jabatan', 'LIKE', '%Senior%')->orWhere('nama_jabatan', 'LIKE', '%Manager%');
        })
        // Kecualikan GM dan SDM dari daftar pilihan atasan
        ->whereDoesntHave('jabatanTerbaru.jabatan', function ($q) {
            $q->where('nama_jabatan', 'LIKE', '%General Manager%')->orWhere('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%');
        })
        ->get();


        return view('pages.cuti.create', compact('seniors', 'sisaCuti'));
    }

    /**
     * Menyimpan pengajuan cuti baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'jenis_izin' => 'required|string|in:Cuti Tahunan,Cuti Besar,Cuti Sakit,Cuti Bersalin,Cuti Alasan Penting',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'jumlah_hari' => 'required|integer|min:1',
            'keterangan' => 'required|string',
            'file_izin' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048|required_if:jenis_izin,Cuti Sakit',
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
            if ($user->jatah_cuti < (int) $validatedData['jumlah_hari']) {
                return redirect()->back()->withErrors(['jumlah_hari' => 'Sisa jatah cuti Anda (' . $user->jatah_cuti . ' hari) tidak mencukupi.'])->withInput();
            }
        }

        // --- PENENTUAN ATASAN DAN ALUR APPROVAL ---
        $sdmUser = User::whereHas('jabatanTerbaru.jabatan', fn ($q) => $q->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))->first();
        $managerUser = User::whereHas('jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'LIKE', '%Manager%')->where('nama_jabatan', 'NOT LIKE', '%General Manager%'))->first();
        $gmUser = User::whereHas('jabatanTerbaru.jabatan', fn ($q) => $q->where('nama_jabatan', 'LIKE', '%General Manager%'))->first();

        // Default
        $statusSsdm = 'Menunggu'; $statusSdm = 'Menunggu'; $statusManager = 'Menunggu'; $statusGm = 'Menunggu';
        $nipUserSsdm = null; $nipUserSdm = null; $nipUserManager = null; $nipUserGm = null;
        $penerimaNotifikasi = null;

        // Alur 1: Karyawan Biasa -> Senior/Manager (ttd) -> SDM (ttd) -> GM (ttd) - 3 TTD
        if ($user->isKaryawanBiasa()) {
            $statusSsdm = 'Menunggu Persetujuan';
            $statusSdm = 'Menunggu'; // Menunggu setelah SSDM
            $statusGm = 'Menunggu'; // Menunggu setelah SDM
            $nipUserSsdm = $request->input('nip_user_ssdm'); // Atasan yang dipilih
            $nipUserSdm = $sdmUser?->nip;
            $nipUserGm = $gmUser?->nip;
            $penerimaNotifikasi = User::where('nip', $nipUserSsdm)->first();
        }
        // Alur 2: Senior -> SDM (ttd) -> GM (ttd) - 2 TTD
        elseif ($user->isSenior()) {
            $statusSsdm = 'Disetujui'; // BYPASS: Senior Self-Approve TTD Atasan Langsung
            $statusSdm = 'Menunggu Persetujuan'; // TTD SDM adalah langkah pertama di alur digital/surat
            $statusGm = 'Menunggu'; // Menunggu setelah SDM

            $nipUserSsdm = $user->nip; // Senior Self-Approve di kolom SSDM
            $nipUserSdm = $sdmUser?->nip;
            $nipUserGm = $gmUser?->nip;
            $penerimaNotifikasi = $sdmUser;
        }
        // Alur 3: SDM -> Manager (ttd) -> GM (ttd) - 2 TTD
        elseif ($user->isSdm()) {
            $statusSsdm = 'Disetujui'; // BYPASS
            $statusSdm = 'Disetujui'; // SDM Self-Approve di kolom SDM (Agar TTD SDM tidak muncul di surat)
            $statusManager = 'Menunggu Persetujuan'; // TTD Manager adalah langkah pertama di alur digital/surat

            $nipUserSsdm = $managerUser?->nip; // Manager sebagai Atasan Langsung (untuk ditampilkan di PDF jika perlu)
            $nipUserSdm = $user->nip; // SDM Self-Approve di kolom SDM
            $nipUserManager = $managerUser?->nip; // NIP Manager untuk persetujuan
            $nipUserGm = $gmUser?->nip;
            $penerimaNotifikasi = $managerUser;
        }
        // Alur 4: Manager -> SDM (ttd) -> GM (ttd) - 2 TTD
        elseif ($user->isManager()) {
            $statusSsdm = 'Disetujui'; // Manager Self-Approve di kolom SSDM
            $statusManager = 'Disetujui'; // Manager Self-Approve di kolom Manager (Agar TTD Manager tidak muncul di surat)
            $statusSdm = 'Menunggu Persetujuan'; // TTD SDM adalah langkah pertama di alur digital/surat

            $nipUserSsdm = $user->nip; // Manager Self-Approve di kolom SSDM
            $nipUserSdm = $sdmUser?->nip; // NIP SDM untuk persetujuan
            $nipUserManager = $user->nip; // Manager Self-Approve di kolom Manager
            $nipUserGm = $gmUser?->nip;
            $penerimaNotifikasi = $sdmUser;
        }

        $pathFileIzin = $request->hasFile('file_izin') ? $request->file('file_izin')->store('file_izin', 'public') : null;

        $cuti = Cuti::create(array_merge($validatedData, [
            'nip_user' => $user->nip,
            'no_surat' => $this->generateNomorSurat(),
            'file_izin' => $pathFileIzin,
            'tgl_upload' => now(),
            'status_ssdm' => $statusSsdm,
            'status_sdm' => $statusSdm,
            'status_manager' => $statusManager,
            'status_gm' => $statusGm,
            'nip_user_ssdm' => $nipUserSsdm,
            'nip_user_sdm' => $nipUserSdm,
            'nip_user_manager' => $nipUserManager,
            'nip_user_gm' => $nipUserGm,
        ]));

        // Kirim Notifikasi ke Atasan Pertama yang statusnya 'Menunggu Persetjuaan'
        if ($penerimaNotifikasi) {
            try {
                $penerimaNotifikasi->notify(new StatusSuratDiperbarui(
                    aktor: $user,
                    jenisSurat: 'Cuti',
                    statusBaru: 'Menunggu Persetujuan',
                    keterangan: "Terdapat pengajuan cuti baru ({$user->nama_lengkap}) yang menunggu persetujuan Anda.",
                    url: route('approvals.index') // Arahkan ke halaman approval
                ));
            } catch (\Exception $e) {
                Log::error("Notif gagal (Store Cuti): " . $e->getMessage());
            }
        }

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dibuat.');
    }

    /**
     * Mengubah status cuti berdasarkan jabatan.
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak',
        ]);

        $user = auth()->user();
        $pembuatCuti = $cuti->user;
        if (!$pembuatCuti) {
             return back()->with('error','Data pembuat cuti tidak ditemukan.');
        }

        $penerimaNotifikasiBerikutnya = null;
        $currentStatus = '';
        $keteranganNotif = '';
        $urlDetail = route('cuti.show', $cuti->id);
        $status = $request->status;
        $isFinalApproval = false; // Flag untuk finalisasi

        DB::beginTransaction();
        try {
            // --- Cek Otorisasi --- (Tetap dipertahankan dari kode lama Anda)
            // Cek apakah user berhak memproses status saat ini.
            if ($user->isGm() && $cuti->status_gm !== 'Menunggu Persetujuan') {
                 return back()->with('error', 'Bukan antrian/wewenang Anda untuk GM.');
            } elseif ($user->isSdm() && $cuti->status_sdm !== 'Menunggu Persetujuan') {
                 return back()->with('error', 'Bukan antrian/wewenang Anda untuk SDM.');
            } elseif ($user->isManager() && $cuti->status_manager !== 'Menunggu Persetujuan' && $cuti->nip_user_manager == $user->nip) {
                // Manager check
            } elseif ($user->isSenior() && $cuti->status_ssdm !== 'Menunggu Persetujuan' && $cuti->nip_user_ssdm == $user->nip) {
                // Otentikasi Senior/SSDM diperkuat dengan cek nip
            } elseif (!$user->isGm() && !$user->isSdm() && !$user->isManager() && !$user->isSenior()) {
                 return back()->with('error', 'Anda tidak berwenang memproses pengajuan cuti ini.');
            }
            // --- End Cek Otorisasi ---

            // 1. General Manager (GM)
            if ($user->isGm()) {
                $cuti->status_gm = $status;
                $cuti->nip_user_gm = $user->nip;
                $cuti->tgl_persetujuan_gm = now();

                if ($status == 'Disetujui') {
                    $isFinalApproval = true; // Alur 1, 2, 3, 4 selesai di sini
                    $currentStatus = 'Disetujui Penuh';
                    $keteranganNotif = "Cuti Anda sudah disetujui penuh. Silakan unduh surat cuti Anda.";
                    $urlDetail = route('cuti.download', $cuti->id);
                } else {
                    $cuti->alasan_penolakan = $request->alasan_penolakan;
                    $currentStatus = 'Ditolak';
                    $keteranganNotif = "Cuti Anda ditolak GM. Alasan: " . $cuti->alasan_penolakan;
                }
            }
            // 2. Manager
            elseif ($user->isManager() && $cuti->nip_user_manager == $user->nip) {
                $cuti->status_manager = $status;
                $cuti->nip_user_manager = $user->nip;
                $cuti->tgl_persetujuan_manager = now();

                if ($status == 'Disetujui') {
                    $isPemohonSdm = $pembuatCuti->isSdm();

                    // Manager sebagai Atasan Langsung Karyawan Biasa (Alur 1)
                    if ($pembuatCuti->isKaryawanBiasa() && $cuti->nip_user_ssdm == $user->nip) {
                        // Manager sebagai SSDM, diteruskan ke SDM
                        $cuti->status_ssdm = 'Disetujui'; // Set status SSDM karena dia bertindak sebagai SSDM
                        $cuti->tgl_persetujuan_ssdm = now(); // Tambahkan tanggal persetujuan SSDM
                        $cuti->status_sdm = 'Menunggu Persetujuan';
                        $penerimaNotifikasiBerikutnya = $cuti->sdm; // Relasi SDM
                        $currentStatus = 'Menunggu Persetujuan SDM';
                        $keteranganNotif = "Disetujui Atasan Langsung (Manager), diteruskan ke SDM.";
                    }
                    // Manager menyetujui Cuti SDM (Alur 3)
                    elseif ($isPemohonSdm && $cuti->nip_user_manager == $user->nip) {
                        // Manager di alur SDM -> Manager -> GM
                        $cuti->status_gm = 'Menunggu Persetujuan';
                        $penerimaNotifikasiBerikutnya = $cuti->gm; // Relasi GM
                        $currentStatus = 'Menunggu Persetujuan GM';
                        $keteranganNotif = "Disetujui Manager, diteruskan ke GM.";
                    }
                    else {
                        // Jika Manager bukan SSDM dan bukan menyetujui SDM, ini adalah skenario yang tidak teridentifikasi
                         DB::rollBack();
                         return back()->with('error', 'Cuti disetujui, tapi alur persetujuan selanjutnya tidak teridentifikasi. [Manager]');
                    }
                } else {
                    $cuti->alasan_penolakan = $request->alasan_penolakan;
                    $cuti->status_sdm = 'Ditolak'; // Otomatis tolak
                    $cuti->status_gm = 'Ditolak'; // Otomatis tolak
                    $currentStatus = 'Ditolak';
                    $keteranganNotif = "Cuti Anda ditolak Manager. Alasan: " . $cuti->alasan_penolakan;
                }
            }
            // 3. SDM (Senior Analis Keuangan, SDM & Umum)
            elseif ($user->isSdm()) {
                $cuti->status_sdm = $status;
                $cuti->nip_user_sdm = $user->nip;
                $cuti->tgl_persetujuan_sdm = now();

                if ($status == 'Disetujui') {
                    // Cuti dari Karyawan Biasa/Senior (Alur 1 & 2) ATAU Cuti dari Manager (Alur 4)
                    // Semuanya diteruskan ke GM
                    $cuti->status_gm = 'Menunggu Persetujuan';
                    $penerimaNotifikasiBerikutnya = $cuti->gm; // Relasi GM
                    $currentStatus = 'Menunggu Persetujuan GM';
                    $keteranganNotif = "Disetujui SDM, diteruskan ke GM.";
                } else {
                    $cuti->alasan_penolakan = $request->alasan_penolakan;
                    $cuti->status_manager = 'Ditolak'; $cuti->status_gm = 'Ditolak';
                    $currentStatus = 'Ditolak';
                    $keteranganNotif = "Cuti Anda ditolak SDM. Alasan: " . $cuti->alasan_penolakan;
                }
            }
            // 4. Senior (SSDM)
            elseif ($user->isSenior() && $cuti->nip_user_ssdm == $user->nip) {
                $cuti->status_ssdm = $status;
                $cuti->tgl_persetujuan_ssdm = now();

                if ($status == 'Disetujui') {
                    // Alur 1 - Diteruskan ke SDM
                    $cuti->status_sdm = 'Menunggu Persetujuan';
                    $penerimaNotifikasiBerikutnya = $cuti->sdm; // Relasi SDM
                    $currentStatus = 'Menunggu Persetujuan SDM';
                    $keteranganNotif = "Disetujui Atasan Langsung, diteruskan ke SDM.";
                } else {
                    $cuti->alasan_penolakan = $request->alasan_penolakan;
                    $cuti->status_sdm = 'Ditolak'; $cuti->status_gm = 'Ditolak';
                    $currentStatus = 'Ditolak';
                    $keteranganNotif = "Cuti Anda ditolak Atasan Langsung. Alasan: " . $cuti->alasan_penolakan;
                }
            }

            // Jika final approval, proses PDF dan Jatah Cuti
            if ($isFinalApproval && $status == 'Disetujui') {
                if ($this->isCutiMengurangiJatah($cuti->jenis_izin, !is_null($cuti->file_izin))) {
                    if ($pembuatCuti->jatah_cuti < $cuti->jumlah_hari) {
                        // Dobel cek jatah cuti sebelum finalisasi
                        DB::rollBack();
                        return back()->with('error', 'Gagal finalisasi: Jatah cuti karyawan (' . $pembuatCuti->jatah_cuti . ' hari) tidak mencukupi untuk ' . $cuti->jumlah_hari . ' hari.');
                    }
                    $pembuatCuti->decrement('jatah_cuti', $cuti->jumlah_hari);
                    $pembuatCuti->refresh();
                    Log::info("Jatah Cuti {$pembuatCuti->nip} dikurangi {$cuti->jumlah_hari} hari.");
                }

                // Generate PDF (Memanggil tanpa parameter kedua)
                $path = $this->generateSuratPdf($cuti);
                if ($path) {
                    $cuti->file_surat = $path;
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Cuti disetujui, tapi gagal membuat file surat. Cek logs.');
                }
            }

            // Simpan semua perubahan status (dan file_surat jika ada)
            $cuti->save();

            // === PERBAIKAN NOTIFIKASI ===

            // Notifikasi ke Pembuat Cuti
            if ($pembuatCuti) {
                try {
                    $pembuatCuti->notify(new StatusSuratDiperbarui(
                        $user, 'Cuti', $currentStatus, $keteranganNotif, $urlDetail
                    ));
                } catch (\Exception $e) {
                     Log::error("Notif GAGAL ke pembuat cuti [{$pembuatCuti->nip}]: " . $e->getMessage());
                }
            }

            // Notifikasi ke Atasan Berikutnya (Jika Disetujui dan belum final)
            if ($penerimaNotifikasiBerikutnya && $status == 'Disetujui' && !$isFinalApproval) {
                try {
                    $penerimaNotifikasiBerikutnya->notify(new StatusSuratDiperbarui(
                        $user,
                        'Cuti',
                        'Menunggu Persetujuan',
                        "Ada pengajuan cuti baru ({$cuti->user->nama_lengkap}) yang menunggu persetujuan Anda.",
                        route('approvals.index') // Link ke halaman approval
                    ));
                } catch (\Exception $e) {
                    Log::error("Notif GAGAL ke atasan berikutnya [{$penerimaNotifikasiBerikutnya?->nip}]: " . $e->getMessage());
                    // Jangan gagalkan transaksi hanya karena notif error
                }
            }
            // === END PERBAIKAN NOTIFIKASI ===


            DB::commit();
            // Arahkan kembali ke halaman approval
            return redirect()->route('approvals.index')->with('success', 'Status pengajuan cuti berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            // LOG ERROR LEBIH DETAIL
            Log::error("Update Cuti Error [ID {$cuti->id}]: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
            // Ini adalah pesan error yang Anda lihat
            return back()->with('error', 'Terjadi kesalahan saat memperbarui status cuti. Cek logs untuk detail.');
        }
    }

    /**
     * Menampilkan detail cuti.
     */
    public function show($id)
    {
        $cuti = Cuti::with('user', 'ssdm', 'sdm', 'gm', 'manager')->findOrFail($id);

        $user = Auth::user();
        // Cek apakah user adalah pemohon atau salah satu approver
        $isPemohon = $user->nip === $cuti->nip_user;
        $isApprover = in_array($user->nip, [
            $cuti->nip_user_ssdm,
            $cuti->nip_user_sdm,
            $cuti->nip_user_manager,
            $cuti->nip_user_gm
        ]);

        if (!$isPemohon && !$isApprover) {
            return back()->with('error', 'Anda tidak berhak melihat detail pengajuan ini.');
        }

        return view('pages.cuti.detail', compact('cuti'));
    }

    /**
     * Mengunduh file surat cuti yang sudah disetujui penuh.
     */
    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        // Otorisasi: Hanya pemohon atau approver yang boleh download
        $user = Auth::user();
        $isPemohon = $user->nip === $cuti->nip_user;
        $isApprover = in_array($user->nip, [
            $cuti->nip_user_ssdm,
            $cuti->nip_user_sdm,
            $cuti->nip_user_manager,
            $cuti->nip_user_gm
        ]);

        if (!$isPemohon && !$isApprover) {
             return back()->with('error', 'Anda tidak berhak mengunduh file ini.');
        }

        if ($cuti->file_surat && Storage::disk('public')->exists($cuti->file_surat)) {
            $filePath = storage_path('app/public/' . $cuti->file_surat);
            $safeName = Str::slug(Str::replace('/', '-', $cuti->no_surat)) . "_Cuti";
            return response()->download($filePath, "{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan atau belum selesai diproses.');
    }

    /**
     * Membatalkan pengajuan cuti yang masih 'Menunggu Persetujuan' SSDM.
     */
    public function cancel(Cuti $cuti)
    {
        if (Auth::user()->nip !== $cuti->nip_user) {
            return redirect()->route('cuti.index')->with('error', 'Anda tidak berhak membatalkan pengajuan ini.');
        }

        // Cek apakah sudah diproses
        $isProcessed = $cuti->status_ssdm !== 'Menunggu Persetujuan' ||
                       $cuti->status_sdm !== 'Menunggu' ||
                       $cuti->status_manager !== 'Menunggu' ||
                       $cuti->status_gm !== 'Menunggu';

        // Pengecualian untuk alur yang bypass SSDM (Senior, SDM, Manager)
        $isProcessedForBypassers = ($cuti->status_sdm !== 'Menunggu Persetujuan' && ($cuti->user->isSenior() || $cuti->user->isManager())) ||
                                   ($cuti->status_manager !== 'Menunggu Persetujuan' && $cuti->user->isSdm());

        // Jika pemohon adalah karyawan biasa dan sudah diproses
        if ($cuti->user->isKaryawanBiasa() && $isProcessed) {
             return redirect()->route('cuti.index')->with('error', 'Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }

        // Jika pemohon adalah senior/manager/sdm dan sudah melewati tahap pertama persetujuan
        if (($cuti->user->isSenior() || $cuti->user->isManager() || $cuti->user->isSdm()) && $isProcessedForBypassers) {
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
            Log::error("Cancel Cuti Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membatalkan cuti.');
        }
    }

    /**
     * Menampilkan informasi verifikasi Cuti dari pemindaian QR Code.
     */
    public function verifikasi($id)
    {
        $cuti = Cuti::with('user', 'ssdm', 'sdm', 'gm', 'manager')->find($id);

        if (!$cuti) {
            return view('pages.cuti.notfound', ['message' => 'Surat Cuti tidak ditemukan.']);
        }
        return view('pages.cuti.verifikasi_info', compact('cuti'));
    }

    /**
     * Menentukan apakah jenis cuti mengurangi jatah tahunan (jatah 12 hari).
     * ATURAN: Semua cuti mengurangi jatah, kecuali Cuti Sakit JIKA ada file pendukung.
     */
    private function isCutiMengurangiJatah(string $jenisIzin, bool $adaFile): bool
    {
        // Cuti Sakit dengan dokumen pendukung TIDAK mengurangi jatah cuti tahunan.
        if ($jenisIzin === 'Cuti Sakit' && $adaFile) {
            return false;
        }

        // Semua jenis cuti lainnya (Cuti Tahunan, Cuti Besar, Cuti Bersalin, Cuti Alasan Penting, atau Cuti Sakit tanpa file)
        // AKAN mengurangi jatah cuti tahunan.
        return true;
    }

    /**
     * Menghasilkan nomor surat cuti otomatis.
     */
    private function generateNomorSurat(): string
    {
        $tahun = date('Y');
        $lastCutiThisYear = Cuti::whereYear('created_at', $tahun)->orderBy('id', 'desc')->first();
        $nomorUrut = $lastCutiThisYear ? ((int) explode('/', $lastCutiThisYear->no_surat)[0] + 1) : 1;
        // Penyesuaian format nomor surat
        return sprintf("%03d/014.1/SDM/ABWWT/%s", $nomorUrut, $tahun);
    }

    /**
     * Menghasilkan URL verifikasi untuk QR Code.
     */
    protected function generateQrCodeUrl(Cuti $cuti)
    {
        return route('cuti.verifikasi', ['id' => $cuti->id]);
    }

    /**
     * Menghasilkan PDF surat cuti.
     * @param Cuti $cuti Data cuti
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
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale' => 5,
                'eccLevel' => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl);

            // 2. Siapkan Data View Umum
            $karyawan = $cuti->user; // <-- DATA NAMA & NPK
            $tahunCuti = Carbon::parse($cuti->tgl_mulai)->format('Y');

            // --- LOGIKA PEMUATAN LOGO DENGAN BASE64 EMBED ---
            $pathToLogo = public_path('images/logo2.jpg');
            $embed = null;
            if (File::exists($pathToLogo)) {
                $type = pathinfo($pathToLogo, PATHINFO_EXTENSION);
                $data = file_get_contents($pathToLogo);
                $embed = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            // --------------------------------------------------

            // --- LOGIKA PERHITUNGAN CUTI ---
            $jatahCutiTahunan = 12;
            $semuaCutiTerpakaiSebelumnya = Cuti::where('nip_user', $karyawan->nip)
                ->where('status_gm', 'Disetujui') // Cek hanya status final GM (4 Alur)
                ->where('id', '!=', $cuti->id)
                ->whereYear('tgl_mulai', $tahunCuti)
                ->get();

            $dataCutiSDM = [ // <-- DATA JUMLAH CUTI TERPAKAI
                'cuti_tahunan' => 0, 'cuti_besar' => 0, 'cuti_sakit' => 0,
                'cuti_bersalin' => 0, 'cuti_alasan_penting' => 0,
            ];
            $totalHariCutiYangMengurangiJatah = 0;
            foreach ($semuaCutiTerpakaiSebelumnya as $item) {
                $jenis = strtolower(str_replace(' ', '_', $item->jenis_izin));
                if (isset($dataCutiSDM[$jenis])) {
                    $dataCutiSDM[$jenis] += $item->jumlah_hari;
                }
                if ($this->isCutiMengurangiJatah($item->jenis_izin, !is_null($item->file_izin))) {
                    $totalHariCutiYangMengurangiJatah += $item->jumlah_hari;
                }
            }
            // Tambahkan cuti saat ini ke total cuti terpakai di kolom cuti
            $jenisCutiSaatIni = strtolower(str_replace(' ', '_', $cuti->jenis_izin));
            if (isset($dataCutiSDM[$jenisCutiSaatIni])) {
                $dataCutiSDM[$jenisCutiSaatIni] += $cuti->jumlah_hari;
            }
            // Tambahkan cuti saat ini ke total HARI cuti yang mengurangi jatah
            if ($this->isCutiMengurangiJatah($cuti->jenis_izin, !is_null($cuti->file_izin))) {
                $totalHariCutiYangMengurangiJatah += $cuti->jumlah_hari;
            }

            $sisaCutiDari12 = max(0, $jatahCutiTahunan - $totalHariCutiYangMengurangiJatah);
            $dataCutiSDM['sisa_cuti_tahunan'] = $sisaCutiDari12; // <-- TAMBAHKAN KE ARRAY

            // --- END LOGIKA PERHITUNGAN CUTI ---

            // ==========================================================
            // LOGIKA PEMILIHAN DATA TANDA TANGAN (SESUAI ALUR KOREKSI)
            // ==========================================================
            $pemohon = $cuti->user;
            $atasanPertama = null; // TTD Kanan Atas (Senior/Manager)
            $pejabatSdm = null;    // TTD Kiri Bawah (SDM)
            $pejabatGm = $cuti->gm; // TTD Kanan Bawah (GM) - Selalu Ada.

            if ($pemohon->isKaryawanBiasa()) {
                // ALUR 1: 3 TTD (SSDM, SDM, GM)
                $atasanPertama = $cuti->ssdm; // Senior/Manager (TTD Kanan Atas)
                $pejabatSdm = $cuti->sdm;     // SDM (TTD Kiri Bawah)
            } elseif ($pemohon->isSenior()) {
                // ALUR 2: 2 TTD (SDM, GM). Menghilangkan TTD Atasan Langsung.
                $atasanPertama = null;        // Dihilangkan
                $pejabatSdm = $cuti->sdm;     // SDM (TTD Kiri Bawah)
            } elseif ($pemohon->isSdm()) {
                // ALUR 3: 2 TTD (Manager, GM). Menghilangkan TTD SDM.
                $atasanPertama = $cuti->manager; // Manager (TTD Kanan Atas)
                $pejabatSdm = null;             // Dihilangkan
            } elseif ($pemohon->isManager()) {
                // ALUR 4: 2 TTD (SDM, GM). Menghilangkan TTD Atasan Langsung/Manager.
                $atasanPertama = null;        // Dihilangkan
                $pejabatSdm = $cuti->sdm;     // SDM (TTD Kiri Bawah)
            }


            // 1. Inisiasi data dasar dan view tunggal
            $viewData = compact(
                'cuti', 'qrCodeBase64', 'karyawan',
                'dataCutiSDM', 'sisaCutiDari12', 'embed', 'pemohon'
            );
            $viewName = 'pages.cuti.surat_cuti_umum_pdf';

            // Variabel untuk TTD
            $viewData['atasan_langsung'] = $atasanPertama; // Diisi/Null
            $viewData['sdm'] = $pejabatSdm; // Diisi/Null
            $viewData['gm'] = $pejabatGm; // Selalu diisi


            // 3. Load View dan Generate PDF
            $pdf = Pdf::loadView($viewName, $viewData) // Gunakan viewName
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'defaultFont' => 'sans-serif' // Tambahkan font default jika perlu
                ])->setPaper('A4');

            // Simpan file
            Storage::disk('public')->put($pathFileCuti, $pdf->output());

            return $pathFileCuti; // Return path relatif untuk disimpan di DB

        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getFile());
            return false;
        }
    }
}

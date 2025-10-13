<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\SP; // Tambahkan
use App\Models\User; // Tambahkan untuk notifikasi
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Tambahkan
use Illuminate\Support\Str; // Tambahkan
use App\Notifications\StatusSuratDiperbarui; // Tambahkan
use ZipArchive;

class SPApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isGm()) {
            // Cuti: Menunggu GM setelah disetujui SDM
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')
                                    ->where('status_sdm', 'Disetujui')
                                    ->where('status_gm', 'Menunggu Persetujuan')
                                    ->latest()->get();
            // SP: Menunggu GM setelah disetujui SDM
            $spsForApproval = SP::with('user.jabatanTerbaru.jabatan')
                                ->where('status_sdm', 'Disetujui')
                                ->where('status_gm', 'Menunggu Persetujuan')
                                ->latest()->get();

            // Riwayat Cuti (Ditolak/Disetujui Final)
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
                                ->where(fn($q) => $q->where('status_gm', 'Disetujui')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'))
                                ->latest()->get();
            // Riwayat SP (Ditolak/Disetujui Final)
            $spsHistory = SP::with('user.jabatanTerbaru.jabatan')
                            ->where(fn($q) => $q->where('status_gm', 'Disetujui')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'))
                            ->latest()->get();

            return view('pages.approval.index-gm', compact('cutisForApproval', 'cutisHistory', 'spsForApproval', 'spsHistory'));

        }
        elseif ($user->isSdm()) {
            // Cuti: Menunggu SDM (Karena tidak ada SSDM, SDM adalah atasan pertama setelah Karyawan)
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')
                                    ->where('status_sdm', 'Menunggu Persetujuan')
                                    ->latest()->get();
            // SP: Menunggu SDM
            $spsForApproval = SP::with('user.jabatanTerbaru.jabatan')
                                ->where('status_sdm', 'Menunggu Persetujuan')
                                ->latest()->get();

            // Riwayat Cuti
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
                                ->where(fn($q) => $q->where('status_gm', 'Disetujui')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'))
                                ->latest()->get();
            // Riwayat SP
            $spsHistory = SP::with('user.jabatanTerbaru.jabatan')
                            ->where(fn($q) => $q->where('status_gm', 'Disetujui')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'))
                            ->latest()->get();

            return view('pages.approval.index-sdm', compact('cutisForApproval', 'cutisHistory', 'spsForApproval', 'spsHistory'));
        }

        // Peran lain tidak melihat halaman persetujuan
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan.');
    }


    /**
     * Mengupdate status pengajuan Cuti atau SP.
     * Alur: SDM -> GM
     */
   public function update(Request $request, SP $sp)
    {
        // 1. Validasi
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak'
        ]);

        $user = auth()->user();
        $status = $request->input('status');

        // Cek jika SP sudah final
        if ($sp->status_gm === 'Disetujui' || $sp->status_sdm === 'Ditolak') {
             return redirect()->back()->with('error', 'Surat Peringatan ini sudah selesai diproses.');
        }

        DB::beginTransaction();
        try {
            $statusField = null; $dateField = null;
            $nextAction = null;

            // --- Logika GM ---
            if ($user->isGm() && $sp->status_gm === 'Menunggu Persetujuan') {
                $statusField = 'status_gm';
                $dateField = 'tgl_persetujuan_gm';

            }
            // --- Logika SDM ---
            elseif ($user->isSdm() && $sp->status_sdm === 'Menunggu Persetujuan') {
                $statusField = 'status_sdm';
                $dateField = 'tgl_persetujuan_sdm';

                if ($status == 'Disetujui') {
                    // Jika Disetujui SDM: Lanjut ke GM
                    $nextAction = 'to_gm';
                }
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Bukan antrian/wewenang Anda atau SP sudah tidak valid.');
            }

            // 2. Update status & waktu
            $sp->$statusField = $status;
            // Asumsi field nip_user_nya adalah nip_user_sdm atau nip_user_gm
            $sp->{"nip_user_" . str_replace('status_', '', $statusField)} = $user->nip;
            $sp->$dateField = now();

            // 3. Logika Transisi Status
            if ($status == 'Ditolak') {
                $sp->alasan_penolakan = $request->input('alasan_penolakan');
                // Jika ditolak di SDM, otomatis status GM menjadi Ditolak
                if ($statusField === 'status_sdm') {
                    $sp->status_gm = 'Ditolak';
                }
            } elseif ($status == 'Disetujui' && $nextAction === 'to_gm') {
                // Disetujui SDM, status GM menjadi Menunggu Persetujuan
                $sp->status_gm = 'Menunggu Persetujuan';
            } elseif ($status == 'Disetujui' && $statusField === 'status_gm') {
                // Disetujui GM (Final)
                $sp->status_gm = 'Disetujui';
            }

            $sp->save();

            // 4. Pemicu Finalisasi
            if ($sp->status_gm === 'Disetujui') {
                 // Memanggil metode finalizeSp dari SPController
                 app(\App\Http\Controllers\SPController::class)->finalizeSp($sp);
            }

            // 5. Kirim Notifikasi
            $this->sendApprovalNotification($user, $sp, $nextAction);

            DB::commit();
            return redirect()->route('sp.approvals.index')->with('success', 'Status Surat Peringatan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("SP Approval Update Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk mengirim notifikasi ke pemohon dan atasan berikutnya.
     */
    private function sendApprovalNotification($approver, SP $sp, $nextAction)
    {
        $jenisSurat = 'Surat Peringatan';
        $pemohon = $sp->user;
        $route = 'sp.show';
        $downloadRoute = 'sp.download';

        $currentStatus = '';
        $keteranganNotif = '';
        $urlDetail = route($route, $sp->id);

        // 1. Tentukan status untuk Notifikasi ke Pemohon
        if ($sp->status_gm === 'Ditolak' || $sp->status_sdm === 'Ditolak') {
            $currentStatus = 'Ditolak';
            $keteranganNotif = "Pengajuan {$jenisSurat} Anda ditolak oleh {$approver->nama_lengkap}.";
            if ($sp->alasan_penolakan) { $keteranganNotif .= " Alasan: {$sp->alasan_penolakan}"; }
        } elseif ($sp->status_gm === 'Disetujui') {
            $currentStatus = 'Disetujui Penuh';
            $keteranganNotif = $jenisSurat . ' sudah disetujui penuh dan diterbitkan.';
            $urlDetail = route($downloadRoute, $sp->id);
        } elseif ($sp->status_gm === 'Menunggu Persetujuan' && $sp->status_sdm === 'Disetujui') {
            $currentStatus = 'Menunggu Persetujuan GM';
            $keteranganNotif = "Disetujui SDM, diteruskan ke GM.";
        } elseif ($sp->status_sdm === 'Menunggu Persetujuan' && $approver->isSdm()) {
             $currentStatus = 'Menunggu Persetujuan SDM';
        }

        // Kirim Notifikasi ke Pemohon (Penerima SP)
        if ($pemohon) {
            try {
                $pemohon->notify(new StatusSuratDiperbarui(
                    $approver, $jenisSurat, $currentStatus, $keteranganNotif, $urlDetail
                ));
            } catch (\Exception $e) {
                Log::error("Notif ke Pemohon gagal: " . $e->getMessage());
            }
        }

        // 2. Notifikasi ke Atasan Berikutnya (Jika Disetujui dan belum final)
        if ($nextAction === 'to_gm') {
            // Asumsi nip_user_gm sudah diisi saat pembuatan SP
            $penerimaBerikutnya = User::where('nip', $sp->nip_user_gm)->first();

            if ($penerimaBerikutnya) {
                try {
                    $penerimaBerikutnya->notify(new StatusSuratDiperbarui(
                        $approver, $jenisSurat, 'Menunggu Persetujuan', 'Ada ' . $jenisSurat . ' yang menunggu persetujuan Anda (dari karyawan ' . $pemohon->nama_lengkap . ').', route('sp.approvals.index') // Arahkan ke halaman persetujuan
                    ));
                } catch (\Exception $e) {
                    Log::error("Notif ke GM gagal: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Mengunduh arsip Surat Peringatan (Report).
     */
    public function downloadReport(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|integer|min:2020|max:' . date('Y'),
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Filter hanya SP yang sudah disetujui GM dan memiliki file surat
        $query = SP::where('status_gm', 'Disetujui')
                    ->whereNotNull('file_surat')
                    ->whereYear('tgl_sp_terbit', $tahun); // Menggunakan tgl_sp_terbit

        if ($bulan !== 'all') {
            $query->whereMonth('tgl_sp_terbit', $bulan);
        }

        $suratSP = $query->get();

        if ($suratSP->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada Surat Peringatan yang ditemukan untuk periode yang dipilih.');
        }

        $namaBulan = ($bulan !== 'all') ? Carbon::create()->month($bulan)->isoFormat('MMMM') : 'Setahun';
        $zipFileName = 'laporan-sp-' . Str::slug($namaBulan) . '-' . $tahun . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($suratSP as $sp) {
                // Pastikan file_surat adalah path relatif yang benar di storage/app/public
                $filePath = storage_path('app/public/' . $sp->file_surat);
                if (File::exists($filePath)) {
                    $safeNoSurat = Str::slug(Str::replace('/', '-', $sp->no_surat));
                    $newFileName = "SP_{$sp->jenis_sp}_" . $safeNoSurat . "_" . Str::slug($sp->user->nama_lengkap) . '.pdf';
                    $zip->addFile($filePath, $newFileName);
                }
            }
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Gagal membuat file arsip ZIP.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}

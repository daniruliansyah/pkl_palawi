<?php

namespace App\Http\Controllers;

use App\Models\SP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\StatusSuratDiperbarui;
use Illuminate\Support\Carbon;
use ZipArchive;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SPApprovalController extends Controller
{
    /**
     * FUNGSI INTI: Menampilkan halaman "inbox" untuk persetujuan.
     */
    public function index()
    {
        $user = Auth::user();
        $baseSpQuery = fn($q) => $q->where('status_gm', 'Disetujui')->orWhere('status_sdm', 'Ditolak SDM')->orWhere('status_gm', 'Ditolak');

        if ($user->isGm()) {
            $spsForApproval = SP::with('user.jabatanTerbaru.jabatan')
                ->where('status_sdm', 'Disetujui SDM')
                ->where('status_gm', 'Menunggu Persetujuan')
                ->latest()->get();
            $spsHistory = SP::with('user.jabatanTerbaru.jabatan')->where($baseSpQuery)->latest()->get();
            return view('pages.riwayat_sp.index-gm', compact('spsForApproval', 'spsHistory'));

        } elseif ($user->isSdm()) {
            $spsForApproval = SP::with('user.jabatanTerbaru.jabatan')
                ->where('status_sdm', 'Menunggu Persetujui')
                ->latest()->get();
            $spsHistory = SP::with('user.jabatanTerbaru.jabatan')->where($baseSpQuery)->latest()->get();
            return view('pages.riwayat_sp.index-sdm', compact('spsForApproval', 'spsHistory'));
        }
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan.');
    }

    /**
     * FUNGSI INTI: Memproses update status (setuju/tolak).
     */
    public function updateStatus(Request $request, SP $sp)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak|max:500',
        ]);
        $user = Auth::user();
        $status = $request->input('status');

        DB::beginTransaction();
        try {
            if ($user->isSdm()) {
                if ($sp->status_sdm !== 'Menunggu Persetujui') {
                    return back()->with('error', 'SP ini tidak lagi menunggu persetujuan SDM.');
                }
                if ($status === 'Disetujui') {
                    $sp->update(['status_sdm' => 'Disetujui SDM', 'nip_user_sdm' => $user->nip, 'tgl_persetujuan_sdm' => Carbon::now(), 'status_gm' => 'Menunggu Persetujuan', 'alasan_penolakan' => null]);
                    $this->sendApprovalNotification($user, $sp, 'to_gm');
                } else {
                    $sp->update(['status_sdm' => 'Ditolak SDM', 'nip_user_sdm' => $user->nip, 'tgl_persetujuan_sdm' => Carbon::now(), 'status_gm' => 'Ditolak', 'alasan_penolakan' => $request->alasan_penolakan]);
                    $this->sendApprovalNotification($user, $sp, null);
                }
            
            // ==========================================================
            // BLOK GM YANG DIPERBAIKI
            // ==========================================================
            } elseif ($user->isGm()) {
                if ($sp->status_gm !== 'Menunggu Persetujuan' || $sp->status_sdm !== 'Disetujui SDM') {
                    return back()->with('error', 'SP ini belum siap untuk persetujuan GM.');
                }
                
                // Cek statusnya lagi (ini yang Anda hapus)
                if ($status === 'Disetujui') {
                    // Update status GM
                    $sp->update(['status_gm' => 'Disetujui', 'nip_user_gm' => $user->nip, 'tgl_persetujuan_gm' => Carbon::now(), 'alasan_penolakan' => null]);
                    
                    // Blok 'if' untuk generate no_surat SUDAH DIHAPUS (BENAR)

                    // Generate PDF
                    $filePath = $this->generateSuratPdf($sp);
                    if (!$filePath) {
                        throw new \Exception('Gagal menghasilkan file PDF. Periksa log untuk detail.');
                    }
                    
                    // Simpan path PDF
                    $sp->update(['file_sp' => $filePath]);
                    $this->sendApprovalNotification($user, $sp, 'final');

                } else { // Ini adalah blok 'Ditolak' oleh GM
                    $sp->update(['status_gm' => 'Ditolak', 'nip_user_gm' => $user->nip, 'tgl_persetujuan_gm' => Carbon::now(), 'alasan_penolakan' => $request->alasan_penolakan]);
                    $this->sendApprovalNotification($user, $sp, null);
                }
            // ==========================================================

            } else {
                return back()->with('error', 'Anda tidak memiliki wewenang untuk aksi ini.');
            }
            
            DB::commit();
            return redirect()->route('sp.approvals.index')->with('success', 'Status Surat Peringatan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("SP Approval/Rejection Error [SP ID: {$sp->id}]: " . $e->getMessage());
            return back()->with('error', 'Gagal memproses permintaan: ' . $e->getMessage());
        }
    }

    /**
     * FUNGSI INTI: Membuat laporan arsip ZIP dari SP yang sudah disetujui.
     */
    public function downloadReport(Request $request)
    {
        // ... (Kode fungsi ini tidak berubah, sudah benar)
    }

    // =============================================================
    // HELPER FUNCTIONS (KHUSUS UNTUK PROSES APPROVAL)
    // =============================================================
    /**
     * FUNGSI HELPER: Menghasilkan PDF (Hanya dipanggil saat GM menyetujui).
     */
    private function generateSuratPdf(SP $sp)
    {
        try {
            $sp->refresh();
            $fileName = Str::slug(Str::replace('/', '-', $sp->no_surat)) . "_SP_{$sp->jenis_sp}_{$sp->id}.pdf";
            $pathFileSP = 'file_sp/' . $fileName;

            $qrCodeUrl = route('sp.verifikasi', ['id' => $sp->id]);

            $options = new QROptions([
                'outputType'    => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64'   => true,
                'eccLevel'      => QRCode::ECC_H,
                'scale'         => 5,
                'quietzoneSize' => 1,
            ]);
            $qrCodeDataUri = (new QRCode($options))->render($qrCodeUrl);

            $karyawan = $sp->user;
            $gm = $sp->gm;
            $tembusanArray = json_decode($sp->tembusan, true) ?? [];

            $pathToLogo = public_path('images/logo2.jpg');
            if (File::exists($pathToLogo)) {
                $type = pathinfo($pathToLogo, PATHINFO_EXTENSION);
                $data = file_get_contents($pathToLogo);
                $embed = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $embed = null;
            }

            $pdf = Pdf::loadView('pages.sp.template-surat', compact('sp', 'qrCodeDataUri', 'karyawan', 'gm', 'embed', 'tembusanArray'))
                ->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true])
                ->setPaper('A4', 'portrait');

            Storage::disk('public')->put($pathFileSP, $pdf->output());
            return $pathFileSP;
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [SP ID {$sp->id}]: " . $e->getMessage());
            return null;
        }
    }

    /**
     * FUNGSI HELPER: Mengirim notifikasi.
     */
    private function sendApprovalNotification($approver, SP $sp, $nextAction)
    {
        $jenisSurat = 'Surat Peringatan';
        $pemohon = $sp->user; // Ini adalah karyawan yg dapat SP
        $pembuat = $sp->pembuat; // Kita perlu relasi ini di Model SP
        $currentStatus = '';
        $keteranganNotif = '';
        $urlDetail = route('sp.show', $sp->id);

        if ($sp->status_gm === 'Ditolak' || $sp->status_sdm === 'Ditolak SDM') {
            $currentStatus = 'Ditolak';
            $keteranganNotif = "Pengajuan {$jenisSurat} untuk {$pemohon->nama_lengkap} ditolak oleh {$approver->nama_lengkap}.";
            if ($sp->alasan_penolakan) { $keteranganNotif .= " Alasan: {$sp->alasan_penolakan}"; }
        } elseif ($sp->status_gm === 'Disetujui') {
            $currentStatus = 'Disetujui Penuh';
            $keteranganNotif = $jenisSurat . ' untuk ' . $pemohon->nama_lengkap . ' sudah disetujui penuh dan diterbitkan.';
            $urlDetail = route('sp.download', $sp->id);
        } elseif ($sp->status_gm === 'Menunggu Persetujuan' && $sp->status_sdm === 'Disetujui SDM') {
            $currentStatus = 'Menunggu Persetujuan GM';
            $keteranganNotif = "Disetujui oleh {$approver->nama_lengkap}, diteruskan ke GM.";
        }

        // Kirim notifikasi ke Pembuat Surat (misal: HRD yg input)
        if ($pembuat && !empty($keteranganNotif)) {
            try {
                // Jangan kirim notif ke diri sendiri jika pembuat = approver
                if($pembuat->nip !== $approver->nip) {
                    $pembuat->notify(new StatusSuratDiperbarui($approver, $jenisSurat, $currentStatus, $keteranganNotif, $urlDetail));
                }
            } catch (\Exception $e) {
                Log::error("Notif ke Pembuat gagal: " . $e->getMessage());
            }
        }
        
        // Kirim notifikasi ke Karyawan ybs HANYA JIKA SUDAH FINAL
        if ($pemohon && ($currentStatus === 'Disetujui Penuh' || $currentStatus === 'Ditolak')) {
             try {
                $pemohon->notify(new StatusSuratDiperbarui($approver, $jenisSurat, $currentStatus, $keteranganNotif, $urlDetail));
            } catch (\Exception $e) {
                Log::error("Notif ke Pemohon gagal: " . $e->getMessage());
            }
        }

        // Kirim notifikasi ke approver berikutnya (GM)
        if ($nextAction === 'to_gm') {
            $penerimaBerikutnya = User::whereHas('jabatanTerbaru', fn($q) => $q->where('jabatan_id', 1))->first(); // Asumsi ID 1 adalah GM
            if ($penerimaBerikutnya) {
                try {
                    $penerimaBerikutnya->notify(new StatusSuratDiperbarui(
                        $approver, $jenisSurat, 'Menunggu Persetujui',
                        'Ada ' . $jenisSurat . ' yang menunggu persetujuan Anda untuk karyawan ' . $pemohon->nama_lengkap,
                        route('sp.approvals.index')
                    ));
                } catch (\Exception $e) {
                    Log::error("Notif ke GM gagal: " . $e->getMessage());
                }
            }
        }
    }

} 
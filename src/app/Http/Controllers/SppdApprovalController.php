<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sppd; // Pastikan model SPPD di-import
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class SppdApprovalController extends Controller
{
    /**
     * Menampilkan halaman persetujuan SPPD.
     * Hanya bisa diakses oleh approver (SDM dan GM).
     */
    public function index()
    {
        $user = Auth::user();

        // Otorisasi: hanya SDM dan GM yang bisa mengakses
        if (!$user->isSdm() && !$user->isGm()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan SPPD.');
        }

        // Ambil data SPPD yang menunggu persetujuan dari user ini
        // Asumsi pemberi tugas ditentukan oleh ID jabatan
        $jabatanId = $user->jabatanTerbaru->id_jabatan ?? null;

        $sppdsForApproval = Sppd::where('pemberi_tugas_id', $jabatanId)
                                ->where('status', 'menunggu')
                                ->with('user.jabatanTerbaru.jabatan', 'penyetuju')
                                ->latest()
                                ->get();

        // Ambil riwayat SPPD yang pernah diproses oleh user ini
        $sppdsHistory = Sppd::where('nip_penyetuju', $user->nip)
                            ->with('user.jabatanTerbaru.jabatan', 'penyetuju')
                            ->latest()
                            ->get();

        return view('pages.surat_sppd.approval', compact('sppdsForApproval', 'sppdsHistory'));
    }

    /**
     * Mengupdate status pengajuan SPPD (menyetujui atau menolak).
     */
    public function update(Request $request, Sppd $sppd)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak',
        ]);

        $user = Auth::user();

        // Otorisasi: Pastikan user yang login adalah pemberi tugas yang benar
        if ($sppd->pemberi_tugas_id !== ($user->jabatanTerbaru->id_jabatan ?? null)) {
            return redirect()->back()->with('error', 'Anda tidak berwenang untuk memproses pengajuan ini.');
        }

        $sppd->status = $request->status;
        $sppd->nip_penyetuju = $user->nip; // Catat NIP siapa yang melakukan aksi
        $sppd->tgl_persetujuan = now();

        if ($request->status === 'Ditolak') {
            $sppd->alasan_penolakan = $request->alasan_penolakan;
        }

        $sppd->save();

        // Jika disetujui, panggil method pembuatan PDF dari SppdController
        if ($sppd->status === 'Disetujui') {
            app(SppdController::class)->generateSuratPdf($sppd);
        }

        return redirect()->route('sppd.approvals.index')->with('success', 'Status SPPD berhasil diperbarui.');
    }
}


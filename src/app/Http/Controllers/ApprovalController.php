<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ApprovalController extends Controller
{
    // ... method index() tidak berubah ...
    public function index()
    {
        $user = Auth::user();

        if ($user->isGm()) {
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')->where('status_sdm', 'Disetujui')->where('status_gm', 'Menunggu Persetujuan')->latest()->get();
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')->where(function ($query) { $query->where('status_gm', 'Disetujui')->orWhere('status_ssdm', 'Ditolak')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'); })->latest()->get();
            return view('pages.approval.index-gm', compact('cutisForApproval', 'cutisHistory'));
        }
        elseif ($user->isSdm()) {
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where(function($query) {
                    $query->where('status_ssdm', 'Disetujui')->where('status_gm', '!=', 'Disetujui')->where('status_sdm', 'Menunggu Persetujuan');
                })->orWhere(function($query) use ($user) {
                    $query->where('nip_user_sdm', $user->nip)->where('status_sdm', 'Menunggu Persetujuan');
                })->latest()->get();
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')->where(function ($query) { $query->where('status_gm', 'Disetujui')->orWhere('status_ssdm', 'Ditolak')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'); })->latest()->get();
            return view('pages.approval.index-sdm', compact('cutisForApproval', 'cutisHistory'));
        }
        elseif ($user->isSenior()) {
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')->where('status_ssdm', 'Menunggu Persetujuan')->where('nip_user_ssdm', $user->nip)->latest()->get();
            $cutisHistory = Cuti::with('user')->where('status_ssdm', '!=', 'Menunggu Persetujuan')->where('nip_user_ssdm', $user->nip)->latest()->get();
            return view('pages.approval.index-ssdm', compact('cutisForApproval', 'cutisHistory'));
        }
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan.');
    }


    /**
     * Mengupdate status pengajuan cuti.
     */
    public function update(Request $request, Cuti $cuti)
    {
        $request->validate(['status' => 'required|in:Disetujui,Ditolak', 'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak']);
        $user = auth()->user();
        $status = $request->input('status');

        DB::beginTransaction();
        try {
            // Logika untuk GM tetap sama
            if ($user->isGm()) {
                if ($cuti->status_gm !== 'Menunggu Persetujuan') return redirect()->back()->with('error', 'Pengajuan ini tidak lagi menunggu persetujuan Anda.');
                $cuti->status_gm = $status;
                if ($status == 'Ditolak') $cuti->alasan_penolakan = $request->input('alasan_penolakan');

            // === BAGIAN YANG DIPERBARUI ===
            } elseif ($user->isSdm()) {
                if ($cuti->status_sdm !== 'Menunggu Persetujuan') return redirect()->back()->with('error', 'Pengajuan ini tidak lagi menunggu persetujuan Anda.');
                
                $cuti->status_sdm = $status;
                
                if ($status == 'Disetujui') {
                    // Cek siapa yang mengajukan cuti dari awal
                    $pemohonCuti = $cuti->user;
                    if ($pemohonCuti->isGm()) {
                        // Jika pemohon adalah GM, persetujuan SDM adalah final
                        $cuti->status_gm = 'Disetujui';
                        $cuti->tgl_persetujuan_gm = now(); // Catat tanggal seolah-olah GM setuju
                    } else {
                        // Jika pemohon bukan GM, teruskan ke GM
                        $cuti->status_gm = 'Menunggu Persetujuan';
                    }
                } else {
                    // Jika SDM menolak, alur berhenti
                    $cuti->alasan_penolakan = $request->input('alasan_penolakan');
                    $cuti->status_gm = 'Ditolak';
                }

            // Logika untuk Senior tetap sama
            } elseif ($user->isSenior()) {
                if ($cuti->status_ssdm !== 'Menunggu Persetujuan') return redirect()->back()->with('error', 'Pengajuan ini tidak lagi menunggu persetujuan Anda.');
                $cuti->status_ssdm = $status;
                if ($status == 'Disetujui') {
                    $cuti->status_sdm = 'Menunggu Persetujuan';
                } else {
                    $cuti->alasan_penolakan = $request->input('alasan_penolakan');
                    $cuti->status_sdm = 'Ditolak';
                    $cuti->status_gm = 'Ditolak';
                }
            } else {
                return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk aksi ini.');
            }
            
            $cuti->save();

            // Pemicu pembuatan PDF akan tetap berjalan normal jika status GM menjadi 'Disetujui'
            if ($cuti->status_gm === 'Disetujui') {
                app(CutiController::class)->finalizeCuti($cuti);
            }

            DB::commit();
            return redirect()->route('approvals.index')->with('success', 'Status pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ... method downloadReport() tidak berubah ...
    public function downloadReport(Request $request)
    {
        $request->validate([ 'bulan' => 'required|string', 'tahun' => 'required|integer|min:2020|max:' . date('Y'), ]);
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $query = Cuti::where('status_gm', 'Disetujui')->whereNotNull('file_surat')->whereYear('tgl_mulai', $tahun);
        if ($bulan !== 'all') { $query->whereMonth('tgl_mulai', $bulan); }
        $suratCuti = $query->get();

        if ($suratCuti->isEmpty()) { return redirect()->back()->with('error', 'Tidak ada surat cuti yang ditemukan untuk periode yang dipilih.'); }
        
        $namaBulan = ($bulan !== 'all') ? \Carbon\Carbon::create()->month($bulan)->isoFormat('MMMM') : 'Setahun';
        $zipFileName = 'laporan-cuti-' . $namaBulan . '-' . $tahun . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($suratCuti as $surat) {
                $filePath = storage_path('app/public/' . $surat->file_surat);
                if (File::exists($filePath)) {
                    $newFileName = 'cuti-' . str_replace(' ', '_', $surat->user->nama_lengkap) . '-' . $surat->tgl_mulai . '.pdf';
                    $zip->addFile($filePath, $newFileName);
                }
            }
            $zip->close();
        } else { return redirect()->back()->with('error', 'Gagal membuat file arsip ZIP.'); }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}


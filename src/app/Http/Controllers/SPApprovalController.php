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
    public function index()
    {
        $user = Auth::user();

        $baseSpQuery = fn($q) => $q->where('status_gm', 'Disetujui')
                                  ->orWhere('status_sdm', 'Ditolak SDM')
                                  ->orWhere('status_gm', 'Ditolak');

        if ($user->isGm()) {
            $spsForApproval = SP::with('user.jabatanTerbaru.jabatan')
                ->where('status_sdm', 'Disetujui SDM')
                ->where('status_gm', 'Menunggu Persetujuan')
                ->latest()->get();

            $spsHistory = SP::with('user.jabatanTerbaru.jabatan')
                ->where(function($query) use ($baseSpQuery, $user) {
                    $query->where($baseSpQuery);
                    $query->orWhere(function($q2) use ($user) {
                        $q2->whereIn('status_gm', ['Disetujui', 'Ditolak'])
                           ->where('nip_user_gm', $user->nip);
                    });
                })
                ->latest()->get();

            return view('pages.riwayat_sp.index-gm', compact('spsForApproval', 'spsHistory'));

        } elseif ($user->isSdm()) {
            $spsForApproval = SP::with('user.jabatanTerbaru.jabatan')
                ->where('status_sdm', 'Menunggu Persetujuan')
                ->latest()->get();

            $spsHistory = SP::with('user.jabatanTerbaru.jabatan')
                ->where(function($query) use ($baseSpQuery, $user) {
                    $query->where($baseSpQuery);
                    $query->orWhere(function($q2) use ($user) {
                        $q2->whereIn('status_sdm', ['Disetujui SDM', 'Ditolak SDM'])
                           ->where('nip_user_sdm', $user->nip);
                    });
                })
                ->latest()->get();

            return view('pages.riwayat_sp.index-sdm', compact('spsForApproval', 'spsHistory'));
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan.');
    }

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
                if ($sp->status_sdm !== 'Menunggu Persetujuan') {
                    return back()->with('error', 'SP ini tidak lagi menunggu persetujuan SDM.');
                }

                if ($status === 'Disetujui') {
                    $sp->update([
                        'status_sdm' => 'Disetujui SDM',
                        'nip_user_sdm' => $user->nip,
                        'tgl_persetujuan_sdm' => Carbon::now(),
                        'status_gm' => 'Menunggu Persetujuan',
                        'alasan_penolakan' => null
                    ]);

                    $this->sendApprovalNotification($user, $sp, 'to_gm');

                } else {
                    $sp->update([
                        'status_sdm' => 'Ditolak SDM',
                        'nip_user_sdm' => $user->nip,
                        'tgl_persetujuan_sdm' => Carbon::now(),
                        'status_gm' => 'Ditolak',
                        'alasan_penolakan' => $request->alasan_penolakan
                    ]);

                    $this->sendApprovalNotification($user, $sp, null);
                }

            } elseif ($user->isGm()) {
                if ($sp->status_gm !== 'Menunggu Persetujuan' || $sp->status_sdm !== 'Disetujui SDM') {
                    return back()->with('error', 'SP ini belum siap untuk persetujuan GM.');
                }

                if ($status === 'Disetujui') {
                    $sp->update([
                        'status_gm' => 'Disetujui',
                        'nip_user_gm' => $user->nip,
                        'tgl_persetujuan_gm' => Carbon::now(),
                        'alasan_penolakan' => null
                    ]);

                    $filePath = $this->generateSuratPdf($sp);
                    if (!$filePath) {
                        throw new \Exception('Gagal menghasilkan file PDF. Periksa log untuk detail.');
                    }

                    $sp->update(['file_sp' => $filePath]);
                    $this->sendApprovalNotification($user, $sp, 'final');

                } else {
                    $sp->update([
                        'status_gm' => 'Ditolak',
                        'nip_user_gm' => $user->nip,
                        'tgl_persetujuan_gm' => Carbon::now(),
                        'alasan_penolakan' => $request->alasan_penolakan
                    ]);

                    $this->sendApprovalNotification($user, $sp, null);
                }

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

    public function downloadReport(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|integer|min:2020|max:' . date('Y'),
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $query = SP::where('status_gm', 'Disetujui')
                    ->whereNotNull('file_sp')
                    ->whereYear('tgl_sp_terbit', $tahun);

        if ($bulan !== 'all') {
            $query->whereMonth('tgl_sp_terbit', $bulan);
        }

        $suratSP = $query->with('user')->get();

        if ($suratSP->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada Surat Peringatan yang ditemukan untuk periode yang dipilih.');
        }

        $namaBulan = ($bulan !== 'all') ? Carbon::create()->month($bulan)->isoFormat('MMMM') : 'Setahun';

        $zipFileName = 'laporan-sp-' . $namaBulan . '-' . $tahun . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($suratSP as $surat) {
                $filePath = storage_path('app/public/' . $surat->file_sp);
                if (File::exists($filePath)) {
                    $newFileName = 'SP' . $surat->jenis_sp . '-' . str_replace(' ', '_', $surat->user->nama_lengkap) . '-' . $surat->tgl_sp_terbit . '.pdf';
                    $zip->addFile($filePath, $newFileName);
                }
            }
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Gagal membuat file arsip ZIP.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function generateSuratPdf(SP $sp)
    {
        try {
            $sp->refresh();
            $fileName = Str::slug(Str::replace('/', '-', $sp->no_surat)) . "_SP_{$sp->jenis_sp}_{$sp->id}.pdf";
            $pathFileSP = 'file_sp/' . $fileName;

            $qrCodeUrl = route('sp.verifikasi', ['id' => $sp->id]);

            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'eccLevel' => QRCode::ECC_H,
                'scale' => 5,
                'quietzoneSize' => 1,
            ]);

            $qrCodeDataUri = (new QRCode($options))->render($qrCodeUrl);

            $karyawan = $sp->user;
            $gm = $sp->gm;
            $tembusanArray = json_decode($sp->tembusan, true) ?? [];

            $pathToLogo = public_path('images/logo2.jpg');
            $embed = null;

            if (File::exists($pathToLogo)) {
                $type = pathinfo($pathToLogo, PATHINFO_EXTENSION);
                $data = file_get_contents($pathToLogo);
                $embed = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                Log::warning('File logo tidak ditemukan di: ' . $pathToLogo);
            }

            $spForPdf = clone $sp;
            $fileBuktiBase64 = null;

            if ($sp->file_bukti && Storage::disk('public')->exists($sp->file_bukti)) {
                try {
                    $fileContents = Storage::disk('public')->get($sp->file_bukti);
                    $mimeType = Storage::disk('public')->mimeType($sp->file_bukti);

                    if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
                        $fileBuktiBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
                    } else {
                        Log::warning("File bukti [SP ID {$sp->id}] bukan gambar ({$mimeType}), tidak bisa di-embed.");
                    }

                } catch (\Exception $e) {
                    Log::error("Gagal membaca/embed file bukti [SP ID {$sp->id}]: " . $e->getMessage());
                }
            }

            $spForPdf->file_bukti = $fileBuktiBase64;

            $pdf = Pdf::loadView('pages.sp.template-surat', [
                'sp' => $spForPdf,
                'qrCodeDataUri' => $qrCodeDataUri,
                'karyawan' => $karyawan,
                'gm' => $gm,
                'embed' => $embed,
                'tembusanArray' => $tembusanArray
            ])
            ->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true])
            ->setPaper('A4', 'portrait');

            Storage::disk('public')->put($pathFileSP, $pdf->output());

            return $pathFileSP;

        } catch (\Exception $e) {
            Log::error("PDF Generation Error [SP ID {$sp->id}]: " . $e->getMessage());
            return null;
        }
    }

    private function sendApprovalNotification($approver, SP $sp, $nextAction)
    {
        $jenisSurat = 'Surat Peringatan';
        $pemohon = $sp->user;
        $pembuat = $sp->pembuat;
        $currentStatus = '';
        $keteranganNotif = '';
        $urlDetail = route('sp.show', $sp->id);

        if ($sp->status_gm === 'Ditolak' || $sp->status_sdm === 'Ditolak SDM') {
            $currentStatus = 'Ditolak';
            $keteranganNotif = "Pengajuan {$jenisSurat} untuk {$pemohon->nama_lengkap} ditolak oleh {$approver->nama_lengkap}.";
            if ($sp->alasan_penolakan) {
                $keteranganNotif .= " Alasan: {$sp->alasan_penolakan}";
            }

        } elseif ($sp->status_gm === 'Disetujui') {
            $currentStatus = 'Disetujui Penuh';
            $keteranganNotif = $jenisSurat . ' untuk ' . $pemohon->nama_lengkap . ' sudah disetujui penuh dan diterbitkan.';
            $urlDetail = route('sp.download', $sp->id);

        } elseif ($sp->status_gm === 'Menunggu Persetujuan' && $sp->status_sdm === 'Disetujui SDM') {
            $currentStatus = 'Menunggu Persetujuan GM';
            $keteranganNotif = "Disetujui oleh {$approver->nama_lengkap}, diteruskan ke GM.";
        }

        if ($pembuat && !empty($keteranganNotif)) {
            try {
                if ($pembuat->nip !== $approver->nip) {
                    $pembuat->notify(new StatusSuratDiperbarui($approver, $jenisSurat, $currentStatus, $keteranganNotif, $urlDetail));
                }
            } catch (\Exception $e) {
                Log::error("Notif ke Pembuat gagal: " . $e->getMessage());
            }
        }

        if ($pemohon && ($currentStatus === 'Disetujui Penuh' || $currentStatus === 'Ditolak')) {
            // ...
        }
    }
}

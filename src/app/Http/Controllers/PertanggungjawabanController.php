<?php

namespace App\Http\Controllers;

use App\Models\Pertanggungjawaban;
use App\Models\Sppd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log; 

class PertanggungjawabanController extends Controller
{
    /**
     * Menampilkan form untuk membuat laporan pertanggungjawaban.
     */
    public function create(Sppd $sppd)
    {
        // Otorisasi: Hanya pembuat SPPD yang bisa membuat laporan
        if (Auth::user()->nip !== $sppd->nip_user) {
            abort(403, 'AKSES DITOLAK');
        }

        // Arahkan ke view form
        return view('pages.surat_sppd.pertanggungjawaban_create', compact('sppd'));
    }

    /**
     * Menyimpan laporan dan men-generate kuitansi PDF.
     */
    public function store(Request $request, Sppd $sppd)
    {
        // Otorisasi: Hanya pembuat SPPD yang bisa menyimpan laporan
        if (Auth::user()->nip !== $sppd->nip_user) {
            abort(403, 'AKSES DITOLAK');
        }

        // 1. Validasi data dari form
        $validated = $request->validate([
            'uang_harian'       => 'nullable|numeric|min:0',
            'transportasi_lokal'=> 'nullable|numeric|min:0',
            'uang_makan'        => 'nullable|numeric|min:0',
            'akomodasi_mandiri' => 'nullable|numeric|min:0',
            'akomodasi_tt'      => 'nullable|numeric|min:0',
            'transportasi_lain' => 'nullable|numeric|min:0',
            'keterangan'        => 'nullable|string',
        ]);

        // Ganti nilai null dengan 0 agar bisa dijumlahkan
        foreach ($validated as $key => $value) {
            $validated[$key] = $value ?? 0;
        }

        // 2. Kalkulasi total biaya
        $total = array_sum(array_filter($validated, 'is_numeric'));

        // 3. Simpan data laporan ke database
        $laporan = Pertanggungjawaban::create(array_merge($validated, [
            'sppd_id'            => $sppd->id,
            'user_id'            => Auth::id(),
            'tanggal_laporan'    => now(),
            'total_biaya_bersih' => $total,
        ]));

        // 4. Siapkan data untuk dikirim ke template PDF
        $dataForPdf = [
            'laporan' => $laporan,
            'sppd'    => $sppd,
            'user'    => $sppd->user, // Mengambil data user dari relasi SPPD
            'total'   => $total,
        ];

        // 5. Generate PDF
        $pdf = PDF::loadView('pages.surat_sppd.kuitansi_template', $dataForPdf)->setPaper('a4', 'portrait');

        // 6. Simpan PDF ke storage
        $fileName = 'kuitansi_' . $laporan->id . '_' . time() . '.pdf';
        $pdf->save(storage_path('app/public/kuitansi/' . $fileName));

        // 7. Update path file di database
        $laporan->update(['file_kuitansi' => 'kuitansi/' . $fileName]);

        return redirect()->route('sppd.index')->with('success', 'Laporan pertanggungjawaban dan kuitansi berhasil dibuat!');
    }

    /**
     * Mengunduh file kuitansi.
     */
    public function download(Pertanggungjawaban $pertanggungjawaban)
    {
    // Otorisasi Anda (biarkan seperti ini)
    if (Auth::id() !== $pertanggungjawaban->user_id && !Auth::user()->isSdm()) {
        abort(403, 'AKSI TIDAK DIIZINKAN. HANYA UNTUK PEMOHON DAN SDM.');
    }

    if ($pertanggungjawaban->file_kuitansi && Storage::disk('public')->exists($pertanggungjawaban->file_kuitansi)) {
        $filePath = storage_path('app/public/' . $pertanggungjawaban->file_kuitansi);
        return response()->download($filePath, "Kuitansi-SPPD-{$pertanggungjawaban->user->nama_lengkap}.pdf");
    }

    return back()->with('error', 'File kuitansi tidak ditemukan.');
    }
}
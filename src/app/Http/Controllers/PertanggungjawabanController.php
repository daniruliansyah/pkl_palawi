<?php

namespace App\Http\Controllers;

use App\Models\Pertanggungjawaban;
use App\Models\Sppd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; // Pastikan ini ada

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
     * (FUNGSI INI SUDAH DIPERBARUI)
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
            'keterangan'        => 'nullable|string', // Keterangan divalidasi sebagai string
        ]);

        // 2. Pisahkan data numerik untuk kalkulasi
        $numericData = $validated;
        unset($numericData['keterangan']); // Hapus 'keterangan' dari array numerik

        // 3. Ganti nilai null dengan 0 HANYA untuk data numerik
        foreach ($numericData as $key => $value) {
            $numericData[$key] = $value ?? 0;
        }

        // 4. Kalkulasi total biaya dari data numerik
        $total = array_sum($numericData);

        // 5. Simpan data laporan ke database (gabungkan data numerik dan data asli)
        $laporan = Pertanggungjawaban::create(array_merge(
            $numericData, // Data numerik yang sudah di-nol-kan
            [
                'keterangan'         => $validated['keterangan'] ?? null, // Ambil 'keterangan' asli
                'sppd_id'            => $sppd->id,
                'nip_user'           => Auth::user()->nip,
                'tanggal_laporan'    => now(),
                'total_biaya_bersih' => $total,
            ]
        ));

        // 6. Siapkan data untuk dikirim ke template PDF
        $dataForPdf = [
            'laporan' => $laporan,
            'sppd'    => $sppd,
            'user'    => $sppd->user,
            'total'   => $total,
        ];

        // 7. Generate PDF
        $pdf = PDF::loadView('pages.surat_sppd.kuitansi_template', $dataForPdf)->setPaper('a4', 'portrait');

        // 8. Tentukan nama file dan path
        $fileName = 'kuitansi_' . $laporan->id . '_' . time() . '.pdf';
        $directory = storage_path('app/public/kuitansi');
        $filePath = $directory . '/' . $fileName;

        // 9. Pastikan folder 'kuitansi' ada
        File::ensureDirectoryExists($directory);

        // 10. Simpan file PDF ke path
        $pdf->save($filePath);

        // 11. Update path file di database
        $laporan->update(['file_kuitansi' => 'kuitansi/' . $fileName]);

        return redirect()->route('sppd.index')->with('success', 'Laporan pertanggungjawaban dan kuitansi berhasil dibuat!');
    }


    /**
     * Mengunduh file kuitansi.
     */
    public function download(Pertanggungjawaban $pertanggungjawaban)
    {
        // Otorisasi
        if (Auth::user()->nip !== $pertanggungjawaban->nip_user && !Auth::user()->isSdm()) {
            abort(403, 'AKSI TIDAK DIIZINKAN. HANYA UNTUK PEMOHON DAN SDM.');
        }

        if ($pertanggungjawaban->file_kuitansi && Storage::disk('public')->exists($pertanggungjawaban->file_kuitansi)) {
            $filePath = storage_path('app/public/' . $pertanggungjawaban->file_kuitansi);
            return response()->download($filePath, "Kuitansi-SPPD-{$pertanggungjawaban->user->nama_lengkap}.pdf");
        }

        return back()->with('error', 'File kuitansi tidak ditemukan.');
    }
}

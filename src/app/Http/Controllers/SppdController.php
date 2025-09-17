<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class SppdController extends Controller
{
    public function index()
    {
        $sppds = Sppd::with('user')->latest()->get();
        return view('pages.surat_sppd.index', compact('sppds'));
    }

    public function create()
    {
        return view('pages.surat_sppd.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'keterangan'    => 'required|string',
            'lokasi_tujuan' => 'required|string|max:100',
            'surat_bukti'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = [
            'nip_user'      => auth()->user()->nip,
            'tgl_mulai'     => $request->tgl_mulai,
            'tgl_selesai'   => $request->tgl_selesai,
            'keterangan'    => $request->keterangan,
            'lokasi_tujuan' => $request->lokasi_tujuan,
            'status_sdm'    => 'menunggu',
            'status_gm'     => 'menunggu',
        ];

        if ($request->hasFile('surat_bukti')) {
            $data['surat_bukti'] = $request->file('surat_bukti')->store('surat_bukti', 'public');
        }

        Sppd::create($data);

        return redirect()->route('sppd.index')
            ->with('success', 'Pengajuan SPPD berhasil dibuat, menunggu persetujuan SDM.');
    }

    public function show($id)
    {
        $sppd = Sppd::with('user')->findOrFail($id);
        return view('sppd.show', compact('sppd'));
    }

    public function updateStatus(Request $request, $id)
    {
        $sppd = Sppd::findOrFail($id);

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'role'   => 'required|in:gm,sdm',
        ]);

        if ($request->role === 'sdm') {
            $sppd->status_sdm   = $request->status;
            $sppd->nip_user_sdm = auth()->user()->nip;
        } elseif ($request->role === 'gm') {
            if ($sppd->status_sdm !== 'disetujui') {
                return redirect()->back()->with('error', 'Surat harus disetujui SDM terlebih dahulu.');
            }
            $sppd->status_gm   = $request->status;
            $sppd->nip_user_gm = auth()->user()->nip;
        }

        $sppd->save();

        // ✅ Kalau dua-duanya sudah disetujui → generate surat
        if ($sppd->status_sdm === 'disetujui' && $sppd->status_gm === 'disetujui') {
            $sppd->no_surat  = 'SPPD-' . now()->format('Ymd') . '-' . $sppd->id;
            $sppd->file_sppd = $this->generateSuratPdf($sppd);
            $sppd->save();
        }

        return redirect()->route('sppd.index')
            ->with('success', "Status {$request->role} berhasil diubah menjadi {$request->status}.");
    }

    public function generate($id)
    {
        $sppd = Sppd::with('user')->findOrFail($id);
        return view('pages.surat_sppd.surat_resmi', compact('sppd'));
    }

    // 🔧 Tambah function generate PDF
            protected function generateSuratPdf($sppd)
        {
            $fileName = 'sppd_' . $sppd->id . '.pdf';
            $path = storage_path('app/public/sppd/' . $fileName);

            $pdf = Pdf::loadView('pages.surat_sppd.surat_resmi', compact('sppd'));
            $pdf->save($path);

            return 'sppd/' . $fileName;
        }

    // 🔧 Download file surat
    public function download($id)
    {
        $sppd = Sppd::findOrFail($id);
        return response()->download(storage_path('app/public/' . $sppd->file_sppd));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    public function index()
    {
        $cutis = Cuti::with('user.jabatanTerbaru.jabatan')->latest()->get();
        return view('pages.cuti.index-ssdm', compact('cutis'));
    }

    public function create()
    {
        return view('pages.cuti.create');
    }

    public function store(Request $request)
    {        
        $validatedData = $request->validate([
            'jenis_izin'    => 'required|string',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'jumlah_hari'   => 'required|integer|min:1|max:12',
            'keterangan'    => 'required|string',
            'file_izin'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Opsional, maks 2MB
        ]);

        $pathFileIzin = null;
        
        if ($request->hasFile('file_izin')) {
            $pathFileIzin = $request->file('file_izin')->store('file_izin', 'public');
        }

        // LOGIKA PEMBUATAN NOMOR SURAT
        $tahun = date('Y');
        $bulan = date('m');
        $lastCuti = Cuti::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->latest('id')->first();
        $nomorUrut = $lastCuti ? (int)substr($lastCuti->no_surat, -3) + 1 : 1;
        $noSurat = sprintf("CUTI/%s/%s/%03d", $tahun, $bulan, $nomorUrut);

        Cuti::create(array_merge(
            $validatedData, // Semua data dari form ada di sini, termasuk 'jenis_izin'
            [
                'nip_user'       => Auth::user()->nip,
                'no_surat'         => $noSurat,
                'file_izin' => $pathFileIzin, // Pastikan ini nama kolom di database Anda
                'status_pengajuan' => 'Diajukan',     // Memberi status default
                'tgl_upload'       => now(),
                'nip_user_ssdm'    => Auth::user()->nip,
                'nip_user_sdm'    => Auth::user()->nip,
                'nip_user_gm'    => Auth::user()->nip,
            ]
        ));

        return redirect()->route('cuti.index')
                         ->with('success', 'Pengajuan Cuti berhasil dibuat.');
    }

    public function updateStatus(Request $request, Cuti $cuti)
    {
        $user = auth()->user();
        $userJabatan = $user->jabatanTerbaru->jabatan->nama_jabatan;

        $statusField = ($userJabatan == 'General Manager') ? 'status_gm' : 'status_sdm' : 'status_ssdm';
        $cuti->{$statusField} = $request->input('status');

        if ($request->input('status') == 'Disetujui') {
            if ($userJabatan == 'General Manager') {
                $cuti->nip_user_gm = $user->nip;
                $cuti->tgl_persetujuan_gm = now();
            } elseif ($userJabatan == 'Senior Analis Keuangan, SDM & Umum') {
                $cuti->nip_user_sdm = $user->nip;
                $cuti->tgl_persetujuan_sdm = now();
            }
            } elseif ($userJabatan == 'Senior Divisi A') {
                $cuti->nip_user_sdm = $user->nip;
                $cuti->tgl_persetujuan_ssdm = now();
            }
        }

        if ($request->input('status') == 'Ditolak') {
            $cuti->reason = $request->input('reason');
        }

        $cuti->save();

        if ($cuti->status_sdm === 'Disetujui' && $cuti->status_gm === 'Disetujui') {
            // Generate nomor surat yang baru
            $cuti->no_surat = $this->generateNoSurat();
            $cuti->save(); // Penting: simpan nomor surat sebelum membuat PDF

            $filePath = $this->generateSuratPdf($cuti);

            if ($filePath) {
                $cuti->file_cuti = $filePath;
                $cuti->save();
            }

            return redirect()->route('cuti.index')
                ->with('success', 'Pengajuan SPPD berhasil disetujui, surat PDF telah dibuat!');
        }

        if ($request->input('status') == 'Disetujui') {
            return redirect()->route('cuti.index')
                ->with('success', 'Pengajuan SPPD berhasil disetujui!');
        } else {
            return redirect()->route('cuti.index')
                ->with('success', 'Pengajuan SPPD berhasil ditolak. Alasan telah dikirim.');
        }
    }

    public function show($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);
        return view('pages.cuti.show', compact('cuti'));
    }
}
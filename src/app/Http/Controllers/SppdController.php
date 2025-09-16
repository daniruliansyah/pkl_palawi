<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use Illuminate\Http\Request;

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
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'required|string',
            'lokasi_tujuan' => 'required|string|max:100',
        ]);

        $sppd = Sppd::create([
            'nip_user' => auth()->user()->nip,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'keterangan' => $request->keterangan,
            'lokasi_tujuan' => $request->lokasi_tujuan,
        ]);

        return redirect()->route('sppd.index')
                         ->with('success', 'Pengajuan SPPD berhasil dibuat.');
    }

    public function show($id)
{
    $sppd = Sppd::with('user')->findOrFail($id);
    return view('sppd.show', compact('sppd'));
}

}

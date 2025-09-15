<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kalender;

class KalenderController extends Controller
{
    public function index() {
        $daftar_event = Kalender::all();
        return view('kalender.index', compact('daftar_event'));
    }

    // Method baru untuk halaman create
    public function create() {
        return view('pages.kalender.create');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_event' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'warna' => 'required|in:Danger,Success,Primary,Warning',
        ]);

        Kalender::create($request->all());

        return redirect()->route('kalender.index')->with('sukses', 'Event berhasil ditambahkan!');
    }
}

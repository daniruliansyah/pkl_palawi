<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{

    // Panggilan API untuk mendapatkan data notifikasi
  public function index()
{
    // Dapatkan notifikasi untuk user yang sedang login (user_id = ID user saat ini)
    $notifikasi = Notifikasi::where('user_id', auth()->id()) // <-- Gunakan Notifikasi::where atau relasi user()
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    // Mapping data agar sesuai dengan nama properti yang diharapkan di front-end
    $formattedNotifikasi = $notifikasi->map(function ($item) {
        // Konversi created_at ke format waktu yang ramah pengguna
        $timeAgo = $item->created_at->diffForHumans();

        return [
            'id' => $item->id,
            'sender' => $item->nama_pengirim, // Sesuai dengan x-text="item.sender"
            'message' => $item->isi_pesan, // Sesuai dengan x-text="item.message"
            'type' => $item->jenis_surat, // Sesuai dengan x-text="item.type"
            'status' => $item->status_persetujuan, // Untuk logic class warna
            'user_image' => asset('storage/' . $item->foto_pengirim), // Sesuai dengan :src="item.user_image"
            'time' => $timeAgo, // Sesuai dengan x-text="item.time"
        ];
    });

    // Mengembalikan response JSON
    return response()->json([
        'success' => true,
        // Kunci 'notifications' HARUS sesuai dengan 'notifications: []' di Alpine.js
        'notifications' => $formattedNotifikasi,
        'unReadCount' => $notifikasi->where('sudah_dibaca', false)->count(),
    ]);
}

    public function tandaiSudahDibaca(Request $request)
    {
        // ... (Logika update yang sudah benar) ...
        auth()->user()->notifikasi()
            ->where('sudah_dibaca', false)
            ->update(['sudah_dibaca' => true]);

        return response()->json(['success' => true]);
    }
}

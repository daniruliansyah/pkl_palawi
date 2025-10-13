<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\User;
use App\Models\MasterPotongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB Facade untuk transaction
use Illuminate\Support\Facades\Log; // Import Log Facade untuk error logging

class GajiController extends Controller
{
    /**
     * Menampilkan form untuk membuat data gaji baru.
     * Kita juga akan mengirimkan data karyawan dan master potongan ke view.
     */
    public function create()
    {
        $karyawan = User::all(); // Ambil semua data user/karyawan
        $masterPotongan = MasterPotongan::where('is_active', true)->get(); // Ambil semua potongan yang aktif

        // Tampilkan view 'gaji.create' dan kirimkan data karyawan & potongan
        return view('admin.gaji.create', compact('karyawan', 'masterPotongan'));
    }

    /**
     * Menyimpan data gaji yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
            'gaji_pokok' => 'required|numeric|min:0',
        ]);
        
        // Memulai database transaction
        // Ini memastikan semua proses berhasil atau tidak sama sekali (dibatalkan)
        DB::beginTransaction();

        try {
            $totalPotongan = 0;
            $potonganInputs = $request->input('potongan', []);

            // Hitung total semua potongan dari input form
            foreach ($potonganInputs as $jumlah) {
                if (!empty($jumlah) && is_numeric($jumlah)) {
                    $totalPotongan += $jumlah;
                }
            }
            
            // Buat record gaji utama
            $gaji = Gaji::create([
                'user_id' => $request->user_id,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'gaji_pokok' => $request->gaji_pokok,
                'total_potongan' => $totalPotongan,
                'gaji_diterima' => $request->gaji_pokok - $totalPotongan,
                'keterangan' => $request->keterangan,
            ]);

            // Simpan rincian potongan (hanya yang nilainya lebih dari 0)
            foreach ($potonganInputs as $master_id => $jumlah) {
                if (!empty($jumlah) && is_numeric($jumlah) && $jumlah > 0) {
                    $gaji->detailPotongan()->create([
                        'master_potongan_id' => $master_id,
                        'jumlah' => $jumlah,
                    ]);
                }
            }

            // Jika semua proses berhasil, commit transaction
            DB::commit();

            // Redirect dengan pesan sukses
            return redirect()->route('gaji.index')->with('success', 'Data gaji berhasil disimpan.');

        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua query (rollback)
            DB::rollBack();
            
            // Log error untuk debugging
            Log::error('Error saat menyimpan gaji: ' . $e->getMessage());

            // Redirect kembali dengan pesan error
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Logika untuk mencetak slip gaji (misalnya dalam format PDF).
     */
    public function cetakSlip(Gaji $gaji)
    {
        // Load relasi agar data user dan detail potongan ikut terambil
        $gaji->load('user', 'detailPotongan.masterPotongan');

        // Di sini Anda akan memanggil library PDF seperti DomPDF atau Snappy
        // Contoh dengan DomPDF:
        // $pdf = PDF::loadView('pdf.slip_gaji', compact('gaji'));
        // return $pdf->download('slip-gaji-'.$gaji->user->name.'-'.$gaji->bulan.'-'.$gaji->tahun.'.pdf');

        // Untuk sekarang, kita tampilkan datanya saja dalam format JSON untuk verifikasi
        return response()->json($gaji);
    }
}
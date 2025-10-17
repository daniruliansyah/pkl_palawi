<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\User;
use App\Models\MasterPotongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB Facade untuk transaction
use Illuminate\Support\Facades\Log; // Import Log Facade untuk error logging
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class GajiController extends Controller
{
    /**
     * Menampilkan form untuk membuat data gaji baru.
     * Kita juga akan mengirimkan data karyawan dan master potongan ke view.
     */
    public function indexForKaryawan(User $user)
    {
        $gajiHistory = $user->gaji()
                            ->orderBy('tahun', 'desc')
                            ->orderBy('bulan', 'desc')
                            ->paginate(10);

        // Mengirim data ke view 'gaji.index'
        return view('pages.gaji.index', [
            'user' => $user,
            'gajiHistory' => $gajiHistory
        ]);
    }

    public function destroy(Gaji $gaji)
    {
        try {
            // Hapus data gaji.
            // Detail potongan yang terkait akan ikut terhapus otomatis
            // karena Anda sudah menggunakan onDelete('cascade') di migrasi.
            $gaji->delete();

            // Redirect kembali ke halaman sebelumnya dengan pesan sukses
            return redirect()->back()->with('success', 'Data gaji berhasil dihapus.');

        } catch (\Exception $e) {
            // Jika terjadi error, redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Gagal menghapus data gaji: ' . $e->getMessage());
        }
    }

    public function create(User $user) // <-- 1. Terima variabel $user dari route
    {
        $masterPotongan = MasterPotongan::where('is_active', true)
                                    ->orderBy('nama_potongan')
                                    ->get();

        // 2. Kirim $user dan $masterPotongan ke view
        return view('pages.gaji.create', [
            'user' => $user, 
            'masterPotongan' => $masterPotongan
        ]);
    }

    /**
     * Menyimpan data gaji yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|digits:4',
            'gaji_pokok' => 'required|numeric|min:0',
            'potongan.*' => 'nullable|numeric|min:0', // Validasi semua input potongan
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Gunakan transaction untuk memastikan semua data tersimpan atau tidak sama sekali
        DB::beginTransaction();
        try {
            // 2. Proses Potongan dan Hitung Totalnya
            $totalPotongan = 0;
            $potonganInput = $request->input('potongan', []);
            $detailPotonganToSave = [];

            foreach ($potonganInput as $master_potongan_id => $jumlah) {
                // Hanya proses potongan yang nilainya diisi dan lebih dari 0
                if ($jumlah > 0) {
                    $totalPotongan += $jumlah;
                    $detailPotonganToSave[] = [
                        'master_potongan_id' => $master_potongan_id,
                        'jumlah' => $jumlah,
                    ];
                }
            }

            // 3. Hitung Gaji Diterima
            $gajiDiterima = $request->gaji_pokok - $totalPotongan;

            // 4. Simpan Data Utama ke Tabel `gaji`
            $gaji = Gaji::create([
                'user_id' => $request->user_id,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'gaji_pokok' => $request->gaji_pokok,
                'total_potongan' => $totalPotongan,
                'gaji_diterima' => $gajiDiterima,
                'keterangan' => $request->keterangan,
            ]);

            // 5. Simpan detail potongan ke tabel `detail_potongan`
            // Pastikan data $gaji berhasil dibuat sebelum lanjut
            if ($gaji) {
                foreach ($detailPotonganToSave as $detail) {
                    $gaji->detailPotongan()->create($detail);
                }
            }

            // Jika semua berhasil, commit transaksi
            DB::commit();

            // 6. Cek apakah user ingin "Simpan & Cetak"
            if ($request->has('simpan_cetak')) {
                // Arahkan ke route untuk mencetak slip gaji dengan ID gaji yang baru dibuat
                return redirect()->route('gaji.cetak', $gaji->id)
                                 ->with('success', 'Data gaji berhasil disimpan.');
            }

            // Jika hanya simpan, kembali ke halaman daftar gaji
            return redirect()->route('gaji.indexForKaryawan', $request->user_id)
                             ->with('success', 'Data gaji berhasil disimpan.');

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua proses (rollback)
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Logika untuk mencetak slip gaji (misalnya dalam format PDF).
     */
    public function cetakSlip(Gaji $gaji)
    {
        // Load relasi yang dibutuhkan
        $gaji->load('user', 'detailPotongan.masterPotongan');

        // Generate PDF dari view 'pdf.slip_gaji' dengan data $gaji
        $pdf = PDF::loadView('pages.gaji.slip', compact('gaji'));

        // Buat nama file yang dinamis
        $fileName = 'slip-gaji-' . $gaji->user->nama_lengkap . '-' . $gaji->bulan . '-' . $gaji->tahun . '.pdf';

        // Tampilkan PDF di browser (stream) atau langsung download (download)
        return $pdf->stream($fileName);
    }
}
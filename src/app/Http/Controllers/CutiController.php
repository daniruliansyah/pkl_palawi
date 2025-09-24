<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
// use Riskihajar\Terbilang\Facades\Terbilang; // Uncomment jika Anda menginstall paket Terbilang

class CutiController extends Controller
{
    /**
     * Menampilkan daftar cuti berdasarkan hak akses user yang login.
     * Method ini "pintar" dan akan mengarahkan ke view yang benar.
     */
    public function index()
    {
        $user = Auth::user();
        $jabatanInfo = $user->jabatanTerbaru()->with('jabatan')->first();

        // Jika user tidak punya data jabatan, anggap sebagai karyawan biasa
        if (!$jabatanInfo || !$jabatanInfo->jabatan) {
            $cutis = Cuti::where('nip_user', $user->nip)->with('user', 'ssdm')->latest()->get();
            return view('pages.cuti.index-karyawan', compact('cutis'));
        }

        $namaJabatan = $jabatanInfo->jabatan->nama_jabatan;

        // Logika untuk General Manager
        if (str_contains($namaJabatan, 'General Manager')) {
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('status_sdm', 'Disetujui')
                ->where('status_gm', 'Menunggu Persetujuan')
                ->latest()->get();
            // Ambil riwayat cuti yang pernah di-approve/reject oleh GM ini
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('nip_user_gm', $user->nip)
                ->latest()->get();
            return view('pages.cuti.index-gm', compact('cutisForApproval', 'cutisHistory'));
        }
        // Logika untuk SDM
        elseif (str_contains($namaJabatan, 'Senior Analis Keuangan, SDM & Umum')) {
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('status_ssdm', 'Disetujui')
                ->where('status_sdm', 'Menunggu Persetujuan')
                ->latest()->get();
            // Ambil riwayat cuti yang pernah di-approve/reject oleh SDM ini
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('nip_user_sdm', $user->nip)
                ->latest()->get();
            return view('pages.cuti.index-sdm', compact('cutisForApproval', 'cutisHistory'));
        }
        // Logika untuk Senior Divisi (berdasarkan nama jabatan)
        elseif (str_contains($namaJabatan, 'Senior') || str_contains($namaJabatan, 'Manager')) {
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('status_ssdm', 'Menunggu Persetujuan')
                ->where('nip_user_ssdm', $user->nip)
                ->latest()->get();
            // Ambil riwayat cuti yang pernah di-approve/reject oleh Senior ini
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('nip_user_ssdm', $user->nip)
                ->where('status_ssdm', '!=', 'Menunggu Persetujuan')
                ->latest()->get();
            return view('pages.cuti.index-ssdm', compact('cutisForApproval', 'cutisHistory'));
        }
        // Logika untuk Karyawan Biasa
        else {
            $cutis = Cuti::where('nip_user', $user->nip)->with('user', 'ssdm', 'sdm', 'gm')->latest()->get();
            return view('pages.cuti.index-karyawan', compact('cutis'));
        }
    }

    // ... (method create, store, updateStatus, dll. tetap sama)
    /**
     * Menampilkan form untuk membuat pengajuan cuti baru.
     */
    public function create()
    {
        // Query diubah untuk mencari atasan berdasarkan nama jabatan
        $seniors = User::whereHas('jabatanTerbaru.jabatan', function($query) {
            $keywords = ['Senior']; // HANYA mencari jabatan yang mengandung kata 'Senior'
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('nama_jabatan', 'LIKE', '%' . $keyword . '%');
                }
            });
        })->get();
        return view('pages.cuti.create', compact('seniors'));
    }

    /**
     * Menyimpan pengajuan cuti baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jenis_izin'    => 'required|string',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'jumlah_hari'   => 'required|integer|min:1',
            'keterangan'    => 'required|string',
            'file_izin'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'nip_user_ssdm' => 'required|string|exists:users,nip',
        ]);

        $pathFileIzin = null;
        if ($request->hasFile('file_izin')) {
            $pathFileIzin = $request->file('file_izin')->store('file_izin', 'public');
        }

        $tahun = date('Y');
        $bulan = date('m');
        $lastCuti = Cuti::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->latest('id')->first();
        $nomorUrut = $lastCuti ? (int)substr($lastCuti->no_surat, -3) + 1 : 1;
        $noSurat = sprintf("CUTI/%s/%s/%03d", $tahun, $bulan, $nomorUrut);

        Cuti::create(array_merge($validatedData, [
            'nip_user'        => Auth::user()->nip,
            'no_surat'        => $noSurat,
            'file_izin'       => $pathFileIzin,
            'tgl_upload'      => now(),
            'status_ssdm'     => 'Menunggu Persetujuan',
            'status_sdm'      => 'Menunggu',
            'status_gm'       => 'Menunggu',
            'nip_user_sdm'    => null,
            'nip_user_gm'     => null,
        ]));

        return redirect()->route('cuti.index')->with('success', 'Pengajuan Cuti berhasil dibuat.');
    }

    /**
     * Mengupdate status pengajuan cuti (approval/reject).
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak',
        ]);

        $user = auth()->user();
        $jabatanInfo = $user->jabatanTerbaru()->with('jabatan')->first();
        $status = $request->input('status');

        if (!$jabatanInfo || !$jabatanInfo->jabatan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang.');
        }

        $namaJabatan = $jabatanInfo->jabatan->nama_jabatan;

        DB::beginTransaction();
        try {
            if (str_contains($namaJabatan, 'General Manager')) {
                if ($cuti->status_gm !== 'Menunggu Persetujuan') {
                    return redirect()->back()->with('error', 'Pengajuan ini tidak lagi menunggu persetujuan Anda.');
                }
                $cuti->status_gm = $status;
                $cuti->nip_user_gm = $user->nip;
                $cuti->tgl_persetujuan_gm = now();
                if ($status == 'Ditolak') $cuti->alasan_penolakan = $request->input('alasan_penolakan');

            } elseif (str_contains($namaJabatan, 'Senior Analis Keuangan, SDM & Umum')) {
                if ($cuti->status_sdm !== 'Menunggu Persetujuan') {
                    return redirect()->back()->with('error', 'Pengajuan ini tidak lagi menunggu persetujuan Anda.');
                }
                $cuti->status_sdm = $status;
                $cuti->nip_user_sdm = $user->nip;
                $cuti->tgl_persetujuan_sdm = now();
                if ($status == 'Disetujui') {
                    $cuti->status_gm = 'Menunggu Persetujuan';
                } else {
                    $cuti->alasan_penolakan = $request->input('alasan_penolakan');
                    $cuti->status_gm = 'Ditolak'; // Jika SDM menolak, GM otomatis ditolak
                }

            } elseif (str_contains($namaJabatan, 'Senior') || str_contains($namaJabatan, 'Manager')) {
                 if ($cuti->status_ssdm !== 'Menunggu Persetujuan') {
                    return redirect()->back()->with('error', 'Pengajuan ini tidak lagi menunggu persetujuan Anda.');
                }
                $cuti->status_ssdm = $status;
                $cuti->tgl_persetujuan_ssdm = now();
                if ($status == 'Disetujui') {
                    $cuti->status_sdm = 'Menunggu Persetujuan';
                } else {
                    $cuti->alasan_penolakan = $request->input('alasan_penolakan');
                    $cuti->status_sdm = 'Ditolak'; // Jika SSDM menolak, SDM & GM otomatis ditolak
                    $cuti->status_gm = 'Ditolak';
                }
            } else {
                 return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk aksi ini.');
            }

            $cuti->save();

            if ($cuti->status_gm === 'Disetujui') {
                $this->generatePdfAndSave($cuti);
            }

            DB::commit();
            return redirect()->route('cuti.index')->with('success', 'Status pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Membatalkan pengajuan cuti oleh karyawan.
     */
    public function cancel(Cuti $cuti)
    {
        // Otorisasi: Hanya user yang membuat yang bisa membatalkan
        if (Auth::user()->nip !== $cuti->nip_user) {
            return redirect()->route('cuti.index')->with('error', 'Anda tidak berhak membatalkan pengajuan ini.');
        }

        // Hanya bisa dibatalkan jika masih menunggu persetujuan level 1
        if ($cuti->status_ssdm !== 'Menunggu Persetujuan') {
            return redirect()->route('cuti.index')->with('error', 'Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }

        $cuti->delete();

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }

    /**
     * Private function untuk membuat dan menyimpan file PDF surat cuti.
     */
    private function generatePdfAndSave(Cuti $cuti)
    {
        $cuti->load('user.jabatanTerbaru.jabatan', 'ssdm.jabatanTerbaru.jabatan', 'sdm.jabatanTerbaru.jabatan', 'gm.jabatanTerbaru.jabatan');
        $verificationUrl = route('cuti.show', $cuti->id);
        $qrCode = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate($verificationUrl));
        $data = ['cuti' => $cuti, 'qrCode' => $qrCode];
        $pdf = Pdf::loadView('pages.cuti.surat_cuti_pdf', $data);
        $fileName = 'surat-cuti-' . $cuti->user->nip . '-' . time() . '.pdf';
        $filePath = 'surat_cuti/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());
        $cuti->file_surat = $filePath;
        $cuti->save();
    }

    /**
     * Menampilkan detail dari satu pengajuan cuti.
     */
    public function show($id)
    {
        $cuti = Cuti::with('user', 'ssdm', 'sdm', 'gm')->findOrFail($id);
        return view('pages.cuti.show', compact('cuti'));
    }
}


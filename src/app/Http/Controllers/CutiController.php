<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CutiController extends Controller
{
    /**
     * Selalu menampilkan riwayat cuti PRIBADI milik user yang login.
     */
    public function index()
    {
        $user = Auth::user();
        $sisaCuti = $user->jatah_cuti;
        $cutis = Cuti::where('nip_user', $user->nip)
                    ->with('user', 'ssdm', 'sdm', 'gm')
                    ->latest()->get();

        return view('pages.cuti.index-karyawan', compact('cutis', 'sisaCuti'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan cuti baru.
     */
    public function create()
    {
        $sisaCuti = Auth::user()->jatah_cuti;
        $seniors = User::whereHas('jabatanTerbaru.jabatan', function($query) {
            $query->where('nama_jabatan', 'LIKE', '%Senior%');
        })->get();
        return view('pages.cuti.create', compact('seniors', 'sisaCuti'));
    }

    /**
     * Menyimpan pengajuan cuti baru dengan alur bertingkat.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'jenis_izin'    => 'required|string|in:Cuti Tahunan,Cuti Besar,Cuti Sakit,Cuti Bersalin,Cuti Alasan Penting',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'jumlah_hari'   => 'required|integer|min:1',
            'keterangan'    => 'required|string',
            'file_izin'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048|required_if:jenis_izin,Cuti Sakit',
        ];
        if ($user->isKaryawanBiasa()) {
            $rules['nip_user_ssdm'] = 'required|string|exists:users,nip';
        }

        $validatedData = $request->validate($rules, [
            'file_izin.required_if' => 'File izin wajib diunggah untuk Cuti Sakit.',
            'nip_user_ssdm.required' => 'Anda harus memilih atasan langsung.',
        ]);

        if ($this->isCutiMengurangiJatah($validatedData['jenis_izin'], $request->hasFile('file_izin'))) {
             if ($user->jatah_cuti < (int)$validatedData['jumlah_hari']) {
                return redirect()->back()->withErrors(['jumlah_hari' => 'Sisa jatah cuti Anda ('.$user->jatah_cuti.' hari) tidak mencukupi.'])->withInput();
            }
        }
        
        $statusSsdm = 'Menunggu Persetujuan'; $statusSdm = 'Menunggu'; $statusGm = 'Menunggu';
        $nipUserSsdm = $request->input('nip_user_ssdm'); $nipUserSdm = null; $nipUserGm = null;

        $sdmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))->first();
        $gmUser = User::whereHas('jabatanTerbaru.jabatan', fn($q) => $q->where('nama_jabatan', 'LIKE', '%General Manager%'))->first();

        if ($user->isGm()) {
            $statusSsdm = 'Disetujui'; $statusSdm = 'Menunggu Persetujuan'; $nipUserSdm = $sdmUser?->nip;
        } elseif ($user->isSdm()) {
            $statusSsdm = 'Disetujui'; $statusSdm = 'Disetujui'; $statusGm = 'Menunggu Persetujuan'; $nipUserGm = $gmUser?->nip;
        } elseif ($user->isSenior()) {
            $statusSsdm = 'Disetujui'; $statusSdm = 'Menunggu Persetujuan'; $nipUserSdm = $sdmUser?->nip;
        }

        $pathFileIzin = $request->hasFile('file_izin') ? $request->file('file_izin')->store('file_izin', 'public') : null;

        Cuti::create(array_merge($validatedData, [
            'nip_user'        => $user->nip,
            'no_surat'        => $this->generateNomorSurat(),
            'file_izin'       => $pathFileIzin,
            'tgl_upload'      => now(),
            'status_ssdm'     => $statusSsdm,
            'status_sdm'      => $statusSdm,
            'status_gm'       => $statusGm,
            'nip_user_ssdm'   => $nipUserSsdm,
            'nip_user_sdm'    => $nipUserSdm,
            'nip_user_gm'     => $nipUserGm,
        ]));

        return redirect()->route('cuti.index')->with('success', 'Pengajuan Cuti berhasil dibuat.');
    }
    
    /**
     * Method publik untuk memfinalisasi cuti setelah disetujui.
     * Dipanggil oleh ApprovalController.
     */
    public function finalizeCuti(Cuti $cuti)
    {
        $karyawan = $cuti->user;
        if ($this->isCutiMengurangiJatah($cuti->jenis_izin, !is_null($cuti->file_izin))) {
            $karyawan->decrement('jatah_cuti', $cuti->jumlah_hari);
        }
        $this->generatePdfAndSave($cuti);
    }
    
    public function cancel(Cuti $cuti)
    {
        if (Auth::user()->nip !== $cuti->nip_user) {
            return redirect()->route('cuti.index')->with('error', 'Anda tidak berhak membatalkan pengajuan ini.');
        }
        if ($cuti->status_ssdm !== 'Menunggu Persetujuan') {
            return redirect()->route('cuti.index')->with('error', 'Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }
        $cuti->delete();
        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }
    
    private function isCutiMengurangiJatah(string $jenisIzin, bool $adaFile): bool
    {
        $cutiKhusus = ['Cuti Sakit', 'Cuti Bersalin'];
        if (in_array($jenisIzin, $cutiKhusus) && $adaFile) {
            return false;
        }
        return true;
    }

    private function generateNomorSurat(): string
    {
        $tahun = date('Y');
        $lastCutiThisYear = Cuti::whereYear('created_at', $tahun)->orderBy('id', 'desc')->first();
        $nomorUrut = $lastCutiThisYear ? ((int)explode('/', $lastCutiThisYear->no_surat)[0] + 1) : 1;
        return sprintf("%03d/014.1/SDM/ABWWT/%s", $nomorUrut, $tahun);
    }

    private function generatePdfAndSave(Cuti $cuti)
    {
        $tahunBerjalan = Carbon::parse($cuti->tgl_mulai)->year;
        $riwayatCuti = Cuti::where('nip_user', $cuti->nip_user)->where('status_gm', 'Disetujui')->whereYear('tgl_mulai', $tahunBerjalan)->select('jenis_izin', DB::raw('SUM(jumlah_hari) as total_hari'))->groupBy('jenis_izin')->pluck('total_hari', 'jenis_izin');
        $data = [
            'cuti' => $cuti->load('user.jabatanTerbaru.jabatan', 'ssdm.jabatanTerbaru.jabatan', 'sdm.jabatanTerbaru.jabatan', 'gm.jabatanTerbaru.jabatan'),
            'qrCode' => base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate(route('cuti.show', $cuti->id))),
            'riwayatCuti' => $riwayatCuti,
        ];
        $pdf = Pdf::loadView('pages.cuti.surat_cuti_pdf', $data);
        $fileName = 'surat-cuti-' . $cuti->user->nip . '-' . time() . '.pdf';
        $filePath = 'surat_cuti/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());
        $cuti->file_surat = $filePath;
        $cuti->save();
    }
    
    public function show($id)
    {
        $cuti = Cuti::with('user', 'ssdm', 'sdm', 'gm')->findOrFail($id);
        return view('pages.cuti.show', compact('cuti'));
    }
}


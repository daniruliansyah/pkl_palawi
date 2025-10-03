<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
use Carbon\Carbon;

class CutiController extends Controller
{
    /**
     * Selalu menampilkan riwayat cuti PRIBADI milik user yang login.
     */
=======
use App\Notifications\StatusSuratDiperbarui;

// Import chillerlan/php-qrcode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CutiController extends Controller
{
>>>>>>> Stashed changes
=======
use App\Notifications\StatusSuratDiperbarui;

// Import chillerlan/php-qrcode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CutiController extends Controller
{
>>>>>>> Stashed changes
=======
use App\Notifications\StatusSuratDiperbarui;

// Import chillerlan/php-qrcode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CutiController extends Controller
{
>>>>>>> Stashed changes
=======
use App\Notifications\StatusSuratDiperbarui;

// Import chillerlan/php-qrcode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CutiController extends Controller
{
>>>>>>> Stashed changes
=======
use App\Notifications\StatusSuratDiperbarui;

// Import chillerlan/php-qrcode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CutiController extends Controller
{
>>>>>>> Stashed changes
=======
use App\Notifications\StatusSuratDiperbarui;

// Import chillerlan/php-qrcode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CutiController extends Controller
{
>>>>>>> Stashed changes
    public function index()
    {
        $user = Auth::user();
        $sisaCuti = $user->jatah_cuti;
        $cutis = Cuti::where('nip_user', $user->nip)
                    ->with('user', 'ssdm', 'sdm', 'gm')
                    ->latest()->get();

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
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
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
        if (!$jabatanInfo || !$jabatanInfo->jabatan) {
            $cutis = Cuti::where('nip_user', $user->nip)->with('user', 'ssdm')->latest()->get();
            return view('pages.cuti.index-karyawan', compact('cutis'));
        }

        $namaJabatan = $jabatanInfo->jabatan->nama_jabatan;

        if (str_contains($namaJabatan, 'General Manager')) {
            $cutisForApproval = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('status_sdm', 'Disetujui')
                ->where('status_gm', 'Menunggu Persetujuan')
                ->latest()->get();
            $cutisHistory = Cuti::where('nip_user_gm', $user->nip)->latest()->get();
            return view('pages.cuti.index-gm', compact('cutisForApproval', 'cutisHistory'));
        }
        elseif (str_contains($namaJabatan, 'Senior Analis Keuangan, SDM & Umum')) {
            $cutisForApproval = Cuti::where('status_ssdm', 'Disetujui')
                ->where('status_sdm', 'Menunggu Persetujuan')
                ->latest()->get();
            $cutisHistory = Cuti::where('nip_user_sdm', $user->nip)->latest()->get();
            return view('pages.cuti.index-sdm', compact('cutisForApproval', 'cutisHistory'));
        }
        elseif (str_contains($namaJabatan, 'Senior') || str_contains($namaJabatan, 'Manager')) {
            $cutisForApproval = Cuti::where('status_ssdm', 'Menunggu Persetujuan')
                ->where('nip_user_ssdm', $user->nip)
                ->latest()->get();
            $cutisHistory = Cuti::where('nip_user_ssdm', $user->nip)
                ->where('status_ssdm', '!=', 'Menunggu Persetujuan')
                ->latest()->get();
            return view('pages.cuti.index-ssdm', compact('cutisForApproval', 'cutisHistory'));
        }
        else {
            $cutis = Cuti::where('nip_user', $user->nip)->with('user','ssdm','sdm','gm')->latest()->get();
            return view('pages.cuti.index-karyawan', compact('cutis'));
        }
    }

    public function create()
    {
        $seniors = User::whereHas('jabatanTerbaru.jabatan', function($q) {
            $q->where('nama_jabatan', 'LIKE', '%Senior%');
        })->get();

        return view('pages.cuti.create', compact('seniors'));
    }

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
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
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
        $tahun = date('Y'); $bulan = date('m');
        $lastCuti = Cuti::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->latest('id')->first();
        $nomorUrut = $lastCuti ? (int)substr($lastCuti->no_surat, -3) + 1 : 1;
        $noSurat = sprintf("CUTI/%s/%s/%03d", $tahun, $bulan, $nomorUrut);

        $cuti = Cuti::create(array_merge($validatedData, [
            'nip_user'     => Auth::user()->nip,
            'no_surat'     => $noSurat,
            'file_izin'    => $pathFileIzin,
            'tgl_upload'   => now(),
            'status_ssdm'  => 'Menunggu Persetujuan',
            'status_sdm'   => 'Menunggu',
            'status_gm'    => 'Menunggu',
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
        ]));

        // Notif ke SSDM
        try {
            $atasan = User::where('nip',$cuti->nip_user_ssdm)->first();
            if ($atasan) {
                $atasan->notify(new StatusSuratDiperbarui(
                    aktor: auth()->user(),
                    jenisSurat: 'Cuti',
                    statusBaru: 'Menunggu Persetujuan',
                    keterangan: 'Terdapat pengajuan cuti baru yang menunggu persetujuan Anda.',
                    url: route('cuti.show', $cuti->id)
                ));
            }
        } catch (\Exception $e) {
            Log::error("Notif gagal (Store Cuti): ".$e->getMessage());
        }
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream

        return redirect()->route('cuti.index')->with('success','Pengajuan cuti berhasil dibuat.');
    }
<<<<<<< Updated upstream
    
    /**
     * Method publik untuk memfinalisasi cuti setelah disetujui.
     * Dipanggil oleh ApprovalController.
     */
    public function finalizeCuti(Cuti $cuti)
    {
        $karyawan = $cuti->user;
        if ($this->isCutiMengurangiJatah($cuti->jenis_izin, !is_null($cuti->file_izin))) {
            $karyawan->decrement('jatah_cuti', $cuti->jumlah_hari);
=======

=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes

        return redirect()->route('cuti.index')->with('success','Pengajuan cuti berhasil dibuat.');
    }

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak',
        ]);

        $user = auth()->user();
        $jabatanInfo = $user->jabatanTerbaru()->with('jabatan')->first();
        if (!$jabatanInfo) return back()->with('error','Tidak ada jabatan.');

        $namaJabatan = $jabatanInfo->jabatan->nama_jabatan;
        $status = $request->status;

        $pembuatCuti = User::where('nip',$cuti->nip_user)->first();
        $penerimaNotifikasi = null;
        $currentStatus = '';
        $keteranganNotif = '';
        $urlDetail = route('cuti.show',$cuti->id);

        DB::beginTransaction();
        try {
            if (str_contains($namaJabatan,'General Manager')) {
                if ($cuti->status_gm !== 'Menunggu Persetujuan')
                    return back()->with('error','Bukan antrian Anda.');
                $cuti->status_gm = $status;
                $cuti->nip_user_gm = $user->nip;
                $cuti->tgl_persetujuan_gm = now();
                if ($status=='Ditolak') {
                    $cuti->alasan_penolakan = $request->alasan_penolakan;
                    $currentStatus='Ditolak';
                    $keteranganNotif="Cuti Anda ditolak GM. Alasan: ".$cuti->alasan_penolakan;
                } else {
                    $currentStatus='Disetujui Penuh';
                    $keteranganNotif="Cuti Anda sudah disetujui penuh.";
                    $urlDetail = route('cuti.download',$cuti->id);
                }
            }
            elseif (str_contains($namaJabatan,'SDM')) {
                if ($cuti->status_sdm !== 'Menunggu Persetujuan')
                    return back()->with('error','Bukan antrian Anda.');
                $cuti->status_sdm = $status;
                $cuti->nip_user_sdm = $user->nip;
                $cuti->tgl_persetujuan_sdm = now();
                if ($status=='Disetujui') {
                    $cuti->status_gm = 'Menunggu Persetujuan';
                    $penerimaNotifikasi = User::whereHas('jabatanTerbaru.jabatan', fn($q)=>$q->where('nama_jabatan','LIKE','%General Manager%'))->first();
                    $currentStatus='Menunggu Persetujuan GM';
                    $keteranganNotif="Disetujui SDM, diteruskan ke GM.";
                } else {
                    $cuti->alasan_penolakan=$request->alasan_penolakan;
                    $cuti->status_gm='Ditolak';
                    $currentStatus='Ditolak';
                    $keteranganNotif="Cuti Anda ditolak SDM. Alasan: ".$cuti->alasan_penolakan;
                }
            }
            elseif (str_contains($namaJabatan,'Senior') || str_contains($namaJabatan,'Manager')) {
                if ($cuti->status_ssdm !== 'Menunggu Persetujuan')
                    return back()->with('error','Bukan antrian Anda.');
                $cuti->status_ssdm=$status;
                $cuti->tgl_persetujuan_ssdm=now();
                if ($status=='Disetujui') {
                    $cuti->status_sdm='Menunggu Persetujuan';
                    $penerimaNotifikasi = User::whereHas('jabatanTerbaru.jabatan', fn($q)=>$q->where('nama_jabatan','LIKE','%SDM%'))->first();
                    $currentStatus='Menunggu Persetujuan SDM';
                    $keteranganNotif="Disetujui SSDM, diteruskan ke SDM.";
                } else {
                    $cuti->alasan_penolakan=$request->alasan_penolakan;
                    $cuti->status_sdm='Ditolak'; $cuti->status_gm='Ditolak';
                    $currentStatus='Ditolak';
                    $keteranganNotif="Cuti Anda ditolak SSDM. Alasan: ".$cuti->alasan_penolakan;
                }
            } else return back()->with('error','Tidak berwenang.');

            $cuti->save();

            if ($pembuatCuti) {
                $pembuatCuti->notify(new StatusSuratDiperbarui(
                    aktor: $user,
                    jenisSurat:'Cuti',
                    statusBaru:$currentStatus,
                    keterangan:$keteranganNotif,
                    url:$urlDetail
                ));
            }

            if ($penerimaNotifikasi && $status=='Disetujui' && $currentStatus!=='Disetujui Penuh') {
                $penerimaNotifikasi->notify(new StatusSuratDiperbarui(
                    aktor: $user,
                    jenisSurat:'Cuti',
                    statusBaru:'Menunggu Persetujuan',
                    keterangan:'Ada cuti yang menunggu persetujuan Anda.',
                    url:route('cuti.show',$cuti->id)
                ));
            }

            // Generate PDF dan simpan path jika sudah disetujui GM
            if ($cuti->status_gm === 'Disetujui') {
                $path = $this->generateSuratPdf($cuti);

                if ($path) {
                    $cuti->file_cuti = $path;
                    $cuti->save();

                    // Notifikasi terpisah untuk karyawan jika Disetujui Penuh
                    if ($pembuatCuti) {
                        $pembuatCuti->notify(new StatusSuratDiperbarui(
                            aktor: Auth::user(),
                            jenisSurat:'Cuti',
                            statusBaru:'Disetujui Penuh',
                            keterangan:'Surat Cuti Anda telah disetujui penuh dan siap diunduh.',
                            url:route('cuti.download',$cuti->id)
                        ));
                    }
                }
            }

            DB::commit();
            return redirect()->route('cuti.index')->with('success','Status diperbarui.');
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error("Update Cuti Error: ".$e->getMessage());
            return back()->with('error',$e->getMessage());
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
        }
        $this->generatePdfAndSave($cuti);
    }
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
    
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
=======

    protected function generateQrCodeUrl(Cuti $cuti)
    {
        return route('cuti.verifikasi', ['id' => $cuti->id]);
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
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
=======

    protected function generateQrCodeUrl(Cuti $cuti)
    {
        return route('cuti.verifikasi', ['id' => $cuti->id]);
    }

    protected function generateSuratPdf(Cuti $cuti)
    {
        try {
            // Gunakan nama file yang unik dan aman
            $fileName = \Illuminate\Support\Str::slug($cuti->no_surat) . "_{$cuti->id}.pdf";
            $pathFileCuti = 'file_cuti/' . $fileName; // Path di dalam storage/app/public

            $qrCodeUrl = $this->generateQrCodeUrl($cuti);

            // Logic QR Code menggunakan chillerlan
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl); // Data URI Base64

            // Ambil data user yang dibutuhkan untuk view surat
            $karyawan = $cuti->user;
            $gm = $cuti->gm;

            $pdf = Pdf::loadView('pages.cuti.surat', compact('cuti', 'qrCodeBase64', 'karyawan', 'gm'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            // Simpan file ke storage/app/public/file_cuti/
            \Illuminate\Support\Facades\Storage::disk('public')->put($pathFileCuti, $pdf->output());

            return $pathFileCuti; // Return path relatif untuk disimpan di DB
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [Cuti ID {$cuti->id}]: " . $e->getMessage());
            return null;
        }
    }

    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->file_cuti && Storage::disk('public')->exists($cuti->file_cuti)) {
            $filePath = storage_path('app/public/' . $cuti->file_cuti);
            $safeName = str_replace('/', '-', $cuti->no_surat);
            return response()->download($filePath, "Surat-Cuti-{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan.');
    }

    public function verifikasi($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->find($id);

        if (!$cuti) {
            // Ganti 'pages.cuti.notfound' dengan view not found Anda
            return view('pages.cuti.notfound', ['message' => 'Surat Cuti tidak ditemukan.']);
        }
        // Ganti 'pages.cuti.verifikasi_info' dengan view info verifikasi Anda
        return view('pages.cuti.verifikasi_info', compact('cuti'));
>>>>>>> Stashed changes
    }
    
=======
    protected function generateSuratPdf(Cuti $cuti)
    {
        try {
            // Gunakan nama file yang unik dan aman
            $fileName = \Illuminate\Support\Str::slug($cuti->no_surat) . "_{$cuti->id}.pdf";
            $pathFileCuti = 'file_cuti/' . $fileName; // Path di dalam storage/app/public

<<<<<<< Updated upstream
            $qrCodeUrl = $this->generateQrCodeUrl($cuti);

            // Logic QR Code menggunakan chillerlan
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl); // Data URI Base64

            // Ambil data user yang dibutuhkan untuk view surat
            $karyawan = $cuti->user;
            $gm = $cuti->gm;

            $pdf = Pdf::loadView('pages.cuti.surat', compact('cuti', 'qrCodeBase64', 'karyawan', 'gm'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            // Simpan file ke storage/app/public/file_cuti/
            \Illuminate\Support\Facades\Storage::disk('public')->put($pathFileCuti, $pdf->output());

            return $pathFileCuti; // Return path relatif untuk disimpan di DB
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [Cuti ID {$cuti->id}]: " . $e->getMessage());
            return null;
        }
    }

    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->file_cuti && Storage::disk('public')->exists($cuti->file_cuti)) {
            $filePath = storage_path('app/public/' . $cuti->file_cuti);
            $safeName = str_replace('/', '-', $cuti->no_surat);
            return response()->download($filePath, "Surat-Cuti-{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan.');
    }

    public function verifikasi($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->find($id);

        if (!$cuti) {
            // Ganti 'pages.cuti.notfound' dengan view not found Anda
            return view('pages.cuti.notfound', ['message' => 'Surat Cuti tidak ditemukan.']);
        }
        // Ganti 'pages.cuti.verifikasi_info' dengan view info verifikasi Anda
        return view('pages.cuti.verifikasi_info', compact('cuti'));
    }

>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======

    protected function generateQrCodeUrl(Cuti $cuti)
    {
        return route('cuti.verifikasi', ['id' => $cuti->id]);
    }

    protected function generateSuratPdf(Cuti $cuti)
    {
        try {
            // Gunakan nama file yang unik dan aman
            $fileName = \Illuminate\Support\Str::slug($cuti->no_surat) . "_{$cuti->id}.pdf";
            $pathFileCuti = 'file_cuti/' . $fileName; // Path di dalam storage/app/public

            $qrCodeUrl = $this->generateQrCodeUrl($cuti);

            // Logic QR Code menggunakan chillerlan
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl); // Data URI Base64

            // Ambil data user yang dibutuhkan untuk view surat
            $karyawan = $cuti->user;
            $gm = $cuti->gm;

            $pdf = Pdf::loadView('pages.cuti.surat', compact('cuti', 'qrCodeBase64', 'karyawan', 'gm'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            // Simpan file ke storage/app/public/file_cuti/
            \Illuminate\Support\Facades\Storage::disk('public')->put($pathFileCuti, $pdf->output());

            return $pathFileCuti; // Return path relatif untuk disimpan di DB
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [Cuti ID {$cuti->id}]: " . $e->getMessage());
            return null;
        }
    }

    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->file_cuti && Storage::disk('public')->exists($cuti->file_cuti)) {
            $filePath = storage_path('app/public/' . $cuti->file_cuti);
            $safeName = str_replace('/', '-', $cuti->no_surat);
            return response()->download($filePath, "Surat-Cuti-{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan.');
    }

    public function verifikasi($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->find($id);

        if (!$cuti) {
            // Ganti 'pages.cuti.notfound' dengan view not found Anda
            return view('pages.cuti.notfound', ['message' => 'Surat Cuti tidak ditemukan.']);
        }
        // Ganti 'pages.cuti.verifikasi_info' dengan view info verifikasi Anda
        return view('pages.cuti.verifikasi_info', compact('cuti'));
    }

>>>>>>> Stashed changes
=======

    protected function generateQrCodeUrl(Cuti $cuti)
    {
        return route('cuti.verifikasi', ['id' => $cuti->id]);
    }

    protected function generateSuratPdf(Cuti $cuti)
    {
        try {
            // Gunakan nama file yang unik dan aman
            $fileName = \Illuminate\Support\Str::slug($cuti->no_surat) . "_{$cuti->id}.pdf";
            $pathFileCuti = 'file_cuti/' . $fileName; // Path di dalam storage/app/public

            $qrCodeUrl = $this->generateQrCodeUrl($cuti);

            // Logic QR Code menggunakan chillerlan
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl); // Data URI Base64

            // Ambil data user yang dibutuhkan untuk view surat
            $karyawan = $cuti->user;
            $gm = $cuti->gm;

            $pdf = Pdf::loadView('pages.cuti.surat', compact('cuti', 'qrCodeBase64', 'karyawan', 'gm'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            // Simpan file ke storage/app/public/file_cuti/
            \Illuminate\Support\Facades\Storage::disk('public')->put($pathFileCuti, $pdf->output());

            return $pathFileCuti; // Return path relatif untuk disimpan di DB
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [Cuti ID {$cuti->id}]: " . $e->getMessage());
            return null;
        }
    }

    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->file_cuti && Storage::disk('public')->exists($cuti->file_cuti)) {
            $filePath = storage_path('app/public/' . $cuti->file_cuti);
            $safeName = str_replace('/', '-', $cuti->no_surat);
            return response()->download($filePath, "Surat-Cuti-{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan.');
    }

    public function verifikasi($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->find($id);

        if (!$cuti) {
            // Ganti 'pages.cuti.notfound' dengan view not found Anda
            return view('pages.cuti.notfound', ['message' => 'Surat Cuti tidak ditemukan.']);
        }
        // Ganti 'pages.cuti.verifikasi_info' dengan view info verifikasi Anda
        return view('pages.cuti.verifikasi_info', compact('cuti'));
    }

>>>>>>> Stashed changes
=======

    protected function generateQrCodeUrl(Cuti $cuti)
    {
        return route('cuti.verifikasi', ['id' => $cuti->id]);
    }

    protected function generateSuratPdf(Cuti $cuti)
    {
        try {
            // Gunakan nama file yang unik dan aman
            $fileName = \Illuminate\Support\Str::slug($cuti->no_surat) . "_{$cuti->id}.pdf";
            $pathFileCuti = 'file_cuti/' . $fileName; // Path di dalam storage/app/public

            $qrCodeUrl = $this->generateQrCodeUrl($cuti);

            // Logic QR Code menggunakan chillerlan
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl); // Data URI Base64

            // Ambil data user yang dibutuhkan untuk view surat
            $karyawan = $cuti->user;
            $gm = $cuti->gm;

            $pdf = Pdf::loadView('pages.cuti.surat', compact('cuti', 'qrCodeBase64', 'karyawan', 'gm'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            // Simpan file ke storage/app/public/file_cuti/
            \Illuminate\Support\Facades\Storage::disk('public')->put($pathFileCuti, $pdf->output());

            return $pathFileCuti; // Return path relatif untuk disimpan di DB
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [Cuti ID {$cuti->id}]: " . $e->getMessage());
            return null;
        }
    }

    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->file_cuti && Storage::disk('public')->exists($cuti->file_cuti)) {
            $filePath = storage_path('app/public/' . $cuti->file_cuti);
            $safeName = str_replace('/', '-', $cuti->no_surat);
            return response()->download($filePath, "Surat-Cuti-{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan.');
    }

    public function verifikasi($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->find($id);

        if (!$cuti) {
            // Ganti 'pages.cuti.notfound' dengan view not found Anda
            return view('pages.cuti.notfound', ['message' => 'Surat Cuti tidak ditemukan.']);
        }
        // Ganti 'pages.cuti.verifikasi_info' dengan view info verifikasi Anda
        return view('pages.cuti.verifikasi_info', compact('cuti'));
    }

>>>>>>> Stashed changes
=======

    protected function generateQrCodeUrl(Cuti $cuti)
    {
        return route('cuti.verifikasi', ['id' => $cuti->id]);
    }

    protected function generateSuratPdf(Cuti $cuti)
    {
        try {
            // Gunakan nama file yang unik dan aman
            $fileName = \Illuminate\Support\Str::slug($cuti->no_surat) . "_{$cuti->id}.pdf";
            $pathFileCuti = 'file_cuti/' . $fileName; // Path di dalam storage/app/public

            $qrCodeUrl = $this->generateQrCodeUrl($cuti);

            // Logic QR Code menggunakan chillerlan
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl); // Data URI Base64

            // Ambil data user yang dibutuhkan untuk view surat
            $karyawan = $cuti->user;
            $gm = $cuti->gm;

            $pdf = Pdf::loadView('pages.cuti.surat', compact('cuti', 'qrCodeBase64', 'karyawan', 'gm'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            // Simpan file ke storage/app/public/file_cuti/
            \Illuminate\Support\Facades\Storage::disk('public')->put($pathFileCuti, $pdf->output());

            return $pathFileCuti; // Return path relatif untuk disimpan di DB
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [Cuti ID {$cuti->id}]: " . $e->getMessage());
            return null;
        }
    }

    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->file_cuti && Storage::disk('public')->exists($cuti->file_cuti)) {
            $filePath = storage_path('app/public/' . $cuti->file_cuti);
            $safeName = str_replace('/', '-', $cuti->no_surat);
            return response()->download($filePath, "Surat-Cuti-{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan.');
    }

    public function verifikasi($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->find($id);

        if (!$cuti) {
            // Ganti 'pages.cuti.notfound' dengan view not found Anda
            return view('pages.cuti.notfound', ['message' => 'Surat Cuti tidak ditemukan.']);
        }
        // Ganti 'pages.cuti.verifikasi_info' dengan view info verifikasi Anda
        return view('pages.cuti.verifikasi_info', compact('cuti'));
    }

>>>>>>> Stashed changes
    public function show($id)
    {
        $cuti = Cuti::with('user','ssdm','sdm','gm')->findOrFail($id);
        return view('pages.cuti.show',compact('cuti'));
    }
}

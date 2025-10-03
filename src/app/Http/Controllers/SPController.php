<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SP;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class SPController extends Controller
{
    public function index()
    {
        $sp = SP::with('user')->latest()->get();
        return view('pages.sp.index', compact('sp'));
    }

    public function create()
    {
        $jabatanTembusan = Jabatan::pluck('nama_jabatan')->toArray();
        return view('pages.sp.create', compact('jabatanTembusan'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip_user'      => 'required|exists:users,nip',
            'hal_surat'     => 'required|string|max:100',
            'jenis_sp'      => 'required|in:Pertama,Kedua,Terakhir',
            'isi_surat'     => 'required|string',
            'tembusan'      => 'nullable|array',
            'tgl_sp_terbit' => 'required|date',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'ket_peringatan'=> 'required|string|max:500',
            'file_bukti'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $karyawan = User::where('nip', $validatedData['nip_user'])
                        ->with('jabatanTerbaru.jabatan')
                        ->firstOrFail();

        $tahun = date('Y');
        $bulan = date('m');

        $lastSP = SP::whereYear('created_at', $tahun)->latest('id')->first();
        $nomorUrut = 1;
        if ($lastSP) {
            $parts = explode('/', $lastSP->no_surat);
            $lastNumber = end($parts);
            if (is_numeric($lastNumber)) $nomorUrut = (int)$lastNumber + 1;
        }
        $noSurat = sprintf("SP/%s/%s/%03d", $tahun, $bulan, $nomorUrut);

        $pathFileBukti = $request->hasFile('file_bukti')
            ? $request->file('file_bukti')->store('file_bukti', 'public')
            : null;

        try {
            DB::beginTransaction();

            $sp = SP::create([
                'nip_user'      => $validatedData['nip_user'],
                'no_surat'      => $noSurat,
                'hal_surat'     => $validatedData['hal_surat'],
                'jenis_sp'      => $validatedData['jenis_sp'],
                'isi_surat'     => $validatedData['isi_surat'],
                'tembusan'      => json_encode($validatedData['tembusan'] ?? []),
                'tgl_sp_terbit' => $validatedData['tgl_sp_terbit'],
                'tgl_mulai'     => $validatedData['tgl_mulai'],
                'tgl_selesai'   => $validatedData['tgl_selesai'],
                'ket_peringatan'=> $validatedData['ket_peringatan'],
                'file_bukti'    => $pathFileBukti,
            ]);

            $gm = User::whereHas('jabatanTerbaru.jabatan', fn($q) =>
                    $q->where('nama_jabatan', 'LIKE', '%General Manager%')
                )->first();

            $qrCodeUrl = route('sp.verifikasi', $sp->id);
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 3,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl);

            $pdf = Pdf::loadView('pages.sp.template-surat', compact('sp', 'karyawan', 'gm', 'qrCodeBase64'))
                      ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                      ->setPaper('A4', 'portrait');

            $pathFileSP = 'file_sp/' . Str::slug($sp->no_surat) . '.pdf';
            Storage::disk('public')->put($pathFileSP, $pdf->output());

            $sp->file_sp = $pathFileSP;
            $sp->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal membuat SP ID {$sp->id} atau PDF: " . $e->getMessage());
            if ($pathFileBukti && Storage::disk('public')->exists($pathFileBukti)) {
                Storage::disk('public')->delete($pathFileBukti);
            }
            return back()->with('error', 'Gagal membuat Surat Peringatan. ' . $e->getMessage())->withInput();
        }

        return redirect()->route('sp.index')->with('success', 'Surat Peringatan berhasil dibuat.');
    }


      protected function generateNoSurat()
    {
        $year  = date('Y');
        $month = $this->numberToRoman(date('n'));

        $lastSppd = SP::whereYear('created_at', $year)
            ->whereNotNull('no_surat')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastSppd) {
            $parts = explode('/', $lastSppd->no_surat);
            $lastNumber = isset($parts[0]) ? (int) $parts[0] : 0;
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return "{$newNumber}/D.1./PAL-ABWWT/{$year}";
    }


public function verifikasi($id)
{
    // Pastikan Anda menggunakan Model yang benar (SP)
    $sp = SP::with('user')->find($id);

    if (!$sp) {
        return view('pages.sp.notfound', ['message' => 'Surat Peringatan tidak ditemukan.']);
    }
    return view('pages.sp.info', compact('sp'));
}


    public function cariKaryawan(Request $request)
    {
        $search = $request->input('q');
        $users = User::where('nip', 'like', "%{$search}%")
                     ->orWhere('nama_lengkap', 'like', "%{$search}%")
                     ->limit(10)
                     ->get();

        $formattedUsers = $users->map(fn($user) => [
            'id'   => $user->nip,
            'text' => "{$user->nama_lengkap} ({$user->nip})",
        ]);

        return response()->json(['results' => $formattedUsers]);
    }

    public function download($id)
    {
        $sp = SP::findOrFail($id);
        if ($sp->file_sp && Storage::disk('public')->exists($sp->file_sp)) {
            $filePath = storage_path('app/public/' . $sp->file_sp);
            $downloadName = 'Surat-Peringatan-' . Str::slug($sp->no_surat) . '.pdf';
            return response()->download($filePath, $downloadName);
        }
        return redirect()->back()->with('error', 'File surat tidak ditemukan.');
    }

    public function downloadBukti($id)
    {
        $sp = SP::findOrFail($id);
        if ($sp->file_bukti && Storage::disk('public')->exists($sp->file_bukti)) {
            $filePath = storage_path('app/public/' . $sp->file_bukti);
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $downloadName = 'Bukti-Pelanggaran-' . Str::slug($sp->no_surat) . '.' . $extension;
            return response()->download($filePath, $downloadName);
        }
        return redirect()->back()->with('error', 'File bukti tidak ditemukan.');
    }

// Di file: App\Http\Controllers\SPController.php
protected function generateQrCodeUrl(SP $sp) // Menggunakan Model SP
{
    return route('sp.verifikasi', ['id' => $sp->id]);
}


}

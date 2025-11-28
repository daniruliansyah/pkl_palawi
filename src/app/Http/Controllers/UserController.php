<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Barryvdh\DomPDF\Facade\Pdf;


class UserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai dari input 'search' jika ada
        $search = $request->input('search');

        // Mulai query untuk model User
        $query = User::query();

        // Jika ada kata kunci pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where('nama_lengkap', 'like', '%' . $search . '%');
        }

        // Eksekusi query untuk mendapatkan data karyawan
        $karyawan = $query->get();

        return view('pages.karyawan.read-card', compact('karyawan'));
    }

    public function create()
    {
        $jabatan = Jabatan::all();
        return view('pages.karyawan.create', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nik' => 'required|string|max:20|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('images', 'public');
        }

        $karyawan = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'nip' => $request->nip,
            'nik' => $request->nik,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'tgl_lahir' => $request->tgl_lahir,
            'tempat_lahir' => $request->tempat_lahir,
            'agama' => $request->agama,
            'foto' => $fotoPath, // bisa null, nanti di view pakai default
            'status_perkawinan' => $request->status_perkawinan,
            'status_aktif' => $request->status_aktif,
            'npk_baru' => $request->npk_baru,
            'npwp' => $request->npwp,
            'join_date' => $request->join_date,
            'jatah_cuti' => $request->jatah_cuti ?? 12,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $karyawan = User::findOrFail($id);
        return view('pages.karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nik' => "required|string|max:20|unique:users,nik,{$id}",
            'email' => "required|email|unique:users,email,{$id}",
        ]);

        $data = $request->all();

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('images', 'public');

            // Hapus foto lama kalau bukan default
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            $data['foto'] = $fotoPath;
        }

        $karyawan->update($data);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diupdate');
    }

    public function destroy($id)
    {
        $karyawan = User::findOrFail($id);

        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }

        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus');
    }

    public function show($id)
    {
        $karyawan = User::with('jabatanTerbaru.jabatan', 'riwayatJabatans.jabatan')->findOrFail($id);
        return view('pages.karyawan.detail', compact('karyawan'));
    }

    public function jabatan($id){
        $karyawan = User::FindOrFail($id);
        $jabatan = Jabatan::all();

        return view('pages.karyawan.addjabatan', compact('karyawan', 'jabatan'));
    }

    // di dalam controller function Anda
    public function updatejabatan(Request $request, $id)
    {
        $karyawan = User::findOrFail($id);

        // 1. Validasi data
        $request->validate([
            'jabatan_id' => 'required|array',
            'jabatan_id.*' => 'required|exists:jabatan,id',
            'tgl_mulai' => 'required|array',
            'tgl_mulai.*' => 'required|date',
            'tgl_selesai' => 'nullable|array',
            'tgl_selesai.*' => 'nullable|date|after_or_equal:tgl_mulai.*',
            'area_bekerja' => 'required|array',
            'area_bekerja.*' => 'required|string',
            'link_berkas' => 'required|array',
            'link_berkas.*' => 'required|string',
            
            // --- TAMBAHAN ---
            'jenjang' => 'nullable|array', 
            'jenjang.*' => 'nullable|string|max:10', // Sesuaikan max length jika perlu
            // --- END TAMBAHAN ---
        ]);

        // 2. Ambil semua data
        $jabatan_ids = $request->jabatan_id;
        $mulais      = $request->tgl_mulai;
        $selesais    = $request->tgl_selesai;
        $areais      = $request->area_bekerja;
        $linkis      = $request->link_berkas;

        // --- TAMBAHAN ---
        $jenjangs = $request->jenjang; 
        // --- END TAMBAHAN ---

        // 3. Loop dan simpan data, tambahkan pengecekan nilai
        foreach ($jabatan_ids as $i => $jabatan_id) {
            // Hanya proses jika jabatan_id tidak kosong
            if (!empty($jabatan_id)) {
                $karyawan->riwayatJabatans()->create([
                    'id_jabatan'   => $jabatan_id,
                    'tgl_mulai'    => $mulais[$i],
                    'tgl_selesai'  => $selesais[$i],
                    'area_bekerja' => $areais[$i],
                    'link_berkas' => $linkis[$i],

                    // --- TAMBAHAN ---
                    'jenjang'      => $jenjangs[$i] ?? null, // Gunakan null coalescing
                    // --- END TAMBAHAN ---
                ]);
            }
        }

        return redirect()
            ->route('karyawan.show', $karyawan->id)
            ->with('success', 'Riwayat jabatan berhasil ditambahkan.');
    }

    public function editpi($id){
        $karyawan = User::findOrFail($id);
        return view('pages.karyawan.editpi', compact('karyawan'));
    }

    public function updatepi(Request $request, $id)
    {
    // 1. Validasi data yang masuk
    $request->validate([
        'nama_lengkap' => 'required|string|max:100',
        'nik' => 'required|string|max:20|unique:users,nik,' . $id,
    ]);

    // 2. Temukan data karyawan yang akan diupdate
    $karyawan = User::findOrFail($id);

    // 3. Update data karyawan dengan data dari form
    $karyawan->update([
        'nama_lengkap' => $request->nama_lengkap,
        'nik' => $request->nik,
        'tgl_lahir' => $request->tgl_lahir,
        'tempat_lahir' => $request->tempat_lahir,
        'jenis_kelamin' => $request->jenis_kelamin,
        'agama' => $request->agama,
        'status_perkawinan' => $request->status_perkawinan,
        'email' => $request->email,
        'no_telp' => $request->no_telp,
    ]);

    // 4. Redirect kembali ke halaman detail dengan pesan sukses
    return view('pages.karyawan.detail', compact('karyawan'))
                     ->with('success', 'Data pribadi karyawan berhasil diperbarui.');
    }

    public function editkep($id){
        $karyawan = User::findOrFail($id);
        return view('pages.karyawan.editkep', compact('karyawan'));
    }

    public function updatekep(Request $request, $id)
    {
    // 1. Validasi data yang masuk
    $request->validate([
        'npk_baru' => 'required|string|max:100',
        'npwp' => 'required|string|max:100',
    ]);

    // 2. Temukan data karyawan yang akan diupdate
    $karyawan = User::findOrFail($id);

    // 3. Update data karyawan dengan data dari form
    $karyawan->update([
        'npk_baru' => $request->npk_baru,
        'npwp'     => $request->npwp,
    ]);

    // 4. Redirect kembali ke halaman detail dengan pesan sukses
    return view('pages.karyawan.detail', compact('karyawan'))
                     ->with('success', 'Data Kepegawaian karyawan berhasil diperbarui.');
    }

    public function cariKaryawan(Request $request)
    {
        $query = $request->get('q');
        $karyawan = User::where('nama_lengkap', 'LIKE', "%{$query}%")
            ->take(5)
            ->get(['id', 'nama_lengkap', 'nip']);

        return response()->json([
            'results' => $karyawan->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => "{$item->nama_lengkap} ({$item->nip})"
                ];
            }),
        ]);
    }

    public function cetakDetail($id)
    {
        $karyawan = User::findOrFail($id);

        $karyawan->load([
            'riwayatJabatans.jabatan', 
            'riwayatPendidikan',       
            'riwayatSP',               
            'jabatanTerbaru.jabatan',
            'riwayatKpo',
            'riwayatLatihanJabatan',
            'riwayatPangkatPerusahaan',
            'riwayatPenghargaan',
        ]);

        // --- LOGIKA TAMBAHAN UNTUK LOGO ---
        // Sesuaikan path ini dengan lokasi file logo Anda yang sebenarnya
        // Biasanya ada di public/images/logo.png atau sejenisnya
        $pathLogo = public_path('images/logo2.png'); 
        
        $logoBase64 = null;
        if (file_exists($pathLogo)) {
            $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
            $data = file_get_contents($pathLogo);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        // ----------------------------------

        $pdf = PDF::loadView('pages.karyawan.cetakinfo', [
            'karyawan' => $karyawan,
            'logo' => $logoBase64 // Kirim variabel logo ke view
        ]);

        $pdf->setPaper('A4', 'portrait');

        $namaFile = 'CV-' . str_replace(' ', '-', $karyawan->nama_lengkap) . '.pdf';
        
        return $pdf->download($namaFile);
    }
}

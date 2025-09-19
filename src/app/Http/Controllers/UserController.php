<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $karyawan = User::all();
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
            // Tambahkan validasi untuk area_bekerja
            'area_bekerja' => 'required|array',
            'area_bekerja.*' => 'required|string',
        ]);

        // 2. Ambil semua data
        $jabatan_ids = $request->jabatan_id;
        $mulais      = $request->tgl_mulai;
        $selesais    = $request->tgl_selesai;
        $areais      = $request->area_bekerja;

        // 3. Loop dan simpan data, tambahkan pengecekan nilai
        foreach ($jabatan_ids as $i => $jabatan_id) {
            // Hanya proses jika jabatan_id tidak kosong
            if (!empty($jabatan_id)) {
                $karyawan->riwayatJabatans()->create([
                    'id_jabatan'   => $jabatan_id,
                    'tgl_mulai'    => $mulais[$i],
                    'tgl_selesai'  => $selesais[$i],
                    'area_bekerja' => $areais[$i],
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

    public function updatekep(Request $request, $id)
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
        'npk' => $request->nama_lengkap,
        'jabatan' => $request->nik,
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
                     ->with('success', 'Data Kepegawaian karyawan berhasil diperbarui.');
    }
}

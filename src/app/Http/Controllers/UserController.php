<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        return view('pages.karyawan.create');
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

        User::create([
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
            'area_bekerja' => $request->area_bekerja,
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
}

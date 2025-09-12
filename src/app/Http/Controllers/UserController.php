<?php

namespace App\Http\Controllers;

use App\Models\User; // pake model User bawaan
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Ambil semua data user
        $karyawan = User::all();

        // lempar ke view
        return view('pages.karyawan.read', compact('karyawan'));
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
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = $request->all();

    // password otomatis di-hash karena casts di model
    // jadi nggak perlu bcrypt()

    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('images', 'public');
        $data['foto'] = $path; // simpan path relatif "images/namafile.jpg"
    }

    User::create($data);

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
            'name' => 'required',
            'email' => "required|email|unique:users,email,{$id}",
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diupdate');
    }

    public function destroy($id)
    {
        $karyawan = User::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus');
    }
}

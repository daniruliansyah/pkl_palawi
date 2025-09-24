<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(Request $request): View
    {
        return view('pages.profile.index', [
            'user' => $request->user(),
        ]);
    }

    public function edit(Request $request): View
    {
        return view('pages.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = \App\Models\User::find(Auth::id());

        // Validasi sederhana
        $request->validate([
            'alamat'   => 'nullable|string|max:255',
            'no_telp'  => 'nullable|string|max:20',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update data umum
        $user->alamat  = $request->alamat;
        $user->no_telp = $request->no_telp;
        $user->email   = $request->email;

        // Update password kalau diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Upload foto baru kalau ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama kalau ada
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // Simpan foto baru
            $fotoPath = $request->file('foto')->store('images', 'public');
            $user->foto = $fotoPath;
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Kalender;

class KalenderController extends Controller
{
    /**
     * [BARU] Menampilkan halaman utama kalender (Blade View).
     * Route: GET /calendar/calender (calendar.index)
     */
    public function showCalendar()
    {
        $nip = Auth::user()->nip;
        return view('pages.calendar.index', compact('nip'));
    }

    /**
     * API: Mengambil semua catatan kalender untuk user yang sedang login.
     * Route: GET /calendar/notes (calendar.api.index)
     */
    public function index(Request $request)
    {
        try {
            $nip_user = Auth::user()->nip;

            $notes = Kalender::where('nip_user', $nip_user)
                ->orderBy('note_date', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'notes' => $notes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data catatan.',
                'details' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Menyimpan atau memperbarui catatan.
     * Route: POST /calendar/notes (calendar.api.store)
     */
    public function storeOrUpdate(Request $request)
    {
        $nip = Auth::user()->nip;

        if (!$nip) {
            return response()->json(['status' => 'error', 'message' => 'User tidak terautentikasi.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'note_date' => 'required|date_format:Y-m-d',
            'notes' => 'required|string|max:1000',
            'urgency' => 'required|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Cek apakah catatan untuk tanggal ini sudah ada untuk user tersebut
        $note = Kalender::firstOrNew([
            'nip_user' => $nip,
            'note_date' => $request->note_date,
        ]);

        $note->notes = $request->notes;
        $note->urgency = $request->urgency;
        $note->nip_user = $nip;

        $message = $note->exists ? 'Catatan berhasil diperbarui.' : 'Catatan berhasil disimpan.';

        try {
            $note->save();

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $note,
            ], $note->wasRecentlyCreated ? 201 : 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan/memperbarui catatan.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Menghapus catatan.
     * Route: DELETE /calendar/notes/{id} (calendar.api.destroy)
     */
    public function destroy($id)
    {
        $nip = Auth::user()->nip;

        if (!$nip) {
            return response()->json(['status' => 'error', 'message' => 'User tidak terautentikasi.'], 401);
        }

        try {
            $note = Kalender::where('id', $id)
                ->where('nip_user', $nip)
                ->first();

            if (!$note) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Catatan tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            $note->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Catatan berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus catatan.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
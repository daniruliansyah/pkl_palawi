<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

class NotifikasiController extends Controller
{
    /**
     * Mengambil daftar notifikasi dan jumlah notifikasi belum dibaca.
     */

    // NotifikasiController.php
         public function index()
        {
            $user = Auth::user();

            // Ambil semua notifikasi 7 hari terakhir
            $notifications = $user->notifications()
                ->where('created_at', '>=', now()->subWeek())
                ->latest()
                ->take(10)
                ->get();

            // Hitung yang belum dibaca
            $unreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'status' => 'success',
                'unread_count' => $unreadCount,
                'notifications' => $notifications->map(function ($notif) {
                    return [
                        'id'       => $notif->id,
                        'data'     => $notif->data,
                        'read_at'  => $notif->read_at,
                        'created_at' => $notif->created_at->diffForHumans(),
                    ];
                }),
            ]);
        }



    public function markAllAsRead()
    {
        $user = Auth::user();

        $user->unreadNotifications()
            ->where('created_at', '>=', now()->subWeek())
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'success']);
    }


}

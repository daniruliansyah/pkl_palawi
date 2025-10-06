<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class StatusSuratDiperbarui extends Notification implements ShouldQueue
{
    use Queueable;

    public $jenisSurat;
    public $statusBaru;
    public $keterangan;
    public $url;
    public $aktor;

    public function __construct($aktor, $jenisSurat, $statusBaru, $keterangan = null, $url = '#')
    {
        $this->aktor = $aktor;
        $this->jenisSurat = $jenisSurat;
        $this->statusBaru = $statusBaru;
        $this->keterangan = $keterangan;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Format untuk Saluran Database (Dropdown Website).
     */
    public function toDatabase($notifiable)
    {
        $aktor = $this->aktor; // Mengambil data User yang melakukan aksi
        // 1. Tentukan path foto
        $pathFoto = $aktor->foto
        ? 'storage/' . $aktor->foto
        : 'images/default.jpg'; // <-- PASTIKAN NAMA FILE INI ADA DI public/images/

        // 2. Generate URL menggunakan asset()
        $userImageUrl = asset($pathFoto);

        $message = "Surat **{$this->jenisSurat}** Anda telah **{$this->statusBaru}**.";
        if ($this->keterangan) {
            $message .= " Keterangan: {$this->keterangan}";
        }

        return [
            'sender' => $aktor->nama_lengkap ?? 'Sistem',
            'message' => $message,
            'type' => $this->jenisSurat,
            'status' => $this->statusBaru,
            'user_image' => $userImageUrl, // <-- Ganti dengan variabel yang sudah diperbaiki
            'url' => $this->url,
        ];
    }

    // Format untuk Saluran Mail (Email) - Sudah benar di kode Anda
    public function toMail($notifiable)
    {
        $status = $this->statusBaru;
        $jenis  = $this->jenisSurat;

        return (new MailMessage)
                    ->subject("Pembaruan Status: {$jenis} Anda Telah {$status}")
                    ->greeting("Yth. {$notifiable->nama_lengkap},")
                    ->line("Status surat **{$jenis}** Anda telah diperbarui menjadi **{$status}**.")
                    ->action('Lihat Detail Surat', url($this->url))
                    ->line('Terima kasih.');
    }
}


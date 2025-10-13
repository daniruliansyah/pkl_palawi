<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StatusSuratDiperbarui extends Notification implements ShouldQueue
{
    use Queueable;

    public $jenisSurat;
    public $statusBaru;
    public $keterangan;
    public $url;
    public $aktor; // User yang melakukan aksi (Pengaju, Penyetuju, atau Penolak)

    /**
     * @param User $aktor User yang melakukan aksi
     */
    public function __construct(User $aktor, $jenisSurat, $statusBaru, $keterangan = null, $url = '#')
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

    // =================================================================
    // METHOD toDatabase (Notifikasi In-App)
    // =================================================================
    public function toDatabase($notifiable)
    {
        $aktor = $this->aktor;
        $status = strtolower($this->statusBaru);
        $jenis = $this->jenisSurat;
        $aktorNama = $aktor->nama_lengkap ?? 'Sistem';

        // -------------------------------------------------------------
        // LOGIKA PESAN IN-APP
        // -------------------------------------------------------------

        // Cek apakah penerima adalah ATASAN/APPROVER yang perlu mengambil tindakan
        // Status awal pengajuan (sesuai SppdController) adalah 'menunggu'
        if ($status === 'menunggu' && $notifiable->nip !== $aktor->nip) {
            // POV: Approver. Pesan fokus pada aksi.
            $message = "**{$aktorNama}** telah mengajukan Surat **{$jenis}** baru dan membutuhkan persetujuan Anda.";
            $sender = $aktorNama;

        // Cek apakah penerima adalah PEMBUAT SURAT (diberi tahu tentang hasil)
        } else {
            // POV: Pembuat Surat. Aktor adalah Penyetuju/Penolak.

            if (in_array($status, ['disetujui', 'diterima'])) {
                 $message = "Surat **{$jenis}** Anda telah **disetujui** oleh **{$aktorNama}**.";
            } elseif (in_array($status, ['ditolak', 'dibatalkan'])) {
                 $message = "Surat **{$jenis}** Anda telah **ditolak** oleh **{$aktorNama}**.";
            } else {
                 $message = "Surat **{$jenis}** Anda berada dalam tahap **{$this->statusBaru}**.";
            }

            // Tambahkan keterangan (misal: alasan penolakan)
            if ($this->keterangan) {
                 $message .= " Keterangan: {$this->keterangan}";
            }
            $sender = $aktorNama;
        }

        // --- Pembuatan URL Foto (Menggunakan Aktor) ---
        $pathFoto = $aktor->foto
            ? 'storage/' . $aktor->foto
            : 'images/default.jpg';
        $userImageUrl = asset($pathFoto);

        return [
            'sender' => $sender,
            'message' => $message,
            'type' => $jenis,
            'status' => $this->statusBaru,
            'user_image' => $userImageUrl,
            'url' => $this->url,
        ];
    }

    // =================================================================
    // METHOD toMail (Notifikasi Email)
    // =================================================================
    public function toMail($notifiable)
    {
        $status = strtolower($this->statusBaru);
        $jenis  = $this->jenisSurat;
        $aktorNama = $this->aktor->nama_lengkap ?? 'Sistem';
        $mailMessage = new MailMessage;

        // ----------------------------------------------------------------------
        // SKENARIO 1: NOTIFIKASI UNTUK APPROVER (TINDAKAN DIPERLUKAN)
        // ----------------------------------------------------------------------
        if ($status === 'menunggu' && $notifiable->nip !== $this->aktor->nip) {

            $mailMessage->subject("TINDAKAN DIPERLUKAN: Pengajuan {$jenis} Baru dari {$aktorNama}")
                ->greeting("Yth. Bapak/Ibu {$notifiable->nama_lengkap},")
                ->line("Anda menerima pengajuan **{$jenis}** baru dari **{$aktorNama}**.")
                ->line('Pengajuan ini memerlukan persetujuan/penolakan Anda. Mohon segera berikan tindakan.')
                ->action('TINJAU & BERI TINDAKAN', url($this->url));

        // ----------------------------------------------------------------------
        // SKENARIO 2: NOTIFIKASI UNTUK PEMBUAT SURAT (Pembaruan Status)
        // ----------------------------------------------------------------------
        } else {

            // Tentukan Subjek dan Baris pertama berdasarkan status
            if (in_array($status, ['disetujui', 'diterima'])) {
                 $subject = "SELAMAT! {$jenis} Anda Telah Disetujui";
                 $line1 = "Surat **{$jenis}** Anda telah **{$this->statusBaru}** oleh {$aktorNama}.";
            } elseif (in_array($status, ['ditolak', 'dibatalkan'])) {
                 $subject = "Pemberitahuan: {$jenis} Anda Ditolak/Dibatalkan";
                 $line1 = "Mohon maaf, surat **{$jenis}** Anda telah **{$this->statusBaru}** oleh {$aktorNama}.";
            } else { // Status dalam proses (misal: Menunggu)
                 $subject = "Pembaruan Status: {$jenis} Dalam Proses ({$this->statusBaru})";
                 $line1 = "Surat **{$jenis}** Anda saat ini berada dalam tahap **{$this->statusBaru}**. Aksi terakhir dilakukan oleh {$aktorNama}.";
            }

            $mailMessage->subject($subject)
                ->greeting("Yth. {$notifiable->nama_lengkap},")
                ->line($line1);

            if ($this->keterangan) {
                 $mailMessage->line("Keterangan Tambahan: **{$this->keterangan}**");
            }

            $mailMessage->action('Lihat Detail Status', url($this->url));
        }

        $mailMessage->line('Terima kasih.');

        return $mailMessage;
    }
}

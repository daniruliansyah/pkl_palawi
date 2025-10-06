<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class ClearOldNotifications extends Command
{
    /**
     * Nama dan signature command
     *
     * @var string
     */
    protected $signature = 'notifications:clear-old';

    /**
     * Deskripsi command
     *
     * @var string
     */
    protected $description = 'Hapus notifikasi yang lebih dari 7 hari';

    /**
     * Jalankan command
     */
    public function handle()
    {
        $deleted = DatabaseNotification::where('created_at', '<', now()->subWeek())->delete();

        $this->info("{$deleted} notifikasi lama dihapus.");
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class PengajuanUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ajuan;

    /**
     * Buat instance baru.
     */
    public function __construct($ajuan)
    {
        $this->ajuan = $ajuan;
    }

    /**
     * Tentukan channel notifikasi.
     */
    public function via($notifiable)
    {
        // return ['database', 'mail'];
        return ['database'];
    }

    /**
     * Isi notifikasi yang disimpan ke database.
     */
    public function toDatabase($notifiable)
    {
        $pesan = 'pengajuan baru dari unit ' . $this->ajuan->unit->nama_unit . PHP_EOL;
        $pesan .= 'nama atau jasa ' . $this->ajuan->produk_ajuan . PHP_EOL;
        $pesan .= 'oleh ' . $this->ajuan->users->name;
        $data = [
            'message' => $pesan,
            'ajuan_id' => $this->ajuan->id,
            'unit_id' => $this->ajuan->units_id,
            'unit_name' => $this->ajuan->unit->nama_unit,
            'created_at' => now(),
        ];

        event(new \App\Events\NotificationReceived((object)[
            'data' => $data
        ], $notifiable->id));

        return $data;
    }

    /**
     * Notifikasi via email
     */
    public function toMail($notifiable)
    {
        $pesan = 'pengajuan baru dari unit ' . $this->ajuan->unit->nama_unit . PHP_EOL;
        $pesan .= 'nama atau jasa ' . $this->ajuan->produk_ajuan . PHP_EOL;
        $pesan .= 'oleh ' . $this->ajuan->users->name;
        return (new MailMessage)
            ->subject($pesan)
            ->markdown('emails.pengajuan.notification', [
                'ajuan' => $this->ajuan,
                'user' => $notifiable,
            ]);
    }
}

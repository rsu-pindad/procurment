<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class StatusPengadaanNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ajuan;

    public function __construct($ajuan)
    {
        $this->ajuan = $ajuan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $pesan = 'Status pengajuan untuk ' . $this->ajuan->produk_ajuan .
            ' telah berubah menjadi: ACC';

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
}

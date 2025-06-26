<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengajuanUnitNotification extends Notification
{
    use Queueable;

    protected $ajaun;

    /**
     * Create a new notification instance.
     */
    public function __construct($ajuan)
    {
        $this->ajuan = $ajuan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        $pesan = 'pengajuan baru untuk unit ' . $this->ajuan->unit->nama_unit . PHP_EOL;
        $nama_produk = 'nama atau jasa ' . $this->ajuan->produk_ajuan . PHP_EOL;
        $created_by = 'oleh ' . $this->ajuan->users->name;
        $data = [
            'message' => $pesan,
            'nama_produk' => $nama_produk,
            'created_by' => $created_by,
            'ajuan_id' => $this->ajuan->id,
            'unit_id' => $this->ajuan->units_id,
            'unit_name' => $this->ajuan->unit->nama_unit,
            'created_at' => now(),
        ];

        event(new \App\Events\UnitNotificationReceived(
            (object)
            [
                'data' => $data
            ],
            $notifiable->id
        ));

        return $data;
    }
}

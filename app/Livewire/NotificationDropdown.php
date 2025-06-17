<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationDropdown extends Component
{
    public $notifications = [];

    protected $listeners = ['notificationReceived' => 'refreshNotifications'];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
            $this->notifications = auth()->user()->unreadNotifications()->latest()->get();
            // $this->notifications = auth()->user()->unreadNotifications()->get();
        }
    }


    public function refreshNotifications()
    {
        $this->notifications = auth()->user()->unreadNotifications()->latest()->get();
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}

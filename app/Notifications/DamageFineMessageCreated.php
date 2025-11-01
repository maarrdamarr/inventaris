<?php

namespace App\Notifications;

use App\DamageFineMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DamageFineMessageCreated extends Notification
{
    use Queueable;

    public function __construct(public DamageFineMessage $msg) {}

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'report_id' => $this->msg->damage_report_id,
            'message' => $this->msg->message,
            'from' => $this->msg->sender->name ?? 'Petugas',
            'type' => 'fine_message',
        ];
    }
}


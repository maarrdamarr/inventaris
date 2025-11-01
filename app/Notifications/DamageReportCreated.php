<?php

namespace App\Notifications;

use App\DamageReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DamageReportCreated extends Notification
{
    use Queueable;

    public function __construct(public DamageReport $report) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'severity' => $this->report->severity,
            'reporter' => $this->report->reporter->name ?? 'Pengguna',
            'created_at' => optional($this->report->created_at)->toDateTimeString(),
        ];
    }
}


<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class SystemNotice extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public string $level = 'info',
        public ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => $this->title,
            'message' => $this->message,
            'level' => $this->level,
            'icon' => match ($this->level) {
                'success' => 'circle-check',
                'warning' => 'alert-triangle',
                'danger' => 'x-circle',
                default => 'info-circle',
            },
            'url' => $this->url,
        ]);
    }
}

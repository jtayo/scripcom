<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class RouterAlert extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public string $level = 'warning',
        public ?string $url = null,
        public ?int $routerId = null,
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
            'icon' => 'alert-triangle',
            'url' => $this->url,
            'router_id' => $this->routerId,
        ]);
    }
}

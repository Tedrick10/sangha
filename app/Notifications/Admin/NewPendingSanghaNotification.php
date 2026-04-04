<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPendingSanghaNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $sanghaName,
        public string $monasteryName,
        public string $sourceLabel,
        public string $actionUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => t('notif_new_sangha_title', 'New sangha application'),
            'body' => strtr(t('notif_new_sangha_body', ':name (:monastery) — :source'), [
                ':name' => $this->sanghaName,
                ':monastery' => $this->monasteryName,
                ':source' => $this->sourceLabel,
            ]),
            'action_url' => $this->actionUrl,
            'kind' => 'new_sangha',
        ];
    }
}

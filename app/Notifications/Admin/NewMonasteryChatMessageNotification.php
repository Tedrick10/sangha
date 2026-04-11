<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMonasteryChatMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $monasteryName,
        public string $preview,
        public string $actionUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => t('notif_monastery_chat_title', 'Monastery chat message'),
            'body' => strtr(t('notif_monastery_chat_body', ':name: :preview'), [
                ':name' => $this->monasteryName,
                ':preview' => $this->preview,
            ]),
            'action_url' => $this->actionUrl,
            'kind' => 'monastery_chat',
        ];
    }
}

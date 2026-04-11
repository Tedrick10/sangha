<?php

namespace App\Notifications\Monastery;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAdminChatMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
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
            'title' => t('notif_admin_chat_title', 'Message from administrator'),
            'body' => $this->preview,
            'action_url' => $this->actionUrl,
            'kind' => 'admin_chat',
        ];
    }
}

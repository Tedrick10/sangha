<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMonasteryRequestNotification extends Notification
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
            'title' => t('notif_message_request_admin_title', 'New message request'),
            'body' => strtr(t('notif_new_monastery_request_body', ':name: :preview'), [
                ':name' => $this->monasteryName,
                ':preview' => $this->preview,
            ]),
            'action_url' => $this->actionUrl,
            'kind' => 'new_monastery_request',
        ];
    }
}

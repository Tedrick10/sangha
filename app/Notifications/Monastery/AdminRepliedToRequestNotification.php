<?php

namespace App\Notifications\Monastery;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminRepliedToRequestNotification extends Notification
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
            'title' => t('notif_message_request_reply_title', 'New reply on your request'),
            'body' => strtr(t('notif_admin_replied_body', ':preview'), [':preview' => $this->preview]),
            'action_url' => $this->actionUrl,
            'kind' => 'admin_reply',
        ];
    }
}

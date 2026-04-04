<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMonasteryRegistrationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $monasteryName,
        public string $actionUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => t('notif_new_monastery_reg_title', 'New monastery registration'),
            'body' => strtr(t('notif_new_monastery_reg_body', ':name registered and is pending review.'), [
                ':name' => $this->monasteryName,
            ]),
            'action_url' => $this->actionUrl,
            'kind' => 'new_monastery_registration',
        ];
    }
}

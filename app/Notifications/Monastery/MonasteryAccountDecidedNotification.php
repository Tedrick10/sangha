<?php

namespace App\Notifications\Monastery;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MonasteryAccountDecidedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
        public ?string $rejectionPreview,
        public string $actionUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->status === 'approved'
            ? t('notif_monastery_account_approved_title', 'Your monastery was approved')
            : t('notif_monastery_account_rejected_title', 'Your monastery was rejected');

        $body = $this->status === 'approved'
            ? t('notif_monastery_account_approved_body', 'You can use all portal features.')
            : t('notif_monastery_account_rejected_body', 'Please check the rejection details in your account area.');

        return [
            'title' => $title,
            'body' => $body,
            'action_url' => $this->actionUrl,
            'kind' => 'monastery_account_'.$this->status,
        ];
    }
}

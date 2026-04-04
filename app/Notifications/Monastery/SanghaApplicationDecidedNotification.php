<?php

namespace App\Notifications\Monastery;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SanghaApplicationDecidedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $sanghaName,
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
            ? t('notif_sangha_approved_title', 'Application approved')
            : t('notif_sangha_rejected_title', 'Application rejected');

        $bodyTemplate = $this->status === 'approved'
            ? t('notif_sangha_approved_body', ':name has been approved.')
            : t('notif_sangha_rejected_body', ':name was rejected.');
        $body = strtr($bodyTemplate, [':name' => $this->sanghaName]);

        return [
            'title' => $title,
            'body' => $body,
            'action_url' => $this->actionUrl,
            'kind' => 'sangha_'.$this->status,
        ];
    }
}

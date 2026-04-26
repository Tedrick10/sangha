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
        $title = match ($this->status) {
            'approved' => t('notif_sangha_approved_title', 'Application approved'),
            'needed_update' => t('notif_sangha_needed_update_title', 'Update requested'),
            default => t('notif_sangha_rejected_title', 'Application rejected'),
        };

        $bodyTemplate = match ($this->status) {
            'approved' => t('notif_sangha_approved_body', ':name has been approved.'),
            'needed_update' => t('notif_sangha_needed_update_body', ':name needs updates before approval.'),
            default => t('notif_sangha_rejected_body', ':name was rejected.'),
        };
        $body = strtr($bodyTemplate, [':name' => $this->sanghaName]);

        return [
            'title' => $title,
            'body' => $body,
            'action_url' => $this->actionUrl,
            'kind' => 'sangha_'.$this->status,
        ];
    }
}

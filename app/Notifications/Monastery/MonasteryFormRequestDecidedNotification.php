<?php

namespace App\Notifications\Monastery;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class MonasteryFormRequestDecidedNotification extends Notification
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
            ? t('notif_monastery_form_request_approved_title', 'Request approved')
            : t('notif_monastery_form_request_rejected_title', 'Request rejected');

        $body = $this->status === 'approved'
            ? t('notif_monastery_form_request_approved_body', 'Your monastery request has been approved.')
            : t('notif_monastery_form_request_rejected_body', 'Your monastery request has been rejected.');

        if ($this->status === 'rejected' && filled($this->rejectionPreview)) {
            $body .= ' ('.Str::limit($this->rejectionPreview, 120).')';
        }

        return [
            'title' => $title,
            'body' => $body,
            'action_url' => $this->actionUrl,
            'kind' => 'monastery_request_'.$this->status,
        ];
    }
}

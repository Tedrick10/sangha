<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Notifications\Notification;

final class AdminNotifications
{
    public static function notifyAll(Notification $notification): void
    {
        foreach (User::query()->cursor() as $user) {
            $user->notify($notification);
        }
    }
}

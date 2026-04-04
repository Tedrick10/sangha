<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function recent(Request $request): JsonResponse
    {
        $user = $request->user();
        $unreadCount = $user->unreadNotifications()->count();
        $items = $user->notifications()->limit(15)->get()->map(fn ($n) => [
            'id' => $n->id,
            'title' => (string) ($n->data['title'] ?? ''),
            'body' => (string) ($n->data['body'] ?? ''),
            'read' => $n->read_at !== null,
            'created_human' => (string) ($n->created_at?->diffForHumans() ?? ''),
            'go_url' => route('admin.notifications.go', $n->id),
        ])->values();

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    public function go(Request $request, string $notification): RedirectResponse
    {
        $n = $request->user()->notifications()->where('id', $notification)->firstOrFail();
        $n->markAsRead();
        $url = is_array($n->data) ? ($n->data['action_url'] ?? null) : null;

        return redirect()->to($url ?: route('admin.dashboard'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}

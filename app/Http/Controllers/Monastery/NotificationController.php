<?php

namespace App\Http\Controllers\Monastery;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function recent(Request $request): JsonResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $unreadCount = $monastery->unreadNotifications()->count();
        $items = $monastery->notifications()->limit(15)->get()->map(fn ($n) => [
            'id' => $n->id,
            'title' => (string) ($n->data['title'] ?? ''),
            'body' => (string) ($n->data['body'] ?? ''),
            'read' => $n->read_at !== null,
            'created_human' => (string) ($n->created_at?->diffForHumans() ?? ''),
            'go_url' => route('monastery.notifications.go', $n->id),
        ])->values();

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    public function go(Request $request, string $notification): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $n = $monastery->notifications()->where('id', $notification)->firstOrFail();
        $n->markAsRead();
        $url = is_array($n->data) ? ($n->data['action_url'] ?? null) : null;

        return redirect()->to($url ?: route('monastery.dashboard'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        Auth::guard('monastery')->user()->unreadNotifications->markAsRead();

        return back();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Monastery;
use App\Models\MonasteryMessage;
use App\Notifications\Monastery\AdminRepliedToRequestNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MonasteryMessageController extends Controller
{
    public function index(): View
    {
        $monasteries = Monastery::query()
            ->withCount([
                'messages as unread_messages_count' => fn ($query) => $query
                    ->where('sender_type', 'monastery')
                    ->whereNull('read_at'),
            ])
            ->withMax('messages', 'created_at')
            ->orderByDesc('messages_max_created_at')
            ->orderBy('name')
            ->paginate(admin_per_page(15));

        return view('admin.monastery-messages.index', compact('monasteries'));
    }

    public function show(Monastery $monastery): View
    {
        MonasteryMessage::where('monastery_id', $monastery->id)
            ->where('sender_type', 'monastery')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $monastery->messages()
            ->with('user:id,name')
            ->latest()
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        return view('admin.monastery-messages.show', compact('monastery', 'messages'));
    }

    public function pollThread(Monastery $monastery): JsonResponse
    {
        $messages = $monastery->messages()
            ->with('user:id,name')
            ->latest()
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        $revision = $messages->isEmpty()
            ? '0-0-0'
            : (($messages->max('id') ?? 0).'-'.$messages->count().'-'.(optional($messages->last())->updated_at?->timestamp ?? 0));

        $html = view('partials.monastery-message-thread-items', [
            'messages' => $messages,
            'monastery' => $monastery,
            'variant' => 'admin',
        ])->render();

        return response()->json([
            'revision' => $revision,
            'html' => $html,
        ]);
    }

    public function reply(Request $request, Monastery $monastery): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        MonasteryMessage::create([
            'monastery_id' => $monastery->id,
            'sender_type' => 'admin',
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        $monastery->notify(new AdminRepliedToRequestNotification(
            Str::limit($validated['message'], 140),
            route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request']),
        ));

        return redirect()
            ->route('admin.monastery-requests.show', $monastery)
            ->with('success', 'Reply sent successfully.');
    }
}

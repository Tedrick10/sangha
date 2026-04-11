<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Monastery;
use App\Models\MonasteryMessage;
use App\Notifications\Monastery\NewAdminChatMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MonasteryChatController extends Controller
{
    public function show(Request $request, Monastery $monastery): View
    {
        $messages = MonasteryMessage::query()
            ->where('monastery_id', $monastery->id)
            ->with(['user:id,name', 'monastery:id,name'])
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        return view('admin.monasteries.chat', compact('monastery', 'messages'));
    }

    public function fetch(Request $request, Monastery $monastery): JsonResponse
    {
        $sinceId = max(0, (int) $request->query('since_id', 0));

        $messages = MonasteryMessage::query()
            ->where('monastery_id', $monastery->id)
            ->where('id', '>', $sinceId)
            ->with(['user:id,name', 'monastery:id,name'])
            ->orderBy('id')
            ->limit(200)
            ->get();

        return response()->json([
            'messages' => $messages->map(fn (MonasteryMessage $m) => $m->toChatPayload($monastery->name))->values()->all(),
        ]);
    }

    public function store(Request $request, Monastery $monastery): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:10000'],
        ]);

        $msg = MonasteryMessage::create([
            'monastery_id' => $monastery->id,
            'sender_type' => MonasteryMessage::SENDER_ADMIN,
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        $msg->loadMissing(['user:id,name', 'monastery:id,name']);

        $monastery->notify(new NewAdminChatMessageNotification(
            Str::limit($msg->message, 200),
            route('monastery.dashboard', ['tab' => 'chat']),
        ));

        return response()->json([
            'message' => $msg->toChatPayload($monastery->name),
        ]);
    }
}

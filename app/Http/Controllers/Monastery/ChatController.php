<?php

namespace App\Http\Controllers\Monastery;

use App\Http\Controllers\Controller;
use App\Models\MonasteryMessage;
use App\Notifications\Admin\NewMonasteryChatMessageNotification;
use App\Support\AdminNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function fetch(Request $request): JsonResponse
    {
        $monastery = Auth::guard('monastery')->user();
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

    public function store(Request $request): JsonResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:10000'],
        ]);

        $msg = MonasteryMessage::create([
            'monastery_id' => $monastery->id,
            'sender_type' => MonasteryMessage::SENDER_MONASTERY,
            'user_id' => null,
            'message' => $validated['message'],
        ]);

        $msg->loadMissing('monastery:id,name');

        AdminNotifications::notifyAll(new NewMonasteryChatMessageNotification(
            $monastery->name,
            Str::limit($msg->message, 140),
            route('admin.monasteries.chat', $monastery),
        ));

        return response()->json([
            'message' => $msg->toChatPayload($monastery->name),
        ]);
    }
}

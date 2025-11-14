<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmojiController extends Controller
{
    public function throwGet(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Emoji throw GET endpoint',
            'data' => $request->all(),
        ]);
    }

    public function throw(Request $request): JsonResponse
    {
        // Hier wird später das Emoji-Event via Reverb gebroadcastet
        $emojiData = $request->validate([
            'id' => 'required|string',
            'emoji' => 'required|string',
            'from' => 'required|string',
            'timestamp' => 'required|integer',
            'room_id' => 'nullable|string',
        ]);

        // TODO: Broadcast event via Laravel Reverb
        // broadcast(new EmojiThrown($emojiData))->toOthers();

        return response()->json([
            'success' => true,
            'data' => $emojiData,
        ]);
    }
}

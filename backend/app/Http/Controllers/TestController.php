<?php

namespace App\Http\Controllers;

use App\Events\TestEvent;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    /**
     * Broadcast a test event via WebSocket.
     */
    public function broadcast(): JsonResponse
    {
        $message = 'Test broadcast sent at ' . now()->format('H:i:s');

        // Dispatch the event
        event(new TestEvent($message));

        return response()->json([
            'success' => true,
            'message' => 'Event broadcasted successfully',
            'data' => [
                'message' => $message,
            ],
        ]);
    }
}

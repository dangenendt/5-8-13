<?php

use App\Http\Controllers\EmojiController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->make(null, 204);
});

Route::post('/api/emoji/throw', [EmojiController::class, 'throw'])->name('emoji.throw');
Route::get('/api/emoji/throw-get', [EmojiController::class, 'throwGet'])->name('emoji.throw.get');

// WebSocket test route
Route::post('/api/test/broadcast', [TestController::class, 'broadcast'])->name('test.broadcast');

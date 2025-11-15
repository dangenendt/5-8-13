<?php

use App\Http\Controllers\EmojiController;
use App\Http\Controllers\JiraSettingsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->make(null, 204);
});

Route::post('/api/emoji/throw', [EmojiController::class, 'throw'])->name('emoji.throw');
Route::get('/api/emoji/throw-get', [EmojiController::class, 'throwGet'])->name('emoji.throw.get');

// WebSocket test route
Route::post('/api/test/broadcast', [TestController::class, 'broadcast'])->name('test.broadcast');

// Jira Settings routes
Route::prefix('api/jira')->group(function () {
    Route::get('/settings', [JiraSettingsController::class, 'index'])->name('jira.settings.index');
    Route::post('/settings', [JiraSettingsController::class, 'store'])->name('jira.settings.store');
    Route::put('/settings/{id}', [JiraSettingsController::class, 'update'])->name('jira.settings.update');
    Route::delete('/settings/{id}', [JiraSettingsController::class, 'destroy'])->name('jira.settings.destroy');
    Route::post('/test-connection', [JiraSettingsController::class, 'testConnection'])->name('jira.test-connection');
    Route::get('/projects', [JiraSettingsController::class, 'getProjects'])->name('jira.projects');
});

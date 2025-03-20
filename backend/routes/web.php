<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/chat/send', [ChatController::class, 'sendMessage'])->middleware('auth');
Route::get('/api/chat/messages/{chatSessionId}', [ChatController::class, 'getMessages'])->middleware('auth');
Route::get('/api/chat/sessions', [ChatController::class, 'getChatSessions'])->middleware('auth');

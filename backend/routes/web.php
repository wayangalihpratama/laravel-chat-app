<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// User Authentication Routes
Route::post('/api/register', [AuthController::class, 'register']);
Route::post('/api/login', [AuthController::class, 'login']);

Route::post('/api/chat/send', [ChatController::class, 'sendMessage'])->middleware('auth');
Route::get('/api/chat/messages/{chatSessionId}', [ChatController::class, 'getMessages'])->middleware('auth');
Route::get('/api/chat/sessions', [ChatController::class, 'getChatSessions'])->middleware('auth');

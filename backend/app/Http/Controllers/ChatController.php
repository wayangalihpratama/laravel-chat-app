<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\ChatSession;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getChatSessions()
    {
        $chatSessions = ChatSession::where('initiator_id', auth()->id())
            ->orWhere('participant_id', auth()->id())
            ->get();

        return response()->json($chatSessions);
    }

    /**
     * Send a message.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_session_id' => 'required|exists:chat_sessions,id',
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'chat_session_id' => $request->chat_session_id,
            'user_id' => $request->user_id,
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }

    /**
     * Get messages for a chat session.
     *
     * @param int $chatSessionId
     * @return \Illuminate\Http\Response
     */
    public function getMessages($chatSessionId)
    {
        $messages = Message::where('chat_session_id', $chatSessionId)->get();
        return response()->json($messages);
    }
}

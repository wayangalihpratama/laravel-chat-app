<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="Chat API",
 *     version="1.0.0",
 *     description="API for chat application"
 * )
 */
class ChatController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/chat/sessions",
     *     summary="Get chat sessions",
     *     @OA\Response(response="200", description="Successful response")
     * )
     */
    public function getChatSessions()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $chatSessions = ChatSession::where('initiator_id', Auth::id())
            ->orWhere('participant_id', Auth::id())
            ->get();

        return response()->json($chatSessions);
    }

    /**
     * @OA\Post(
     *     path="/api/chat/send",
     *     summary="Send a message",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="chat_session_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Message sent successfully"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function sendMessage(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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
     * @OA\Get(
     *     path="/api/chat/messages/{chatSessionId}",
     *     summary="Get messages for a chat session",
     *     @OA\Parameter(
     *         name="chatSessionId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Successful response")
     * )
     */
    public function getMessages($chatSessionId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $messages = Message::where('chat_session_id', $chatSessionId)->get();
        return response()->json($messages);
    }
}

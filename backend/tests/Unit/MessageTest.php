<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_can_be_created()
    {
        $user = User::factory()->create();
        $chatSession = ChatSession::create([
            'user_one_id' => $user->id,
            'user_two_id' => User::factory()->create()->id,
        ]);

        $message = Message::create([
            'chat_session_id' => $chatSession->id,
            'user_id' => $user->id,
            'message' => 'Hello, World!',
        ]);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'message' => 'Hello, World!',
        ]);
    }
}

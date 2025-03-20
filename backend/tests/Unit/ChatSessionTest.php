<?php

namespace Tests\Unit;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_session_can_be_created()
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $chatSession = ChatSession::create([
            'user_one_id' => $userOne->id,
            'user_two_id' => $userTwo->id,
        ]);

        $this->assertDatabaseHas('chat_sessions', [
            'id' => $chatSession->id,
            'user_one_id' => $userOne->id,
            'user_two_id' => $userTwo->id,
        ]);
    }
}

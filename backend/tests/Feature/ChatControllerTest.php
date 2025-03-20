<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChatSession;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_send_message()
    {
        $chatSession = ChatSession::create([
            'user_one_id' => $this->user->id,
            'user_two_id' => User::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)->post('/api/chat/send', [
            'chat_session_id' => $chatSession->id,
            'user_id' => $this->user->id,
            'message' => 'Hello, World!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'chat_session_id' => $chatSession->id,
            'user_id' => $this->user->id,
            'message' => 'Hello, World!',
        ]);
    }

    public function test_user_can_get_messages()
    {
        $chatSession = ChatSession::create([
            'user_one_id' => $this->user->id,
            'user_two_id' => User::factory()->create()->id,
        ]);

        Message::create([
            'chat_session_id' => $chatSession->id,
            'user_id' => $this->user->id,
            'message' => 'Hello, World!',
        ]);

        $response = $this->actingAs($this->user)->get('/api/chat/messages/' . $chatSession->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Hello, World!']);
    }

    public function test_user_can_get_chat_sessions()
    {
        $chatSession = ChatSession::create([
            'user_one_id' => $this->user->id,
            'user_two_id' => User::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)->get('/api/chat/sessions');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $chatSession->id]);
    }
}

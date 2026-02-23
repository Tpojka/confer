<?php

namespace Tpojka\Confer\Tests\Unit;

use Tpojka\Confer\Tests\TestCase;
use Tpojka\Confer\Message;
use Tpojka\Confer\Conversation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a message belongs to a conversation.
     *
     * @return void
     */
    public function test_message_belongs_to_conversation()
    {
        $conversation = Conversation::create(['name' => 'Test Conversation', 'is_private' => false]);
        $message = Message::create([
            'body' => 'Hello',
            'conversation_id' => $conversation->id,
            'sender_id' => 1,
            'type' => 'user_message'
        ]);

        $this->assertEquals($conversation->id, $message->conversation->id);
    }

    /**
     * Test that a message belongs to a sender.
     *
     * @return void
     */
    public function test_message_belongs_to_sender()
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);
        $message = Message::create([
            'body' => 'Hello',
            'conversation_id' => 1,
            'sender_id' => $user->id,
            'type' => 'user_message'
        ]);

        $this->assertEquals($user->id, $message->sender->id);
    }

    /**
     * Test the getEventData method.
     *
     * @return void
     */
    public function test_get_event_data_method()
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);
        $conversation = Conversation::create(['name' => 'Test Conversation', 'is_private' => false]);
        $message = Message::create([
            'body' => 'Hello',
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'type' => 'user_message'
        ]);

        $eventData = $message->getEventData();

        $this->assertArrayHasKey('conversation', $eventData);
        $this->assertArrayHasKey('message', $eventData);
        $this->assertArrayHasKey('sender', $eventData);
        $this->assertEquals($message->id, $eventData['message']->id);
    }
}

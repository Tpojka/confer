<?php

namespace Tpojka\Confer\Tests\Unit;

use Tpojka\Confer\Tests\TestCase;
use Tpojka\Confer\Conversation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanConferTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can join a conversation.
     *
     * @return void
     */
    public function test_user_can_join_conversation()
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);
        $conversation = Conversation::create(['name' => 'Test', 'is_private' => false]);

        $user->conversations()->attach($conversation->id);

        $this->assertTrue($user->participatesIn($conversation->id));
    }

    /**
     * Test that a user can leave a conversation.
     *
     * @return void
     */
    public function test_user_can_leave_conversation()
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);
        $conversation = Conversation::create(['name' => 'Test', 'is_private' => false]);
        $user->conversations()->attach($conversation->id);

        $user->leaveConversation($conversation);

        $this->assertFalse($user->participatesIn($conversation->id));
    }

    /**
     * Test the getPresenceData method.
     *
     * @return void
     */
    public function test_get_presence_data()
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);
        $data = $user->getPresenceData();

        $this->assertEquals(['name' => 'John Doe'], $data);
    }
}

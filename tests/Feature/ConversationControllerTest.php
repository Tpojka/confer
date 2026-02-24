<?php

namespace Tpojka\Confer\Tests\Feature;

use Tpojka\Confer\Tests\TestCase;
use Tpojka\Confer\Conversation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConversationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an authenticated user can access the conversations index.
     *
     * @return void
     */
    public function test_authenticated_user_can_access_conversations_index()
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);
        $this->actingAs($user);

        $response = $this->get('confer/conversations');

        $response->assertStatus(200);
        $response->assertViewIs('confer::conversationlist');
    }

    /**
     * Test that a guest cannot access the conversations index.
     *
     * @return void
     */
    public function test_guest_cannot_access_conversations_index()
    {
        $response = $this->get('confer/conversations');

        $response->assertRedirect('login');
    }

    /**
     * Test that a user can access the user list.
     *
     * @return void
     */
    public function test_user_can_access_user_list()
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);
        $this->actingAs($user);

        // Mocking Push because listUsers calls Confer->getUsersState()
        \Push::shouldReceive('get')->andReturn((object)['users' => []]);

        $response = $this->get('confer/users');

        $response->assertStatus(200);
        $response->assertViewIs('confer::userlist');
    }

    /**
     * Test that a user can show a conversation.
     *
     * @return void
     */
    public function test_user_can_show_conversation()
    {
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);
        $this->actingAs($user);

        $conversation = Conversation::create(['name' => 'Test Conversation', 'is_private' => false]);
        $conversation->participants()->attach($user->id);

        $response = $this->get('confer/conversation/' . $conversation->id);

        $response->assertStatus(200);
        $response->assertViewIs('confer::conversation');
        $response->assertViewHas('conversation', $conversation);
    }
}

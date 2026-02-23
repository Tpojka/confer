<?php

namespace Tpojka\Confer\Tests\Unit;

use Tpojka\Confer\Tests\TestCase;
use Tpojka\Confer\Conversation;
use Tpojka\Confer\Message;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a conversation can have participants.
     *
     * @return void
     */
    public function test_conversation_can_have_participants()
    {
        $conversation = Conversation::create(['name' => 'Test Conversation', 'is_private' => false]);
        $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret']);

        $conversation->participants()->attach($user->id);

        $this->assertCount(1, $conversation->participants);
        $this->assertEquals('John Doe', $conversation->participants->first()->name);
    }

    /**
     * Test the isGlobal method.
     *
     * @return void
     */
    public function test_is_global_method()
    {
        $conversation1 = Conversation::create(['name' => 'Global', 'is_private' => false]);
        $conversation1->id = 1;
        $conversation1->save();

        $conversation2 = Conversation::create(['name' => 'Not Global', 'is_private' => false]);
        $conversation2->id = 2;
        $conversation2->save();

        $this->assertTrue($conversation1->isGlobal());
        $this->assertFalse($conversation2->isGlobal());
    }

    /**
     * Test that getPotentialInvitees returns users who are not participants.
     *
     * @return void
     */
    public function test_get_potential_invitees()
    {
        $conversation = Conversation::create(['name' => 'Test Conversation', 'is_private' => false]);
        $participant = User::create(['name' => 'Participant', 'email' => 'p@example.com', 'password' => 'secret']);
        $notParticipant = User::create(['name' => 'Not Participant', 'email' => 'np@example.com', 'password' => 'secret']);

        $conversation->participants()->attach($participant->id);

        $potentialInvitees = $conversation->getPotentialInvitees();

        $this->assertCount(1, $potentialInvitees);
        $this->assertTrue($potentialInvitees->contains('id', $notParticipant->id));
        $this->assertFalse($potentialInvitees->contains('id', $participant->id));
    }

    /**
     * Test that findOrCreateBetween returns an existing conversation if it exists.
     *
     * @return void
     */
    public function test_find_or_create_between_returns_existing_conversation()
    {
        $user1 = User::create(['name' => 'User 1', 'email' => 'u1@example.com', 'password' => 'secret']);
        $user2 = User::create(['name' => 'User 2', 'email' => 'u2@example.com', 'password' => 'secret']);

        $conversation = Conversation::create(['name' => 'Private', 'is_private' => true]);
        $conversation->participants()->attach([$user1->id, $user2->id]);

        $foundConversation = Conversation::findOrCreateBetween($user1, $user2);

        $this->assertEquals($conversation->id, $foundConversation->id);
    }
}

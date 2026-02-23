<?php

namespace Tpojka\Confer\Tests\Unit;

use Tpojka\Confer\Tests\TestCase;
use Tpojka\Confer\Confer;
use Tpojka\Confer\Facades\Push;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConferTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that getUsersState returns online and offline users correctly.
     *
     * @return void
     */
    public function test_get_users_state_returns_online_and_offline_users()
    {
        // Mock Push facade for get method
        Push::shouldReceive('get')
            ->once()
            ->with('/channels/presence-global/users')
            ->andReturn((object)[
                'users' => [
                    ['id' => 1],
                    ['id' => 2]
                ]
            ]);

        // Create some users
        $user1 = new User(['name' => 'Online User 1']);
        $user1->id = 1;
        $user1->save();

        $user2 = new User(['name' => 'Online User 2']);
        $user2->id = 2;
        $user2->save();

        $user3 = new User(['name' => 'Offline User']);
        $user3->id = 3;
        $user3->save();

        // Login as another user to be ignored by ignoreMe()
        $me = new User(['name' => 'Me']);
        $me->id = 4;
        $me->save();
        $this->actingAs($me);

        $confer = new Confer();
        $state = $confer->getUsersState();

        $this->assertCount(2, $state['online']);
        $this->assertCount(1, $state['offline']);
        $this->assertTrue($state['online']->contains('id', 1));
        $this->assertTrue($state['online']->contains('id', 2));
        $this->assertTrue($state['offline']->contains('id', 3));
        $this->assertFalse($state['online']->contains('id', 4));
        $this->assertFalse($state['offline']->contains('id', 4));
    }
}

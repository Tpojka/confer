<?php

use App\User;
use Tpojka\Confer\Conversation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ConferSeeder extends Seeder {

	/**
	 * Seed the confer tables of the application.
	 *
	 * @return void
	 */
	public function run()
	{
		$conversation = Conversation::create([
			'name' => 'Global',
			'is_private' => false
		]);

		foreach (User::all() as $user)
		{
			$user->conversations()->attach($conversation->id);
		}
	}

}

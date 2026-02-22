<?php

namespace Tpojka\Confer;

use Push;
use App\User;

class Confer {
	
	public $global = 'presence-global';
	
	public function __construct()
	{

	}

	/**
	 * Get the online/offline state of the users of the confer system
	 * 
	 * @return Array
	 */
	public function getUsersState()
	{
		$channel_info = Push::get('/channels/' . $this->global . '/users');
		$online_users = is_object($channel_info) && isset($channel_info->users) ? (array) $channel_info->users : [];

		$online_ids = [];
		foreach ($online_users as $online_user) {
			$online_ids[] = is_object($online_user) ? $online_user->id : (is_array($online_user) ? $online_user['id'] : null);
		}
		$online_ids = array_filter($online_ids);

		$users = User::ignoreMe()->get();
		$online = $users->filter(function($user) use ($online_ids) {
			return in_array($user->id, $online_ids);
		});
		$offline = $users->filter(function($user) use ($online_ids) {
			return ! in_array($user->id, $online_ids);
		});

		return ['online' => $online, 'offline' => $offline];
	}

}

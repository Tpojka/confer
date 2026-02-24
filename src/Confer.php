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
	 * @return array
	 */
	public function getUsersState()
	{
		$channelInfo = Push::get('/channels/' . $this->global . '/users');
		$onlineUsers = is_object($channelInfo) && isset($channelInfo->users) ? (array) $channelInfo->users : [];

		$onlineIds = [];
		foreach ($onlineUsers as $onlineUser) {
			$onlineIds[] = is_object($onlineUser) ? $onlineUser->id : (is_array($onlineUser) ? $onlineUser['id'] : null);
		}
		$onlineIds = array_filter($onlineIds);

		$users = User::ignoreMe()->get();
		$online = $users->filter(function($user) use ($onlineIds) {
			return in_array($user->id, $onlineIds);
		});
		$offline = $users->filter(function($user) use ($onlineIds) {
			return ! in_array($user->id, $onlineIds);
		});

		return ['online' => $online, 'offline' => $offline];
	}

}

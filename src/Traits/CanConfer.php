<?php

namespace Tpojka\Confer\Traits;

use Auth;
use Tpojka\Confer\Conversation;
use Tpojka\Confer\Message;

trait CanConfer {

	/**
	 * Handle events on the user model
	 *
	 * Currently used to make the user join the global conversation when created
	 */
	protected static function bootHandleEvents()
	{
		static::created(function($user) {
			$user->joinGlobal();
		});
	}

	/**
	 * Join the global conversation
	 *
	 * Fired by the User::created event
	 */
	private function joinGlobal()
	{
		$this->conversations()->attach(1);
	}
	
	/**
	 * Get the required data for presence
	 * 
	 * @return Array
	 */
	public function getPresenceData()
	{
		return [
			'name' => $this->name
		];
	}

	/**
	 * Get the conversations that are required in the messages bar
	 * 
	 * @return Collection of Tpojka\Confer\Conversation
	 */
	public function getBarConversations()
	{
		return $this->conversations()->where('is_private', false)->ignoreGlobal()->with('messages.sender')->orderBy('updated_at', 'DESC')->take(3)->get();
	}

	/**
	 * Filter query to not include current user
	 * 
	 * @param  $query
	 * @return $query
	 */
	public function scopeIgnoreMe($query)
	{
		return $query->where('id', '<>', Auth::user()->id);
	}

	/**
	 * Get the conversations that this user participates in
	 * 
	 * @return belongsToMany
	 */
	public function conversations()
	{
		return $this->belongsToMany(Conversation::class, 'confer_conversation_participants', 'user_id', 'conversation_id');
	}

	/**
	 * Identify whether a user participates in a conversation based on it's ID
	 * 
	 * @param  String $conversationId
	 * @return boolean
	 */
	public function participatesIn($conversationId)
	{
		return ! $this->conversations()->where('confer_conversations.id', $conversationId)->get()->isEmpty();
	}

	/**
	 * Get the IDs of the conversations that the user participates in
	 *
	 * Global channel is ignored
	 * 
	 * @return Array
	 */
	public function participatingConversations()
	{
		return $this->conversations()->ignoreGlobal()->pluck('confer_conversations.id');
	}

	public function privateConversations()
	{
		return $this->conversations()->isPrivate()->pluck('confer_conversations.id');
	}

	public function leaveConversation(Conversation $conversation)
	{
		$this->conversations()->detach($conversation->id);
	}

	/**
	 * Get the messages that the user has sent
	 * 
	 * @return hasMany
	 */
	public function sent()
	{
		return $this->hasMany(Message::class, 'sender_id');
	}

}

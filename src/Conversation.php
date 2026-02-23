<?php

namespace Tpojka\Confer;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model {
	
	protected $fillable = ['name', 'is_private'];
	protected $table = 'confer_conversations';
	protected $guarded = ['id'];

	// Relationships
	
	/**
	 * Get the participants of the conversation
	 * 
	 * @return belongsToMany
	 */
	public function participants()
	{
		return $this->belongsToMany('App\User', 'confer_conversation_participants', 'user_id', 'conversation_id');
	}

	/**
	 * Get the messages in the conversation
	 * 
	 * @return hasMany
	 */
	public function messages()
	{
		return $this->hasMany('Tpojka\Confer\Message', 'conversation_id');
	}

	public function isGlobal()
	{
		return $this->id == 1;
	}

	public function isPrivate()
	{
		return $this->is_private;
	}

	public function getChannel()
	{
		return 'private-conversation-' . $this->id;
	}

	/**
	 * Get the users who could be invited into the conversation
	 * 
	 * @return Collection
	 */
	public function getPotentialInvitees()
	{
		$currentParticipants = $this->participants()->pluck('id');
		return \App\User::whereNotIn('id', $currentParticipants)->get();

	}

	public function createNewWithAdditionalParticipants(Array $users, $name)
	{
		$conversation = $this->create([
			'name' => empty($name) ? 'Opps, I forgot to write a name - how embarrassing' : ucwords($name),
			'is_private' => false
		]);

		$currentParticipants = $this->participants()->pluck('id');
		$conversation->participants()->sync(array_merge($currentParticipants, $users));

		return $conversation;
	}

	public function addAdditionalParticipants(Array $users)
	{
		//$this->participants()->attach($users); cannot use this method due to SQL 2005
		$this->participants()->sync(array_merge($this->participants()->pluck('id'), $users));
	}

	public static function findOrCreateBetween(\App\User $user, \App\User $otherUser)
	{
		$userParticipates = $user->privateConversations();
		$otherUserParticipates = $otherUser->privateConversations();

		$static = new static;

		$sharedParticipations = collect(array_intersect($userParticipates, $otherUserParticipates));
		return $sharedParticipations->isEmpty() ? $static->createBetween($user, $otherUser) : $static->find($sharedParticipations->first());
	}

	public function createBetween($user, $otherUser)
	{
		$conversation = $this->create([
			'name' => 'Conversation between ' . $user->name . ' and ' . $otherUser->name,
			'is_private' => true
		]);

		$conversation->participants()->sync([$user->id, $otherUser->id]);

		return $conversation;
		//$user->conversations()->attach($conversation->id);
		//$other_user->conversations()->attach($conversation->id);
	}

	public function scopeIgnoreGlobal($query)
	{
		return $query->where('id', '<>', 1);
	}

	public function scopeIsPrivate($query)
	{
		return $query->where('is_private', true);
	}

}

<?php

namespace Tpojka\Confer;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Conversation extends Model
{
	protected $fillable = ['name', 'is_private'];
	protected $table = 'confer_conversations';
	protected $guarded = ['id'];

	// Relationships
	
	/**
	 * Get the participants of the conversation
     * @return BelongsToMany
	 */
	public function participants()
    {
		return $this->belongsToMany(User::class, 'confer_conversation_participants', 'conversation_id', 'user_id');
	}

	/**
	 * Get the messages in the conversation
	 */
	public function messages()
	{
		return $this->hasMany(Message::class, 'conversation_id');
	}

    /**
     * @return bool
     */
	public function isGlobal()
    {
		return $this->id == 1;
	}

    /**
     * @return bool
     */
	public function isPrivate()
    {
		return $this->is_private == 1;
	}

    /**
     * @return string
     */
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
		$currentParticipants = $this->participants()->pluck('users.id');
		return User::whereNotIn('users.id', $currentParticipants)->get();

	}

    /**
     * @param array $users
     * @param $name
     * @return static
     */
	public function createNewWithAdditionalParticipants(Array $users, $name)
    {
		$conversation = $this->create([
			'name' => empty($name) ? 'Opps, I forgot to write a name - how embarrassing' : ucwords($name),
			'is_private' => false
		]);

		$currentParticipants = $this->participants()->pluck('users.id');
		$conversation->participants()->sync(array_merge($currentParticipants->toArray(), $users));

		return $conversation;
	}

    /**
     * @param array $users
     * @return void
     */
	public function addAdditionalParticipants(array $users)
    {
		//$this->participants()->attach($users); cannot use this method due to SQL 2005
		$this->participants()->sync(array_merge($this->participants()->pluck('users.id')->toArray(), $users));
	}

    /**
     * @param User $user
     * @param User $otherUser
     * @return static
     */
	public static function findOrCreateBetween(User $user, User $otherUser)
    {
		$userParticipates = $user->privateConversations();
		$otherUserParticipates = $otherUser->privateConversations();

		$static = new static;

		$sharedParticipations = collect(array_intersect($userParticipates->toArray(), $otherUserParticipates->toArray()));
		return $sharedParticipations->isEmpty() 
            ? $static->createBetween($user, $otherUser) 
            : $static->find($sharedParticipations->first());
	}

    /**
     * @param $user
     * @param $otherUser
     * @return static
     */
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

    /**
     * @param $query
     * @return Builder
     */
	public function scopeIgnoreGlobal($query)
    {
		return $query->where('confer_conversations.id', '<>', 1);
	}

    /**
     * @param $query
     * @return Builder
     */
	public function scopeIsPrivate($query)
    {
		return $query->where('is_private', true);
	}
}

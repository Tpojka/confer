<?php

namespace Tpojka\Confer\Commands;

use App\User;
use Tpojka\Confer\Confer;
use Tpojka\Confer\Facades\Push;
use Tpojka\Confer\Conversation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConversationWasRequested implements ShouldQueue {
	
	use InteractsWithQueue;

	protected $conversation;
	protected $requester;
	protected $is_group;
	protected $confer;

	public function __construct(Conversation $conversation, User $requester)
	{
		$this->conversation = $conversation;
		$this->requester = $requester;
		$this->confer = new Confer();
	}

	/**
	 * Handle the command.
	 */
	public function handle()
	{
		$other_participants = $this->conversation->participants()->where('id', '<>', $this->requester->id)->get();
		foreach ($other_participants as $participant) {
			Push::trigger('private-notifications-' . $participant->id, 'ConversationWasRequested', ['conversation' => $this->conversation, 'requester' => $this->requester]);
		}
	}

}

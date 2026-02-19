<?php

namespace Tpojka\Confer\Commands;

use App\User;
use Tpojka\Confer\Confer;
use Tpojka\Confer\Message;
use Tpojka\Confer\Conversation;
use Tpojka\Confer\Commands\MessageWasSent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Tpojka\Confer\Commands\ConversationWasRequested;

class ParticipantsWereAdded {

	use DispatchesJobs;

	protected $conversation;
	protected $conversation_was_created;
	protected $confer;
	protected $users;
	protected $inviter;

	public function __construct(Conversation $conversation, Array $users, User $inviter, $conversation_was_created)
	{
		$this->conversation = $conversation;
		$this->users = $users;
		$this->inviter = $inviter;
		$this->conversation_was_created = $conversation_was_created;
		$this->confer = new Confer();
	}

	/**
	 * Handle the command.
	 */
	public function handle()
	{
		if ($this->conversation_was_created)
		{
			$this->makeConversationCreatedMessage();
			//$this->makeNameSetMessage();
		}	
		$this->makeJoinMessage();
		$this->inviteUsers();
	}

	private function inviteUsers()
	{
		$users = $this->conversation_was_created ? $this->conversation->participants()->ignoreMe()->get() : User::whereIn('id', $this->users)->get();
		foreach ($users as $user)
		{
			$this->dispatch(new ConversationWasRequested($this->conversation, $this->inviter, $user));
		}
	}

	private function makeJoinMessage()
	{
		$users = $this->conversation_was_created ? confer_make_list($this->conversation->participants()->ignoreMe()->pluck('name')) : confer_make_list(User::whereIn('id', $this->users)->pluck('name'));
		$message = Message::create([
			'conversation_id' => $this->conversation->id,
			'body' => '<strong>' . $users . '</strong> joined the conversation on ' . $this->inviter->name . '\'s invitation',
			'sender_id' => $this->inviter->id,
			'type' => 'conversation_message'
		]);
		$this->dispatch(new MessageWasSent($message));
	}

	private function makeConversationCreatedMessage()
	{
		$message = Message::create([
			'conversation_id' => $this->conversation->id,
			'body' => $this->inviter->name . ' created the conversation and called it <strong>' . $this->conversation->name . '</strong>',
			'sender_id' => $this->inviter->id,
			'type' => 'conversation_message'
		]);
		$this->dispatch(new MessageWasSent($message));
	}

	private function makeNameSetMessage()
	{
		$message = Message::create([
			'conversation_id' => $this->conversation->id,
			'body' => $this->inviter->name . ' set the name to <strong>' . $this->conversation->name . '</strong>',
			'sender_id' => $this->inviter->id,
			'type' => 'conversation_message'
		]);
		$this->dispatch(new MessageWasSent($message));
	}

}

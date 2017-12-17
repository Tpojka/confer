<?php

namespace Tpojka\Confer\Commands;

use App\User;
use Tpojka\Confer\Confer;
use App\Commands\Command;
use Tpojka\Confer\Message;
use Tpojka\Confer\Conversation;
use Tpojka\Confer\Commands\MessageWasSent;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ParticipantLeft {

	use DispatchesJobs;

	protected $conversation;
	protected $leaver;

	public function __construct(Conversation $conversation, User $leaver)
	{
		$this->conversation = $conversation;
		$this->leaver = $leaver;
	}

	/**
	 * Handle the command.
	 */
	public function handle()
	{
		$this->makeLeftMessage();
	}

	private function makeLeftMessage()
	{
		$message = Message::create([
			'conversation_id' => $this->conversation->id,
			'body' => '<strong>' . $this->leaver->name . '</strong> left the conversation',
			'sender_id' => $this->leaver->id,
			'type' => 'conversation_message'
		]);
		$this->dispatch(new MessageWasSent($message));
	}

}
